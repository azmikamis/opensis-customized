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

#######################################################################################################################
include('../../Redirect_modules.php');
	if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='print' && $_REQUEST['report'])
	{
		echo '<style type="text/css">*{font-family:arial; font-size:12px;}</style>';
	
                                    if(clean_param($_REQUEST['id'],PARAM_ALPHANUM))
                                    {
                                        $from=",courses c";
                                        $where = ' AND c.course_id=cp.course_id AND c.grade_level='.$_REQUEST['id'];
                                    }
               $sql = 'select distinct
				(select title from course_subjects where subject_id=(select subject_id from courses where course_id=cp.course_id)) as subject,
				(select title from courses where course_id=cp.course_id) as COURSE_TITLE,cp.course_id
				from course_periods cp'.$from.' where cp.school_id=\''.UserSchool().'\' and cp.syear=\''.UserSyear().'\' '.$where.' order by subject,COURSE_TITLE';


                                    $ret = DBGet(DBQuery($sql));

		if(count($ret))
		{
			
			foreach($ret as $s_id)
			{
				echo "<table width=100%  style=\" font-family:Arial; font-size:12px;\" >";
				$grade_level_RET=  DBGet(DBQuery('SELECT TITLE FROM school_gradelevels WHERE id=\''.$_REQUEST['id'].'\''));
                                                                        $grade_title = $grade_level_RET[1]['TITLE'];
                    
		if($grade_title!='')
		{
				echo "<tr><td width=105>".DrawLogo()."</td><td  style=\"font-size:15px; font-weight:bold; padding-top:20px;\">". GetSchool(UserSchool())."<div style=\"font-size:12px;\">Course catalog : ".$grade_title."</div></td><td align=right style=\"padding-top:20px;\">". ProperDate(DBDate()) ."<br />Powered by openSIS</td></tr><tr><td colspan=3 style=\"border-top:1px solid #333;\">&nbsp;</td></tr></table>";
		 }
		 else
		 {
			 echo "<tr><td width=105>".DrawLogo()."</td><td  style=\"font-size:15px; font-weight:bold; padding-top:20px;\">". GetSchool(UserSchool())."<div style=\"font-size:12px;\">Course catalog : All</div></td><td align=right style=\"padding-top:20px;\">". ProperDate(DBDate()) ."<br />Powered by openSIS</td></tr><tr><td colspan=3 style=\"border-top:1px solid #333;\">&nbsp;</td></tr></table>";
		 }

			echo '<div align="center">';
			echo '<table border="0" width="97%" align="center"><tr><td><font face=verdana size=-1><b>'.$s_id['SUBJECT'].'</b></font></td></tr>';
			echo '<tr><td align="right"><table border="0" width="97%"><tr><td><font face=verdana size=-1><b>'.$s_id['COURSE_TITLE'].'</b></font></td></tr>';
			
			
                        $sql_periods = 'SELECT cp.SHORT_NAME,(SELECT TITLE FROM school_periods WHERE period_id=cp.period_id) AS PERIOD,cp.ROOM,cp.DAYS,(SELECT CONCAT(LAST_NAME,\' \',FIRST_NAME,\' \') from staff where staff_id=cp.TEACHER_ID) as TEACHER from course_periods cp where cp.course_id='.$s_id['COURSE_ID'].' and cp.syear=\''.UserSyear().'\' and cp.school_id=\''.UserSchool().'\'';
			$period_list = DBGet(DBQuery($sql_periods));
			
##############################################List Output Generation##################################################
			
			$columns = array('SHORT_NAME'=>'Course','PERIOD'=>'Time','DAYS'=>'Days','ROOM'=>'Location','TEACHER'=>'Teacher');
			
			echo '<tr><td colspan="2" valign="top" align="right">';
			PrintCatalog($period_list,$columns,'Course','Courses','','',array('search'=>false));
			echo '</td></tr></table></td></tr></table></td></tr>';
			
			######################################################################################################################
			echo '</table></div>';
	
			echo "<div style=\"page-break-before: always;\"></div>";
			}
		}
		else
		echo '<table width=100%><tr><td align=center><font color=red face=verdana size=2><strong>No Courses were found in this Grade Level</strong></font></td></tr></table>';
		
		
	}
	else
	{
	PopTable('header','Print Catalog by Grade Level','width=700');
	echo '<table width=100%><tr><td>';
	echo "<FORM id='search' name='search' method=POST action=Modules.php?modname=$_REQUEST[modname]>";
                  $grade_level_RET=  DBGet(DBQuery('SELECT ID,TITLE FROM school_gradelevels WHERE school_id=\''.UserSchool().'\''));
                if(count($grade_level_RET))
                {
                        echo CreateSelect($grade_level_RET, 'id', 'All', 'Select Grade Level: ', 'Modules.php?modname='.$_REQUEST['modname'].'&id=');
                }	
	echo '</td></tr></table>';
	if(clean_param($_REQUEST['id'],PARAM_ALPHANUM))
	{
                    $grade_level_RET=  DBGet(DBQuery('SELECT TITLE FROM school_gradelevels WHERE id=\''.$_REQUEST['id'].'\''));
                    $grade_title = $grade_level_RET[1]['TITLE'];
                    DrawHeader('<div align="center">Report generated for '.$grade_title.' Grade Level</div>');
	}
	else
	DrawHeader('<div align="center">Report generated for all Grade Levels</div>');	
	echo '</form>';
	echo "<FORM name=exp id=exp action=for_export.php?modname=$_REQUEST[modname]&modfunc=print&id=".$_REQUEST['id']."&_openSIS_PDF=true&report=true method=POST target=_blank>";
	echo '<table width=100%><tr><td align="center"><INPUT type=submit class=btn_medium value=\'Print\'></td></tr></table>';
	echo '</form>';
	PopTable('footer');
}






##########Functions###################
	function CreateSelect($val, $name, $opt, $cap, $link)
	{
		$html .= "<table width=100% border=0><tr><td width=40% align=center>";
		$html .= $cap;
		$html .= "<select name=".$name." id=".$name." onChange=\"window.location='".$link."' + this.options[this.selectedIndex].value;\">";
		$html .= "<option value=''>".$opt."</option>";
		
				foreach($val as $key=>$value)
				{
					if($value[strtoupper($name)]==$_REQUEST[$name])
						$html .= "<option selected value=".$value[strtoupper($name)].">".$value['TITLE']."</option>";
					else
						$html .= "<option value=".$value[strtoupper($name)].">".$value['TITLE']."</option>";	
				}
		
		
		$html .= "</select></td></tr></table>";		
		return $html;
	}

?>
