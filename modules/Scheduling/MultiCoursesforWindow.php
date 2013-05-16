<?php
#**************************************************************************
#  openSIS is a free student information system for public and non-public 
#  schools from Open Solutions for Education, Inc. web: www.os4ed.com
#
#  openSIS is  web-based, open source, and comes packed with features that 
#  include student demographic info, scheduling, grade book, attendance, 
#  report cards, eligibility, transcripts, parent portal, 
#  student portal and more.   
#
#  Visit the openSIS web site at http://www.opensis.com to learn more.
#  If you have question regarding this system or the license, please send 
#  an email to info@os4ed.com.
#
#  This program is released under the terms of the GNU General Public License as  
#  published by the Free Software Foundation, version 2 of the License. 
#  See license.txt.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#***************************************************************************************
include('../../Redirect_modules.php');
unset($_SESSION['_REQUEST_vars']['subject_id']);unset($_SESSION['_REQUEST_vars']['course_id']);unset($_SESSION['_REQUEST_vars']['course_period_id']);
if($_REQUEST['ses'])
{
    unset($_SESSION['course_period']);
    unset($_SESSION['crs_id']);
    unset($_SESSION['marking_period_id']);
    unset($_SESSION['mp']);
}
// if only one subject, select it automatically -- works for Course Setup and Choose a Course
if($_REQUEST['modfunc']!='delete' && !$_REQUEST['subject_id'])
{
	$subjects_RET = DBGet(DBQuery('SELECT SUBJECT_ID,TITLE FROM course_subjects WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\''));
	if(count($subjects_RET)==1)
		$_REQUEST['subject_id'] = $subjects_RET[1]['SUBJECT_ID'];
}

if($_REQUEST['course_modfunc']=='search')
{
	PopTable('header','Search');
	echo "<FORM name=F1 id=F1 action=for_window.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&course_modfunc=search method=POST>";
	echo '<TABLE><TR><TD><INPUT type=text class=cell_floating name=search_term value="'.$_REQUEST['search_term'].'"></TD><TD><INPUT type=submit class=btn_medium value=Search onclick=\'formload_ajax("F1")\';></TD></TR></TABLE>';
	echo '</FORM>';
	PopTable('footer');

	if($_REQUEST['search_term'])
	{
		$subjects_RET = DBGet(DBQuery('SELECT SUBJECT_ID,TITLE FROM course_subjects WHERE (UPPER(TITLE) LIKE \''.'%'.strtoupper($_REQUEST['search_term']).'%' .'\' OR UPPER(SHORT_NAME) = \''.strtoupper($_REQUEST['search_term']).'\') AND SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\''));
		$courses_RET = DBGet(DBQuery('SELECT SUBJECT_ID,COURSE_ID,TITLE FROM courses WHERE (UPPER(TITLE) LIKE \''.'%'.strtoupper($_REQUEST['search_term']).'%'.'\' OR UPPER(SHORT_NAME) = \''.strtoupper($_REQUEST['search_term']).'\') AND SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\''));
		$periods_RET = DBGet(DBQuery('SELECT c.SUBJECT_ID,cp.COURSE_ID,cp.COURSE_PERIOD_ID,cp.TITLE FROM course_periods cp,courses c WHERE cp.COURSE_ID=c.COURSE_ID AND (UPPER(cp.TITLE) LIKE \''.'%'.strtoupper($_REQUEST['search_term']).'%'.'\' OR UPPER(cp.SHORT_NAME) = \''.strtoupper($_REQUEST['search_term']).'\') AND cp.SYEAR=\''.UserSyear().'\' AND cp.SCHOOL_ID=\''.UserSchool().'\''));

		echo '<TABLE><TR><TD valign=top>';
		$link['TITLE']['link'] = "for_window.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]";
		//$link['TITLE']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]\");'";
		$link['TITLE']['variables'] = array('subject_id'=>'SUBJECT_ID');
		ListOutput($subjects_RET,array('TITLE'=>'Subject'),'Subject','Subjects',$link,array(),array('search'=>false,'save'=>false));
		echo '</TD><TD valign=top>';
		$link['TITLE']['link'] = "for_window.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]";
		//$link['TITLE']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]\");'";
		$link['TITLE']['variables'] = array('subject_id'=>'SUBJECT_ID','course_id'=>'COURSE_ID');
		ListOutput($courses_RET,array('TITLE'=>'Course'),'Course','Courses',$link,array(),array('search'=>false,'save'=>false));
		echo '</TD><TD valign=top>';
		$link['TITLE']['link'] = "for_window.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]";
		//$link['TITLE']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]\");'";
		$link['TITLE']['variables'] = array('subject_id'=>'SUBJECT_ID','course_id'=>'COURSE_ID','course_period_id'=>'COURSE_PERIOD_ID');
		ListOutput($periods_RET,array('TITLE'=>'Course Period'),'Course Period','Course Periods',$link,array(),array('search'=>false,'save'=>false));
		echo '</TD></TR></TABLE>';
	}
}

