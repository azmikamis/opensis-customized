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


$extra['search'] .= '<TR><TD align=center colspan=2><TABLE><TR><TD><DIV id=fields_div></DIV></TD></TR></TABLE></TD></TR>';
$extra['new'] = true;

$_openSIS['CustomFields'] = true;
if($_REQUEST['fields']['FIRST_NAME'] || $_REQUEST['fields']['LAST_NAME'] || $_REQUEST['fields']['MIDDLE_NAME'] || $_REQUEST['fields']['LAST_YEAR_ID'] || $_REQUEST['fields']['PHONE'] || $_REQUEST['fields']['USERNAME'] || $_REQUEST['fields']['IS_DISABLE'] || $_REQUEST['fields']['EMAIL'] || $_REQUEST['fields']['LAST_LOGIN'] || $_REQUEST['fields']['PROFILE'])
{
	#$extra['SELECT'] .= ',s.FIRST_NAME,s.LAST_NAME,s.MIDDLE_NAME,s.ROLLOVER_ID,s.USERNAME,s.LAST_LOGIN,s.EMAIL,s.PHONE,s.IS_DISABLE,s.SCHOOLS ';
        $extra['SELECT'] .= ',s.FIRST_NAME,s.LAST_NAME,s.MIDDLE_NAME,s.USERNAME,s.LAST_LOGIN,s.EMAIL,s.PHONE,s.IS_DISABLE,s.CURRENT_SCHOOL_ID ';
	
}

if(!$extra['functions'])
	$extra['functions'] = array('LAST_LOGIN'=>'ProperDate','SCHOOLS'=>'Tot_School');

