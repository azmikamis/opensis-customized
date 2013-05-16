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
unset($_SESSION['_REQUEST_vars']['subject_id']);
unset($_SESSION['_REQUEST_vars']['course_id']);
unset($_SESSION['_REQUEST_vars']['course_period_id']);


if ($_REQUEST['modfunc'] != 'delete' && !$_REQUEST['subject_id']) {
    $subjects_RET = DBGet(DBQuery("SELECT SUBJECT_ID,TITLE FROM course_subjects WHERE SCHOOL_ID='" . UserSchool() . "' AND SYEAR='" . UserSyear() . "'"));
    if (count($subjects_RET) == 1)
        $_REQUEST['subject_id'] = $subjects_RET[1]['SUBJECT_ID'];
}

if (clean_param($_REQUEST['course_modfunc'], PARAM_ALPHAMOD) == 'search') {
    PopTable('header', 'Search');
    echo "<FORM name=F1 id=F1 action=Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&course_modfunc=search method=POST>";
    echo '<TABLE><TR><TD><INPUT type=text class=cell_floating name=search_term value="' . $_REQUEST['search_term'] . '"></TD><TD><INPUT type=submit class=btn_medium value=Search onclick=\'formload_ajax("F1")\';></TD></TR></TABLE>';
    echo '</FORM>';
    PopTable('footer');

    if ($_REQUEST['search_term']) {
        $subjects_RET = DBGet(DBQuery('SELECT SUBJECT_ID,TITLE FROM course_subjects WHERE (UPPER(TITLE) LIKE \'%' . strtoupper($_REQUEST['search_term']) . '%\' OR UPPER(SHORT_NAME) = \'' . strtoupper($_REQUEST['search_term']) . '\') AND SYEAR=\'' . UserSyear() . '\' AND SCHOOL_ID=\'' . UserSchool() . '\''));
        $courses_RET = DBGet(DBQuery('SELECT SUBJECT_ID,COURSE_ID,TITLE FROM courses WHERE (UPPER(TITLE) LIKE \'%' . strtoupper($_REQUEST['search_term']) . '%\' OR UPPER(SHORT_NAME) = \'' . strtoupper($_REQUEST['search_term']) . '\') AND SYEAR=\'' . UserSyear() . '\' AND SCHOOL_ID=\'' . UserSchool() . '\''));
        $periods_RET = DBGet(DBQuery('SELECT c.SUBJECT_ID,cp.COURSE_ID,cp.COURSE_PERIOD_ID,cp.TITLE FROM course_periods cp,courses c WHERE cp.COURSE_ID=c.COURSE_ID AND (UPPER(cp.TITLE) LIKE \'%' . strtoupper($_REQUEST['search_term']) . '%\' OR UPPER(cp.SHORT_NAME) = \'' . strtoupper($_REQUEST['search_term']) . '\') AND cp.SYEAR=\'' . UserSyear() . '\' AND cp.SCHOOL_ID=\'' . UserSchool() . '\''));

        echo '<TABLE><TR><TD valign=top>';
        $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]";
        $link['TITLE']['variables'] = array('subject_id' => 'SUBJECT_ID');
        ListOutput($subjects_RET, array('TITLE' => 'Subject'), 'Subject', 'Subjects', $link, array(), array('search' => false, 'save' => false));
        echo '</TD><TD valign=top>';
        $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]";
        $link['TITLE']['variables'] = array('subject_id' => 'SUBJECT_ID', 'course_id' => 'COURSE_ID');
        ListOutput($courses_RET, array('TITLE' => 'Course'), 'Course', 'Courses', $link, array(), array('search' => false, 'save' => false));
        echo '</TD><TD valign=top>';
        $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]";
        $link['TITLE']['variables'] = array('subject_id' => 'SUBJECT_ID', 'course_id' => 'COURSE_ID', 'course_period_id' => 'COURSE_PERIOD_ID');
        ListOutput($periods_RET, array('TITLE' => 'Course Period'), 'Course Period', 'Course Periods', $link, array(), array('search' => false, 'save' => false));
        echo '</TD></TR></TABLE>';
    }
}

