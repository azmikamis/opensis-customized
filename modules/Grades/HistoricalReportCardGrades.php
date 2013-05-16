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
include 'modules/Grades/DeletePromptX.fnc.php';
DrawBC("Gradebook > ".ProgramTitle());
#Search('student_id','','true');
Search('student_id');
echo '<style type="text/css">#div_margin { margin-top:-20px; _margin-top:-1px; }</style>';

if(isset($_REQUEST['student_id']) )
{
	$RET = DBGet(DBQuery('SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME,NAME_SUFFIX,SCHOOL_ID FROM students,student_enrollment WHERE students.STUDENT_ID=\''.$_REQUEST['student_id'].'\' AND student_enrollment.STUDENT_ID = students.STUDENT_ID '));
	//$_SESSION['UserSchool'] = $RET[1]['SCHOOL_ID'];
        $count_student_RET=DBGet(DBQuery('SELECT COUNT(*) AS NUM FROM students'));
        if($count_student_RET[1]['NUM']>1){
	DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Deselect</font></A>) | <A HREF=Modules.php?modname='.$_REQUEST['modname'].'&search_modfunc=list&next_modname=Students/Student.php&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');
	//DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) | <A HREF=Modules.php?modname=Scheduling/Schedule.php&search_modfunc=list&next_modname=Scheduling/Schedule.php&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');



//DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) | <A HREF=Modules.php?modname='.$_REQUEST['modname'].'&search_modfunc=list&next_modname='.$_REQUEST['modname'].'&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');
        }else if($count_student_RET[1]['NUM']==1){
        DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Deselect</font></A>) ');
        }

	//echo '<div align="left" style="padding-left:16px"><b>Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'</b></div>';
}
####################