// UPDATING
if($_REQUEST['tables'] && ($_POST['tables'] || $_REQUEST['ajax']) && AllowEdit())
{
	$where = array('course_subjects'=>'SUBJECT_ID',
					'courses'=>'COURSE_ID',
					'course_periods'=>'COURSE_PERIOD_ID');

	if($_REQUEST['tables']['parent_id'])
		$_REQUEST['tables']['course_periods'][$_REQUEST['course_period_id']]['PARENT_ID'] = $_REQUEST['tables']['parent_id'];

	foreach($_REQUEST['tables'] as $table_name=>$tables)
	{
		foreach($tables as $id=>$columns)
		{
			if($columns['TOTAL_SEATS'] && !is_numeric($columns['TOTAL_SEATS']))
				$columns['TOTAL_SEATS'] = ereg_replace('[^0-9]+','',$columns['TOTAL_SEATS']);
			if($columns['DAYS'])
			{
				foreach($columns['DAYS'] as $day=>$y)
				{
					if($y=='Y')
						$days .= $day;
				}
				$columns['DAYS'] = $days;
			}

			if($id!='new')
			{
				if($table_name=='courses' && $columns['SUBJECT_ID'] && $columns['SUBJECT_ID']!=$_REQUEST['subject_id'])
					$_REQUEST['subject_id'] = $columns['SUBJECT_ID'];

				$sql = 'UPDATE '.$table_name.' SET ';

				if($table_name=='course_periods')
				{
					$current = DBGet(DBQuery('SELECT TEACHER_ID,PERIOD_ID,MARKING_PERIOD_ID,DAYS,SHORT_NAME FROM course_periods WHERE '.$where[$table_name].'=\''.$id.'\''));

					if($columns['TEACHER_ID'])
						$staff_id = $columns['TEACHER_ID'];
					else
						$staff_id = $current[1]['TEACHER_ID'];
					if($columns['PERIOD_ID'])
						$period_id = $columns['PERIOD_ID'];
					else
						$period_id = $current[1]['PERIOD_ID'];
					if(isset($columns['MARKING_PERIOD_ID']))
						$marking_period_id = $columns['MARKING_PERIOD_ID'];
					else
						$marking_period_id = $current[1]['MARKING_PERIOD_ID'];
					if($columns['DAYS'])
						$days = $columns['DAYS'];
					else
						$days = $current[1]['DAYS'];
					if($columns['SHORT_NAME'])
						$short_name = $columns['SHORT_NAME'];
					else
						$short_name = $current[1]['SHORT_NAME'];

					$teacher = DBGet(DBQuery('SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME FROM staff WHERE SYEAR=\''.UserSyear().'\' AND STAFF_ID=\''.$staff_id.'\''));
					$period = DBGet(DBQuery('SELECT TITLE FROM school_periods WHERE PERIOD_ID=\''.$period_id.'\' AND SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\''));
					if(GetMP($marking_period_id,'TABLE')!='school_years')
						$mp_title = GetMP($marking_period_id,'SHORT_NAME').' - ';
					if(strlen($days)<5)
						$mp_title .= $days.' - ';
					if($short_name)
						$mp_title .= $short_name.' - ';

					$title = str_replace("'","''",$period[1]['TITLE'].' - '.$mp_title.$teacher[1]['FIRST_NAME'].' '.$teacher[1]['MIDDLE_NAME'].' '.$teacher[1]['LAST_NAME']);
					$sql .= 'TITLE=\''.$title.'\',';

					if(isset($columns['MARKING_PERIOD_ID']))
					{
						if(GetMP($columns['MARKING_PERIOD_ID'],'TABLE')=='school_years')
							$columns['MP'] = 'FY';
						elseif(GetMP($columns['MARKING_PERIOD_ID'],'TABLE')=='school_semesters')
							$columns['MP'] = 'SEM';
						else
							$columns['MP'] = 'QTR';
					}
				}

				foreach($columns as $column=>$value)
					$sql .= $column.'=\''.str_replace("\'","''",$value).'\',';

				$sql = substr($sql,0,-1) . ' WHERE '.$where[$table_name].'=\''.$id.'\'';
				DBQuery($sql);

			}
			else
			{
				$sql = 'INSERT INTO '.$table_name.' ';

				if($table_name=='course_subjects')
				{
					//$id = DBGet(DBQuery("SELECT ".db_seq_nextval('COURSE_SUBJECTS_SEQ').' AS ID'.FROM_DUAL));
                                        $id = DBGet(DBQuery('SHOW TABLE STATUS LIKE \''.'course_subjects'.'\''));
                                      $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
					$fields = 'SCHOOL_ID,SYEAR,';
					$values = "'".UserSchool()."','".UserSyear()."',";
					$_REQUEST['subject_id'] = $id[1]['ID'];
				}
				elseif($table_name=='courses')
				{
					//$id = DBGet(DBQuery("SELECT ".db_seq_nextval('COURSES_SEQ').' AS ID'.FROM_DUAL));
                                        /*  $id = DBGet(DBQuery("SELECT max(COURSE_ID) AS ID from courses "));
					$_REQUEST['course_id'] = $id[1]['ID']; */
                                        $id = DBGet(DBQuery('SHOW TABLE STATUS LIKE \''.'courses'.'\''));
                                        $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
                                        $_REQUEST['course_id'] = $id[1]['ID'];
					$fields = 'SUBJECT_ID,SCHOOL_ID,SYEAR,';
					$values = '\''.$_REQUEST[subject_id].'\',\''.UserSchool().'\',\''.UserSyear().'\',';
                                      
				}

				elseif($table_name=='course_periods')
				{
					//$id = DBGet(DBQuery("SELECT ".db_seq_nextval('COURSE_PERIODS_SEQ').' AS ID'.FROM_DUAL));
                                        $id = DBGet(DBQuery('SHOW TABLE STATUS LIKE '.'course_periods'.'\''));
                                      $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
					$fields = 'SYEAR,SCHOOL_ID,COURSE_ID,TITLE,';
					$teacher = DBGet(DBQuery('SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME FROM staff WHERE SYEAR=\''.UserSyear().'\' AND STAFF_ID=\''.$columns[TEACHER_ID].'\''));
					$period = DBGet(DBQuery('SELECT TITLE FROM school_periods WHERE PERIOD_ID=\''.$columns[PERIOD_ID].'\' AND SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\''));

					if(!isset($columns['PARENT_ID']))
						$columns['PARENT_ID'] = $id[1]['ID'];

					if(isset($columns['MARKING_PERIOD_ID']))
					{
						if(GetMP($columns['MARKING_PERIOD_ID'],'TABLE')=='school_years')
							$columns['MP'] = 'FY';
						elseif(GetMP($columns['MARKING_PERIOD_ID'],'TABLE')=='school_semesters')
							$columns['MP'] = 'SEM';
						else
							$columns['MP'] = 'QTR';

						if(GetMP($columns['MARKING_PERIOD_ID'],'TABLE')!='school_years')
							$mp_title = GetMP($columns['MARKING_PERIOD_ID'],'SHORT_NAME').' - ';
					}

					if(strlen($columns['DAYS'])<5)
						$mp_title .= $columns['DAYS'].' - ';
					if($columns['SHORT_NAME'])
						$mp_title .= $columns['SHORT_NAME'].' - ';
					$title = str_replace("'","''",$period[1]['TITLE'].' - '.$mp_title.$teacher[1]['FIRST_NAME'].' '.$teacher[1]['MIDDLE_NAME'].' '.$teacher[1]['LAST_NAME']);

					$values = '\''.UserSyear().'\',\''.UserSchool().'\',\''.$_REQUEST[course_id].'\',\''.$title.'\',';
					$_REQUEST['course_period_id'] = $id[1]['ID'];
				}

				$go = 0;
				foreach($columns as $column=>$value)
				{
					if(isset($value))
					{
						$fields .= $column.',';
						$values .= '\''.str_replace("\'","''",$value).'\',';
						$go = true;
					}
				}
				$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';

				if($go)
					DBQuery($sql);
                                        
			}
		}
	}
	unset($_REQUEST['tables']);
}

if($_REQUEST['modfunc']=='delete' && AllowEdit())
{
 	unset($sql);
	if($_REQUEST['course_period_id'])
	{
		$table = 'course period';
		$sql[] = 'UPDATE course_periods SET PARENT_ID=NULL WHERE PARENT_ID=\''.$_REQUEST[course_period_id].'\'';
		$sql[] = 'DELETE FROM course_periods WHERE COURSE_PERIOD_ID=\''.$_REQUEST[course_period_id].'\'';
		$sql[] = 'DELETE FROM schedule WHERE COURSE_PERIOD_ID=\''.$_REQUEST[course_period_id].'\'';
	}

	elseif($_REQUEST['course_id'])
	{
		$table = 'course';
		$sql[] = 'DELETE FROM courses WHERE COURSE_ID=\''.$_REQUEST[course_id].'\'';
		$sql[] = "UPDATE course_periods SET PARENT_ID=NULL WHERE PARENT_ID IN (SELECT COURSE_PERIOD_ID FROM course_periods WHERE COURSE_ID='$_REQUEST[course_id]')";
		$sql[] = 'DELETE FROM course_periods WHERE COURSE_ID=\''.$_REQUEST[course_id].'\'';
		$sql[] = 'DELETE FROM schedule WHERE COURSE_ID=\''.$_REQUEST[course_id].'\'';
		$sql[] = 'DELETE FROM schedule_requests WHERE COURSE_ID=\''.$_REQUEST[course_id].'\'';
	}
	elseif($_REQUEST['subject_id'])
	{
		$table = 'subject';
		$sql[] = 'DELETE FROM course_subjects WHERE SUBJECT_ID=\''.$_REQUEST[subject_id].'\'';
		$courses = DBGet(DBQuery('SELECT COURSE_ID FROM courses WHERE SUBJECT_ID=\''.$_REQUEST[subject_id].'\''));
		if(count($courses))
		{
			foreach($courses as $course)
			{
				$sql[] = 'DELETE FROM courses WHERE COURSE_ID=\''.$course[COURSE_ID].'\'';
				$sql[] = 'UPDATE course_periods SET PARENT_ID=NULL WHERE PARENT_ID IN (SELECT COURSE_PERIOD_ID FROM course_periods WHERE COURSE_ID=\''.$course[COURSE_ID].'\')';
				$sql[] = 'DELETE FROM course_periods WHERE COURSE_ID=\''.$course[COURSE_ID].'\'';
				$sql[] = 'DELETE FROM schedule WHERE COURSE_ID=\''.$course[COURSE_ID].'\'';
				$sql[] = 'DELETE FROM schedule_requests WHERE COURSE_ID=\''.$course[COURSE_ID].'\'';
			}
		}
	}

	if(DeletePrompt($table))
	{
		foreach($sql as $query)
			DBQuery($query);
		unset($_REQUEST['modfunc']);
	}
}