// UPDATING
if (clean_param($_REQUEST['re_assignment_teacher'], PARAM_NOTAGS) && ($_POST['re_assignment_teacher'] || $_REQUEST['ajax']) && AllowEdit()) 
{
            $id=$_REQUEST['course_period_id'];
            $today=date('Y-m-d');
            $pre_staff_id=$_REQUEST['re_assignment_pre_teacher'];
            $_SESSION['undo_teacher']=$pre_staff_id;
            $staff_id=$_REQUEST['re_assignment_teacher'];
            
            if($_REQUEST['day_re_assignment'] && $_REQUEST['month_re_assignment'] && $_REQUEST['year_re_assignment'])
                       $assign_date = date('Y-m-d',strtotime($_REQUEST['day_re_assignment'].'-'.$_REQUEST['month_re_assignment'].'-'.substr($_REQUEST['year_re_assignment'],2,4)));
            if($_REQUEST['day_re_assignment']!='' && $_REQUEST['month_re_assignment']!='' && $_REQUEST['year_re_assignment']!='')
            {
             if(strtotime($assign_date)>=strtotime(date('Y-m-d')))
             {
                    if(scheduleAssociation($id))
                    {
                        $reassigned=  DBGet(DBQuery('SELECT COURSE_PERIOD_ID,TEACHER_ID,ASSIGN_DATE,PRE_TEACHER_ID,MODIFIED_DATE,MODIFIED_BY,UPDATED FROM teacher_reassignment WHERE course_period_id=\''.$id.'\' AND assign_date=\''.$assign_date.'\''));
                        if($reassigned)
                        {
                            DBQuery('UPDATE teacher_reassignment SET teacher_id=\''.$staff_id.'\',pre_teacher_id=\''.$pre_staff_id.'\',modified_date=\''.$today.'\',modified_by=\''.  User('STAFF_ID').'\',updated=\'N\' WHERE course_period_id=\''.$id.'\' AND assign_date=\''.$assign_date.'\'');
                            $_SESSION['undo']='UPDATE teacher_reassignment SET teacher_id=\''.$pre_staff_id.'\',pre_teacher_id=\''.$reassigned[1]['PRE_TEACHER_ID'].'\',modified_date=\''.$reassigned[1]['MODIFIED_DATE'].'\',modified_by=\''.  $reassigned[1]['MODIFIED_BY'].'\',updated=\''.$reassigned[1]['UPDATED'].'\' WHERE course_period_id=\''.$id.'\' AND assign_date=\''.$assign_date.'\'';
                        }
                        else
                        {
                                DBQuery('INSERT INTO teacher_reassignment(course_period_id,teacher_id,assign_date,pre_teacher_id,modified_date,modified_by)VALUES(\''.$id.'\',\''.$staff_id.'\',\''.$assign_date.'\',\''.$pre_staff_id.'\',\''.$today.'\',\''.  User('STAFF_ID').'\')');
                                $_SESSION['undo']='DELETE FROM teacher_reassignment WHERE course_period_id=\''.$id.'\' AND teacher_id=\''.$staff_id.'\' AND assign_date=\''.$assign_date.'\'';
                        }
                        $undo_possible=true;
                        $title_RET=  DBGet(DBQuery('SELECT TITLE FROM course_periods WHERE COURSE_PERIOD_ID=\''.$id.'\''));
                        $_SESSION['undo_title']=$title_RET[1]['TITLE'];
                        DBQuery('CALL TEACHER_REASSIGNMENT()');
                        
                        //UpdateMissingAttendance($id);
                    }
                    else
                    {
                        ShowErrPhp('There is no associations in this Course Period. You can delete it from School Set Up>> Course Manager');
                    }

             }
             else
             {
                 ShowErrPhp('Assigned date cannot be lesser than today\'s date');
             }
            }
            else 
            {
                ShowErrPhp('Please enter proper date');
            }

}

if($_REQUEST['action']=='undo')
{
    DBQuery($_SESSION['undo']);
    DBQuery('UPDATE course_periods set title=\''.$_SESSION['undo_title'].'\',teacher_id=\''.$_SESSION['undo_teacher'].'\' WHERE course_period_id=\''.$_REQUEST['course_period_id'].'\'');
    //UpdateMissingAttendance($_REQUEST['course_period_id']);
    unset($_SESSION['undo']);
    unset($_SESSION['undo_teacher']);
    unset($_SESSION['undo_title']);
}

