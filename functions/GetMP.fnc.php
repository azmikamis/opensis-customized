<?php

function GetMP($mp,$column='TITLE')
{	global $_openSIS;

	// mab - need to translate marking_period_id to title to be useful as a function call from dbget
	// also, it doesn't make sense to ask for same thing you give
	if($column=='MARKING_PERIOD_ID')
		$column='TITLE';

	if(!$_openSIS['GetMP'])
	{
		$_openSIS['GetMP'] = DBGet(DBQuery('SELECT MARKING_PERIOD_ID,TITLE,POST_START_DATE,POST_END_DATE,\'school_quarters\'        AS `TABLE`,SORT_ORDER,SHORT_NAME,START_DATE,END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS FROM school_quarters         WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\'
					UNION      SELECT MARKING_PERIOD_ID,TITLE,POST_START_DATE,POST_END_DATE,\'school_semesters\'       AS `TABLE`,SORT_ORDER,SHORT_NAME,START_DATE,END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS FROM school_semesters        WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\'
					UNION      SELECT MARKING_PERIOD_ID,TITLE,POST_START_DATE,POST_END_DATE,\'school_years\'           AS `TABLE`,SORT_ORDER,SHORT_NAME,START_DATE,END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS FROM school_years            WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\'
					UNION      SELECT MARKING_PERIOD_ID,TITLE,POST_START_DATE,POST_END_DATE,\'school_progress_periods\' AS `TABLE`,SORT_ORDER,SHORT_NAME,START_DATE,END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS FROM school_progress_periods WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\''),array(),array('MARKING_PERIOD_ID'));
	}
	if(substr($mp,0,1)=='E')
	{
		if($column=='TITLE' || $column=='SHORT_NAME')
			$suffix = ' Exam';
		$mp = substr($mp,1);
	}

	if($mp==0 && $column=='TITLE')
		return 'Full Year'.$suffix;
	else
		return $_openSIS['GetMP'][$mp][1][$column].$suffix;
}

function GetMPTable($mp_table)
{
	switch($mp_table)
	{
		case 'school_years':
			return 'FY';
		break;
		case 'school_semesters':
			return 'SEM';
		break;
		case 'school_quarters':
			return 'QTR';
		break;
		case 'school_progress_periods':
			return 'PRO';
		break;
		default:
			return 'FY';
		break;
	}
}

function GetCurrentMP($mp,$date,$error=true)
{	global $_openSIS;

	switch($mp)
	{
		case 'FY':
			$table = 'school_years';
		break;

		case 'SEM':
			$table = 'school_semesters';
		break;

		case 'QTR':
			$table = 'school_quarters';
		break;

		case 'PRO':
			$table = 'school_progress_periods';
		break;
	}

	if(!$_openSIS['GetCurrentMP'][$date][$mp])
	 	$_openSIS['GetCurrentMP'][$date][$mp] = DBGet(DBQuery('SELECT MARKING_PERIOD_ID FROM '.$table.' WHERE \''.$date.'\' BETWEEN START_DATE AND END_DATE AND SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\''));

	if($_openSIS['GetCurrentMP'][$date][$mp][1]['MARKING_PERIOD_ID'])
		return $_openSIS['GetCurrentMP'][$date][$mp][1]['MARKING_PERIOD_ID'];
	elseif(strpos($_SERVER['PHP_SELF'],'Side.php')===false && $error==true)
		ErrorMessage(array("You are not currently in a marking period"));
		//ShowErr("You are not currently in a marking period");
}
?>