if(UserStudentID())
{
    $student_id = UserStudentID();
    $_REQUEST['mp_id'];
    if(isset($_REQUEST['mp_id'] ) )
    $mp_id = $_REQUEST['mp_id'];
    else
    {
        $current_markingperiod = DBGet(DBQuery('SELECT MARKING_PERIOD_ID AS MARKING_PERIOD_ID from student_gpa_calculated where  marking_period_id='.UserMP().' AND STUDENT_ID='.$student_id.''));
    
            if(!$current_markingperiod[1]['MARKING_PERIOD_ID'])
            {
                $paretmarkingperiod = DBGet(DBQuery('SELECT PARENT_ID AS MARKING_PERIOD_ID from marking_periods where  marking_period_id='.UserMP().' '));
                $current_markingperiod = DBGet(DBQuery('SELECT MARKING_PERIOD_ID AS MARKING_PERIOD_ID from student_gpa_calculated where  marking_period_id='.$paretmarkingperiod[1][MARKING_PERIOD_ID].' AND STUDENT_ID='.$student_id.''));
            }
           $mp_id = $current_markingperiod[1]['MARKING_PERIOD_ID'];
    }      
    $tab_id = ($_REQUEST['tab_id']?$_REQUEST['tab_id']:'grades');
    if ($_REQUEST['modfunc']=='update' && $_REQUEST['removemp'] && $mp_id && DeletePromptX('Marking Period')){
            DBQuery('DELETE FROM student_mp_stats WHERE student_id = '.$student_id.' and marking_period_id = '.$mp_id.'');
            unset($mp_id);
    }
    
    if ($_REQUEST['modfunc']=='update' && !$_REQUEST['removemp']){
	
        if ($_REQUEST['new_sms']) {
		
			// ------------------------ Start -------------------------- //
			$res=DBQuery('SELECT * FROM student_gpa_calculated WHERE student_id='.$student_id.' AND marking_period_id='.$_REQUEST['new_sms']);
			$rows = mysql_num_rows($res);
			
			if($rows==0)
			{
				DBQuery('INSERT INTO student_gpa_calculated (student_id, marking_period_id) VALUES ('.$student_id.', '.$_REQUEST['new_sms'].')');
			}
			elseif($rows!=0)
			{
				echo "<b>This Marking Periods has been updated.</b>";
			}
			// ------------------------- End --------------------------- //
            $mp_id = $_REQUEST['new_sms'];
            
        }

        if (($_REQUEST['SMS_GRADE_LEVEL']|| $_REQUEST['SCHOOL_NAME'] )&& $mp_id) {
            $updategl = DBQuery('UPDATE student_gpa_calculated SET grade_level_short = \''.trim($_REQUEST['SMS_GRADE_LEVEL']).'\'
                            WHERE marking_period_id = '.$mp_id.' AND student_id = '.$student_id.'');
           
            $res      = DBQuery('SELECT * FROM history_school WHERE student_id='.$student_id.' AND marking_period_id='.$mp_id.' ');
	     $rows    = mysql_num_rows($res);
		if($rows!=0)
	        {
                     $updatestats = 'UPDATE history_school SET school_name=\''.trim($_REQUEST['SCHOOL_NAME']).'\'
                                     WHERE marking_period_id = '.$mp_id.' AND student_id = '.$student_id.'';
                }
                elseif($rows==0)
                {	
                    $updatestats = 'INSERT INTO history_school  (student_id, marking_period_id,school_name) VALUES
                        ('.$student_id.','.$mp_id.',\''.trim($_REQUEST['SCHOOL_NAME']).'\')';
                }       
            DBQuery($updatestats);
        } 
        foreach($_REQUEST['values'] as $id=>$columns)
        {
                if($id!='new')
                {
                        $sql = 'UPDATE student_report_card_grades SET ';
                        if($columns['UNWEIGHTED_GP']){
                            $gp=$columns['UNWEIGHTED_GP'];
                        }
                        else {
                            $gp_RET=DBGet(DBQuery('SELECT IF(ISNULL(UNWEIGHTED_GP),  WEIGHTED_GP,UNWEIGHTED_GP ) AS GP FROM student_report_card_grades WHERE id=\''.$id.'\''));
                            $gp=$gp_RET[1];
                            $gp=$gp['GP'];
                        }
                        
                        $go = false;
                        if( $columns['WEIGHTED_GP']=='Y' && $tab_id=='grades'){
                                $sql .= 'WEIGHTED_GP'.'=\''.$gp.'\',UNWEIGHTED_GP=NULL,';
                                $go=true;
                        }
                        elseif($tab_id=='grades'){
                            $sql .= 'UNWEIGHTED_GP'.'=\''.$gp.'\',WEIGHTED_GP=NULL,';
                            $go=true;
                        }
                        foreach($columns as $column=>$value)
                        {
                            if($column=='UNWEIGHTED_GP' || $column=='WEIGHTED_GP')
                                continue;
                            $go=true;
                            $sql .= $column.'=\''.str_replace("\'","''",$value).'\',';
                        }
//                        if($_REQUEST['tab_id']!='new')
                            $sql = substr($sql,0,-1) . " WHERE ID='$id'";
//                        else
//                            $sql = substr($sql,0,-1) . " WHERE ID='$id'";
                            if($go)
                                DBQuery($sql);
                }
            else
            {
                $sql = 'INSERT INTO student_report_card_grades ';
                $fields = 'SCHOOL_ID, SYEAR, STUDENT_ID, MARKING_PERIOD_ID, ';
                $values = UserSchool().', '.UserSyear().', '.$student_id.', '.$mp_id.', ';

                $go = false;
               
                 if( $columns['WEIGHTED_GP']=='Y' && $tab_id=='grades'){
                     $fields .= 'WEIGHTED_GP,';
                     if($columns['UNWEIGHTED_GP']=="")
                     {
                         $values .='NULL'.',';
                     }
                     else
                     {
                        $values .=$columns['UNWEIGHTED_GP'].',';
                     }
                 }
                 elseif($tab_id=='grades'){
                     $fields .= 'UNWEIGHTED_GP,';
                     if($columns['UNWEIGHTED_GP']=="")
                     {
                         $values .='NULL'.',';
                     }
                     else
                     {
                        $values .=$columns['UNWEIGHTED_GP'].',';
                     }
                 }
                 
                foreach($columns as $column=>$value)
                {
                    if($column=='UNWEIGHTED_GP' || $column=='WEIGHTED_GP')
                                continue;
                        
                    if(trim($value))
                    {
                        
                        $fields .= $column.',';
                        $values .= '\''.str_replace("\'","''",$value).'\',';
                        $go = true;
                    }
                }
                $sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';

                if($go && $mp_id && $student_id)
                    DBQuery($sql);
            }
        }
        unset($_REQUEST['modfunc']); 

    }
    if($_REQUEST['modfunc']=='remove')
    {
        if(DeletePromptX('Student Grade'))
        {
            DBQuery('DELETE FROM student_report_card_grades WHERE ID=\''.$_REQUEST['id'].'\'');
        }
    }    
    if(!$_REQUEST['modfunc']){    
        $stuRET = DBGet(DBQuery('SELECT LAST_NAME, FIRST_NAME, MIDDLE_NAME, NAME_SUFFIX from students where STUDENT_ID = '.$student_id.''));
        $stuRET = $stuRET[1];
        $displayname = $stuRET['LAST_NAME'].(($stuRET['NAME_SUFFIX'])?$stuRET['suffix'].' ':'').', '.$stuRET['FIRST_NAME'].' '.$stuRET['MIDDLE_NAME'];
       
//       $gquery = "SELECT mp.syear, mp.marking_period_id as mp_id, mp.title as mp_name, mp.post_end_date as posted, sms.grade_level_short as grade_level, 
//       (sms.sum_weighted_factors/sms.count_weighted_factors)*s.reporting_gp_scale as weighted_gpa,
//        sms.cum_weighted_factor*s.reporting_gp_scale as weighted_cum,
//        (sms.sum_unweighted_factors/sms.count_unweighted_factors)*s.reporting_gp_scale as unweighted_gpa,
//        sms.cum_unweighted_factor*s.reporting_gp_scale as unweighted_cum
//       FROM marking_periods mp, student_mp_stats sms, schools s
//       WHERE sms.marking_period_id = mp.marking_period_id and
//             s.id = mp.school_id and sms.student_id = $student_id
//       AND mp.school_id = '".UserSchool()."' order by mp.post_end_date";
       
       $gquery = 'SELECT mp.syear, mp.marking_period_id as mp_id, mp.title as mp_name, mp.post_end_date as posted, sgc.grade_level_short as GRADE_LEVEL, 
       sgc.weighted_gpa, sgc.unweighted_gpa
       FROM marking_periods mp, student_gpa_calculated sgc, schools s
       WHERE sgc.marking_period_id = mp.marking_period_id and
             s.id = mp.school_id and sgc.student_id = '.$student_id.'
       AND mp.school_id = \''.UserSchool().'\' order by mp.post_end_date';
           
        $GRET = DBGet(DBQuery($gquery));
       
       
        $last_posted = null;
        $gmp = array(); //grade marking_periodso
        $grecs = array();  //grade records
        if($GRET){
            foreach($GRET as $rec){
                if ($mp_id == null || $mp_id == $rec['MP_ID']){
                    $mp_id = $rec['MP_ID'];
                    $gmp[$rec['MP_ID']] = array('schoolyear'=>formatSyear($rec['SYEAR']),
                                                'mp_name'=>$rec['MP_NAME'],
                                                'grade_level'=>$rec['GRADE_LEVEL'],
                                                'weighted_cum'=>$rec['WEIGHTED_CUM'],
                                                'unweighted_cum'=>$rec['UNWEIGHTED_CUM'],
                                                'weighted_gpa'=>$rec['WEIGHTED_GPA'],
                                                'unweighted_gpa'=>$rec['UNWEIGHTED_GPA'],
                                                'gpa'=>$rec['GPA']);
                }
                if ($mp_id != $rec['MP_ID']){
                    $gmp[$rec['MP_ID']] = array('schoolyear'=>formatSyear($rec['SYEAR']),
                                                'mp_name'=>$rec['MP_NAME'],
                                                'grade_level'=>$rec['GRADE_LEVEL'],
                                                'weighted_cum'=>$rec['WEIGHTED_CUM'],
                                                'unweighted_cum'=>$rec['UNWEIGHTED_CUM'],
                                                'weighted_gpa'=>$rec['WEIGHTED_GPA'],
                                                'unweighted_gpa'=>$rec['UNWEIGHTED_GPA'],
                                                'gpa'=>$rec['GPA']);
                }    
            }
        } else {
            
//            $current_markingperiod = DBGet(DBQuery("SELECT MARKING_PERIOD_ID AS MARKING_PERIOD_ID from student_gpa_calculated where  marking_period_id=".UserMP()." AND STUDENT_ID=$student_id"));
//            if(!$current_markingperiod[1]['MARKING_PERIOD_ID'])
//            {
//                $paretmarkingperiod = DBGet(DBQuery("SELECT PARENT_ID AS MARKING_PERIOD_ID from marking_periods where  marking_period_id=".UserMP()." "));
//                $current_markingperiod = DBGet(DBQuery("SELECT MARKING_PERIOD_ID AS MARKING_PERIOD_ID from student_gpa_calculated where  marking_period_id=".$paretmarkingperiod[1][MARKING_PERIOD_ID]." AND STUDENT_ID=$student_id"));
//            }
//            $mp_id = $current_markingperiod[1]['MARKING_PERIOD_ID'];
             $mp_id =0;
        }
       if($mp_id)
        $historyschool = DBGet(DBQuery('SELECT SCHOOL_NAME  from history_school where STUDENT_ID = '.$student_id.' and marking_period_id='.$mp_id.' '));
        
        $mpselect = "<FORM action=Modules.php?modname=$_REQUEST[modname]&tab_id=".$_REQUEST['tab_id']." method=POST>";
        $mpselect .= "<SELECT name=mp_id onchange='this.form.submit();'>";
        foreach ($gmp as $id=>$mparray){
            $mpselect .= "<OPTION value=".$id.(($id==$mp_id)?' SELECTED':'').">".$mparray['schoolyear'].' '.$mparray['mp_name'].', Grade '.$mparray['grade_level']."</OPTION>";
        }
        $mpselect .= "<OPTION value=0 ".(($mp_id=='0')?' SELECTED':'').">Add another marking period</OPTION>";   
        $mpselect .= '</SELECT>';
        
        echo '</FORM>';

            echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=update&tab_id=$_REQUEST[tab_id]&mp_id=$mp_id method=POST>";
            //DrawHeaderHome($mpselect);
              $sms_grade_level = TextInput($gmp[$mp_id]['grade_level'],"SMS_GRADE_LEVEL","",'size=25  class=cell_floating');
           echo '<BR><table  width=90%><tr><td >Student:</td><td>'.$displayname.'</td></tr>
                 <tr><td >Weighted GPA:</td><td>'.sprintf('%0.2f',$gmp[$mp_id]['weighted_gpa']).'</td></tr>
                 <tr> <td >Unweighted GPA:</td><td>'.sprintf('%0.2f',$gmp[$mp_id]['unweighted_gpa']).'</td></tr>
                 ';
                 
            
            
          
            
            if ($mp_id=="0"){
                $syear = UserSyear();
                $sql = 'SELECT MARKING_PERIOD_ID, SYEAR, TITLE, POST_END_DATE FROM marking_periods WHERE SCHOOL_ID = \''.UserSchool().
                        '\' ORDER BY POST_END_DATE';
                $MPRET = DBGet(DBQuery($sql));
                if ($MPRET){
                    $mpoptions = array();
                    foreach ($MPRET as $id=>$mp){
                        $mpoptions[$mp['MARKING_PERIOD_ID']] = formatSyear($mp['SYEAR']).' '.$mp['TITLE'];
                    } 
//                   PopTable_grade_header('header');
//                    echo "<TABLE align=center><TR><TD>";
//               f ($mp_id=="0"){
                  
                   
//                    echo "</TD>";
//					echo "<TD WIDTH=14%></TD>";
//					echo "<TD>";
//                    echo $sms_grade_level;
//                    echo "</TD></TR></TABLE>";
                     // echo '<tr> <td >Select Marking Period:</td><td>'.$mpselect.'</td> 
                   // $extra= "onchange=GetSchool(this.value);";
                    echo '<tr> <td >New Marking Period:</td><td>'.SelectInput(null,'new_sms','',$mpoptions,false,$extra).'</td></tr>';
                    echo '<tr> <td >School Name:</td><td>'.TextInput($historyschool[1]['school_name'],"SCHOOL_NAME","",'size=35  class=cell_floating ').'</td></tr>
                           <tr> <td >Grade Level:</td><td>'.$sms_grade_level.'</td>
                          </tr></table>';
                    
//					PopTable ('footer');
                } 
                
            } 
            
            else {
               //  echo '<tr><td align=right width=50% valign=top>Grade:</td><td width=50% valign=top>'.$sms_grade_level.'</td></tr><tr><td class=clear></td></tr></table>';
                $selectedmp = $mp_id;
               
                if($historyschool[1]['SCHOOL_NAME'])
                    $school_name= $historyschool[1]['SCHOOL_NAME'];
                else
                {
                    $get_schoolid = mysql_fetch_array(mysql_query("SELECT school_id FROM  marking_periods  WHERE marking_period_id = $selectedmp"));
                    if($get_schoolid['school_id'])
                    {
                        $get_schoolid = mysql_fetch_array(mysql_query("SELECT title FROM  schools  WHERE id = $get_schoolid[school_id]")); 
                        $school_name=  $get_schoolid['title'];
                    }
                }
                echo '<tr> <td >Grade Level:</td><td>'.$sms_grade_level.'</td>
                      <tr> <td >Select Marking Period:</td><td>'.$mpselect.'</td></tr>
                      <tr> <td >School Name:</td><td>'.TextInput($school_name,"SCHOOL_NAME","",'size=35  class=cell_floating').'</td>
                      </tr></table>'; 
                $sql = 'SELECT ID,COURSE_TITLE,GRADE_PERCENT,GRADE_LETTER,
                    IF(ISNULL(UNWEIGHTED_GP),  WEIGHTED_GP,UNWEIGHTED_GP ) AS GP,WEIGHTED_GP as WEIGHTED_GP,
                    GP_SCALE,CREDIT_ATTEMPTED,CREDIT_EARNED,CREDIT_CATEGORY
                       FROM student_report_card_grades WHERE STUDENT_ID = '.$student_id.' AND MARKING_PERIOD_ID = '.$mp_id.' ORDER BY ID';
            
                //build forms based on tab selected
              
                    $functions = array( 'COURSE_TITLE'=>'makeTextInput',
                                        'GRADE_PERCENT'=>'makeTextInput',
                                        'GRADE_LETTER'=>'makeTextInput',
                                        'GP'=>'makeTextInput',                  
                                        'WEIGHTED_GP'=>'makeCheckboxInput',
                                        'GP_SCALE'=>'makeTextInput',
                                        'CREDIT_ATTEMPTED'=>'makeTextInput',
                                        'CREDIT_EARNED'=>'makeTextInput',
                                        'CREDIT_CATEGORY'=>'makeTextInput'
                                        );
                    $LO_columns = array('COURSE_TITLE'=>'Course Name',
                                        'GRADE_PERCENT'=>'Percentage',
                                        'GRADE_LETTER'=>'Letter Grade',
                                        'GP'=>'GP Value',
                                        'WEIGHTED_GP'=>'Weighted GP',
                                        'GP_SCALE'=>'Grade Scale',
                                        'CREDIT_ATTEMPTED'=>'Credit Attempted',
                                        'CREDIT_EARNED'=>'Credit Earned',
                                        'CREDIT_CATEGORY'=>'Credit Category'
                                        );
                    $link['add']['html'] = array('COURSE_TITLE'=>makeTextInput('','COURSE_TITLE'),
                                        'GRADE_PERCENT'=>makeTextInput('','GRADE_PERCENT'),
                                        'GRADE_LETTER'=>makeTextInput('','GRADE_LETTER'),
                                        'GP'=>makeTextInput('','GP'),
                                        'WEIGHTED_GP'=>makeCheckboxInput('','WEIGHTED_GP'),
                                        'GP_SCALE'=>makeTextInput('','GP_SCALE'),
                                         'CREDIT_ATTEMPTED'=>makeTextInput('','CREDIT_ATTEMPTED'),
                                        'CREDIT_EARNED'=>makeTextInput('','CREDIT_EARNED'),
                                        'CREDIT_CATEGORY'=>makeTextInput('','CREDIT_CATEGORY')
                                        );
               
                $link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove&mp_id=$mp_id";
                $link['remove']['variables'] = array('id'=>'ID');
                $link['add']['html']['remove'] = button('add');
               if($mp_id)
               {$LO_ret = DBGet(DBQuery($sql),$functions);
//				echo '<div  id="div_margin">';
//				
//				echo '</div>';
            	ListOutput($LO_ret,$LO_columns,'','',$link,array(),array('count'=>true,'download'=>true,'search'=>true));
               }
				
            }
            echo '<CENTER>';
//            if (!$LO_ret){
//                echo SubmitButton('Remove Marking Period', 'removemp','class=btn_large');
//				echo '&nbsp;';
//            }
            echo SubmitButton('Save','','class=btn_medium').'</CENTER>';
            echo '</FORM>';
    }
}
function makeTextInput($value,$name)
{    global $THIS_RET;

    if($THIS_RET['ID'])
        $id = $THIS_RET['ID'];
    else
        $id = 'new';
    if($name=='COURSE_TITLE')
        $extra = 'size=25 maxlength=25 class=cell_floating';
    elseif($name=='GRADE_PERCENT')
        $extra = 'size=6 maxlength=6 class=cell_floating';
    elseif($name=='GRADE_LETTER' )
        $extra = 'size=5 maxlength=5 class=cell_floating';
     elseif( $name=='GP' )
     {  $name='UNWEIGHTED_GP'; 
        $extra = 'size=5 maxlength=5 class=cell_floating';
     }
    else
    $extra = 'size=10 maxlength=10 class=cell_floating';

    return TextInput($value,"values[$id][$name]",'',$extra);
}
function formatSyear($value){
    return substr($value,2).'-'.substr($value+1,2);
}

function makeCheckboxInput($value,$name)
{	global $THIS_RET;
	
	if($THIS_RET['ID'])
		$id = $THIS_RET['ID'];
	else
		$id = 'new';
        
        if($THIS_RET['WEIGHTED_GP']!=NULL)
		$yes='Yes';
        else
            $no='No';
	
	return '<input type=hidden name=values['.$id.']['.$name.'] value="'.$value.'" />'.CheckboxInput($value,'values['.$id.']['.$name.']','','',($id=='new'?true:false),$yes,$no,false);
                    
}
//unction CheckboxInput($value,$name,$title='',$checked='',$new=false,$yes='Yes',$no='No',$div=true,$extra='')
?>
