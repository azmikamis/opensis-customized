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
#error_reporting(1);
error_reporting(0);
include("functions/del_directory.fnc.php");
include("functions/ParamLib.php");
include("remove_backup.php");
$url=validateQueryString(curPageURL());
if($url===FALSE){
 header('Location: index.php');
 }
//if($_REQUEST['dis']=='fl_count'){
if(optional_param('dis','',PARAM_ALPHAEXT)=='fl_count')
{
$error[] = "Either your account is inactive or your access permission has been revoked. Please contact the school administration.
";
}
if(optional_param('dis','',PARAM_ALPHAEXT)=='assoc_mis')
{
    $error[] = "No student is associated with the parent. Please contact the school administration.";
}
if(isset($_GET['ins']))

	$install = optional_param('ins','',PARAM_ALPHAEXT);
	
	
if($install == 'comp')
{
	if (is_dir('install')) 
	{
		$dir = 'install/'; // IMPORTANT: with '/' at the end
		$remove_directory = delete_directory($dir);
	}
}

require_once('Warehouse.php');

//if($_REQUEST['modfunc']=='logout')
if(optional_param('modfunc','',PARAM_ALPHAEXT)=='logout')
{
    if($_SESSION)
    {
        DBQuery("DELETE FROM log_maintain WHERE SESSION_ID = '".$_SESSION['X']."'");
        header("Location: $_SERVER[PHP_SELF]?modfunc=logout".(($_REQUEST['reason'])?'&reason='.$_REQUEST['reason']:''));
         
       // header("Location: $_SERVER[PHP_SELF]?modfunc=logout".optional_param('reason','',PARAM_ALPHAEXT)?'&reason='.optional_param('reason','',PARAM_ALPHAEXT):'');
    }
    session_destroy();
}

