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
if(clean_param($_REQUEST['modfunc'],PARAM_ALPHA)=='save')
{
    $start_date = $_REQUEST['day'].'-'.$_REQUEST['month'].'-'.$_REQUEST['year'];
    if(!VerifyDate($start_date))
            BackPrompt('The date you entered is not valid');
    if($_REQUEST['student'])
    {
        foreach($_REQUEST['student'] as $student_id=>$yes)
        {
            $next_grade=  DBGet(DBQuery('SELECT NEXT_GRADE_ID FROM school_gradelevels WHERE ID=\''.$_REQUEST['grade_id'].'\' AND SCHOOL_ID=\''.UserSchool().'\''));
            if($next_grade[1]['NEXT_GRADE_ID']!='')
                $rolling_ret=1;
            else
                $rolling_ret=0;
            DBQuery('INSERT INTO student_enrollment (SYEAR,SCHOOL_ID,STUDENT_ID,GRADE_ID,START_DATE,ENROLLMENT_CODE,NEXT_SCHOOL,CALENDAR_ID) VALUES (\''.UserSyear().'\',\''.UserSchool().'\','.$student_id.',\''.$_REQUEST['grade_id'].'\',\''.$start_date.'\',\''.$_REQUEST['en_code'].'\',\''.$rolling_ret.'\',\''.$_REQUEST['cal_id'].'\')');
        }
        $enroll_msg="Selected students are successfully re enrolled";
    }
    else
    {
        $err="<b><font color=red>No students are selected.</font></b>";    
    }
unset($_REQUEST['modfunc']);
}

	DrawBC("Students > ".ProgramTitle());
	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM name=sav id=sav action=Modules.php?modname=$_REQUEST[modname]&modfunc=save method=POST>";
		PopTable_wo_header ('header');
                $calendar = DBGet(DBQuery('SELECT CALENDAR_ID FROM attendance_calendars WHERE SCHOOL_ID=\''.UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY DEFAULT_CALENDAR DESC LIMIT 0,1"));
		
                echo '<INPUT TYPE=hidden name=cal_id value='.$calendar[1]["CALENDAR_ID"].'>';
                
		echo '<TABLE><TR><TD>Start Date <font color="red">*</font> </TD><TD>: </TD><TD>'.PrepareDate(DBDate(),'').'</TD></TR>';

		echo '<TR><TD>Grade<font color="red">*</font>  </TD><TD>: </TD><TD>';
		$sel_grade = DBGet(DBQuery('SELECT TITLE,ID FROM school_gradelevels WHERE SCHOOL_ID=\''.UserSchool().'\''));
		echo '<SELECT name=grade_id id=grade_id><OPTION value="">Select Grade</OPTION>';
		foreach($sel_grade as $g_id)
			echo "<OPTION value=$g_id[ID]>".$g_id['TITLE'].'</OPTION>';
		echo '</SELECT>';
		echo '</TD></TR>';
                
                echo '<TR><TD>Enrollment Code <font color="red">*</font> </TD><TD>: </TD><TD>';
		$enroll_code = DBGet(DBQuery('SELECT TITLE,ID FROM student_enrollment_codes WHERE SYEAR=\''.UserSyear().'\' AND TYPE IN (\''.Add.'\',\''.TrnE.'\',\''.Roll.'\')'));
		echo '<SELECT name=en_code id=en_code><OPTION value="">Select Enroll Code</OPTION>';
		foreach($enroll_code as $enr_code)
			echo "<OPTION value=$enr_code[ID]>".$enr_code['TITLE'].'</OPTION>';
		echo '</SELECT>';
		echo '</TD></TR>';
		echo '</TABLE>';
		PopTable ('footer');
	}

	if($enroll_msg)
		DrawHeader('<IMG SRC=assets/check.gif>'.$enroll_msg);
        if($err)
            DrawHeader('<IMG SRC=assets/warning_button.gif>'.$err);
	
if(!$_REQUEST['modfunc'])
{
	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ',Concat(NULL) AS CHECKBOX ';
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller onclick="checkAll(this.form,this.form.controller.checked,\'student\');"><A>');
	$extra['new'] = true;
        $extra['GROUP']="STUDENT_ID";
        $extra['WHERE']=' AND  ssm.STUDENT_ID NOT IN (SELECT STUDENT_ID FROM student_enrollment WHERE SYEAR =\''.UserSyear().'\' AND END_DATE IS NULL)';
	Search('student_id',$extra);
	if($_REQUEST['search_modfunc']=='list')
	{
            if($_SESSION['count_stu']!=0)
		echo '<BR><CENTER>'.SubmitButton('Re Enroll Selected Students','','class=btn_re_enroll onclick=\'return reenroll();\'').'</CENTER>';
		echo "</FORM>";
	}

}




function _makeChooseCheckbox()
{	global $THIS_RET;

		return "<INPUT type=checkbox name=student[".$THIS_RET['STUDENT_ID']."] value=Y>";
}

?>
