<?php

function GetSchool($sch)
{	global $_openSIS;
	
	if(!$_openSIS['GetSchool'])
	{
		$QI=DBQuery('SELECT ID,TITLE FROM schools');
		$_openSIS['GetSchool'] = DBGet($QI,array(),array('ID'));
	}

	if($_openSIS['GetSchool'][$sch])
		return $_openSIS['GetSchool'][$sch][1]['TITLE'];
	else
		return $sch;
}

function GetUserSchools($staff_id,$str=false)
{
        $str_return='';
        $schools=DBGet(DBQuery('SELECT SCHOOL_ID FROM staff_school_relationship WHERE staff_id='.$staff_id.' AND syear='.  UserSyear()));
        foreach($schools as $school)
        {
            $return[]=$school['SCHOOL_ID'];
            $str_return .=$school['SCHOOL_ID'].',';
        }
        if($str==true)
            return substr($str_return,0,-1);
        else
            return $return;
}
?>