if ((!$_REQUEST['modfunc'] || clean_param($_REQUEST['modfunc'], PARAM_ALPHAMOD) == 'choose_course') && !$_REQUEST['course_modfunc']) {
    if ($_REQUEST['modfunc'] != 'choose_course')
        DrawBC("Scheduling > " . ProgramTitle());
    $sql = 'SELECT SUBJECT_ID,TITLE FROM course_subjects WHERE SCHOOL_ID=\'' . UserSchool() . '\' AND SYEAR=\'' . UserSyear() . '\' ORDER BY TITLE';
    $QI = DBQuery($sql);
    $subjects_RET = DBGet($QI);

    if ($_REQUEST['modfunc'] != 'choose_course') {
        if (clean_param($_REQUEST['course_period_id'], PARAM_ALPHANUM)) 
        {
            $sql = 'SELECT TITLE,TEACHER_ID,SECONDARY_TEACHER_ID
						FROM course_periods
						WHERE COURSE_PERIOD_ID=\''.$_REQUEST[course_period_id].'\'';
            $QI = DBQuery($sql);
            $RET = DBGet($QI);
            $RET = $RET[1];
            $title = $RET['TITLE'];
            if($undo_possible==true)
                $title .='<br><br>&nbsp;&nbsp;<span align="center">Teacher Re-Assignment Done&nbsp;&nbsp;<a href="#" onclick="load_link(\'Modules.php?modname='.$_REQUEST['modname'].'&subject_id='.$_REQUEST['subject_id'].'&course_id='.$_REQUEST['course_id'].'&course_period_id='.$_REQUEST['course_period_id'].'&action=undo\')">Undo</a></span>';
            echo "<FORM name=F2 id=F2 action=Modules.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]&course_period_id=$_REQUEST[course_period_id] method=POST>";
            DrawHeaderHome($title, SubmitButton('Save', '', 'class=btn_medium onclick="formcheck_teacher_reassignment();"'));

            $header .= '<TABLE cellpadding=3 width=760 >';
            $header .= '<TR>';
            $header .='<TD>Select New Teacher :</TD>';
            $teachers_RET = DBGet(DBQuery('SELECT STAFF_ID,LAST_NAME,FIRST_NAME,MIDDLE_NAME FROM staff st INNER JOIN staff_school_relationship ssr USING (staff_id) WHERE SYEAR=\'' . UserSyear() . '\' AND PROFILE=\'teacher\' AND staff_id <>\''.$RET['TEACHER_ID'].'\' AND ISNULL(IS_DISABLE) ORDER BY LAST_NAME,FIRST_NAME '));
            if (count($teachers_RET)) {
                foreach ($teachers_RET as $teacher)
                    $teachers[$teacher['STAFF_ID']] = $teacher['LAST_NAME'] . ', ' . $teacher['FIRST_NAME'] . ' ' . $teacher['MIDDLE_NAME'];
            }
            $header .= '<TD>' . SelectInput('', 're_assignment_teacher', '', $teachers) . '</TD>';
            $header .='<TD>Assign Date :</TD>';
            $header .='<TD>'.  DateInput('','re_assignment','',false).'</TD>';
            $header .='<TD><TD><input type=hidden name=course_period_id value='.$_REQUEST['course_period_id'].'><input type=hidden name=re_assignment_pre_teacher value='.$RET['TEACHER_ID'].'></TD>';
            $header .= '</TR></TABLE>';
            DrawHeaderHome($header);
            echo '</FORM>';
            //--------------------------------------------Re Assignment Record-------------------------------------------------------------
        
        $sql = 'SELECT COURSE_PERIOD_ID,(SELECT CONCAT_WS(\' \',last_name,middle_name,first_name) FROM staff WHERE staff_id=teacher_id) AS TEACHER,ASSIGN_DATE,(SELECT CONCAT_WS(\' \',last_name,middle_name,first_name) FROM staff WHERE staff_id=pre_teacher_id) AS PRE_TEACHER_ID,MODIFIED_DATE,(SELECT CONCAT_WS(\' \',last_name,first_name) FROM staff WHERE staff_id=modified_by) AS MODIFIED_BY FROM teacher_reassignment WHERE course_period_id=\''.$_REQUEST['course_period_id'].'\' ORDER BY assign_date DESC';
        $QI = DBQuery($sql);
        $courses_RET = DBGet($QI,array('ASSIGN_DATE'=>'ProperDAte','MODIFIED_DATE'=>'ProperDate'));

        echo '<TABLE width=100%><TR><TD valign=top>';
         $LO_options = array('save' => false, 'search' => false);
        $columns = array('TEACHER' => 'Teacher','ASSIGN_DATE'=>'Assign Date','PRE_TEACHER_ID'=>'Previous Teacher','MODIFIED_DATE'=>'Modified Date','MODIFIED_BY'=>'Modified By');
        $link = array();
        $link['TITLE']['variables'] = array('course_id' => 'COURSE_ID');

        ListOutput($courses_RET, $columns, 'Re-Assignment Record', 'Re-Assignment Records', $link, array(), $LO_options);
        echo '</TD></TR></TABLE>';
        echo '<div class=break></div>';
            //--------------------------------------------------------------------------------------------------------------------------------------------
        }
    }

    // DISPLAY THE MENU
    $LO_options = array('save' => false, 'search' => false);

    if (!$_REQUEST['subject_id'] || clean_param($_REQUEST['modfunc'], PARAM_ALPHAMOD) == 'choose_course')
        DrawHeaderHome('Courses', "<A HREF=for_window.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&course_modfunc=search>Search</A>");

    echo '<TABLE><TR>';

    if (count($subjects_RET)) {
        if (clean_param($_REQUEST['subject_id'], PARAM_ALPHANUM)) {
            foreach ($subjects_RET as $key => $value) {
                if ($value['SUBJECT_ID'] == $_REQUEST['subject_id'])
                    $subjects_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
            }
        }
    }

    echo '<TD valign=top>';
    $columns = array('TITLE' => 'Subject');
    $link = array();
    $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]";
    $link['TITLE']['variables'] = array('subject_id' => 'SUBJECT_ID');
    
        $link['TITLE']['link'] .= "&modfunc=$_REQUEST[modfunc]";

    ListOutput($subjects_RET, $columns, 'Subject', 'Subjects', $link, array(), $LO_options);
    echo '</TD>';

    if (clean_param($_REQUEST['subject_id'], PARAM_ALPHANUM) && $_REQUEST['subject_id'] != 'new') {
        $sql = 'SELECT COURSE_ID,TITLE FROM courses WHERE SUBJECT_ID=\''.$_REQUEST['subject_id'].'\' ORDER BY TITLE';
        $QI = DBQuery($sql);
        $courses_RET = DBGet($QI);

        if (count($courses_RET)) {
            if (clean_param($_REQUEST['course_id'], PARAM_ALPHANUM)) {
                foreach ($courses_RET as $key => $value) {
                    if ($value['COURSE_ID'] == $_REQUEST['course_id'])
                        $courses_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
                }
            }
        }

        echo '<TD valign=top>';
        $columns = array('TITLE' => 'Course');
        $link = array();
        $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]";
        $link['TITLE']['variables'] = array('course_id' => 'COURSE_ID');

        ListOutput($courses_RET, $columns, 'Course', 'Courses', $link, array(), $LO_options);
        echo '</TD>';

        if (clean_param($_REQUEST['course_id'], PARAM_ALPHANUM) && $_REQUEST['course_id'] != 'new') {

            $sql_mp_filter = 'SELECT MP_TYPE, PARENT_ID, GRANDPARENT_ID FROM marking_periods WHERE MARKING_PERIOD_ID=' . UserMP();
            $res_mp_filter = DBQuery($sql_mp_filter);
            $row_mp_filter = DBGet($res_mp_filter);

            $mp_type = $row_mp_filter[1]['MP_TYPE'];
            $p_id = $row_mp_filter[1]['PARENT_ID'];
            $gp_id = $row_mp_filter[1]['GRANDPARENT_ID'];

            if ($mp_type == 'quarter') {
                $cond = ' AND (MARKING_PERIOD_ID = ' . UserMP() . ' OR MARKING_PERIOD_ID = ' . $p_id . ' OR MARKING_PERIOD_ID = ' . $gp_id . ')';
            }
            if ($mp_type == 'semester') {
                $cond = ' AND (MARKING_PERIOD_ID = ' . UserMP() . ' OR MARKING_PERIOD_ID = ' . $p_id . ')';
            }
            if ($mp_type == 'year') {
                $cond = ' AND MARKING_PERIOD_ID = ' . UserMP();
            }


            $sql = 'SELECT COURSE_PERIOD_ID,TITLE,COALESCE(TOTAL_SEATS-FILLED_SEATS,0) AS AVAILABLE_SEATS FROM course_periods WHERE COURSE_ID=\''.$_REQUEST[course_id].'\'' . $cond . ' ORDER BY TITLE';

            $QI = DBQuery($sql);
            $periods_RET = DBGet($QI);

            if (count($periods_RET)) {
                if (clean_param($_REQUEST['course_period_id'], PARAM_ALPHANUM)) {
                    foreach ($periods_RET as $key => $value) {
                        if ($value['COURSE_PERIOD_ID'] == $_REQUEST['course_period_id'])
                            $periods_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
                    }
                }
            }

            echo '<TD valign=top>';
            $columns = array('TITLE' => 'Course Period');
            if ($_REQUEST['modname'] == 'Scheduling/Schedule.php')
                $columns += array('AVAILABLE_SEATS' => 'Available Seats');
            $link = array();
            $link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&subject_id=$_REQUEST[subject_id]&course_id=$_REQUEST[course_id]";
            $link['TITLE']['variables'] = array('course_period_id' => 'COURSE_PERIOD_ID');

            ListOutput($periods_RET, $columns, 'Period', 'Periods', $link, array(), $LO_options);
            echo '</TD>';
        }
    }

    echo '</TR></TABLE>';
}
?>