//if($_REQUEST['register'])
if(optional_param('register','',PARAM_NOTAGS))
{
    if(optional_param('R1','',PARAM_ALPHA)=='register')
    header("Location:register.php");
}
//if($_REQUEST['USERNAME']&& $_REQUEST['PASSWORD'])
if(optional_param('USERNAME','',PARAM_RAW) && optional_param('PASSWORD','',PARAM_RAW))
{
    db_start();
	
	# --------------------------- Seat Count Update Start ------------------------------------------ #
    //DBQuery("CALL SEAT_COUNT()");
    //DBQuery("CALL SEAT_FILL()");
	
    $course_name = DBGet(DBQuery("SELECT DISTINCT(COURSE_PERIOD_ID)FROM schedule WHERE  END_DATE <'".date("Y-m-d")."' AND  DROPPED =  'N' "));

         foreach($course_name as $column=>$value)
         {
             $course_count = DBGet(DBQuery("SELECT *  FROM schedule WHERE  COURSE_PERIOD_ID='".$value[COURSE_PERIOD_ID]."' AND  END_DATE <'".date("Y-m-d")."'AND  DROPPED =  'N' "));
              for($i=1;$i<=count($course_count);$i++)
                    {
                        DBQuery("CALL SEAT_FILL()");
                        DBQuery("UPDATE course_periods SET filled_seats=filled_seats-1 WHERE COURSE_PERIOD_ID IN (SELECT COURSE_PERIOD_ID FROM schedule WHERE end_date IS NOT NULL AND END_DATE  <'".date("Y-m-d")."' AND  DROPPED='N' AND COURSE_PERIOD_ID='".$value[COURSE_PERIOD_ID]."')");
			DBQuery(" UPDATE schedule SET  DROPPED='Y' WHERE END_DATE  IS NOT NULL AND COURSE_PERIOD_ID='".$value[COURSE_PERIOD_ID]."' AND END_DATE  <'".date("Y-m-d")."'AND   DROPPED =  'N' AND  STUDENT_ID='".$course_count[$i][STUDENT_ID]."'");
                    }
         }
	
	# ---------------------------- Seat Count Update End ------------------------------------------- #
	
	
	
     $username = mysql_real_escape_string(optional_param('USERNAME','',PARAM_RAW));
    # $password = md5($_REQUEST['PASSWORD']);
       if($password==optional_param('PASSWORD','',PARAM_RAW))
        $password = str_replace("\'","",md5(optional_param('PASSWORD','',PARAM_RAW)));
	$password = str_replace("&","",md5(optional_param('PASSWORD','',PARAM_RAW)));
	$password = str_replace("\\","",md5(optional_param('PASSWORD','',PARAM_RAW)));
	
	$student_disable_storeproc_RET = DBGet(DBQuery("SELECT s.STUDENT_ID FROM students s,student_enrollment se WHERE UPPER(s.USERNAME)=UPPER('$username') AND UPPER(s.PASSWORD)=UPPER('$password') AND se.STUDENT_ID=s.STUDENT_ID LIMIT 1"));
	if($student_disable_storeproc_RET[1]['STUDENT_ID']){
	DBQuery("SELECT STUDENT_DISABLE('".$student_disable_storeproc_RET[1]['STUDENT_ID']."')");
	}
$maintain_RET=DBGet(DBQuery("SELECT SYSTEM_MAINTENANCE_SWITCH FROM system_preference_misc LIMIT 1"));
	 $maintain=$maintain_RET[1];	   
         $login_Check=DBGet(DBQuery("SELECT USERNAME FROM staff WHERE UPPER(USERNAME)=UPPER('$username') AND UPPER(PASSWORD)=UPPER('$password')"));
         if($login_Check[1]['USERNAME']!='')
         {
         $login_RET = DBGet(DBQuery("SELECT USERNAME,PROFILE,STAFF_ID,CURRENT_SCHOOL_ID,LAST_LOGIN,FIRST_NAME,LAST_NAME,PROFILE_ID,FAILED_LOGIN,IS_DISABLE,MAX(ssr.SYEAR) AS SYEAR
                                        FROM staff s INNER JOIN staff_school_relationship ssr USING(staff_id),school_years sy
                                        WHERE sy.school_id=s.current_school_id AND sy.syear=ssr.syear AND UPPER(USERNAME)=UPPER('$username') AND UPPER(PASSWORD)=UPPER('$password')"));
         }
         else
         {
             $error[]="Incorrect username or password. Please try again.";
         }
           $loged_staff_id = $login_RET[1]['STAFF_ID'];
           if($login_RET[1]['PROFILE']=='parent')
           {
               $is_inactive= DBGet(DBQuery("SELECT se.ID FROM student_enrollment se,students_join_users sju WHERE sju.STUDENT_ID= se.STUDENT_ID AND sju.STAFF_ID=$loged_staff_id AND se.SYEAR=(SELECT MAX(SYEAR) FROM student_enrollment WHERE STUDENT_ID=sju.STUDENT_ID) AND CURRENT_DATE>=se.START_DATE AND (CURRENT_DATE<=se.END_DATE OR se.END_DATE IS NULL)"));
               if(!$is_inactive)
               {
                  session_destroy(); 
		  header("location:index.php?modfunc=logout&dis=assoc_mis");
               }
           }
           
           if($login_RET[1]['PROFILE']=='teacher')
           {
               $sql='SELECT STAFF_ID FROM staff_school_relationship WHERE STAFF_ID='.$loged_staff_id.' AND (END_DATE>=CURDATE() OR END_DATE=\'0000-00-00\') AND SYEAR=\''.$login_RET[1]['SYEAR'].'\'';
               $is_teacher_assoc=DBGet(DBQuery('SELECT STAFF_ID FROM staff_school_relationship WHERE STAFF_ID='.$loged_staff_id.' AND (END_DATE>=CURDATE() OR END_DATE=\'0000-00-00\') AND SYEAR=\''.$login_RET[1]['SYEAR'].'\''));
               if(empty($is_teacher_assoc))
               {
                    //$error[]="error";
                    header("location:index.php?modfunc=logout&staff=na");
               }
           }
    $student_RET = DBGet(DBQuery("SELECT s.USERNAME,s.STUDENT_ID,s.FIRST_NAME,s.LAST_NAME,s.LAST_LOGIN,s.IS_DISABLE,s.FAILED_LOGIN,se.SYEAR,se.SCHOOL_ID FROM students s,student_enrollment se WHERE UPPER(s.USERNAME)=UPPER('$username') AND UPPER(s.PASSWORD)=UPPER('$password') AND se.STUDENT_ID=s.STUDENT_ID AND se.SYEAR=(SELECT MAX(SYEAR) FROM student_enrollment WHERE STUDENT_ID=s.STUDENT_ID) AND CURRENT_DATE>=se.START_DATE AND (CURRENT_DATE<=se.END_DATE OR se.END_DATE IS NULL)"));

    if($maintain['SYSTEM_MAINTENANCE_SWITCH']==Y && ( $login_RET || $student_RET)){   
 $login=$login_RET[1];
 if(($login && $login['PROFILE_ID']!=1)||$login['PROFILE_ID']==0){
  header("Location:index.php?maintain=Y");
  exit;
 }
 }

if(!$login_RET && !$student_RET)
{
            $valid_pass=false;
            $db_pass=DBGet(DBQuery("SELECT STAFF_ID FROM staff WHERE PROFILE='admin' AND UPPER(PASSWORD)=UPPER('$password')"));
            if(count($db_pass)>0)
                    $valid_pass=true;
            if($valid_pass==true)
            {
//                $login_RET = DBGet(DBQuery("SELECT USERNAME,PROFILE,STAFF_ID,CURRENT_SCHOOL_ID,LAST_LOGIN,FIRST_NAME,LAST_NAME,PROFILE_ID,FAILED_LOGIN,IS_DISABLE FROM staff WHERE SYEAR=(SELECT MAX(SYEAR) FROM staff WHERE UPPER(USERNAME)=UPPER('$username')) AND UPPER(USERNAME)=UPPER('$username')"));
   $login_RET = DBGet(DBQuery("SELECT s.USERNAME AS USERNAME,s.PROFILE AS PROFILE,s.STAFF_ID AS STAFF_ID,s.CURRENT_SCHOOL_ID AS CURRENT_SCHOOL_ID,s.LAST_LOGIN AS LAST_LOGIN,s.FIRST_NAME AS FIRST_NAME,s.LAST_NAME AS LAST_NAME,s.PROFILE_ID AS PROFILE_ID,s.FAILED_LOGIN AS FAILED_LOGIN,s.IS_DISABLE AS IS_DISABLE FROM staff s,staff_school_relationship ssr WHERE s.STAFF_ID=ssr.STAFF_ID AND ssr.SYEAR=(SELECT MAX(ssr1.SYEAR) FROM staff_school_relationship ssr1,staff s1 WHERE ssr1.STAFF_ID=s1.STAFF_ID AND UPPER(s1.USERNAME)=UPPER('$username')) AND UPPER(s.USERNAME)=UPPER('$username')")); //pinki             
                $student_RET = DBGet(DBQuery("SELECT s.FIRST_NAME as FIRST_NAME,s.USERNAME,s.STUDENT_ID,s.LAST_LOGIN,s.FAILED_LOGIN FROM students s,student_enrollment se WHERE UPPER(s.USERNAME)=UPPER('$username') AND se.STUDENT_ID=s.STUDENT_ID AND se.SYEAR=(SELECT MAX(SYEAR) FROM student_enrollment WHERE STUDENT_ID=s.STUDENT_ID) AND CURRENT_DATE>=se.START_DATE AND (CURRENT_DATE<=se.END_DATE OR se.END_DATE IS NULL)"));
            }
            else
            {
                    $admin_RET = DBGet(DBQuery("SELECT STAFF_ID FROM staff WHERE PROFILE='$username' AND UPPER(PASSWORD)=UPPER('$password')"));  // Uid and Password Checking
                    if($admin_RET)
                    {
                    $login_RET = DBGet(DBQuery("SELECT USERNAME,PROFILE,STAFF_ID,CURRENT_SCHOOL_ID,LAST_LOGIN,FIRST_NAME,LAST_NAME,PROFILE_ID,FAILED_LOGIN,IS_DISABLE FROM staff WHERE SYEAR=(SELECT MAX(SYEAR) FROM staff WHERE UPPER(USERNAME)=UPPER('$username') AND UPPER(PASSWORD)=UPPER('$password')) AND UPPER(USERNAME)=UPPER('$username')"));
                    $student_RET = DBGet(DBQuery("SELECT s.FIRST_NAME as FIRST_NAME,s.USERNAME,s.STUDENT_ID,s.LAST_LOGIN,s.FAILED_LOGIN FROM students s,student_enrollment se WHERE UPPER(s.USERNAME)=UPPER('$username') AND se.STUDENT_ID=s.STUDENT_ID AND se.SYEAR=(SELECT MAX(SYEAR) FROM student_enrollment WHERE STUDENT_ID=s.STUDENT_ID) AND CURRENT_DATE>=se.START_DATE AND (CURRENT_DATE<=se.END_DATE OR se.END_DATE IS NULL)"));
                    }
            }
}
    if($login_RET && $login_RET[1]['IS_DISABLE']!='Y')
    {
        $_SESSION['STAFF_ID'] = $login_RET[1]['STAFF_ID'];
        $_SESSION['LAST_LOGIN'] = $login_RET[1]['LAST_LOGIN'];
         #$failed_login = $login_RET[1]['FAILED_LOGIN'];
       # $_SESSION['ACC_EXP_DATE'] = $login_RET[1]['ACC_EXP_DATE'];
		#$_SESSION['USER_ACTIVITY_CHK'] = $login_RET[1]['USER_ACTIVITY_CHK'];
        $syear_RET=DBGet(DBQuery("SELECT MAX(SYEAR) AS SYEAR FROM school_years WHERE SCHOOL_ID=".$login_RET[1]['CURRENT_SCHOOL_ID']));
                $_SESSION['UserSyear'] =$syear_RET[1]['SYEAR'];
                $_SESSION['UserSchool']=$login_RET[1]['CURRENT_SCHOOL_ID'];
		$_SESSION['PROFILE_ID'] = $login_RET[1]['PROFILE_ID'];
		$_SESSION['FIRST_NAME'] = $login_RET[1]['FIRST_NAME'];
                $_SESSION['LAST_NAME'] = $login_RET[1]['LAST_NAME'];
		$_SESSION['PROFILE'] = $login_RET[1]['PROFILE'];
		$_SESSION['USERNAME'] = $login_RET[1]['USERNAME'];
		$_SESSION['FAILED_LOGIN'] = $login_RET[1]['FAILED_LOGIN'];
		$_SESSION['CURRENT_SCHOOL_ID'] = $login_RET[1]['CURRENT_SCHOOL_ID'];
		#$_SESSION['IS_DISABLED'] = $login_RET[1]['IS_DISABLED'];
		$_SESSION['USERNAME'] = optional_param('USERNAME','',PARAM_RAW);
//		$_SESSION['PASSWORD'] = optional_param('PASSWORD',' ',PARAM_RAW);
        # --------------------- Set Session Id Start ------------------------- #
		$_SESSION['X'] = session_id();
		$random = rand();
	# ---------------------- Set Session Id End -------------------------- #
	DBQuery("INSERT INTO log_maintain( value, session_id) values($random, '".$_SESSION['X']."')");

	$r_id_min = DBGet(DBQuery("SELECT MIN(id) as MIN_ID FROM log_maintain WHERE SESSION_ID = '".$_SESSION['X']."'"));
	$row_id_min = $r_id_min[1]['MIN_ID'];

	$val_min_id = DBGet(DBQuery("SELECT VALUE FROM log_maintain WHERE ID = $row_id_min"));
	$value_min_id = $val_min_id[1]['VALUE'];

	$r_id_max = DBGet(DBQuery("SELECT MAX(id) as MAX_ID FROM log_maintain WHERE SESSION_ID = '".$_SESSION['X']."'"));
	$row_id_max = $r_id_max[1]['MAX_ID'];

	$val_max_id = DBGet(DBQuery("SELECT VALUE FROM log_maintain WHERE ID = $row_id_max"));
	$value_max_id = $val_max_id[1]['VALUE'];
################################## For Inserting into Log tables  ######################################
//if($_REQUEST['USERNAME']!='' && $_REQUEST['PASSWORD']!='' && $value_min_id == $value_max_id)
		if(optional_param('USERNAME','',PARAM_RAW)!='' && optional_param('PASSWORD','',PARAM_RAW)!='' && $value_min_id == $value_max_id)
		{
			
			if ($_SERVER['HTTP_X_FORWARDED_FOR']){
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			
			
			$date=date("Y-m-d H:i:s");
			DBQuery("INSERT INTO login_records (SYEAR,STAFF_ID,FIRST_NAME,LAST_NAME,PROFILE,USER_NAME,LOGIN_TIME,FAILLOG_COUNT,IP_ADDRESS,STATUS,SCHOOL_ID) values('$_SESSION[UserSyear]','$_SESSION[STAFF_ID]','$_SESSION[FIRST_NAME]','$_SESSION[LAST_NAME]','$_SESSION[PROFILE]','$_SESSION[USERNAME]','$date','$_SESSION[FAILED_LOGIN]','$ip','Success',$_SESSION[CURRENT_SCHOOL_ID])");
			
			
			/*$date=date("Y-m-d H:i:s");
			DBQuery("INSERT INTO login_records (SYEAR,STAFF_ID,FIRST_NAME,LAST_NAME,PROFILE,USER_NAME,LOGIN_TIME,FAILLOG_COUNT,IP_ADDRESS,STATUS,SCHOOL_ID) values('$DefaultSyear','$_SESSION[STAFF_ID]','$_SESSION[FIRST_NAME]','$_SESSION[LAST_NAME]','$_SESSION[PROFILE]','$_SESSION[USERNAME]','$date','$_SESSION[FAILED_LOGIN]','$_SERVER[REMOTE_ADDR]','Success',$_SESSION[CURRENT_SCHOOL_ID])");*/
		}

		$disable=$_SESSION['IS_DISABLED'];
		$failed_login= $_SESSION['FAILED_LOGIN'];
		$profile_id = $_SESSION['PROFILE_ID'];

		$admin_failed_count = DBGet(DBQuery("SELECT FAIL_COUNT FROM system_preference_misc"));
		$ad_f_cnt = $admin_failed_count[1]['FAIL_COUNT'];

		if ($ad_f_cnt && $ad_f_cnt!=0 && $failed_login>$ad_f_cnt && $profile_id!=1)
		{

		  DBQuery("UPDATE staff SET IS_DISABLE='Y' WHERE STAFF_ID='".$_SESSION['STAFF_ID']."' AND SYEAR='$_SESSION[UserSyear]' AND PROFILE_ID!=1");

		  session_destroy();
		  #header("location:index.php?modfunc=logout");
		  header("location:index.php?modfunc=logout&dis=fl_count");
		 }



		if($disable==true)
		{
		  session_destroy();
		 # header("location:index.php?modfunc=logout");
		  header("location:index.php?modfunc=logout&dis=fl");
		}
		$activity = DBGet(DBQuery("SELECT ACTIVITY_DAYS FROM system_preference_misc"));
		$activity = $activity[1]['ACTIVITY_DAYS'];
		$last_login=$_SESSION['LAST_LOGIN'];
		$date1 = date("Y-m-d H:m:s");
		$date2 = $last_login; //  yyyy/mm/dd
		$days = (strtotime($date1) - strtotime($date2)) / (60 * 60 * 24);

		if ( $activity && $activity!=0 && $days>$activity && $profile_id!=1 && $last_login)
		{
		 // DBQuery("UPDATE staff SET IS_DISABLE='Y' WHERE STAFF_ID='".$_SESSION['STAFF_ID']."' AND SYEAR='$_SESSION[UserSyear]' AND PROFILE_ID!=1");
       DBQuery("UPDATE staff s,staff_school_relationship ssp SET s.IS_DISABLE='Y' WHERE s.STAFF_ID=ssp.STAFF_ID AND s.STAFF_ID='".$_SESSION['STAFF_ID']."' AND ssp.SYEAR='$_SESSION[UserSyear]' AND s.PROFILE_ID!=1"); //pinki
		  session_destroy();
		  #header("location:index.php?modfunc=logout");
		  header("location:index.php?modfunc=logout&dis=fl_count");
		 }


############################################################################################
      $failed_login = $login_RET[1]['FAILED_LOGIN'];
        if($admin_RET)
        DBQuery("UPDATE staff SET LAST_LOGIN=CURRENT_TIMESTAMP WHERE STAFF_ID='".$admin_RET[1]['STAFF_ID']."'");
        else
        DBQuery("UPDATE staff SET LAST_LOGIN=CURRENT_TIMESTAMP,FAILED_LOGIN=NULL WHERE STAFF_ID='".$login_RET[1]['STAFF_ID']."'");

        if(Config('LOGIN')=='No')
        {
            require('soaplib/nusoap.php');
            $parameters = array($_SERVER['SERVER_NAME'], $_SERVER['SERVER_ADDR'], $openSISVersion, $_SERVER['PHP_SELF'], $_SERVER['DOCUMENT_ROOT'], $_SERVER['SCRIPT_NAME']);
            $s = new nusoap_client('http://register.os4ed.com/register.php');
            $result = $s->call('installlog',$parameters);

            DBQuery("UPDATE config SET LOGIN='Y'");
        }
    }
    #elseif($login_RET && $login_RET[1]['PROFILE']=='none')
	elseif(($login_RET && $login_RET[1]['IS_DISABLE']=='Y') || ($student_RET && $student_RET[1]['IS_DISABLE']=='Y'))
	{
            $admin_failed_count = DBGet(DBQuery("SELECT FAIL_COUNT FROM system_preference_misc"));
            $ad_f_cnt = $admin_failed_count[1]['FAIL_COUNT'];
            if(isset($login_RET) && count($login_RET)>0)
            {
                if($ad_f_cnt && $ad_f_cnt!=0 && $login_RET[1]['FAILED_LOGIN']<$ad_f_cnt && $login_RET[1]['PROFILE']!='admin')
                    $error[] = "Either your account is inactive or your access permission has been revoked. Please contact the school administration.";
                else 
                  $error[] = "Due to excessive incorrect login attempts your account has been disabled. Contact the school administration to enable your account.";  
            }
            if(isset($student_RET) && count($student_RET)>0)
            {
                if($ad_f_cnt && $ad_f_cnt!=0 && $student_RET[1]['FAILED_LOGIN']<$ad_f_cnt)
                    $error[] = "Either your account is inactive or your access permission has been revoked. Please contact the school administration.";
                else 
                    $error[] = "Due to excessive incorrect login attempts your account has been disabled. Contact the school administration to enable your account.";
            }
	}
    elseif($student_RET)
    {
                if ($_SERVER['HTTP_X_FORWARDED_FOR']){
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
	
			
			$date=date("Y-m-d H:i:s");
			DBQuery("INSERT INTO login_records (SYEAR,STAFF_ID,FIRST_NAME,LAST_NAME,PROFILE,USER_NAME,LOGIN_TIME,FAILLOG_COUNT,IP_ADDRESS,STATUS,SCHOOL_ID) values('".$_SESSION['UserSyear']."','','".$student_RET[1][FIRST_NAME]."','".$student_RET[1][LAST_NAME]."','Student','".$student_RET[1][USERNAME]."','$date','".$student_RET[1][FAILED_LOGIN]."','$ip','Success','".$student_RET[1][SCHOOL_ID]."')");
		$failed_login= $student_RET[1]['FAILED_LOGIN'];

		$admin_failed_count = DBGet(DBQuery("SELECT FAIL_COUNT FROM system_preference_misc"));
		$ad_f_cnt = $admin_failed_count[1]['FAIL_COUNT'];

		if ($ad_f_cnt && $ad_f_cnt!=0 && $failed_login>$ad_f_cnt)
		{

		  DBQuery("UPDATE students SET IS_DISABLE='Y' WHERE STUDENT_ID='".$student_RET[1]['STUDENT_ID']."' ");

		  session_destroy();
		  
		  header("location:index.php?modfunc=logout&dis=fl_count");
		 }
	
	    $_SESSION['STUDENT_ID'] = $student_RET[1]['STUDENT_ID'];
        $_SESSION['LAST_LOGIN'] = $student_RET[1]['LAST_LOGIN'];
                      $_SESSION['UserSyear'] = $student_RET[1]['SYEAR'];
		$activity = DBGet(DBQuery("SELECT ACTIVITY_DAYS FROM system_preference_misc"));
		$activity = $activity[1]['ACTIVITY_DAYS'];
		$last_login=$_SESSION['LAST_LOGIN'];
		$date1 = date("Y-m-d H:m:s");
		$date2 = $last_login; //  yyyy/mm/dd
		$days = (strtotime($date1) - strtotime($date2)) / (60 * 60 * 24);

		if ( $activity && $activity!=0 && $days>$activity && $profile_id!=1 && $last_login)
		{
		  DBQuery("UPDATE students SET IS_DISABLE='Y' WHERE STUDENT_ID='".$student_RET[1]['STUDENT_ID']."' ");

		  session_destroy();
		  
		  header("location:index.php?modfunc=logout&dis=fl_count");
		 }
		
        $failed_login = $student_RET[1]['FAILED_LOGIN'];
        if($admin_RET)
        DBQuery("UPDATE staff SET LAST_LOGIN=CURRENT_TIMESTAMP WHERE STAFF_ID='".$admin_RET[1]['STAFF_ID']."'");
        else
        DBQuery("UPDATE students SET LAST_LOGIN=CURRENT_TIMESTAMP,FAILED_LOGIN=NULL WHERE STUDENT_ID='".$student_RET[1]['STUDENT_ID']."'");
    }
    else
    {  /* cleaning using other parameters other than ALPHAEXT is not working----*/
        $stf_fl_cnt_syear= DBGet(DBQuery("SELECT MAX(SYEAR) AS SYEAR FROM staff INNER JOIN staff_school_relationship ssr USING(staff_id) WHERE UPPER(USERNAME)=UPPER('".optional_param('USERNAME', 0, PARAM_RAW)."')"));

      DBQuery("UPDATE staff SET FAILED_LOGIN=FAILED_LOGIN+1 WHERE UPPER(USERNAME)=UPPER('".optional_param('USERNAME', 0, PARAM_RAW)."')");
      DBQuery("UPDATE students SET FAILED_LOGIN=FAILED_LOGIN+1 WHERE UPPER(USERNAME)=UPPER('".optional_param('USERNAME', 0, PARAM_RAW)."')");
         #  $error[] = "Incorrect username or password. Please try again.";
    #	DBQuery("UPDATE staff SET FAILED_LOGIN=".db_case(array('FAILED_LOGIN',"''",'1','FAILED_LOGIN+1'))." WHERE UPPER(USERNAME)=UPPER('$_REQUEST[USERNAME]') AND SYEAR='$DefaultSyear'");
	#	DBQuery("UPDATE students SET FAILED_LOGIN=".db_case(array('FAILED_LOGIN',"''",'1','FAILED_LOGIN+1'))." WHERE UPPER(USERNAME)=UPPER('$_REQUEST[USERNAME]')");
		
            if ($_SERVER['HTTP_X_FORWARDED_FOR']){
                            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                    } else {
                            $ip = $_SERVER['REMOTE_ADDR'];
                    }


            $faillog_time=date("Y-m-d h:i:s");
            //DBQuery("INSERT INTO login_records (USER_NAME,FAILLOG_TIME,IP_ADDRESS,SYEAR,STATUS) values('$_REQUEST[USERNAME]','$faillog_time','$ip','$DefaultSyear','Failed')");

            DBQuery("INSERT INTO login_records (USER_NAME,FAILLOG_TIME,IP_ADDRESS,SYEAR,STATUS) values('".optional_param('USERNAME','',PARAM_ALPHAEXT)."','$faillog_time','$ip','$_SESSION[UserSyear]','Failed')"); 


            $max_id = DBGet(DBQuery("SELECT MAX(id) FROM login_records"));
            $m_id= $max_id[1]['MAX'];
            if($faillog_time)
            DBQuery("UPDATE login_records SET LOGIN_TIME=FAILLOG_TIME WHERE USER_NAME='".optional_param('USERNAME','',PARAM_ALPHAEXT)."' AND ID='".$m_id."'");

            $admin_failed_count = DBGet(DBQuery("SELECT FAIL_COUNT FROM system_preference_misc"));
            $ad_f_cnt = $admin_failed_count[1]['FAIL_COUNT'];

            $res= DBGet(DBQuery("SELECT FAILED_LOGIN,PROFILE FROM staff WHERE UPPER(USERNAME)=UPPER('".optional_param('USERNAME', 0, PARAM_RAW)."')"));
            $failed_login_staff=$res[1]['FAILED_LOGIN'];
            if($failed_login_staff!='')
            {
                if ($ad_f_cnt && $ad_f_cnt!=0 && $failed_login_staff >= $ad_f_cnt && $res[1]['PROFILE']!='admin')
                {
                    #DBQuery("UPDATE staff SET IS_DISABLE='Y' WHERE UPPER(USERNAME)=UPPER('".optional_param('USERNAME', 0, PARAM_RAW)."') AND SYEAR='".$stf_fl_cnt_syear[1]['SYEAR']."'");
                     DBQuery("UPDATE staff SET IS_DISABLE='Y' WHERE UPPER(USERNAME)=UPPER('".optional_param('USERNAME', 0, PARAM_RAW)."')");
                    if($failed_login_staff == $ad_f_cnt)
                    {
                        unset($error);
                        $error[] = "Incorrect username or password. Please try again.";
                        
                    
                    }
                        else {
                        unset($error);
                        $error[] = "Due to excessive incorrect login attempts your account has been disabled. Contact the school administration to enable your account.";
                        }
                
                }
                else{
                    unset($error);
                    $error[] = "Incorrect username or password. Please try again.";
                    }
                
            }

            $res= DBGet(DBQuery("SELECT FAILED_LOGIN FROM students WHERE UPPER(USERNAME)=UPPER('".optional_param('USERNAME', 0, PARAM_RAW)."')"));
            $failed_login_stu=$res[1]['FAILED_LOGIN'];
            if($failed_login_stu!='')
            {
                if ($ad_f_cnt && $ad_f_cnt!=0 && $failed_login_stu >= $ad_f_cnt)
                {
                    DBQuery("UPDATE students SET IS_DISABLE='Y' WHERE UPPER(USERNAME)=UPPER('".optional_param('USERNAME', 0, PARAM_RAW)."')");
                    if($failed_login_stu == $ad_f_cnt)
                        $error[] = "Incorrect username or password. Please try again.";
                     else
                    $error[] = "Due to excessive incorrect login attempts your account has been disabled. Contact the school administration to enable your account.";
                }
                else
                    $error[] = "Incorrect username or password. Please try again.";
            }
    }
}
if($_REQUEST[staff]=='na')
{
    $error[]="You are not asigned to any school";
} 

//if($_REQUEST['modfunc']=='create_account')
if(optional_param('modfunc','',PARAM_ALPHA)=='create_account')
{
    Warehouse('header');
    $_openSIS['allow_edit'] = true;
    if($_REQUEST['staff']['USERNAME'])
    $_REQUEST['modfunc'] = 'update';
    else
    $_REQUEST['staff_id'] = 'new';
    include('modules/Users/User.php');

    if(!$_REQUEST['staff']['USERNAME'])
    Warehouse('footer_plain');
    else
    {
        $note[] = 'Your account has been created.  You will be notified by email when it is verified by school administration and you can log in.';
        session_destroy();
    }
}

if(!$_SESSION['STAFF_ID'] && !$_SESSION['STUDENT_ID'] && $_REQUEST['modfunc']!='create_account')
{
    //Login
    require "login.inc.php";
}
elseif($_REQUEST['modfunc']!='create_account')
{
    echo "
        <HTML>
            <HEAD><TITLE>".Config('TITLE')."</TITLE><link rel=\"shortcut icon\" href=\"favicon.ico\"></HEAD>";
    echo "<noscript><META http-equiv=REFRESH content='0;url=index.php?modfunc=logout&reason=javascript' /></noscript>";
    echo "<frameset id=mainframeset rows='*,0' border=0 framespacing=0>
                <frameset cols='0,*' border=0>
                    <frame name='side' src='' frameborder='0' />
                    <frame name='body' src='Modules.php?modname=".($_REQUEST['modname']='misc/Portal.php')."&failed_login=$failed_login' frameborder='0' style='border: inset #C9C9C9 2px' />
                </frameset>
                <frame name='help' src='' />
            </frameset>
        </HTML>";
}
?>