if($_REQUEST['search_modfunc']=='list')
{
	if(!$fields_list)
	{
		$fields_list = array('FULL_NAME'=>(Preferences('NAME')=='Common'?'Last, Common':'Last, First M'),'FIRST_NAME'=>'First','TITLE'=>'Title','LAST_NAME'=>'Last','MIDDLE_NAME'=>'Middle','STAFF_ID'=>'Staff Id','ROLLOVER_ID'=>'Last Year Id','SCHOOLS'=>'Schools','USERNAME'=>'Username','IS_DISABLE'=>'Disable','EMAIL'=>'Email ID','PHONE'=>'Phone','LAST_LOGIN'=>'Last Login','PROFILE'=>'User Profile');
		if($extra['field_names'])
			$fields_list += $extra['field_names'];

//		$periods_RET = DBGet(DBQuery("SELECT TITLE,PERIOD_ID FROM school_periods WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));
//		
//		foreach($periods_RET as $period)
//			$fields_list['PERIOD_'.$period['PERIOD_ID']] = $period['TITLE'].' Teacher - Room';
	}

	$custom_RET = DBGet(DBQuery("SELECT TITLE,ID,TYPE FROM staff_fields ORDER BY SORT_ORDER"));

	foreach($custom_RET as $field)
	{
                        //if(!$fields_list['CUSTOM_'.$field['ID']]){
                                //$fields_list['CUSTOM_'.$field['ID']] = $field['TITLE'];}
                        if(!$fields_list[$field['TITLE']])
                        {
                                $title=strtolower(trim($field['TITLE']));
                                if(strpos(trim($field['TITLE']),' ')!=0)
                                {
                                     $p1=substr(trim($field['TITLE']),0,strpos(trim($field['TITLE']),' '));
                                     $p2=substr(trim($field['TITLE']),strpos(trim($field['TITLE']),' ')+1);
                                     $title=strtolower($p1.'_'.$p2);
                                }
                                $fields_list[$title] = $field['TITLE'];
                                $extra['SELECT'] .= ',REPLACE(s.CUSTOM_'.$field['ID'].',"||",",") AS CUSTOM_'.$field['ID'];

                        }
	}
//	$addr_RET = DBGet(DBQuery("SELECT TITLE,ID,TYPE FROM address_fields ORDER BY SORT_ORDER"));
//
//	foreach($addr_RET as $field)
//	{
//                        if(!$fields_list['ADDRESS_'.$field['ID']])
//                        {
//                                $fields_list['ADDRESS_'.$field['ID']] = $field['TITLE'];
//                                $extra['SELECT'] .= ',a.CUSTOM_'.$field['ID'].' AS ADDRESS_'.$field['ID'];
//                                $extra['addr'] = true;
//                        }
//	}
//
//	foreach($periods_RET as $period)
//	{
//                        if($_REQUEST['month_include_active_date'])
//                                $date = $_REQUEST['day_include_active_date'].'-'.$_REQUEST['month_include_active_date'].'-'.$_REQUEST['year_include_active_date'];
//                        else
//                                $date = DBDate();
//
//                        if($_REQUEST['fields']['PERIOD_'.$period['PERIOD_ID']]=='Y') {
//                                                    #$extra['SELECT'] .= ',(SELECT CONCAT(COALESCE(st.FIRST_NAME,\' \'),\' \',COALESCE(st.LAST_NAME,\' \'),\' - \',COALESCE(cp.ROOM,\' \')) FROM staff st,schedule ss,course_periods cp,marking_periods mp WHERE mp.marking_period_id=cp.marking_period_id AND ss.STUDENT_ID=ssm.STUDENT_ID AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND cp.TEACHER_ID=st.STAFF_ID AND cp.PERIOD_ID=\''.$period['PERIOD_ID'].'\' AND \''.$date.'\' BETWEEN ss.START_DATE AND COALESCE(ss.END_DATE,mp.END_DATE)) AS PERIOD_'.$period['PERIOD_ID'];
//                                                    $extra['SELECT'] .= ',(SELECT GROUP_CONCAT(CONCAT(COALESCE(st.FIRST_NAME,\' \'),\' \',COALESCE(st.LAST_NAME,\' \'),\' - \',COALESCE(cp.ROOM,\' \'))) FROM staff st,schedule ss,course_periods cp WHERE ss.STUDENT_ID=ssm.STUDENT_ID AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND cp.TEACHER_ID=st.STAFF_ID AND cp.PERIOD_ID=\''.$period['PERIOD_ID'].'\' AND (\''.$date.'\' BETWEEN ss.START_DATE AND ss.END_DATE OR \''.$date.'\'>=ss.START_DATE AND ss.END_DATE IS NULL) LIMIT 1) AS PERIOD_'.$period['PERIOD_ID'];
//                        }
//			
//	}

                if($_REQUEST['fields'])
                {
                    foreach($_REQUEST['fields'] as $field => $on)
                    {
                        $columns[strtoupper($field)] = $fields_list[$field];
                        if(!$fields_list[$field])
                        {
                            $get_column = DBGet(DBQuery("SELECT ID,TITLE FROM staff_fields  ORDER BY SORT_ORDER"));
                            foreach($get_column as $COLUMN_NAME)
                            {
                                if('CUSTOM_'.$COLUMN_NAME['ID']==$field)
                                    $columns[strtoupper($field)] = $COLUMN_NAME['TITLE'];
                                else if(str_replace (" ", "_",strtoupper($COLUMN_NAME['TITLE']))==strtoupper($field))
                                    $columns[strtoupper($field)] = $COLUMN_NAME['TITLE'];
                            }
                            if(strpos($field,'CUSTOM')===0)
                            {
                                    $custom_id=str_replace("CUSTOM_","",$field);
                                    $custom_RET=DBGet(DBQuery("SELECT TYPE FROM staff_fields WHERE ID=$custom_id"));
                                    if($custom_RET[1]['TYPE']=='date' && !$extra['functions'][$field]){
                                        $extra['functions'][$field] = 'ProperDate';
                                    }
                                    elseif($custom_RET[1]['TYPE']=='codeds' && !$extra['functions'][$field]){
                                        $extra['functions'][$field] = 'DeCodeds';
                                    }
                            }
                        }
                }
                    $RET = GetStaffList($extra);
                    if($extra['array_function'] && function_exists($extra['array_function']))
                        $extra['array_function']($RET);
                    echo "<html><link rel='stylesheet' type='text/css' href='styles/export.css'><body style=\" font-family:Arial; font-size:12px;\">";
                    ListOutputPrint_Report($RET,$columns,$extra['singular']?$extra['singular']:'User',$extra['plural']?$extra['plural']:'Users',array(),$extra['LO_group'],$extra['LO_options']);
                    echo "</body></html>";
                }
}
else
{
	if(!$fields_list)
	{
		if(AllowUse('Users/User.php&category_id=1'))
			$fields_list['General'] = array('FULL_NAME'=>(Preferences('NAME')=='Common'?'Last, Common':'Last, First M'),'FIRST_NAME'=>'First','TITLE'=>'Title','LAST_NAME'=>'Last','MIDDLE_NAME'=>'Middle','STAFF_ID'=>'Staff Id','ROLLOVER_ID'=>'Last Year Id','SCHOOLS'=>'Schools','USERNAME'=>'Username','IS_DISABLE'=>'Disable','EMAIL'=>'Email ID','PHONE'=>'Phone','LAST_LOGIN'=>'Last Login','PROFILE'=>'User Profile');
//		if(AllowUse('Students/Student.php&category_id=2'))
//		{
//			$fields_list['Schedule'] = array('ADDRESS'=>'Address','CITY'=>'City','STATE'=>'State','ZIPCODE'=>'Zip Code','MAIL_ADDRESS'=>'Mailing Address','MAIL_CITY'=>'Mailing City','MAIL_STATE'=>'Mailing State','MAIL_ZIPCODE'=>'Mailing Zipcode');
//			$categories_RET = DBGet(DBQuery("SELECT ID,TITLE FROM address_field_categories ORDER BY SORT_ORDER"));
//			$custom_RET = DBGet(DBQuery("SELECT TITLE,ID,TYPE,CATEGORY_ID FROM address_fields ORDER BY SORT_ORDER"),array(),array('CATEGORY_ID'));
//
//			foreach($categories_RET as $category)
//			{
//				foreach($custom_RET[$category['ID']] as $field)
//				{
//					$fields_list['Address']['ADDRESS_'.$field['ID']] = str_replace("'",'&#39;',$field['TITLE']);
//				}
//			}
//		}
		if($extra['field_names'])
			$fields_list['General'] += $extra['field_names'];
	}
/*******************************************************************************/
	$categories_RET = DBGet(DBQuery('SELECT ID,TITLE FROM staff_field_categories ORDER BY SORT_ORDER'));
	$custom_RET = DBGet(DBQuery('SELECT TITLE,ID,TYPE,CATEGORY_ID FROM staff_fields ORDER BY SORT_ORDER'),array(),array('CATEGORY_ID'));
	foreach($categories_RET as $category)
	{
		if(AllowUse('Users/User.php&category_id='.$category['ID']))
		{
			foreach($custom_RET[$category['ID']] as $field)
			{
				 $title=strtolower(trim($field['TITLE']));
			 	if(strpos(trim($field['TITLE']),' ')!=0)
				{
				 $p1=substr(trim($field['TITLE']),0,strpos(trim($field['TITLE']),' '));
				 $p2=substr(trim($field['TITLE']),strpos(trim($field['TITLE']),' ')+1);
				 $title=strtolower($p1.'_'.$p2);
				}
				$fields_list[$category['TITLE']]['CUSTOM_'.$field['ID']] = $field['TITLE'];
			}	
		}
	}

//	$periods_RET = DBGet(DBQuery("SELECT TITLE,PERIOD_ID FROM school_periods WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"));
//	foreach($periods_RET as $period)
//		$fields_list['Schedule']['PERIOD_'.$period['PERIOD_ID']] = $period['TITLE'].' Teacher - Room';

//	if($openSISModules['Food_Service'])
//		$fields_list['Food_Service'] = array('FS_ACCOUNT_ID'=>'Account ID','FS_DISCOUNT'=>'Discount','FS_STATUS'=>'Status','FS_BARCODE'=>'Barcode','FS_BALANCE'=>'Balance');

	
	echo '<TABLE><TR><TD valign="top" width="300">';
	DrawHeader("<div><a class=big_font><img src=\"themes/Blue/expanded_view.png\" />Select Fields To Generate Report</a></div>",$extra['header_right']);
	PopTable_wo_header('header');
	echo '<TABLE><TR>';
	foreach($fields_list as $category=>$fields)
	{
		
		echo '<TD colspan=2 class=break_headers ><b>'.$category.'<BR></b></TD></TR><TR>';
		foreach($fields as $field=>$title)
		{
			$i++;
			echo '<TD><INPUT type=checkbox onclick="addHTML(\'<LI>'.$title.'</LI>\',\'names_div\',false);addHTML(\'<INPUT type=hidden name=fields['.$field.'] value=Y>\',\'fields_div\',false);addHTML(\'\',\'names_div_none\',true);this.disabled=true">'.$title.($field=='PARENTS'?'<BR>(<small>Relation: </small><input type=text id=relation name=relation size=8>)':'').'</TD>';
			if($i%2==0)
				echo '</TR><TR>';
		}
		if($i%2!=0)
		{
			echo '<TD></TD></TR><TR>';
			$i++;
		}
	}
	echo '</TR></TABLE>';
	PopTable('footer');
	echo '</TD><TD valign=top>';
	DrawHeader("<div><a class=big_font><img src=\"themes/Blue/expanded_view.png\" />Selected Fields</a></div>",$extra['header_right']);
	echo '<div id="names_div_none" class="error_msg" style="padding:6px 0px 0px 6px;">no fields selected</div><ol id=names_div class="selected_report_list"></ol>';
	

	echo '</TD></TR></TABLE>';
}

function Tot_School($value,$column)
{	
	$school=substr($value,1);
        $school=substr($school,0,-1);
        $sql = "SELECT ID,TITLE FROM schools WHERE ID IN($school)";
	$QI = DBQuery($sql);
	$schools_RET = DBGet($QI);
        foreach($schools_RET as $key=>$s_name)
        $Schools.=$s_name['TITLE'].',<br>';
        return substr($Schools,0,-1);  
}
?>
