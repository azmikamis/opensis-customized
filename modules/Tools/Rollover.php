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
$next_syear = UserSyear()+1;
$_SESSION['DT'] = $DatabaseType; 
$_SESSION['DS'] = $DatabaseServer; 
$_SESSION['DU'] = $DatabaseUsername; 
$_SESSION['DP'] = $DatabasePassword; 
$_SESSION['DB'] = $DatabaseName; 
$_SESSION['DBP'] = $DatabasePort; 
$_SESSION['NY'] = $next_syear;
echo"<div id='start_date' style='color:red'></div>";

echo '<table width="80%" cellpadding="6" cellspacing="6"><tr><td width="50%" valign="top"><div id="calculating" style="display: none; padding-top:60px;" align="center"><img src="assets/rollover_anim.gif" /><br/><br/><strong>School year rolling over, please wait...</strong></div><div id="response" style="font-size:14px"></div></td>';
$notice_roll_date=DBGet(DBQuery('SELECT SYEAR FROM school_years WHERE SYEAR>\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\''));
$cur_session_RET=DBGet(DBQuery('SELECT YEAR(start_date) AS PRE,YEAR(end_date) AS POST FROM school_years WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\''));
if($cur_session_RET[1]['PRE']==$cur_session_RET[1]['POST'])
{
    $cur_session=$cur_session_RET[1]['PRE'];
}
else
{
    $cur_session=$cur_session_RET[1]['PRE'].'-'.$cur_session_RET[1]['POST'];
}
$rolled=count($notice_roll_date);
$_SESSION['roll_start_date']=date('Y-m-d',strtotime($_REQUEST['year_roll_start_date']."-".$_REQUEST['month_roll_start_date']."-".$_REQUEST['day_roll_start_date']));
if($rolled==0)
{
    
    //============================
    $tables = array('staff'=>'Users','school_periods'=>'School Periods','school_years'=>'Marking Periods','attendance_calendars'=>'Calendars','report_card_grade_scales'=>'Report Card Grade Codes','course_subjects'=>'Subjects','courses'=>'Courses','course_periods'=>'Course Periods','student_enrollment'=>'Students','report_card_comments'=>'Report Card Comment Codes','honor_roll'=>'Honor Roll Setup','attendance_codes'=>'Attendance Codes','student_enrollment_codes'=>'Student Enrollment Codes');
$no_school_tables = array('student_enrollment_codes'=>true,'staff'=>true);
$required=array('staff'=>true,'school_years'=>true,'student_enrollment'=>true,'student_enrollment_codes'=>true);
$table_list = '<TABLE align=center>';
$table_list .= '<tr><td colspan=3 class=clear></td></tr>';
foreach($tables as $table=>$name)
{
            $table_list .= '<TR><td width=1%></td><TD width=5%><INPUT type=checkbox value=Y name='.$table.' CHECKED '.($required[$table]? ' disabled="disabled"':'').' onchange="validate_rollover(this.form,this)"></TD><TD width=94%>'.$name.'</TD></TR>';
}
$table_list .= '</TABLE>';
    //===============================
    if(Prompt_rollover('Confirm Rollover','Are you sure you want to roll the data for '.$cur_session.' to the next school year?',$table_list))
{
       echo "<script type='text/javascript'>ajax_rollover('staff');</script>";
  
}
}
 else {
        Prompt_rollover_back('Rollover Completed','Data has been rolledover for '.$cur_session.' for '.  GetSchool(UserSchool()).'');
}

foreach($tables as $table=>$name)
{
    echo '<INPUT type=hidden name=hide_'.$table.' id=chk_'.$table.' value='.$_REQUEST[$table].'>';
}
echo'   
		<td align="left" valign="top">
        <TABLE><TR><TD><div id="staff"></div></TD></TR>
        <TR><TD><div id="school_periods"></div></TD></TR>
        <TR><TD><div id="school_years"></div></TD></TR>
        <TR><TD><div id="attendance_calendars"></div></TD></TR>
        <TR><TD><div id="report_card_grade_scales"></div></TD></TR>
        <TR><TD><div id="course_subjects"></div></TD></TR>
        <TR><TD><div id="courses"></div></TD></TR>
        <TR><TD><div id="course_periods"></div></TD></TR>
        <TR><TD><div id="student_enrollment"></div></TD></TR>
        <TR><TD><div id="honor_roll"></div></TD></TR>
        <TR><TD><div id="attendance_codes"></div></TD></TR>
        <TR><TD><div id="student_enrollment_codes"></div></TD></TR>
        <TR><TD><div id="report_card_comments"></div></TD></TR></TABLE>
		<td></tr></table>';
?>