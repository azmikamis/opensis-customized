<?php
//function UpdateMissingAttendance($course_period_id)
//{
//
//    DBQuery("DELETE FROM missing_attendance WHERE COURSE_PERIOD_ID='".$course_period_id."'");
//    
//    DBQuery("INSERT INTO missing_attendance(SCHOOL_ID,SYEAR,SCHOOL_DATE,COURSE_PERIOD_ID,PERIOD_ID,TEACHER_ID,SECONDARY_TEACHER_ID) SELECT s.ID AS SCHOOL_ID,acc.SYEAR,acc.SCHOOL_DATE,cp.COURSE_PERIOD_ID,cp.PERIOD_ID, ".  db_case(array("tra.course_period_id=cp.course_period_id AND acc.school_date<tra.assign_date",'true','tra.pre_teacher_id','cp.teacher_id'))." AS TEACHER_ID,cp.SECONDARY_TEACHER_ID " .
//				"FROM attendance_calendar acc " .
//				"INNER JOIN marking_periods mp ON mp.SYEAR=acc.SYEAR AND mp.SCHOOL_ID=acc.SCHOOL_ID " .
//				" AND acc.SCHOOL_DATE BETWEEN mp.START_DATE AND mp.END_DATE " .
//				"INNER JOIN course_periods cp ON cp.MARKING_PERIOD_ID=mp.MARKING_PERIOD_ID AND cp.DOES_ATTENDANCE='Y' AND cp.CALENDAR_ID=acc.CALENDAR_ID " .
//                                                                        "LEFT JOIN teacher_reassignment tra ON (cp.course_period_id=tra.course_period_id) " .
//				"INNER JOIN school_periods sp ON sp.SYEAR=acc.SYEAR AND sp.SCHOOL_ID=acc.SCHOOL_ID AND sp.PERIOD_ID=cp.PERIOD_ID " .
//				" AND (sp.BLOCK IS NULL AND position(substring('UMTWHFS' FROM DAYOFWEEK(acc.SCHOOL_DATE) FOR 1) IN cp.DAYS)>0 " .
//				"   OR sp.BLOCK IS NOT NULL AND acc.BLOCK IS NOT NULL AND sp.BLOCK=acc.BLOCK) " .
//				"INNER JOIN schools s ON s.ID=acc.SCHOOL_ID " .
//				"INNER JOIN schedule sch ON sch.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND sch.START_DATE<=acc.SCHOOL_DATE " .
//                                                                        "AND (sch.END_DATE IS NULL OR sch.END_DATE>=acc.SCHOOL_DATE ) ".
//                                                                        "AND cp.COURSE_PERIOD_ID='".$course_period_id."'".
//				"LEFT JOIN attendance_completed ac ON ac.SCHOOL_DATE=acc.SCHOOL_DATE AND ".  db_case(array("tra.course_period_id=cp.course_period_id AND acc.school_date<tra.assign_date",'true','ac.staff_id=tra.pre_teacher_id','ac.staff_id=cp.teacher_id'))." AND ac.PERIOD_ID=sp.PERIOD_ID " .
//				"WHERE acc.SYEAR='".UserSyear()."' AND acc.SCHOOL_ID='".  UserSchool()."'" .
//				" AND (acc.MINUTES IS NOT NULL AND acc.MINUTES>0) " .
//				" AND acc.SCHOOL_DATE<='".date('Y-m-d',strtotime(DBDate()))."' " .
//                                                                        " AND ac.STAFF_ID IS NULL " .
//				"GROUP BY s.TITLE,acc.SCHOOL_DATE,cp.TITLE,cp.COURSE_PERIOD_ID,cp.TEACHER_ID");
//}


//function UpdateMissingAttendanceByDate($school_date)
//{
//    DBQuery("11DELETE FROM missing_attendance WHERE SCHOOL_DATE='".$school_date."' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".  UserSchool()."'");
//    
//    DBQuery("INSERT INTO missing_attendance(SCHOOL_ID,SYEAR,SCHOOL_DATE,COURSE_PERIOD_ID,PERIOD_ID,TEACHER_ID,SECONDARY_TEACHER_ID) SELECT s.ID AS SCHOOL_ID,acc.SYEAR,acc.SCHOOL_DATE,cp.COURSE_PERIOD_ID,cp.PERIOD_ID,".  db_case(array("tra.course_period_id=cp.course_period_id AND acc.school_date<tra.assign_date",'true','tra.pre_teacher_id','cp.teacher_id'))." AS TEACHER_ID,cp.SECONDARY_TEACHER_ID " .
//				"FROM attendance_calendar acc " .
//				"INNER JOIN marking_periods mp ON mp.SYEAR=acc.SYEAR AND mp.SCHOOL_ID=acc.SCHOOL_ID " .
//				" AND acc.SCHOOL_DATE BETWEEN mp.START_DATE AND mp.END_DATE " .
//				"INNER JOIN course_periods cp ON cp.MARKING_PERIOD_ID=mp.MARKING_PERIOD_ID AND cp.DOES_ATTENDANCE='Y' AND cp.CALENDAR_ID=acc.CALENDAR_ID " .
//                                                                        "LEFT JOIN teacher_reassignment tra ON (cp.course_period_id=tra.course_period_id) " .
//				"INNER JOIN school_periods sp ON sp.SYEAR=acc.SYEAR AND sp.SCHOOL_ID=acc.SCHOOL_ID AND sp.PERIOD_ID=cp.PERIOD_ID " .
//				" AND (sp.BLOCK IS NULL AND position(substring('UMTWHFS' FROM DAYOFWEEK(acc.SCHOOL_DATE) FOR 1) IN cp.DAYS)>0 " .
//				"   OR sp.BLOCK IS NOT NULL AND acc.BLOCK IS NOT NULL AND sp.BLOCK=acc.BLOCK) " .
//				"INNER JOIN schools s ON s.ID=acc.SCHOOL_ID " .
//				"INNER JOIN staff st ON (st.SCHOOLS IS NULL OR position(acc.SCHOOL_ID IN st.SCHOOLS)>0) " .
//				"INNER JOIN schedule sch ON sch.COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID AND sch.START_DATE<=acc.SCHOOL_DATE " .
//                                                                        "AND (sch.END_DATE IS NULL OR sch.END_DATE>=acc.SCHOOL_DATE ) ".
//				"LEFT JOIN attendance_completed ac ON ac.SCHOOL_DATE=acc.SCHOOL_DATE AND ac.STAFF_ID=cp.TEACHER_ID AND ac.PERIOD_ID=sp.PERIOD_ID " .
//				"WHERE acc.SYEAR='".UserSyear()."' AND acc.SCHOOL_ID='".  UserSchool()."'" .
//				" AND (acc.MINUTES IS NOT NULL AND acc.MINUTES>0) " .
//				" AND st.STAFF_ID='".User('STAFF_ID')."' " .
//				" AND acc.SCHOOL_DATE='".$school_date."' " .
//                                                                        " AND ac.STAFF_ID IS NULL " .
//				"GROUP BY s.TITLE,acc.SCHOOL_DATE,cp.TITLE,cp.COURSE_PERIOD_ID,cp.TEACHER_ID");
//
//}

//function DeleteMissingAttendanceByDate($calendar_id,$school_date)
//{
//        DBQuery("11DELETE mi.* FROM missing_attendance mi,course_periods cp WHERE mi.course_period_id=cp.course_period_id and cp.calendar_id=$calendar_id AND  mi.SCHOOL_DATE='".$school_date."' AND mi.SYEAR='".UserSyear()."' AND mi.SCHOOL_ID='".  UserSchool()."'");
//}
?>
