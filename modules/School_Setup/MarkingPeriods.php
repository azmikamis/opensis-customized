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
DrawBC("School Setup > ".ProgramTitle());

if(!$_REQUEST['marking_period_id'] && count($fy_RET = DBGet(DBQuery('SELECT MARKING_PERIOD_ID FROM school_years WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' ORDER BY SORT_ORDER')))==1 && !$_REQUEST['ajax'])
{
	$_REQUEST['marking_period_id'] = $fy_RET[1]['MARKING_PERIOD_ID'];
	$_REQUEST['mp_term'] = 'FY';
}

unset($_SESSION['_REQUEST_vars']['marking_period_id']);
unset($_SESSION['_REQUEST_vars']['mp_term']);
//print_r($_POST);
//include 'validation_markingperiods.php';
switch($_REQUEST['mp_term'])
{   
	case 'FY':
		$table = 'school_years';
		if($_REQUEST['marking_period_id']=='new')
			$title = 'New Year';
	break;

	case 'SEM':
		$table = 'school_semesters';
		if($_REQUEST['marking_period_id']=='new')
			$title = 'New Semester';
	break;

	case 'QTR':
		$table = 'school_quarters';
		if($_REQUEST['marking_period_id']=='new')
			$title = 'New Marking Period';
	break;

	case 'PRO':
		$table = 'school_progress_periods';
		if($_REQUEST['marking_period_id']=='new')
			$title = 'New Progress Period';
	break;
}
$syear='';
// UPDATING
if($_REQUEST['day_tables'] && ($_POST['day_tables'] || $_REQUEST['ajax']))
{
	foreach($_REQUEST['day_tables'] as $id=>$values)
	{
            $syear=$_REQUEST['year_tables'][$id]['START_DATE'];
		if($_REQUEST['day_tables'][$id]['START_DATE'] && $_REQUEST['month_tables'][$id]['START_DATE'] && $_REQUEST['year_tables'][$id]['START_DATE'])
			$_REQUEST['tables'][$id]['START_DATE'] = $_REQUEST['day_tables'][$id]['START_DATE'].'-'.$_REQUEST['month_tables'][$id]['START_DATE'].'-'.$_REQUEST['year_tables'][$id]['START_DATE'];

		elseif(isset($_REQUEST['day_tables'][$id]['START_DATE']) && isset($_REQUEST['month_tables'][$id]['START_DATE']) && isset($_REQUEST['year_tables'][$id]['START_DATE']))
			$_REQUEST['tables'][$id]['START_DATE'] = '';

		if($_REQUEST['day_tables'][$id]['END_DATE'] && $_REQUEST['month_tables'][$id]['END_DATE'] && $_REQUEST['year_tables'][$id]['END_DATE'])
			$_REQUEST['tables'][$id]['END_DATE'] = $_REQUEST['day_tables'][$id]['END_DATE'].'-'.$_REQUEST['month_tables'][$id]['END_DATE'].'-'.$_REQUEST['year_tables'][$id]['END_DATE'];
		elseif(isset($_REQUEST['day_tables'][$id]['END_DATE']) && isset($_REQUEST['month_tables'][$id]['END_DATE']) && isset($_REQUEST['year_tables'][$id]['END_DATE']))
			$_REQUEST['tables'][$id]['END_DATE'] = '';

		if($_REQUEST['day_tables'][$id]['POST_START_DATE'] && $_REQUEST['month_tables'][$id]['POST_START_DATE'] && $_REQUEST['year_tables'][$id]['POST_START_DATE'])
			$_REQUEST['tables'][$id]['POST_START_DATE'] = $_REQUEST['day_tables'][$id]['POST_START_DATE'].'-'.$_REQUEST['month_tables'][$id]['POST_START_DATE'].'-'.$_REQUEST['year_tables'][$id]['POST_START_DATE'];
		elseif(isset($_REQUEST['day_tables'][$id]['POST_START_DATE']) && isset($_REQUEST['month_tables'][$id]['POST_START_DATE']) && isset($_REQUEST['year_tables'][$id]['POST_START_DATE']))
			$_REQUEST['tables'][$id]['POST_START_DATE'] = '';

		if($_REQUEST['day_tables'][$id]['POST_END_DATE'] && $_REQUEST['month_tables'][$id]['POST_END_DATE'] && $_REQUEST['year_tables'][$id]['POST_END_DATE'])
			$_REQUEST['tables'][$id]['POST_END_DATE'] = $_REQUEST['day_tables'][$id]['POST_END_DATE'].'-'.$_REQUEST['month_tables'][$id]['POST_END_DATE'].'-'.$_REQUEST['year_tables'][$id]['POST_END_DATE'];
		elseif(isset($_REQUEST['day_tables'][$id]['POST_END_DATE']) && isset($_REQUEST['month_tables'][$id]['POST_END_DATE']) && isset($_REQUEST['year_tables'][$id]['POST_END_DATE']))
			$_REQUEST['tables'][$id]['POST_END_DATE'] = '';
	}
	if(!$_POST['tables'])
		$_POST['tables'] = $_REQUEST['tables'];
}
	
if(clean_param($_REQUEST['tables'],PARAM_NOTAGS) && ($_POST['tables'] || $_REQUEST['ajax']) && AllowEdit())
{ 
	// ---------------------- Insert & Update Start ------------------------------ //
		foreach($_REQUEST['tables'] as $id=>$columns)
		{
                    if($table=='school_years')
                    {   
                        $chk_tbl='school_semesters';
                        $nm="semester";
                        $date_sql='SELECT MIN(START_DATE) AS START_DATE,MAX(END_DATE) AS END_DATE FROM '.$chk_tbl.' WHERE YEAR_ID = '.$_REQUEST['marking_period_id'].' AND SCHOOL_ID =\''.  UserSchool().'\' AND SYEAR = \''.  UserSyear().'\'';
                        $dates=  DBGet(DBQuery($date_sql));
                        $dates=$dates[1];
                        $value= date('Y-m-d',strtotime($columns['START_DATE']));
                        $prev_fy_sql='SELECT END_DATE FROM school_years WHERE SCHOOL_ID =\''.  UserSchool().'\' AND SYEAR < \''.UserSyear().'\'';
                        $prev_fy_dates=DBGet(DBQuery($prev_fy_sql));
                        $prev_fy_dates=$prev_fy_dates[1];
                    }
                    if($table=='school_semesters')
                    {
                        $chk_tbl='school_years';
                        $nm="full year";
                        $date_sql='SELECT START_DATE,END_DATE FROM '.$chk_tbl.' WHERE MARKING_PERIOD_ID = '.$_REQUEST[year_id].' AND SCHOOL_ID =\''.  UserSchool().'\' AND SYEAR = \''.  UserSyear().'\'';
                        $dates=  DBGet(DBQuery($date_sql));
                        $dates=$dates[1];
                    }
                    if($table=='school_quarters')
                    {
                        $chk_tbl='school_semesters';
                        $nm="semester";
                        $date_sql='SELECT START_DATE,END_DATE FROM '.$chk_tbl.' WHERE MARKING_PERIOD_ID = '.$_REQUEST[semester_id].' AND SCHOOL_ID =\''.  UserSchool().'\' AND SYEAR = \''.  UserSyear().'\'';
                        $dates=  DBGet(DBQuery($date_sql));
                        $dates=$dates[1];
                    }
                    if($table=='school_progress_periods')
                    {
                        $chk_tbl='school_quarters';
                        $nm="quarter";
                        $date_sql='SELECT START_DATE,END_DATE FROM '.$chk_tbl.' WHERE MARKING_PERIOD_ID = '.$_REQUEST[quarter_id].' AND SCHOOL_ID =\''.  UserSchool().'\' AND SYEAR = \''.  UserSyear().'\'';
                        $dates=  DBGet(DBQuery($date_sql));
                        $dates=$dates[1];
                    }
                    if($id!='new')
                        {     
                                if(isset($columns['START_DATE'])){
                                    $check=$columns['START_DATE'];
                                    $check_start=$check;
                                }
                                else {
                                    $check_date=DBGet(DBQuery('SELECT START_DATE FROM '.$table.' WHERE marking_period_id=\''.$id.'\''));
                                    $check_date=$check_date[1];
                                    $check=$check_date['START_DATE'];
                                    $check_start=$check;
                                }
                                if(isset($columns['END_DATE'])){
                                    $check1=$columns['END_DATE'];
                                }
                                else {
                                    $check_date1=DBGet(DBQuery('SELECT END_DATE FROM '.$table.' WHERE marking_period_id=\''.$id.'\''));
                                    $check_date1=$check_date1[1];
                                    $check1=$check_date1['END_DATE'];
                                }
                                 $days=floor((strtotime($check1,0)-strtotime($check,0))/86400); 
                                if($days<=0)
                                {
                                        $err_msg='Data not saved because start and end date is not valid';
                                }
                                else 
                                {
                                        if(isset($columns['POST_START_DATE'])){
                                            $check=$columns['POST_START_DATE'];
                                        }
                                        else {
                                            $check_date=DBGet(DBQuery('SELECT POST_START_DATE FROM '.$table.' WHERE marking_period_id=\''.$id.'\''));
                                            $check_date=$check_date[1];
                                            $check=$check_date['POST_START_DATE'];
                                        }
                                        if(isset($columns['POST_END_DATE'])){
                                            $check1=$columns['POST_END_DATE'];
                                        }
                                        else {
                                            $check_date1=DBGet(DBQuery('SELECT POST_END_DATE FROM '.$table.' WHERE marking_period_id=\''.$id.'\''));
                                            $check_date1=$check_date1[1];
                                            $check1=$check_date1['POST_END_DATE'];
                                        }
                                        if(strtotime($check,0)=='')
                                        {
                                        $days=0;    
                                        }
                                        if(strtotime($check,0)!='')
                                        {
                                        $days=floor((strtotime($check1,0)-strtotime($check,0))/86400);    
                                        }
                                         
                                        #echo $columns['DOES_GRADES'];
                                        if(isset($columns['DOES_GRADES']) && $days<=0)
                                        {
                                             if($days==0)
                                                $err_msg='Please Give a Grade Posting Begins and End Date';
                                            else
                                                $err_msg='Data not saved because grade posting date is not valid';
                                            $error=true;  
                                        }
                                        $graded=  DBGet(DBQuery('SELECT DOES_GRADES FROM '.$table.' WHERE marking_period_id=\''.$id.'\''));
//                                        if($graded[1]['DOES_GRADES']=='' && !isset($columns['DOES_GRADES']))
//                                        {   
//                                            $err_msg='Data not saved because grade posting is disabled';
//                                            $error=true;
//                                        }
                                        if($graded[1]['DOES_GRADES']=='Y' && !isset($columns['DOES_GRADES']) && $days<=0)
                                        {   
                                            if($days==0)
                                                $err_msg='Please Give a Grade Posting Begins and End Date';
                                            else
                                                $err_msg='Data not saved because grade posting date is not valid';
                                            $error=true;
                                        }
                                        if($error!=true)
                                        { 
                                                $sql = 'UPDATE '.$table.' SET ';
                                                
                                                foreach($columns as $column=>$value)
                                                {
                                                        $value=paramlib_validation($column,trim($value));
                                                        if($column=='START_DATE' && $columns['START_DATE']!='')
                                                        {
                                                            if($_REQUEST['mp_term']!='FY')
                                                            {
                                                                if(strtotime($dates['START_DATE'])<=strtotime($columns['START_DATE']))
                                                                {
                                                                        if($value!='')
                                                                        {
                                                                            while(!VerifyDate($value))
                                                                            {
                                                                                $value= date('d-M-Y',strtotime($value)-86400);
                                                                            }
                                                                            $sql .= $column.'=\''.str_replace("'","''",str_replace("\'","''",trim($value))).'\',';
                                                                        }
                                                                }
                                                                else
                                                                {
                                                                    $err_msg="Start date cannot be earlier than $nm start date";
                                                                    break 2;
                                                                }
                                                            }
                                                            else
                                                            {
                                                                if($dates['START_DATE']!='')
                                                                {
                                                                    if(strtotime($dates['START_DATE'])>=strtotime($columns['START_DATE']))
                                                                    { 
                                                                        if(strtotime($columns['START_DATE'])<=strtotime($prev_fy_dates['END_DATE']))
                                                                        {
                                                                            $err_msg="Start date cannot be earlier than previous year end date";
                                                                            break 2;
                                                                        }
                                                                        else
                                                                        {
                                                                            $cal_sql='SELECT CALENDAR_ID FROM attendance_calendars WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\'';
                                                                            $calender=  DBGet(DBQuery($cal_sql));
                                                                            $calender=$calender[1];
                                                                            $attendance_calendar=DBGet(DBQuery('SELECT MIN(SCHOOL_DATE) as START_DATE,MAX(SCHOOL_DATE) as END_DATE FROM attendance_calendar WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\''));
//                                                                         echo   'SELECT MIN(SCHOOL_DATE) as START_DATE,MAX(SCHOOL_DATE) as END_DATE FROM attendance_calendar WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\'';
//                                                                         echo "<br>";
//                                                                         print_r($attendance_calendar);
                                                                            $calender=$calender[1];

                                                                            #if(count($calender)>0)
                                                                            if(strtotime($columns['START_DATE'])>strtotime($attendance_calendar[1]['START_DATE']))
                                                                            {
                                                                         
                                                                             $err_msg="Start date cannot be changed  because the calender has already been created";
                                                                             break 2; 
                                                                            }
                                                                            else
                                                                            {  
                                                                                $stu_sql='SELECT s.STUDENT_ID FROM student_enrollment se,students s WHERE se.SCHOOL_ID=\''.UserSchool().'\' AND se.SYEAR=\''.UserSyear().'\' AND se.END_DATE IS NULL AND s.IS_DISABLE IS NULL';
                                                                                $students=  DBGet(DBQuery($stu_sql));
                                                                                $students=$students[1];

                                                                                if(count($students)>0 && $syear!=UserSyear())
                                                                                {
                                                                                $err_msg="Start date cannot be changed because the SYEAR is associated with students";
                                                                                    break 2; 
                                                                                }
                                                                                else
                                                                                {
                                                                                    $stf_sql='SELECT ssr.STAFF_ID FROM staff s,staff_school_relationship ssr WHERE s.PROFILE_ID =\'2\' AND ssr.SCHOOL_ID=\''.UserSchool().'\' AND ssr.SYEAR=\''.UserSyear().'\'';
                                                                                    $staffs=  DBGet(DBQuery($stf_sql));
                                                                                    $staffs=$staffs[1];

                                                                                    if(count($staffs)>0 && $syear!=UserSyear())
                                                                                    {
                                                                                    $err_msg="Start date cannot be changed because the SYEAR is associated with staff";
                                                                                        break 2; 
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                        $subj_sql='SELECT SUBJECT_ID FROM course_subjects WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\'';
                                                                                        $subjects=  DBGet(DBQuery($subj_sql));
                                                                                        $subjects=$subjects[1];

                                                                                        if(count($subjects)>0 && $syear!=UserSyear())
                                                                                        {
                                                                                        $err_msg="Start date cannot be changed because the SYEAR is associated with subjects";
                                                                                            break 2; 
                                                                                        }
                                                                                        else
                                                                                        {
                                                                                            $att_codes_sql='SELECT ID FROM attendance_codes WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\'';
                                                                                            $att_codes=  DBGet(DBQuery($att_codes_sql));
                                                                                            $att_codes=$att_codes[1];

                                                                                            if(count($att_codes)>0 && $syear!=UserSyear())
                                                                                            {
                                                                                            $err_msg="Start date cannot be changed because the SYEAR is associated with attendance codes";
                                                                                                break 2; 
                                                                                            }
                                                                                            else
                                                                                            {
                                                                                                $sp_sql='SELECT PERIOD_ID FROM school_periods WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\'';
                                                                                                $sp=  DBGet(DBQuery($sp_sql));
                                                                                                $sp=$sp[1];
                                                                                                
                                                                                                if(count($sp)>0 && $syear!=UserSyear())
                                                                                                {
                                                                                                $err_msg="Start date cannot be changed because the SYEAR is associated with school periods";
                                                                                                    break 2; 
                                                                                                }
                                                                                                else
                                                                                                {
                                                                                                    $fy_sql='SELECT SYEAR FROM school_years WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.$syear.'\'';
                                                                                                    $fy=  DBGet(DBQuery($fy_sql));
                                                                                                    $fy=$fy[1];
                                                                                                    if(count($fy)>0 && $syear!=UserSyear())
                                                                                                    {
                                                                                                        $err_msg="Start date cannot be changed because the SYEAR already exists in previous year";
                                                                                                        break 2;
                                                                                                    }
                                                                                                    else
                                                                                                    {
                                                                                                        if($value!='')
                                                                                                        {
                                                                                                            while(!VerifyDate($value))
                                                                                                            {
                                                                                                                $value= date('d-M-Y',strtotime($value)-86400);
                                                                                                            }
                                                                                                            $sql .= $column.'=\''.str_replace("'","''",str_replace("\'","''",trim($value))).'\',';


                                                                                                            $fy_sql='UPDATE school_years SET SYEAR=\''.$syear.'\' WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\'';
                                                                                                            DBQuery($fy_sql);
                                                                                                            $sem_sql='UPDATE  school_semesters SET SYEAR=\''.$syear.'\' WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\'';
                                                                                                            DBQuery($sem_sql);
                                                                                                            $qtr_sql='UPDATE  school_quarters SET SYEAR=\''.$syear.'\' WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\'';
                                                                                                            DBQuery($qtr_sql);
                                                                                                            $progp_sql='UPDATE school_progress_periods SET SYEAR=\''.$syear.'\' WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\'';
                                                                                                            DBQuery($progp_sql);
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                    else
                                                                    {
                                                                        $err_msg="Start date cannot be after $nm start date";
                                                                        break 2;
                                                                    }
                                                                }
                                                                else 
                                                                {
                                                                    if(strtotime($columns['START_DATE'])<=strtotime($prev_fy_dates['END_DATE']))
                                                                    {
                                                                        $err_msg="Start date cannot be earlier than previous year end date";
                                                                        break 2;
                                                                    }
                                                                    else
                                                                    {
                                                                        $cal_sql='SELECT CALENDAR_ID FROM attendance_calendars WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\'';
                                                                        $calender=  DBGet(DBQuery($cal_sql));
                                                                        $attendance_calendar=DBGet(DBQuery('SELECT MIN(SCHOOL_DATE) as START_DATE,MAX(SCHOOL_DATE) as END_DATE FROM attendance_calendar WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\''));
//                                                                         echo   'SELECT MIN(SCHOOL_DATE) as START_DATE,MAX(SCHOOL_DATE) as END_DATE FROM attendance_calendar WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\'';
//                                                                         echo "<br>";
//                                                                         print_r($attendance_calendar);
                                                                        $calender=$calender[1];
                                                                        #if(count($calender)>0)
                                                                        if($attendance_calendar[1]['START_DATE']!='' && strtotime($columns['START_DATE'])>strtotime($attendance_calendar[1]['START_DATE']))
                                                                        {
                                                                         
                                                                        $err_msg="Start date cannot be changed  because the calender has already been created";
                                                                            break 2; 
                                                                        }
                                                                        else
                                                                        {
                                                                            $stu_sql='SELECT s.STUDENT_ID FROM student_enrollment se,students s WHERE se.SCHOOL_ID=\''.UserSchool().'\' AND se.SYEAR=\''.UserSyear().'\' AND se.END_DATE IS NULL AND s.IS_DISABLE IS NULL';
                                                                            $students=  DBGet(DBQuery($stu_sql));
                                                                            $students=$students[1];

                                                                            if(count($students)>0 && $syear!=UserSyear())
                                                                            {
                                                                               $err_msg="Start date cannot be changed because the SYEAR is associated with students";
                                                                                break 2; 
                                                                            }
                                                                            else
                                                                            {
                                                                                $stf_sql='SELECT ssr.STAFF_ID FROM staff s,staff_school_relationship ssr WHERE s.PROFILE_ID =\'2\' AND ssr.SCHOOL_ID=\''.UserSchool().'\' AND ssr.SYEAR=\''.UserSyear().'\'';
                                                                                $staffs=  DBGet(DBQuery($stf_sql));
                                                                                $staffs=$staffs[1];

                                                                                if(count($staffs)>0 && $syear!=UserSyear())
                                                                                {
                                                                                $err_msg="Start date cannot be changed because the SYEAR is associated with staff";
                                                                                    break 2; 
                                                                                }
                                                                                else
                                                                                {
                                                                                   $subj_sql='SELECT SUBJECT_ID FROM course_subjects WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\'';
                                                                                    $subjects=  DBGet(DBQuery($subj_sql));
                                                                                    $subjects=$subjects[1];

                                                                                    if(count($subjects)>0 && $syear!=UserSyear())
                                                                                    {
                                                                                    $err_msg="Start date cannot be changed because the SYEAR is associated with subjects";
                                                                                        break 2; 
                                                                                    }
                                                                                    else
                                                                                    {  
                                                                                        $att_codes_sql='SELECT ID FROM attendance_codes WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\'';
                                                                                        $att_codes=  DBGet(DBQuery($att_codes_sql));
                                                                                        $att_codes=$att_codes[1];

                                                                                        if(count($att_codes)>0 && $syear!=UserSyear())
                                                                                        {
                                                                                        $err_msg="Start date cannot be changed because the SYEAR is associated with attendance codes";
                                                                                            break 2; 
                                                                                        }
                                                                                        else
                                                                                        {
                                                                                            $sp_sql='SELECT PERIOD_ID FROM school_periods WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\'';
                                                                                            $sp=  DBGet(DBQuery($sp_sql));
                                                                                            $sp=$sp[1];

                                                                                            if(count($sp)>0 && $syear!=UserSyear())
                                                                                            {
                                                                                            $err_msg="Start date cannot be changed because the SYEAR is associated with school periods";
                                                                                                break 2; 
                                                                                            }
                                                                                            else
                                                                                            {
                                                                                                $fy_sql='SELECT SYEAR FROM school_years WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.$syear.'\'';
                                                                                                $fy=  DBGet(DBQuery($fy_sql));
                                                                                                $fy=$fy[1];
                                                                                                if(count($fy)>0 && $syear!=UserSyear())
                                                                                                {
                                                                                                    $err_msg="Start date cannot be changed because the SYEAR already exists in previous year";
                                                                                                    break 2;
                                                                                                }
                                                                                                else
                                                                                                {
                                                                                                    if($value!='')
                                                                                                    {
                                                                                                        while(!VerifyDate($value))
                                                                                                        {
                                                                                                            $value= date('d-M-Y',strtotime($value)-86400);
                                                                                                        }
                                                                                                        $sql .= $column.'=\''.str_replace("'","''",str_replace("\'","''",trim($value))).'\',';


                                                                                                        $fy_sql='UPDATE school_years SET SYEAR=\''.$syear.'\' WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\'';
                                                                                                        DBQuery($fy_sql);
                                                                                                        $sem_sql='UPDATE  school_semesters SET SYEAR=\''.$syear.'\' WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\'';
                                                                                                        DBQuery($sem_sql);
                                                                                                        $qtr_sql='UPDATE  school_quarters SET SYEAR=\''.$syear.'\' WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\'';
                                                                                                        DBQuery($qtr_sql);
                                                                                                        $progp_sql='UPDATE school_progress_periods SET SYEAR=\''.$syear.'\' WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\'';
                                                                                                        DBQuery($progp_sql);
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                            
                                                        }
                                                        if($column=='END_DATE' && $columns['END_DATE']!='')
                                                        {
                                                            if($_REQUEST['mp_term']!='FY')
                                                            {
                                                                if(strtotime($dates['END_DATE'])>=strtotime($columns['END_DATE']))
                                                                {
                                                                    if($value!='')
                                                                        {
                                                                            while(!VerifyDate($value))
                                                                            {
                                                                                $value= date('d-M-Y',strtotime($value)-86400);
                                                                            }
                                                                            $sql .= $column.'=\''.str_replace("'","''",str_replace("\'","''",$value)).'\',';
                                                                        }
                                                                }
                                                                else
                                                                { 
                                                                    $err_msg="End date cannot be after $nm end date";
                                                                    break 2;
                                                                }
                                                            }
                                                            else
                                                            {
                                                                if($dates['END_DATE']!='')
                                                                {        
                                                                    if(strtotime($dates['END_DATE'])<=strtotime($columns['END_DATE']))
                                                                    {
                                                                       
                                                                            $cal_sql='SELECT CALENDAR_ID FROM attendance_calendars WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\'';
                                                                            $calender=  DBGet(DBQuery($cal_sql));
                                                                            $calender=$calender[1];
                                                                            $attendance_calendar=DBGet(DBQuery('SELECT MIN(SCHOOL_DATE) as START_DATE,MAX(SCHOOL_DATE) as END_DATE FROM attendance_calendar WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\''));
//                                                                         echo   'SELECT MIN(SCHOOL_DATE) as START_DATE,MAX(SCHOOL_DATE) as END_DATE FROM attendance_calendar WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\'';
//                                                                         echo "<br>";
//                                                                         print_r($attendance_calendar);
                                                                            $calender=$calender[1];

                                                                            #if(count($calender)>0)
                                                                            if($attendance_calendar[1]['END_DATE']!='' && strtotime($columns['END_DATE'])>strtotime($attendance_calendar[1]['END_DATE']))
                                                                            {
                                                                         
                                                                             $err_msg="End date cannot be changed  because the calender has already been created";
                                                                             break 2; 
                                                                            }
                                                                        if($value!='')
                                                                        {   
                                                                            while(!VerifyDate($value))
                                                                            {
                                                                                $value= date('d-M-Y',strtotime($value)-86400);
                                                                            }
                                                                            $sql .= $column.'=\''.str_replace("'","''",str_replace("\'","''",$value)).'\',';
                                                                        }
                                                                    }
                                                                    else
                                                                    { 
                                                                        $err_msg="End date cannot be before $nm end date";
                                                                        break 2;
                                                                    }
                                                                }
                                                                else
                                                                {   
                                                                    if($value!='')
                                                                        {
                                                                            $cal_sql='SELECT CALENDAR_ID FROM attendance_calendars WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\'';
                                                                            $calender=  DBGet(DBQuery($cal_sql));
                                                                            $calender=$calender[1];
                                                                            $attendance_calendar=DBGet(DBQuery('SELECT MIN(SCHOOL_DATE) as START_DATE,MAX(SCHOOL_DATE) as END_DATE FROM attendance_calendar WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\''));
//                                                                         echo   'SELECT MIN(SCHOOL_DATE) as START_DATE,MAX(SCHOOL_DATE) as END_DATE FROM attendance_calendar WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\'';
//                                                                         echo "<br>";
//                                                                         print_r($attendance_calendar);
                                                                            $calender=$calender[1];

                                                                            #if(count($calender)>0)
                                                                            if(strtotime($columns['END_DATE'])<strtotime($attendance_calendar[1]['END_DATE']))
                                                                            {
                                                                         
                                                                             $err_msg="End date cannot be changed  because the calender has already been created";
                                                                             break 2; 
                                                                            }
                                                                            else{
                                                                            while(!VerifyDate($value))
                                                                            {
                                                                                $value= date('d-M-Y',strtotime($value)-86400);
                                                                            }
                                                                            $sql .= $column.'=\''.str_replace("'","''",str_replace("\'","''",$value)).'\',';
                                                                        }
                                                                        
                                                                        }
                                                                }
                                                            }
                                                        }
                                                        if($column=='POST_START_DATE')
                                                        {   
                                                            if($graded[1]['DOES_GRADES']=='' && !isset($columns['DOES_GRADES']))
                                                            {   
                                                            $column='POST_START_DATE';
                                                            $sql .= $column.'=\'\',';    
                                                            $column='POST_END_DATE';
                                                            $sql .= $column.'=\'\',';
                                                            if($value!='')
                                                            {
                                                            $err_msg="Data not saved as grade posting is disabled";
                                                            }
                                                            }
                                                            else
                                                            {
                                                                if($value!='')
                                                                {
                                                                    while(!VerifyDate($value))
                                                                    {
                                                                        $value= date('d-M-Y',strtotime($value)-86400);
                                                                    }
                                                                    if(strtotime($value)>=strtotime($check_start))
                                                                        $sql .= $column.'=\''.str_replace("'","''",str_replace("\'","''",$value)).'\',';
                                                                    else
                                                                    {
                                                                        $err_msg="Grade Posting Begins date can not occur before the Marking Period Begins date";
                                                                        break 2;
                                                                    }   
                                                                }
                                                                else 
                                                                {
                                                                 $sql .= $column.'=\'\',';
                                                                 $column='POST_END_DATE';
                                                                 $sql .= $column.'=\'\',';
                                                                }
                                                            }
                                                        }
                                                        if($column=='POST_END_DATE')
                                                        {
                                                            if($graded[1]['DOES_GRADES']=='' && !isset($columns['DOES_GRADES']))
                                                            {   
                                                            $column='POST_START_DATE';
                                                            $sql .= $column.'=\'\',';    
                                                            $column='POST_END_DATE';
                                                            $sql .= $column.'=\'\','; 
                                                            if($value!='')
                                                            {
                                                            $err_msg="Data not saved as grade posting is disabled";
                                                            }
                                                            }
                                                            else
                                                            {
                                                                if($value!='')
                                                                {
                                                                    while(!VerifyDate($value))
                                                                    {
                                                                        $value= date('d-M-Y',strtotime($value)-86400);
                                                                    }
                                                                     $sql .= $column.'=\''.str_replace("'","''",str_replace("\'","''",trim($value))).'\',';
                                                                }
                                                                else 
                                                                {
                                                                    $sql .= $column.'=\'\',';
                                                                }
                                                            }
                                                        }
                                                        if($column!='START_DATE' && $column!='END_DATE' && $column!='POST_START_DATE' && $column!='POST_END_DATE')
                                                        {   
                                                            $sql .= $column.'=\''.str_replace("'","''",str_replace("\'","''",trim($value))).'\',';
                                                            if($column=='DOES_GRADES')
                                                            {
                                                               if($value!='Y')
                                                               {
                                                                $column='POST_START_DATE';
                                                                $sql .= $column.'=\'\',';    
                                                                $column='POST_END_DATE';
                                                                $sql .= $column.'=\'\',';
                                                               }
                                                                
                                                            }
                                                            
                                                        }        
                                                }
                                                $sql = substr($sql,0,-1) . ' WHERE MARKING_PERIOD_ID=\''.$id.'\'';
                                                $go = true;
                                        }
                                }
                        }
                                            
			else
			{
                                    // $id_RET = DBGet(DBQuery('SELECT '.db_seq_nextval('MARKING_PERIOD_SEQ').' AS ID'.FROM_DUAL));
                                   DBQuery('INSERT INTO marking_period_id_generator (id)VALUES (NULL)');

                                    $id_RET = DBGet(DBQuery('SELECT  max(id) AS ID from marking_period_id_generator' ));
                                    
				$sql = 'INSERT INTO '.$table.' ';
				$fields = 'MARKING_PERIOD_ID,SYEAR,SCHOOL_ID,';
				$values = '\''.$id_RET[1]['ID'].'\',\''.UserSyear().'\',\''.UserSchool().'\',';
	
				$_REQUEST['marking_period_id'] = $id_RET[1]['ID'];
	
				switch($_REQUEST['mp_term'])
				{
					case 'SEM':
						$fields .= 'YEAR_ID,';
						$values .= '\''.$_REQUEST['year_id'].'\',';
					break;
	
					case 'QTR':
						$fields .= 'SEMESTER_ID,';
						$values .= '\''.$_REQUEST['semester_id'].'\',';
					break;
	
					case 'PRO':
						$fields .= 'QUARTER_ID,';
						$values .= '\''.$_REQUEST['quarter_id'].'\',';
					break;
				}
	
				$go = false;
				foreach($columns as $column=>$value)
				{
                                    $value=paramlib_validation($column,trim($value));
					if($column=='START_DATE' || $column=='END_DATE' || $column=='POST_START_DATE' || $column=='POST_END_DATE')
					{
						if(!VerifyDate($value) && $value!='')
							BackPrompt('Not all of the dates were entered correctly.');
					}
					if($value)
					{
                                            if($column=='START_DATE' && $columns['START_DATE']!='')
                                            {
                                                if(strtotime($dates['START_DATE'])<=strtotime($columns['START_DATE']))
                                                {
                                                    $fields .= $column.',';
                                                    $values .= '\''.str_replace("'","''",str_replace("\'","''",$value)).'\',';
                                                    
                                                    $go = true;
                                                }
                                                else
                                                {
                                                    $err_msg="Start date cannot be earlier than $nm start date";
                                                    $_REQUEST['marking_period_id']='new';
                                                    break 2;
                                                }
                                            }
                                            if($column=='END_DATE' && $columns['END_DATE']!='')
                                            {  
                                                if(strtotime($dates['END_DATE'])>=strtotime($columns['END_DATE']))
                                                {
                                                    $fields .= $column.',';
                                                    $values .= '\''.str_replace("'","''",str_replace("\'","''",$value)).'\',';
                                                    $go = true;
                                                }
                                                else
                                                {
                                                    $err_msg="End date cannot be after $nm end date";
                                                    $_REQUEST['marking_period_id']='new';
                                                    break 2;
                                                }
                                            }
                                            if(($column=='POST_START_DATE' && $columns['POST_START_DATE']!='') || ($column=='POST_END_DATE' && $columns['POST_END_DATE']!=''))
                                            {
                                                if($column=='POST_START_DATE'  )
                                                {
//                                                    if(strtotime($columns['POST_START_DATE'])>=strtotime($columns['START_DATE']))
//                                                    {
                                                        $fields .= $column.',';
                                                        $values .= '\''.str_replace("'","''",str_replace("\'","''",$value)).'\',';
                                                        $go = true;
//                                                    }
//                                                    else
//                                                    {
//                                                        $err_msg="Grade Posting Begins date can not occur before the Marking Period Begins date";
//                                                        if($columns['DOES_GRADES']=='Y')
//                                                        {
//                                                            $_REQUEST['marking_period_id']='new';
//                                                            break 2;
//                                                        }
//                                                        else
//                                                            continue;
//                                                    }
                                                }
                                                
                                                if($column=='POST_END_DATE')
                                                {
//                                                    if( strtotime($columns['POST_END_DATE'])>=strtotime($columns['POST_START_DATE']))
//                                                    {
                                                        $fields .= $column.',';
                                                        $values .= '\''.str_replace("'","''",str_replace("\'","''",$value)).'\',';
                                                        $go = true;
//                                                    }
//                                                    else
//                                                    {
//                                                        $err_msg="Grade Posting End date can not occur before the Grade Posting Begins date";
//                                                        if($columns['DOES_GRADES']=='Y')
//                                                        {
//                                                            $_REQUEST['marking_period_id']='new';
//                                                            break 2;
//                                                        }
//                                                        else
//                                                            continue;
//                                                    }
                                                }
                                                
                                            }
                                            if($column!='START_DATE' && $column!='END_DATE' && $column!='POST_START_DATE' && $column!='POST_END_DATE')
                                            {
                                                $fields .= $column.',';
                                                $values .= '\''.str_replace("'","''",str_replace("\'","''",$value)).'\',';
                                                $go = true;
                                            }
					}
				}
                               $sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
			}
	
			// CHECK TO MAKE SURE ONLY ONE MP & ONE GRADING PERIOD IS OPEN AT ANY GIVEN TIME
			$columns['START_DATE']=date("Y-m-d",strtotime($columns['START_DATE']));
			$columns['END_DATE']=date("Y-m-d",strtotime($columns['END_DATE']));
			$dates_RET = DBGet(DBQuery('SELECT MARKING_PERIOD_ID FROM '.$table.' WHERE (true=false'
				.(($columns['START_DATE'])?' OR \''.$columns['START_DATE'].'\' BETWEEN START_DATE AND END_DATE':'')
				.(($columns['END_DATE'])?' OR \''.$columns['END_DATE'].'\' BETWEEN START_DATE AND END_DATE':'')
				.(($columns['START_DATE'] && $columns['END_DATE'])?' OR START_DATE BETWEEN \''.$columns['START_DATE'].'\' AND \''.$columns['END_DATE'].'\'
				OR END_DATE BETWEEN \''.$columns['START_DATE'].'\' AND \''.$columns['END_DATE'].'\'':'')
				.') AND SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\''.(($id!='new')?' AND SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' AND MARKING_PERIOD_ID!=\''.$id.'\'':'')
			));
			$posting_RET = DBGet(DBQuery('SELECT MARKING_PERIOD_ID FROM '.$table.' WHERE (true=false'
				.(($columns['POST_START_DATE'])?' OR \''.$columns['POST_START_DATE'].'\' BETWEEN POST_START_DATE AND POST_END_DATE':'')
				.(($columns['POST_END_DATE'])?' OR \''.$columns['POST_END_DATE'].'\' BETWEEN POST_START_DATE AND POST_END_DATE':'')
				.(($columns['POST_START_DATE'] && $columns['POST_END_DATE'])?' OR POST_START_DATE BETWEEN \''.$columns['POST_START_DATE'].'\' AND \''.$columns['POST_END_DATE'].'\'
				OR POST_END_DATE BETWEEN \''.$columns['POST_START_DATE'].'\' AND \''.$columns['POST_END_DATE'].'\'':'')
				.') AND SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\''.(($id!='new')?' AND MARKING_PERIOD_ID!=\''.$id.'\'':'')
			));
	
			if($go)
				DBQuery($sql);
//----------------------------------------------------------------------------------------------------------------------		
                                            if($go){
                                                $UserMp=GetCurrentMP('QTR',DBDate());
                                                $_SESSION['UserMP']=$UserMp;
                                                if(!$UserMp){
                                                    $UserMp=GetCurrentMP('SEM',DBDate());
                                                    $_SESSION['UserMP']=$UserMp;
                                                }
                                                if(!$UserMp){
                                                    $UserMp=GetCurrentMP('FY',DBDate());
                                                    $_SESSION['UserMP']=$UserMp;
                                                }
                                            }
//---------------------------------------------------------------------------------------------------------------------------
                                        }
		// ---------------------- Insert & Update End ------------------------------ //
	
	unset($_REQUEST['tables']);
	unset($_SESSION['_REQUEST_vars']['tables']);
}


if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='delete')
{
	$extra = array();
	switch($table)
	{
		case 'school_years':
			$name = 'year';
			$parent_term = ''; $parent_id = '';
                                                      $parent_table='';
            $year_id=paramlib_validation($column=MARKING_PERIOD_ID,$_REQUEST[marking_period_id]);
			$extra[] = 'DELETE FROM school_progress_periods WHERE QUARTER_ID IN (SELECT MARKING_PERIOD_ID FROM school_quarters WHERE SEMESTER_ID IN (SELECT MARKING_PERIOD_ID FROM school_semesters WHERE YEAR_ID=\''.$year_id.'\'))';
			$extra[] = 'DELETE FROM school_quarters WHERE SEMESTER_ID IN (SELECT MARKING_PERIOD_ID FROM school_semesters WHERE YEAR_ID=\''.$year_id.'\')';
			$extra[] = 'DELETE FROM school_semesters WHERE YEAR_ID=\''.$year_id.'\'';
		break;

		case 'school_semesters':
			$name = 'semester';
			$parent_term = 'FY'; $parent_id = paramlib_validation($column=MARKING_PERIOD_ID,$_REQUEST['year_id']);
                                                      $parent_table='school_years';
            $sems_id=paramlib_validation($column=MARKING_PERIOD_ID,$_REQUEST[marking_period_id]);
			$extra[] = 'DELETE FROM school_progress_periods WHERE QUARTER_ID IN (SELECT MARKING_PERIOD_ID FROM school_quarters WHERE SEMESTER_ID=\''.$sems_id.'\')';
			$extra[] = 'DELETE FROM school_quarters WHERE SEMESTER_ID=\''.$sems_id.'\'';
		break;

		case 'school_quarters':
			$name = 'quarter';
			$parent_term = 'SEM'; $parent_id = paramlib_validation($column=MARKING_PERIOD_ID,$_REQUEST['semester_id']);
                                                      $parent_table='school_semesters';
            $qrt_id=paramlib_validation($column=MARKING_PERIOD_ID,$_REQUEST[marking_period_id]);
			$extra[] = 'DELETE FROM school_progress_periods WHERE QUARTER_ID=\''.$qrt_id.'\'';
		break;

		case 'school_progress_periods':
			$name = 'progress period';
			$parent_term = 'QTR'; $parent_id = paramlib_validation($column=MARKING_PERIOD_ID,$_REQUEST['quarter_id']);
                                                      $parent_table='school_quarters';
		break;
	}
$has_assigned_RET=DBGet(DBQuery('SELECT COUNT(*) AS TOTAL_ASSIGNED FROM course_details WHERE MARKING_PERIOD_ID=\''.paramlib_validation($column=MARKING_PERIOD_ID,$_REQUEST[marking_period_id]).'\' OR MARKING_PERIOD_ID IN(SELECT MARKING_PERIOD_ID FROM marking_periods WHERE PARENT_ID=\''.paramlib_validation($column=MARKING_PERIOD_ID,$_REQUEST[marking_period_id]).'\')'));
	$has_assigned=$has_assigned_RET[1]['TOTAL_ASSIGNED'];
                  $queryString="mp_term=$_REQUEST[mp_term]&year_id=$_REQUEST[year_id]&semester_id=$_REQUEST[semester_id]&marking_period_id=$_REQUEST[marking_period_id]";
	if($has_assigned>0){
                        UnableDeletePromptMod('Marking period cannot be deleted because it has other associations.','',$queryString);
	}else{
	if(DeletePromptMod($name,$queryString))
	{
		foreach($extra as $sql)
			DBQuery($sql);
		DBQuery('DELETE FROM '.$table.' WHERE MARKING_PERIOD_ID=\''.paramlib_validation($column=MARKING_PERIOD_ID,$_REQUEST[marking_period_id]).'\'');
		unset($_REQUEST['modfunc']);
		$_REQUEST['mp_term'] = $parent_term;
		$_REQUEST['marking_period_id'] = $parent_id;
                                    $table=$parent_table;
                                    
	}
	}
	unset($_SESSION['_REQUEST_vars']['modfunc']);
	
}

if(!$_REQUEST['modfunc'])
{
	if($_REQUEST['marking_period_id']!='new')
		$delete_button = "<INPUT type=button class=btn_medium value=Delete onClick='load_link(\"Modules.php?modname=$_REQUEST[modname]&modfunc=delete&mp_term=$_REQUEST[mp_term]&year_id=$_REQUEST[year_id]&semester_id=$_REQUEST[semester_id]&quarter_id=$_REQUEST[quarter_id]&marking_period_id=$_REQUEST[marking_period_id]\")'>";

	// ADDING & EDITING FORM
	if($_REQUEST['marking_period_id'] && $_REQUEST['marking_period_id']!='new')
	{
		$sql = 'SELECT TITLE,SHORT_NAME,SORT_ORDER,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,
						START_DATE,END_DATE,POST_START_DATE,POST_END_DATE
				FROM '.$table.'
				WHERE MARKING_PERIOD_ID=\''.paramlib_validation($column=MARKING_PERIOD_ID,$_REQUEST[marking_period_id]).'\'';
		$QI = DBQuery($sql);
		$RET = DBGet($QI);
		$RET = $RET[1];
		$title = $RET['TITLE'];
	}

	if(clean_param($_REQUEST['marking_period_id'],PARAM_ALPHANUM))
	{
	if($err_msg)
        {
            echo "<b style='color:red'>".$err_msg."</b>";
        
            unset($err_msg);
        }	
            echo "<FORM name=marking_period id=marking_period action=Modules.php?modname=$_REQUEST[modname]&mp_term=$_REQUEST[mp_term]&marking_period_id=$_REQUEST[marking_period_id]&year_id=$_REQUEST[year_id]&semester_id=$_REQUEST[semester_id]&quarter_id=$_REQUEST[quarter_id] method=POST>";
		PopTable ('header',$title);
		$header .= '<TABLE cellspacing=0 cellpadding=3 border=0>';
		

		$header .= '<TR><td class=lable >Title</td><TD>' . TextInput($RET['TITLE'],'tables['.$_REQUEST['marking_period_id'].'][TITLE]','','class=cell_floating') . '</TD></tr>';
		$header .= '<TR><td class=lable>Short Name</td><TD>' . TextInput($RET['SHORT_NAME'],'tables['.$_REQUEST['marking_period_id'].'][SHORT_NAME]','','class=cell_floating') . '</TD></tr>';
		
		if(clean_param($_REQUEST['marking_period_id'],PARAM_ALPHANUM)=='new')
			$header .= '<TR><td class=lable>Sort Order</td><TD>' . TextInput($RET['SORT_ORDER'],'tables['.$_REQUEST['marking_period_id'].'][SORT_ORDER]','','class=cell_small onKeyDown="return numberOnly(event);"') . '</TD></tr>';
		else
			$header .= '<TR><td class=lable>Sort Order</td><TD>' . TextInput($RET['SORT_ORDER'],'tables['.$_REQUEST['marking_period_id'].'][SORT_ORDER]','','class=cell_small onKeyDown=\"return numberOnly(event);\"') . '</TD></tr>';
			
		$header .= '<TR><td class=lable>Graded</td><TD>' . CheckboxInput($RET['DOES_GRADES'],'tables['.$_REQUEST['marking_period_id'].'][DOES_GRADES]','',$checked,$_REQUEST['marking_period_id']=='new','<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>') . '</TD></tr>';
		$header .= '<TR><td class=lable>Exam</td><TD>' . CheckboxInput($RET['DOES_EXAM'],'tables['.$_REQUEST['marking_period_id'].'][DOES_EXAM]','',$checked,$_REQUEST['marking_period_id']=='new','<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>') . '</TD></tr>';
		$header .= '<TR><td class=lable>Comments</td><TD>' . CheckboxInput($RET['DOES_COMMENTS'],'tables['.$_REQUEST['marking_period_id'].'][DOES_COMMENTS]','',$checked,$_REQUEST['marking_period_id']=='new','<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>') . '</TD></tr>';
		$header .= '<TR><td class=lable>Begins</td><TD>' . DateInput($RET['START_DATE'],'tables['.$_REQUEST['marking_period_id'].'][START_DATE]','') . '</TD></tr>';
		$header .= '<TR><td class=lable>Ends</td><TD>' . DateInput($RET['END_DATE'],'tables['.$_REQUEST['marking_period_id'].'][END_DATE]','') . '</TD></tr>';
		$header .= '<TR><td class=lable>Grade Posting Begins</td><TD>' . DateInput($RET['POST_START_DATE'],'tables['.$_REQUEST['marking_period_id'].'][POST_START_DATE]','') . '</TD></tr>';
		$str_srch='<TR><td class=lable>Comments</td><TD>' . CheckboxInput($RET['DOES_COMMENTS'],'tables['.$_REQUEST['marking_period_id'].'][DOES_COMMENTS]','',$checked,$_REQUEST['marking_period_id']=='new','<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>') . '</TD></tr>';
		$header .= '<TR><td class=lable>Grade Posting Ends</td><TD>' . DateInput($RET['POST_END_DATE'],'tables['.$_REQUEST['marking_period_id'].'][POST_END_DATE]','') . '</TD></tr>';

		
		$header .= '</TABLE>';
		DrawHeader($header);
		PopTable('footer');
		
		if(clean_param($_REQUEST['marking_period_id'],PARAM_ALPHANUM)=='new')
			DrawHeaderHome('','',AllowEdit()?'<INPUT type=submit value=Save class="btn_medium" onclick="formcheck_school_setup_marking();">':'');
		elseif($_REQUEST['mp_term']!='FY')
			DrawHeaderHome('','',AllowEdit()?$delete_button.'&nbsp;&nbsp;<INPUT type=submit name=btn_save id=btn_save value=Save class="btn_medium">':'');
                else
                        DrawHeaderHome('','','<INPUT type=submit name=btn_save id=btn_save value=Save class="btn_medium">');

		echo '</FORM>';
		unset($_SESSION['_REQUEST_vars']['marking_period_id']);
		unset($_SESSION['_REQUEST_vars']['mp_term']);
	}

	// DISPLAY THE MENU
	$LO_options = array('save'=>false,'search'=>false);

	echo '<TABLE cellpadding=3 width=100%><tr><td align="center"><br>';
	echo '<TABLE><TR>';

	// FY
	$sql = 'SELECT MARKING_PERIOD_ID,TITLE FROM school_years WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' ORDER BY SORT_ORDER';
	$QI = DBQuery($sql);
	$fy_RET = DBGet($QI);

	if(count($fy_RET))
	{
		if($_REQUEST['mp_term'])
		{
			if($_REQUEST['mp_term']=='FY')
				$_REQUEST['year_id'] = $_REQUEST['marking_period_id'];

			foreach($fy_RET as $key=>$value)
			{
				if($value['MARKING_PERIOD_ID']==$_REQUEST['year_id'])
					$fy_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
			}
		}
	}

	echo '<TD valign=top>';
	$columns = array('TITLE'=>'Year');
	$link = array();
	$link['TITLE']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&mp_term=FY\");'";
	$link['TITLE']['variables'] = array('marking_period_id'=>'MARKING_PERIOD_ID');
	if(!count($fy_RET))
		$link['add']['link'] = "Modules.php?modname=$_REQUEST[modname]&mp_term=FY&marking_period_id=new";

	ListOutput($fy_RET,$columns,'Year','Years',$link,array(),$LO_options);
	echo '</TD>';

	// SEMESTERS
	if(($_REQUEST['mp_term']=='FY' && $_REQUEST['marking_period_id']!='new') || $_REQUEST['mp_term']=='SEM' || $_REQUEST['mp_term']=='QTR' || $_REQUEST['mp_term']=='PRO')
	{
		$sql = 'SELECT MARKING_PERIOD_ID,TITLE FROM school_semesters WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' AND YEAR_ID=\''.$_REQUEST['year_id'].'\' ORDER BY SORT_ORDER';
		$QI = DBQuery($sql);
		$sem_RET = DBGet($QI);

		if(count($sem_RET))
		{
			if($_REQUEST['mp_term'])
			{
				if($_REQUEST['mp_term']=='SEM')
					$_REQUEST['semester_id'] = $_REQUEST['marking_period_id'];

				foreach($sem_RET as $key=>$value)
				{
					if($value['MARKING_PERIOD_ID']==$_REQUEST['semester_id'])
						$sem_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
				}
			}
		}

		echo '<TD valign=top>';
		$columns = array('TITLE'=>'Semester');
		$link = array();
		$link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&mp_term=SEM&year_id=$_REQUEST[year_id]";//." onclick='grabA(this); return false;'";
		$link['TITLE']['variables'] = array('marking_period_id'=>'MARKING_PERIOD_ID');
		$link['add']['link'] = "Modules.php?modname=$_REQUEST[modname]&mp_term=SEM&marking_period_id=new&year_id=$_REQUEST[year_id]";


		ListOutput($sem_RET,$columns,'Semester','Semesters',$link,array(),$LO_options);
		echo '</TD>';

		// QUARTERS
		if(($_REQUEST['mp_term']=='SEM' && $_REQUEST['marking_period_id']!='new') || $_REQUEST['mp_term']=='QTR' || $_REQUEST['mp_term']=='PRO')
		{
			$sql = 'SELECT MARKING_PERIOD_ID,TITLE FROM school_quarters WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' AND SEMESTER_ID=\''.$_REQUEST['semester_id'].'\' ORDER BY SORT_ORDER';
			$QI = DBQuery($sql);
			$qtr_RET = DBGet($QI);

			if(count($qtr_RET))
			{
				if(($_REQUEST['mp_term']=='QTR' && $_REQUEST['marking_period_id']!='new') || $_REQUEST['mp_term']=='PRO')
				{
					if($_REQUEST['mp_term']=='QTR')
						$_REQUEST['quarter_id'] = $_REQUEST['marking_period_id'];

					foreach($qtr_RET as $key=>$value)
					{
						if($value['MARKING_PERIOD_ID']==$_REQUEST['quarter_id'])
							$qtr_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
					}
				}
			}

			echo '<TD valign=top>';
			$columns = array('TITLE'=>'Quarter');
			$link = array();
			$link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&mp_term=QTR&year_id=$_REQUEST[year_id]&semester_id=$_REQUEST[semester_id]";
			$link['TITLE']['variables'] = array('marking_period_id'=>'MARKING_PERIOD_ID');
			$link['add']['link'] = "Modules.php?modname=$_REQUEST[modname]&mp_term=QTR&marking_period_id=new&year_id=$_REQUEST[year_id]&semester_id=$_REQUEST[semester_id]";

			ListOutput($qtr_RET,$columns,'Quarter','Quarters',$link,array(),$LO_options);
			echo '</TD>';

			// PROGRESS PERIODS
			if(($_REQUEST['mp_term']=='QTR' && $_REQUEST['marking_period_id']!='new') || $_REQUEST['mp_term']=='PRO')
			{
				$sql = 'SELECT MARKING_PERIOD_ID,TITLE FROM school_progress_periods WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' AND QUARTER_ID=\''.$_REQUEST['quarter_id'].'\' ORDER BY SORT_ORDER';
				$QI = DBQuery($sql);
				$pro_RET = DBGet($QI);

				if(count($pro_RET))
				{
					if(($_REQUEST['mp_term']=='PRO' && $_REQUEST['marking_period_id']!='new'))
					{
						$_REQUEST['progress_period_id'] = $_REQUEST['marking_period_id'];

						foreach($pro_RET as $key=>$value)
						{
							if($value['MARKING_PERIOD_ID']==$_REQUEST['marking_period_id'])
								$pro_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
						}
					}
				}

				echo '<TD valign=top>';
				$columns = array('TITLE'=>'Progress Period');
				$link = array();
				$link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&mp_term=PRO&year_id=$_REQUEST[year_id]&semester_id=$_REQUEST[semester_id]&quarter_id=$_REQUEST[quarter_id]";
				$link['TITLE']['variables'] = array('marking_period_id'=>'MARKING_PERIOD_ID');
				$link['add']['link'] = "Modules.php?modname=$_REQUEST[modname]&mp_term=PRO&marking_period_id=new&year_id=$_REQUEST[year_id]&semester_id=$_REQUEST[semester_id]&quarter_id=$_REQUEST[quarter_id]";
	$sql_mp_id = 'SELECT MARKING_PERIOD_ID,TITLE FROM school_progress_periods';
				ListOutput($pro_RET,$columns,'Progress Period','Progress Periods',$link,array(),$LO_options);
				echo '</TD>';
			}
		}
	}

	echo '</TR></TABLE></td></tr></table>';
}
?>