if((!$_REQUEST['modfunc'] || $_REQUEST['modfunc']=='choose_course') && !$_REQUEST['course_modfunc'])
{
	if($_REQUEST['modfunc']!='choose_course')
		DrawBC("Scheduling > ".ProgramTitle());
	$sql = 'SELECT SUBJECT_ID,TITLE FROM course_subjects WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' ORDER BY TITLE';
	$QI = DBQuery($sql);
	$subjects_RET = DBGet($QI);

	if($_REQUEST['modfunc']!='choose_course')
	{
		if(AllowEdit())
			$delete_button = "<INPUT type=button class=btn_medium value=Delete onClick='javascript:window.location=\"for_window.php?modname=$_REQUEST[modname]&modfunc=delete&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]&course_period_id=$_REQUEST[course_period_id]\"'> ";
		// ADDING & EDITING FORM
		if($_REQUEST['course_period_id'])
		{
			if($_REQUEST['course_period_id']!='new')
			{
				$sql = 'SELECT PARENT_ID,TITLE,SHORT_NAME,PERIOD_ID,DAYS,
								MP,MARKING_PERIOD_ID,TEACHER_ID,CALENDAR_ID,
								ROOM,TOTAL_SEATS,DOES_ATTENDANCE,
								GRADE_SCALE_ID,DOES_HONOR_ROLL,DOES_CLASS_RANK,
								GENDER_RESTRICTION,HOUSE_RESTRICTION,CREDITS,
								HALF_DAY,DOES_BREAKOFF
						FROM course_periods
						WHERE COURSE_PERIOD_ID=\''.$_REQUEST[course_period_id].'\'';
				$QI = DBQuery($sql);
				$RET = DBGet($QI);
				$RET = $RET[1];
				$title = $RET['TITLE'];
				$new = false;
			}
			else
			{
				$sql = 'SELECT TITLE
						FROM courses
						WHERE COURSE_ID=\''.$_REQUEST[course_id].'\'';
				$QI = DBQuery($sql);
				$RET = DBGet($QI);
				$title = $RET[1]['TITLE'].' - New Period';
				unset($delete_button);
				unset($RET);
				$checked = 'CHECKED';
				$new = true;
			}

			echo "<FORM name=F2 id=F2 action=for_window.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]&course_period_id=$_REQUEST[course_period_id] method=POST>";
			DrawHeaderHome($title,$delete_button.SubmitButton('Save','','class=btn_medium onclick="formcheck_scheduling_course_F2();"'));
			
			$header .= '<TABLE cellpadding=3 width=760 >';
			$header .= '<TR>';

			$header .= '<TD>' . TextInput($RET['SHORT_NAME'],'tables[course_periods]['.$_REQUEST['course_period_id'].'][SHORT_NAME]','Short Name','class=cell_floating') . '</TD>';

			$teachers_RET = DBGet(DBQuery("SELECT STAFF_ID,LAST_NAME,FIRST_NAME,MIDDLE_NAME FROM staff WHERE (SCHOOLS IS NULL OR strpos(SCHOOLS,',".UserSchool().",')>0) AND SYEAR='".UserSyear()."' AND PROFILE='teacher' ORDER BY LAST_NAME,FIRST_NAME"));
			if(count($teachers_RET))
			{
				foreach($teachers_RET as $teacher)
					$teachers[$teacher['STAFF_ID']] = $teacher['LAST_NAME'].', '.$teacher['FIRST_NAME'].' '.$teacher['MIDDLE_NAME'];
			}
			$header .= '<TD>' . SelectInput($RET['TEACHER_ID'],'tables[course_periods]['.$_REQUEST['course_period_id'].'][TEACHER_ID]','Teacher',$teachers) . '</TD>';

			$header .= '<TD>' . TextInput($RET['ROOM'],'tables[course_periods]['.$_REQUEST['course_period_id'].'][ROOM]','Room','class=cell_floating') . '</TD>';

			$periods_RET = DBGet(DBQuery('SELECT PERIOD_ID,TITLE FROM school_periods WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' ORDER BY SORT_ORDER'));
			if(count($periods_RET))
			{
				foreach($periods_RET as $period)
					$periods[$period['PERIOD_ID']] = $period['TITLE'];
			}
			$header .= '<TD>' . SelectInput($RET['PERIOD_ID'],'tables[course_periods]['.$_REQUEST['course_period_id'].'][PERIOD_ID]','Period',$periods) . '</TD>';
			$header .= '<TD>';
			if($new==false)
				$header .= '<DIV id=days><div onclick=\'addHTML("';
			$header .= '<TABLE><TR>';
			$days = array('U','M','T','W','H','F','S');
			foreach($days as $day)
			{
				if(strpos($RET['DAYS'],$day)!==false || ($new && $day!='S' && $day!='U'))
					$value = 'Y';
				else
					$value = '';

				$header .= '<TD>'.str_replace('"','\"',CheckboxInput($value,'tables[course_periods]['.$_REQUEST['course_period_id'].'][DAYS]['.$day.']',($day=='U'?'S':$day),$checked,false,'','',false)).'</TD>';
			}
			$header .= '</TR></TABLE>';
			
			if($new==false)
				$header .= '","days",true);\'>'.$RET['DAYS'].'</div></DIV><small><FONT color='.Preferences('TITLES').'>Meeting Days</FONT></small>';
			$header .= '</TD>';
			//$header .= '<TD>' . SelectInput($RET['MP'],'tables[course_periods]['.$_REQUEST['course_period_id'].'][MP]','Length',array('FY'=>'Full Year','SEM'=>'Semester','QTR'=>'Marking Period')) . '</TD>';
			$mp_RET = DBGet(DBQuery('SELECT MARKING_PERIOD_ID,SHORT_NAME,2 AS TABLE,SORT_ORDER FROM school_quarters WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' UNION SELECT MARKING_PERIOD_ID,SHORT_NAME,1 AS TABLE,SORT_ORDER FROM school_semesters WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' UNION SELECT MARKING_PERIOD_ID,SHORT_NAME,0 AS TABLE,SORT_ORDER FROM school_years WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' ORDER BY 3,4'));
			unset($options);

			if(count($mp_RET))
			{
				foreach($mp_RET as $mp)
					$options[$mp['MARKING_PERIOD_ID']] = $mp['SHORT_NAME'];
			}
			$header .= '<TD>' . SelectInput($RET['MARKING_PERIOD_ID'],'tables[course_periods]['.$_REQUEST['course_period_id'].'][MARKING_PERIOD_ID]','Marking Period',$options,false) . '</TD>';
			$header .= '<TD>' . TextInput($RET['TOTAL_SEATS'],'tables[course_periods]['.$_REQUEST['course_period_id'].'][TOTAL_SEATS]','Seats','size=4 class=cell_floating') . '</TD>';

			$header .= '</TR>';

			$header .= '<TR>';

			$header .= '<TD>' . CheckboxInput($RET['DOES_ATTENDANCE'],'tables[course_periods]['.$_REQUEST['course_period_id'].'][DOES_ATTENDANCE]','Takes Attendance',$checked,$new,'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>') . '</TD>';
			$header .= '<TD>' . CheckboxInput($RET['DOES_HONOR_ROLL'],'tables[course_periods]['.$_REQUEST['course_period_id'].'][DOES_HONOR_ROLL]','Affects Honor Roll',$checked,$new,'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>') . '</TD>';
			$header .= '<TD>' . CheckboxInput($RET['DOES_CLASS_RANK'],'tables[course_periods]['.$_REQUEST['course_period_id'].'][DOES_CLASS_RANK]','Affects Class Rank',$checked,$new,'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>') . '</TD>';
			$header .= '<TD>' . SelectInput($RET['GENDER_RESTRICTION'],'tables[course_periods]['.$_REQUEST['course_period_id'].'][GENDER_RESTRICTION]','Gender Restriction',array('N'=>'None','M'=>'Male','F'=>'Female'),false) . '</TD>';

			$options_RET = DBGet(DBQuery('SELECT TITLE,ID FROM report_card_grade_scales WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\''));
			$options = array();
			foreach($options_RET as $option)
				$options[$option['ID']] = $option['TITLE'];
			$header .= '<TD>' . SelectInput($RET['GRADE_SCALE_ID'],'tables[course_periods]['.$_REQUEST['course_period_id'].'][GRADE_SCALE_ID]','Grading Scale',$options,'Not Graded') . '</TD>';
            //BJJ Added to handle credits
            $header .= '<TD>' . TextInput(sprintf('%0.3f',$RET['CREDITS']),'tables[course_periods]['.$_REQUEST['course_period_id'].'][CREDITS]','Credits','size=4 class=cell_floating') . '</TD>';
			$options_RET = DBGet(DBQuery("SELECT TITLE,CALENDAR_ID FROM attendance_calendars WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY DEFAULT_CALENDAR"));
			$options = array();
			foreach($options_RET as $option)
				$options[$option['CALENDAR_ID']] = $option['TITLE'];
			$header .= '<TD>' . SelectInput($RET['CALENDAR_ID'],'tables[course_periods]['.$_REQUEST['course_period_id'].'][CALENDAR_ID]','Calendar',$options,false) . '</TD>';

			//BJJ Parent course select was here.

			$header .= '</TR>';

			$header .= '<TR>';

			//$header .= '<TD>' . CheckboxInput($RET['HOUSE_RESTRICTION'],'tables[course_periods]['.$_REQUEST['course_period_id'].'][HOUSE_RESTRICTION]','Restricts House','',$new) . '</TD>';
			$header .= '<TD>' . CheckboxInput($RET['HALF_DAY'],'tables[course_periods]['.$_REQUEST['course_period_id'].'][HALF_DAY]','Half Day',$checked,$new,'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>') . '</TD>';
			$header .= '<TD>' . CheckboxInput($RET['DOES_BREAKOFF'],'tables[course_periods]['.$_REQUEST['course_period_id'].'][DOES_BREAKOFF]','Allow Teacher Gradescale',$checked,$new,'<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>') . '</TD>';
            //BJJ added cells to place parent selection in last column.
            $header .=  "<td colspan= 4>&nbsp;</td>";
            
                                   
            //BJJ moved parent course select here:
            if($_REQUEST['course_period_id']!='new' && $RET['PARENT_ID']!=$_REQUEST['course_period_id'])
            {
                $parent = DBGet(DBQuery('SELECT cp.TITLE as CP_TITLE,c.TITLE AS C_TITLE FROM course_periods cp,courses c WHERE c.COURSE_ID=cp.COURSE_ID AND cp.COURSE_PERIOD_ID=\''.$RET['PARENT_ID'].'\''));
                $parent = $parent[1]['C_TITLE'].' : '.$parent[1]['CP_TITLE'];
            }
            elseif($_REQUEST['course_period_id']!='new')
            {
                $children = DBGet(DBQuery('SELECT COURSE_PERIOD_ID FROM course_periods WHERE PARENT_ID=\''.$_REQUEST['course_period_id'].'\' AND COURSE_PERIOD_ID!=\''.$_REQUEST['course_period_id'].'\''));
                if(count($children))
                    $parent = 'N/A';
                else
                    $parent = 'None';
            }

            $header .= "<TD colspan=2><DIV id=course_div>".$parent."</DIV> ".($parent!='N/A'?"<A HREF=# onclick='window.open(\"for_window.php?modname=".$_REQUEST['modname']."&modfunc=choose_course\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'><SMALL>Choose</SMALL></A><BR>":'')."<small><FONT color=".Preferences('TITLES').">Parent Course Period</FONT></small></TD>";
			$header .= '</TR>';
			$header .= '</TABLE>';
			PopTable_wo_header ('header');
			DrawHeader($header);
			PopTable ('footer');
			//echo '</FORM>';
		}

		elseif($_REQUEST['course_id'])
		{
			if($_REQUEST['course_id']!='new')
			{
				$sql = 'SELECT TITLE,SHORT_NAME,GRADE_LEVEL
						FROM courses
						WHERE COURSE_ID=\''.$_REQUEST[course_id].'\'';
				$QI = DBQuery($sql);
				$RET = DBGet($QI);
				$RET = $RET[1];
				$title = $RET['TITLE'];
			}
			else
			{
				$sql = 'SELECT TITLE
						FROM course_subjects
						WHERE SUBJECT_ID=\''.$_REQUEST[subject_id].'\' ORDER BY TITLE';
				$QI = DBQuery($sql);
				$RET = DBGet($QI);
				$title = $RET[1]['TITLE'].' - New Course';
				unset($delete_button);
				unset($RET);
			}

			echo "<FORM name=F3 id=F3 action=for_window.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id] method=POST>";
			DrawHeaderHome($title,$delete_button.SubmitButton('Save','','class=btn_medium onclick="formcheck_scheduling_course_F3();"'));
			$header .= '<TABLE cellpadding=3 width=100%>';
			$header .= '<TR>';

			$header .= '<TD>' . TextInput($RET['TITLE'],'tables[courses]['.$_REQUEST['course_id'].'][TITLE]','Title','class=cell_floating') . '</TD>';
			$header .= '<TD>' . TextInput($RET['SHORT_NAME'],'tables[courses]['.$_REQUEST['course_id'].'][SHORT_NAME]','Short Name','class=cell_floating') . '</TD>';
			if($_REQUEST['modfunc']!='choose_course')
			{
				foreach($subjects_RET as $type)
					$options[$type['SUBJECT_ID']] = $type['TITLE'];

				$header .= '<TD>' . SelectInput($RET['SUBJECT_ID']?$RET['SUBJECT_ID']:$_REQUEST['subject_id'],'tables[courses]['.$_REQUEST['course_id'].'][SUBJECT_ID]','Subject',$options,false) . '</TD>';
			}
			$header .= '</TR>';
			$header .= '</TABLE>';
			DrawHeaderHome($header);
			echo '</FORM>';
		}
		elseif($_REQUEST['subject_id'])
		{
			if($_REQUEST['subject_id']!='new')
			{
				$sql = 'SELECT TITLE
						FROM course_subjects
						WHERE SUBJECT_ID=\''.$_REQUEST[subject_id].'\'';
				$QI = DBQuery($sql);
				$RET = DBGet($QI);
				$RET = $RET[1];
				$title = $RET['TITLE'];
			}
			else
			{
				$title = 'New Subject';
				unset($delete_button);
			}

			echo "<FORM name=F4 id=F4 action=for_window.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id] method=POST>";
			DrawHeaderHome($title,$delete_button.SubmitButton('Save','','class=btn_medium onclick="formcheck_scheduling_course_F4();"'));
			$header .= '<TABLE cellpadding=3 width=100%>';
			$header .= '<TR>';

			$header .= '<TD>' . TextInput($RET['TITLE'],'tables[course_subjects]['.$_REQUEST['subject_id'].'][TITLE]','Title','class=cell_floating') . '</TD>';

			$header .= '</TR>';
			$header .= '</TABLE>';
			DrawHeader($header);
			echo '</FORM>';
		}
	}
        if((!$_REQUEST['course_period'] && isset($_REQUEST['add_cid'])))
        echo '<center><p><font color=#FF0000><b>Please Select A Course Period.</b></font></p></center>';
        if(isset($_REQUEST['add_cid']))
        {
            foreach ($_REQUEST['course_period'] as $c_pid => $select_cpid)
            {
                $warnings='';
              if($_SESSION['course_period'])
              {
                    foreach ($_SESSION['course_period'] as $ses_pid => $ses_value)
                    {
                        $min_date = DBGet(DBQuery('SELECT min(SCHOOL_DATE) AS MIN_DATE FROM attendance_calendar WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\''));
                        if($min_date[1]['MIN_DATE'] && DBDate('postgres')<$min_date[1]['MIN_DATE'])
                                $date = $min_date[1]['MIN_DATE'];
                        else
                                $date = DBDate();

                        $mp_RET = DBGet(DBQuery('SELECT MP,MARKING_PERIOD_ID,DAYS,PERIOD_ID,MARKING_PERIOD_ID,TOTAL_SEATS,COALESCE(FILLED_SEATS,0) AS FILLED_SEATS FROM course_periods WHERE COURSE_PERIOD_ID=\''.$select_cpid.'\''));
                        $mps = GetAllMP(GetMPTable(GetMP($mp_RET[1]['MARKING_PERIOD_ID'],'TABLE')),$mp_RET[1]['MARKING_PERIOD_ID']);
                        $period_RET = DBGet(DBQuery('SELECT DAYS FROM course_periods  WHERE COURSE_PERIOD_ID=\''.$ses_value.'\' AND PERIOD_ID=\''.$mp_RET[1]['PERIOD_ID'].'\''));
                        $days_conflict = false;
                        foreach($period_RET as $existing)
                        {
                                if(strlen($mp_RET[1]['DAYS'])+strlen($existing['DAYS'])>7)
                                {
                                        $days_conflict = true;
                                        break;
                                }
                                else
                                        foreach(_str_split($mp_RET[1]['DAYS']) as  $i)
                                                                                if(strpos($existing['DAYS'],$i)!==false)
                                                                                {
                                                                                        $days_conflict = true;
                                                                                        break 2;
                                                                                }
                        }
                        if($days_conflict)
                        {
                                $course_title=DBGet(DBQuery('SELECT TITLE FROM course_periods WHERE COURSE_PERIOD_ID=\''.$select_cpid.'\' AND SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\''));
                                $warnings .= 'There is already a course selected in that period for  '.$course_title[1]['TITLE'].'.';
                                $clash[] = $warnings;
                                continue;
                        }
                        $course_per_id = clean_param($select_cpid,PARAM_INT);
                        $per_id = DBGet(DBQuery('SELECT PERIOD_ID, DAYS, MARKING_PERIOD_ID FROM course_periods WHERE COURSE_PERIOD_ID =\''.$course_per_id.'\''));
                        $period_id = $per_id[1]['PERIOD_ID'];
                        $days = $per_id[1]['DAYS'];
                        $day_st_count = strlen($days);
                        $mp_id = $per_id[1]['MARKING_PERIOD_ID'];


                        #$st_time = DBGet(DBQuery("SELECT START_TIME, END_TIME FROM SCHOOL_PERIODS WHERE PERIOD_ID = $period_id"));
        //                                    if($FULL_PERIOD)
                                                $st_time = DBGet(DBQuery('SELECT START_TIME, END_TIME FROM school_periods WHERE PERIOD_ID = $period_id AND (IGNORE_SCHEDULING IS NULL OR IGNORE_SCHEDULING!=\''.'Y'.'\')'));    /********* for homeroom scheduling*/
        //                                    else
        //                                        $st_time = DBGet(DBQuery("SELECT START_TIME, END_TIME FROM SCHOOL_PERIODS WHERE PERIOD_ID = $period_id "));    /********* for homeroom scheduling*/
                                            $start_time = $st_time[1]['START_TIME'];
                        $min_start_time = get_min($start_time);
                        $end_time = $st_time[1]['END_TIME'];
                        $min_end_time = get_min($end_time);

                    
                        $sel_per_id = DBGet(DBQuery('SELECT COURSE_PERIOD_ID, PERIOD_ID, DAYS, MARKING_PERIOD_ID FROM course_periods WHERE COURSE_PERIOD_ID = \''.$ses_value.'\''));
                        $sel_period_id = $sel_per_id[1]['PERIOD_ID'];
                        $sel_days = $sel_per_id[1]['DAYS'];
                        $sel_mp = $sel_per_id[1]['MARKING_PERIOD_ID'];
                        $sel_cp = $sel_per_id[1]['COURSE_PERIOD_ID'];
                        $sel_st_time = DBGet(DBQuery('SELECT START_TIME, END_TIME FROM school_periods WHERE PERIOD_ID = \''.$sel_period_id.'\''));
                        if($sel_st_time)
                        {
                            $sel_start_time = $sel_st_time[1]['START_TIME'];
                            $min_sel_start_time = get_min($sel_start_time);
                            $sel_end_time = $sel_st_time[1]['END_TIME'];
                            $min_sel_end_time = get_min($sel_end_time);
                            # ---------------------------- Days conflict ------------------------------------ #
                            $j = 0;
                            for($i=0; $i<$day_st_count; $i++)
                            {
                                    $clip = substr($days, $i, 1);
                                    $pos = strpos($sel_days, $clip);
                                    if($pos !== false)
                                            $j++;
                            }
                            # ---------------------------- Days conflict ------------------------------------ #
                            if($j != 0)
                            {
                                    if((($min_sel_start_time <= $min_start_time) && ($min_sel_end_time >= $min_start_time)) || (($min_sel_start_time <= $min_end_time) && ($min_sel_end_time >= $min_end_time)) || (($min_sel_start_time >= $min_start_time) && ($min_sel_end_time <= $min_end_time)))
                                    {
                                            $time_clash_conflict = true;
                                            break;
                                    }
                                    else
                                    {
                                            $time_clash_conflict = false;
                                    }
                            }
                        }

            if($time_clash_conflict)
            {
                                    $course_title=DBGet(DBQuery('SELECT TITLE FROM course_periods WHERE COURSE_PERIOD_ID=\''.$select_cpid.'\' AND SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\''));
                                    $warnings.= 'There is a period time clash for '.$course_title[1]['TITLE'].'.';
                                    $clash[] = $warnings;
                                    continue;
            }
          }
                  
       }

                                    # ------------------------------------ GENDER RESTRICTION STARTS----------------------------------------- #
                                    $cp_RET=DBGet(DBQuery('SELECT GENDER_RESTRICTION FROM course_periods WHERE COURSE_PERIOD_ID=\''.$select_cpid.'\''));
                                    $stu_Gen=DBGet(DBQuery('SELECT LEFT(GENDER,1) AS GENDER FROM students WHERE STUDENT_ID=\''.UserStudentID().'\''));
                                    $stu_Gender=$stu_Gen[1]['GENDER'];
                                    if($cp_RET[1]['GENDER_RESTRICTION']!="N" && $stu_Gender!=$cp_RET[1]['GENDER_RESTRICTION'])
                                    {
                                          $course_title=DBGet(DBQuery('SELECT TITLE FROM course_periods WHERE COURSE_PERIOD_ID=\''.$select_cpid.'\' AND SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\''));
                                          $warnings.= 'There is gender restriction for   '.$course_title[1]['TITLE'].'.';
                                          $clash[] = $warnings;
                                          continue;
                                    }
                                    # ------------------------------------ GENDER RESTRICTION ENDS----------------------------------------- #
                                    else
                                    {
                                        # ------------------------------------ PARENT RESTRICTION STARTS----------------------------------------- #
                                    $pa_RET=DBGet(DBQuery('SELECT PARENT_ID FROM course_periods WHERE COURSE_PERIOD_ID=\''.$select_cpid.'\''));
                                    if($pa_RET[1]['PARENT_ID']!=$select_cpid)
                                    {
                                        $stu_pa=DBGet(DBQuery('SELECT START_DATE,END_DATE FROM schedule WHERE STUDENT_ID=\''.UserStudentID().'\' AND COURSE_PERIOD_ID=\''.$pa_RET[1]['PARENT_ID'].'\' AND DROPPED='.'N'.' AND START_DATE<=\''.date('Y-m-d').'\''));
                                        $par_sch=count($stu_pa);
                                        if($par_sch<1 || (strtotime(DBDate())<strtotime($stu_pa[$par_sch]['START_DATE']) && $stu_pa[$par_sch]['START_DATE']!="") || (strtotime(DBDate())>strtotime($stu_pa[$par_sch]['END_DATE']) && $stu_pa[$par_sch]['END_DATE']!=""))
                                        {
                                             $course_title=DBGet(DBQuery('SELECT TITLE FROM course_periods WHERE COURSE_PERIOD_ID=\''.$select_cpid.'\' AND SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\''));
                                             $warnings.= 'Your are not scheduled in the parent course of  '.$course_title[1]['TITLE'].'.';
                                             $clash[] = $warnings;
                                             continue;
                                        }

                                    }


                                    # ------------------------------------ PARENT RESTRICTION ENDS----------------------------------------- #

		$min_date = DBGet(DBQuery('SELECT min(SCHOOL_DATE) AS MIN_DATE FROM attendance_calendar WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\''));
		if($min_date[1]['MIN_DATE'] && DBDate('postgres')<$min_date[1]['MIN_DATE'])
			$date = $min_date[1]['MIN_DATE'];
		else
			$date = DBDate();

		$mp_RET = DBGet(DBQuery('SELECT MP,MARKING_PERIOD_ID,DAYS,PERIOD_ID,TOTAL_SEATS,COALESCE(FILLED_SEATS,0) AS FILLED_SEATS FROM course_periods WHERE COURSE_PERIOD_ID=\''.$select_cpid.'\''));
		$mps = GetAllMP(GetMPTable(GetMP($mp_RET[1]['MARKING_PERIOD_ID'],'TABLE')),$mp_RET[1]['MARKING_PERIOD_ID']);

		if(is_numeric($mp_RET[1]['TOTAL_SEATS']) && $mp_RET[1]['TOTAL_SEATS']<=$mp_RET[1]['FILLED_SEATS'])
		{
                        $course_title=DBGet(DBQuery('SELECT TITLE FROM course_periods WHERE COURSE_PERIOD_ID=\''.$select_cpid.'\' AND SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\''));
			$warnings.= 'That section is already full for  '.$course_title[1]['TITLE'].'.';
                        $clash[] = $warnings;
                        continue;
                }

		# ------------------------------------ Same Days Conflict Start ----------------------------------------- #

		$period_RET = DBGet(DBQuery('SELECT cp.DAYS FROM schedule s,course_periods cp,school_periods sp WHERE cp.period_id=sp.period_id AND cp.COURSE_PERIOD_ID=s.COURSE_PERIOD_ID AND s.STUDENT_ID=\''.UserStudentID().'\' AND cp.PERIOD_ID=\''.$mp_RET[1]['PERIOD_ID'].'\' AND s.MARKING_PERIOD_ID IN ('.$mps.') AND (s.END_DATE IS NULL OR \''.DBDate().'\'<=s.END_DATE) AND sp.ignore_scheduling IS NULL'));
		$days_conflict = false;
		foreach($period_RET as $existing)
		{
			if(strlen($mp_RET[1]['DAYS'])+strlen($existing['DAYS'])>7)
			{
				$days_conflict = true;
				break;
			}
			else
				foreach(_str_split($mp_RET[1]['DAYS']) as  $i)
                                                                        if(strpos($existing['DAYS'],$i)!==false)
                                                                        {
                                                                                $days_conflict = true;
                                                                                break 2;
                                                                        }
		}
		if($days_conflict)
		{
                        $course_title=DBGet(DBQuery('SELECT TITLE FROM course_periods WHERE COURSE_PERIOD_ID=\''.$select_cpid.'\' AND SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\''));
			$warnings .= 'There is already a course scheduled in that period for  '.$course_title[1]['TITLE'].'.';
                        $clash[] = $warnings;
                        continue;
                }
               //------------------------------------- same course period scheduling conflict -----------------------------------//
                $sql_dupl     = DBQuery('SELECT COURSE_PERIOD_ID FROM schedule WHERE STUDENT_ID = \''.UserStudentID().'\' AND COURSE_PERIOD_ID = \''.$select_cpid.'\' AND (END_DATE IS NULL OR (\''.DBDate().'\'  BETWEEN START_DATE AND END_DATE)) AND SCHOOL_ID=\''.UserSchool().'\'');
                $count_entry  = mysql_num_rows($sql_dupl);
                $days_conflict2 = false;
                if($count_entry>=1)
                            $days_conflict2 = true;
                if($days_conflict2)
                        {
                             $course_title=DBGet(DBQuery('SELECT TITLE FROM course_periods WHERE COURSE_PERIOD_ID=\''.$select_cpid.'\' AND SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\''));
                             $warnings .= 'student is already scheduled in that period  '.$course_title[1]['TITLE'].'.';
                             $clash[] = $warnings;
                            continue;
			
                        }
                //------------------------------------- same course period scheduling conflict  ends -----------------------------------//        
		# ------------------------------------ Same Days Conflict End ------------------------------------------ #

		# ------------------------------------ Time Clash Conflict Start ----------------------------------- #


//                                    $full_period = DBGET(DBQuery("SELECT IGNORE_SCHEDULING,PERIOD_ID FROM SCHOOL_PERIODS WHERE SCHOOL_ID='".UserSchool()."' AND IGNORE_SCHEDULING='Y'"));
//                                    $FULL_PERIOD=$full_period[1]['IGNORE_SCHEDULING'];
//                                    $block_period=$full_period[1]['PERIOD_ID'];
//
//                                    $periods_list .= ",'".$block_period."'";
//                                    $periods_list = '('.substr($periods_list,1).')';


		$course_per_id = clean_param($select_cpid,PARAM_INT);
		$per_id = DBGet(DBQuery('SELECT PERIOD_ID, DAYS, MARKING_PERIOD_ID FROM course_periods WHERE COURSE_PERIOD_ID =\''.$course_per_id.'\''));
		$period_id = $per_id[1]['PERIOD_ID'];
		$days = $per_id[1]['DAYS'];
		$day_st_count = strlen($days);
		$mp_id = $per_id[1]['MARKING_PERIOD_ID'];


		#$st_time = DBGet(DBQuery("SELECT START_TIME, END_TIME FROM SCHOOL_PERIODS WHERE PERIOD_ID = $period_id"));
//                                    if($FULL_PERIOD)
                                        $st_time = DBGet(DBQuery('SELECT START_TIME, END_TIME FROM school_periods WHERE PERIOD_ID = '.$period_id.' AND (IGNORE_SCHEDULING IS NULL OR IGNORE_SCHEDULING!=\''.'Y'.'\')'));    /********* for homeroom scheduling*/
//                                    else
//                                        $st_time = DBGet(DBQuery("SELECT START_TIME, END_TIME FROM SCHOOL_PERIODS WHERE PERIOD_ID = $period_id "));    /********* for homeroom scheduling*/
                                    $start_time = $st_time[1]['START_TIME'];
		$min_start_time = get_min($start_time);
		$end_time = $st_time[1]['END_TIME'];
		$min_end_time = get_min($end_time);


			#$sql = "SELECT COURSE_PERIOD_ID,START_DATE FROM SCHEDULE WHERE STUDENT_ID = ".UserStudentID()." AND (END_DATE IS NULL OR END_DATE>CURRENT_DATE) AND SCHOOL_ID='".UserSchool()."' AND MARKING_PERIOD_ID='".$mp_id."'";
			// edited 7.12.2009
 $child_mpid = $mps;


  //$sql = "SELECT COURSE_PERIOD_ID,START_DATE FROM SCHEDULE WHERE STUDENT_ID = ".UserStudentID()." AND (END_DATE IS NULL OR END_DATE>CURRENT_DATE) AND SCHOOL_ID='".UserSchool()."' AND COURSE_PERIOD_ID NOT IN (SELECT COURSE_PERIOD_ID FROM COURSE_PERIODS CP,SCHOOL_PERIODS SP WHERE CP.PERIOD_ID=SP.PERIOD_ID AND SP.IGNORE_SCHEDULING != '')  AND MARKING_PERIOD_ID='".$mp_id."'";

  $sql = 'SELECT COURSE_PERIOD_ID,START_DATE FROM schedule WHERE STUDENT_ID = \''.UserStudentID().'\' AND (END_DATE IS NULL OR END_DATE>CURRENT_DATE) AND SCHOOL_ID=\''.UserSchool().'\' AND COURSE_PERIOD_ID NOT IN (SELECT COURSE_PERIOD_ID FROM course_periods CP,school_periods SP WHERE CP.PERIOD_ID=SP.PERIOD_ID AND SP.IGNORE_SCHEDULING != \''.' '.'\')  AND MARKING_PERIOD_ID IN('.$child_mpid.'\')';
                                   $xyz = mysql_query($sql);
                                    $time_clash_conflict = false;
                                    while($coue_p_id = mysql_fetch_array($xyz))
                                    {
                                            $cp_id = $coue_p_id[0];
                                            $st_dt = $coue_p_id[1];
                                            $convdate = con_date($st_dt);
                                                                                    $sel_per_id = DBGet(DBQuery('SELECT COURSE_PERIOD_ID, PERIOD_ID, DAYS, MARKING_PERIOD_ID FROM course_periods WHERE COURSE_PERIOD_ID = '.$cp_id.'\''));
                                                                                    $sel_period_id = $sel_per_id[1]['PERIOD_ID'];
                                                                                    $sel_days = $sel_per_id[1]['DAYS'];
                                                                                    $sel_mp = $sel_per_id[1]['MARKING_PERIOD_ID'];
                                                                                    $sel_cp = $sel_per_id[1]['COURSE_PERIOD_ID'];
                                                                                    $sel_st_time = DBGet(DBQuery('SELECT START_TIME, END_TIME FROM school_periods WHERE PERIOD_ID = '.$sel_period_id.'\''));
                                                                                    if($sel_st_time)
                                                                                    {
                                                                                        $sel_start_time = $sel_st_time[1]['START_TIME'];
                                                                                        $min_sel_start_time = get_min($sel_start_time);
                                                                                        $sel_end_time = $sel_st_time[1]['END_TIME'];
                                                                                        $min_sel_end_time = get_min($sel_end_time);
                                                                                        # ---------------------------- Days conflict ------------------------------------ #
                                                                                        $j = 0;
                                                                                        for($i=0; $i<$day_st_count; $i++)
                                                                                        {
                                                                                                $clip = substr($days, $i, 1);
                                                                                                $pos = strpos($sel_days, $clip);
                                                                                                if($pos !== false)
                                                                                                        $j++;
                                                                                        }
                                                                                        # ---------------------------- Days conflict ------------------------------------ #
                                                                                        if($j != 0)
                                                                                        {
                                                                                                if((($min_sel_start_time <= $min_start_time) && ($min_sel_end_time >= $min_start_time)) || (($min_sel_start_time <= $min_end_time) && ($min_sel_end_time >= $min_end_time)) || (($min_sel_start_time >= $min_start_time) && ($min_sel_end_time <= $min_end_time)))
                                                                                                {
                                                                                                        $time_clash_conflict = true;
                                                                                                        break;
                                                                                                }
                                                                                                else
                                                                                                {
                                                                                                        $time_clash_conflict = false;
                                                                                                }
                                                                                        }
                                                                                    }
                                    }
		if($time_clash_conflict)
                {
                                        $course_title=DBGet(DBQuery('SELECT TITLE FROM course_periods WHERE COURSE_PERIOD_ID='.$select_cpid.' AND SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\''));
                                        $warnings.= 'There is a period time clash for '.$course_title[1]['TITLE'].'.';
                                        $clash[] = $warnings;
                                        continue;
                }
		# ------------------------------------ Time Clash Conflict End ----------------------------------------- #
                                    }
                                    if($warnings=='')
                                    {
                                        $_SESSION['course_period'][$c_pid]=$select_cpid;
                                        $_SESSION['crs_id'][$c_pid]=$_REQUEST['course_id'];
                                        $_SESSION['marking_period_id'][$c_pid]=$mp_RET[1]['MARKING_PERIOD_ID'];
                                        $_SESSION['mp'][$c_pid]=$mp_RET[1]['MP'];
                                    }                 
          }
        }
        
        if($clash)
                                    echo "<center><b>There is a conflict. You cannot add this course period </b></center>".ErrorMessage($clash,'note')."";
        echo '<FORM name="courses" method="post">';
        if($_REQUEST['confirm_cid'] && !$_REQUEST['sel_course_period'])
        {
            unset($_SESSION['course_period']);
            unset($_SESSION['crs_id']);
            unset($_SESSION['marking_period_id']);
            unset($_SESSION['mp']);
        }
        if($_SESSION['course_period'])
        {
            echo '<center><TABLE>';
            foreach($_SESSION['course_period'] as $key_cpid => $selected_cp)
            {
                $course_title=DBGet(DBQuery('SELECT TITLE ,COURSE_PERIOD_ID FROM course_periods WHERE COURSE_PERIOD_ID='.$selected_cp.' AND SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\''));
                echo '<tr><td><INPUT type="checkbox" id="Sel_course" name="sel_course_period['.$course_title[1]["COURSE_PERIOD_ID"].']" checked="checked" value="'.$course_title[1]["COURSE_PERIOD_ID"].'"></td><td><b> '.$course_title[1]["TITLE"].'</b></td></tr>';
            }
            echo '</center></TABLE>';
        }
	// DISPLAY THE MENU
	$LO_options = array('save'=>false,'search'=>false);

	if(!$_REQUEST['subject_id'] || $_REQUEST['modfunc']=='choose_course')
		#DrawHeader('Courses');
	DrawHeaderHome('Courses',"<A HREF=for_window.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&course_modfunc=search>Search</A>");

	echo '<TABLE><TR>';

	if(count($subjects_RET))
	{
		if($_REQUEST['subject_id'])
		{
			foreach($subjects_RET as $key=>$value)
			{
				if($value['SUBJECT_ID']==$_REQUEST['subject_id'])
					$subjects_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
			}
		}
	}

	echo '<TD valign=top>';
	$columns = array('TITLE'=>'Subject');
	$link = array();
	$link['TITLE']['link'] = "for_window.php?modname=$_REQUEST[modname]";
	//$link['TITLE']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]\");'";
	$link['TITLE']['variables'] = array('subject_id'=>'SUBJECT_ID');
	if($_REQUEST['modfunc']!='choose_course')
		$link['add']['link'] = "for_window.php?modname=$_REQUEST[modname]&subject_id=new";
		//$link['add']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&subject_id=new\");'";
	else
		$link['TITLE']['link'] .= "&modfunc=$_REQUEST[modfunc]";

	ListOutput($subjects_RET,$columns,'Subject','Subjects',$link,array(),$LO_options,'for_window');
	echo '</TD>';

	if($_REQUEST['subject_id'] && $_REQUEST['subject_id']!='new')
	{
		//$sql = "SELECT COURSE_ID,TITLE FROM courses WHERE SUBJECT_ID='$_REQUEST[subject_id]' ORDER BY TITLE";
            $sql='SELECT COURSE_ID,c.TITLE, CONCAT_WS(\''.' - '.'\',c.title,sg.title) AS GRADE_COURSE FROM courses c LEFT JOIN school_gradelevels sg ON c.grade_level=sg.id WHERE SUBJECT_ID=\''.$_REQUEST[subject_id].'\' ORDER BY c.TITLE';
		$QI = DBQuery($sql);
		$courses_RET = DBGet($QI);

		if(count($courses_RET))
		{
			if($_REQUEST['course_id'])
			{
				foreach($courses_RET as $key=>$value)
				{
					if($value['COURSE_ID']==$_REQUEST['course_id'])
						$courses_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
				}
			}
		}

		echo '<TD valign=top>';
		$columns = array('GRADE_COURSE'=>'Course');
		$link = array();
		$link['GRADE_COURSE']['link'] = "for_window.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]";
		//$link['TITLE']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]\");'";
		$link['GRADE_COURSE']['variables'] = array('course_id'=>'COURSE_ID');
		if($_REQUEST['modfunc']!='choose_course')
			$link['add']['link'] = "for_window.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=new";
			//$link['add']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=new\");'";
		else
			$link['GRADE_COURSE']['link'] .= "&modfunc=$_REQUEST[modfunc]";
	
		ListOutput($courses_RET,$columns,'Course','Courses',$link,array(),$LO_options,'for_window');
		echo '</TD>';

		if($_REQUEST['course_id'] && $_REQUEST['course_id']!='new')
		{

                #$sql = "SELECT COURSE_PERIOD_ID,TITLE,COALESCE(TOTAL_SEATS-FILLED_SEATS,0) AS AVAILABLE_SEATS FROM course_periods WHERE COURSE_ID='$_REQUEST[course_id]' ORDER BY TITLE";
				
				
				$sql_mp_filter = 'SELECT MP_TYPE, PARENT_ID, GRANDPARENT_ID FROM marking_periods WHERE MARKING_PERIOD_ID=\''.UserMP().'\'';
				$res_mp_filter = DBQuery($sql_mp_filter);
                $row_mp_filter = DBGet($res_mp_filter);
				
				$mp_type = $row_mp_filter[1]['MP_TYPE'];
				$p_id = $row_mp_filter[1]['PARENT_ID'];
				$gp_id = $row_mp_filter[1]['GRANDPARENT_ID'];
				
				if($mp_type == 'quarter')
				{
					$cond = ' AND (MARKING_PERIOD_ID = \''.UserMP().'\' OR MARKING_PERIOD_ID = \''.$p_id.'\' OR MARKING_PERIOD_ID = \''.$gp_id.'\')';
				}
				if($mp_type == 'semester')
				{
					$cond = ' AND (MARKING_PERIOD_ID = \''.UserMP().'\' OR MARKING_PERIOD_ID = \''.$p_id.'\')';
				}
				if($mp_type == 'year')
				{
					$cond = ' AND MARKING_PERIOD_ID = \''.UserMP().'\'';
				}
				
				$sql = 'SELECT COURSE_PERIOD_ID AS CHECKBOX,COURSE_PERIOD_ID,TITLE,COALESCE(TOTAL_SEATS-FILLED_SEATS,0) AS AVAILABLE_SEATS FROM course_periods WHERE COURSE_ID=\''.$_REQUEST[course_id].'\''.$cond.' ORDER BY TITLE';
				
                $QI = DBQuery($sql);

                $functions= array('CHECKBOX'=>'_makeChooseCheckbox');
                $periods_RET = DBGet($QI,$functions);
                if(count($periods_RET))
                {
                    if($_REQUEST['course_period_id'])
                    {
                        foreach($periods_RET as $key=>$value)
                        {
                            if($value['COURSE_PERIOD_ID']==$_REQUEST['course_period_id'])
                                $periods_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
                        }
                    }
                }

                echo '<TD valign=top>';
                
                $columns = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller  onclick="checkAll(this.form,this.form.controller.checked,\'course_period\');"><A>','TITLE'=>'Course Period');
                if($_REQUEST['modname']=='Scheduling/Schedule.php')
                    $columns += array('AVAILABLE_SEATS'=>'Available Seats');
                $link = array();
                //$link['TITLE']['link'] = "for_window.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]";
				//$link['TITLE']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]\");'";
                //$link['TITLE']['variables'] = array('course_period_id'=>'COURSE_PERIOD_ID');
                //if($_REQUEST['modfunc']!='choose_course')
                    //$link['add']['link'] = "for_window.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]&course_period_id=new";
					//$link['add']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]&course_period_id=new\");'";
                //else
                    //$link['TITLE']['link'] .= "&modfunc=$_REQUEST[modfunc]";

                ListOutput($periods_RET,$columns,'Period','Periods',$link,array(),$LO_options,'for_window');
                if(count($periods_RET))
                {
                    echo '<BR>'.SubmitButton('Add','add_cid','class=btn_medium').'&nbsp; &nbsp;'.SubmitButton('Confirm','confirm_cid','class=btn_medium ');
                }
                echo '</TD>';
            echo '</FORM>';
		}
	}

	echo '</TR></TABLE>';
}

if($_REQUEST['modname']=='Scheduling/Courses.php' && $_REQUEST['modfunc']=='choose_course' && $_REQUEST['course_period_id'])
{
	$course_title = DBGet(DBQuery("SELECT TITLE FROM course_periods WHERE COURSE_PERIOD_ID='".$_REQUEST['course_period_id']."'"));
	$course_title = $course_title[1]['TITLE'] . '<INPUT type=hidden name=tables[parent_id] value='.$_REQUEST['course_period_id'].'>';

	echo "<script language=javascript>opener.document.getElementById(\"course_div\").innerHTML = \"$course_title</small>\"; window.close();</script>";
}
function _makeChooseCheckbox($value,$title)
{
        return '<INPUT type=checkbox id=course name=course_period['.$value.'] value='.$value.'>';
}

//                                        # ------------------------ Functions Start ----------------------------- #
							function get_min($time)
							{
								$org_tm = $time;
								$stage = substr($org_tm,-2);
								$main_tm = substr($org_tm,0,5);
								$main_tm = trim($main_tm);
								$sp_time = split(':',$main_tm);
								$hr = $sp_time[0];
								$min = $sp_time[1];
								if($hr == 12)
								{
									$hr = $hr;
								}
								else
								{
									if($stage == 'AM')
										$hr = $hr;
									if($stage == 'PM')
										$hr = $hr + 12;
								}

								$time_min = (($hr * 60) + $min);
								return $time_min;
							}

							function con_date($date)
							{
								$mother_date = $date;
								$year = substr($mother_date, 2, 2);
								$temp_month = substr($mother_date, 5, 2);

									if($temp_month == '01')
										$month = 'JAN';
									elseif($temp_month == '02')
										$month = 'FEB';
									elseif($temp_month == '03')
										$month = 'MAR';
									elseif($temp_month == '04')
										$month = 'APR';
									elseif($temp_month == '05')
										$month = 'MAY';
									elseif($temp_month == '06')
										$month = 'JUN';
									elseif($temp_month == '07')
										$month = 'JUL';
									elseif($temp_month == '08')
										$month = 'AUG';
									elseif($temp_month == '09')
										$month = 'SEP';
									elseif($temp_month == '10')
										$month = 'OCT';
									elseif($temp_month == '11')
										$month = 'NOV';
									elseif($temp_month == '12')
										$month = 'DEC';

									$day = substr($mother_date, 8, 2);

									$select_date = $day.'-'.$month.'-'.$year;
									return $select_date;
							}
//					# ------------------------ Functions End ----------------------------- #

function _str_split($str)
{
	$ret = array();
	$len = strlen($str);
	for($i=0;$i<$len;$i++)
		$ret [] = substr($str,$i,1);
	return $ret;
}
?>
