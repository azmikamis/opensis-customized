<?php
include('Redirect_root.php');
include('Warehouse.php');
$next_syear=$_SESSION['NY'];
$table=$_REQUEST['table_name'];
$next_start_date=$_SESSION['roll_start_date'];
$tables = array('staff'=>'Users','school_periods'=>'School Periods','school_years'=>'Marking Periods','attendance_calendars'=>'Calendars','report_card_grade_scales'=>'Report Card Grade Codes','course_subjects'=>'Subjects','courses'=>'Courses','course_periods'=>'Course Periods','student_enrollment'=>'Students','honor_roll'=>'Honor Roll Setup','attendance_codes'=>'Attendance Codes','student_enrollment_codes'=>'Student Enrollment Codes','report_card_comments'=>'Report Card Comment Codes','NONE'=>'none');
$no_school_tables = array('student_enrollment_codes'=>true,'staff'=>true);
switch($table)
{
		case 'staff':
		
//			$user_custom='';
//			$fields_RET = DBGet(DBQuery("SELECT ID FROM staff_fields"));
//			foreach($fields_RET as $field)
//			     $user_custom .= ',CUSTOM_'.$field['ID'];
////			DBQuery("DELETE FROM students_join_users WHERE STAFF_ID IN (SELECT STAFF_ID FROM staff WHERE SYEAR=$next_syear)");
////			DBQuery("DELETE FROM staff_exceptions WHERE USER_ID IN (SELECT STAFF_ID FROM staff WHERE SYEAR=$next_syear)");
////			DBQuery("DELETE FROM program_user_config WHERE USER_ID IN (SELECT STAFF_ID FROM staff WHERE SYEAR=$next_syear)");
////			DBQuery("DELETE FROM staff WHERE SYEAR='$next_syear'");
//                        $staff_rollovered=DBGet(DBQuery("SELECT STAFF_ID FROM staff WHERE SYEAR='$next_syear'"));
//                        $total_staff=count($staff_rollovered);
//                        if($total_staff==0){
//                            DBQuery("INSERT INTO staff (SYEAR,CURRENT_SCHOOL_ID,TITLE,FIRST_NAME,LAST_NAME,MIDDLE_NAME,USERNAME,PASSWORD,PHONE,EMAIL,PROFILE,HOMEROOM,LAST_LOGIN,SCHOOLS,PROFILE_ID,ROLLOVER_ID$user_custom) SELECT SYEAR+1,CURRENT_SCHOOL_ID,TITLE,FIRST_NAME,LAST_NAME,MIDDLE_NAME,USERNAME,PASSWORD,PHONE,EMAIL,PROFILE,HOMEROOM,NULL,SCHOOLS,PROFILE_ID,STAFF_ID$user_custom FROM staff WHERE SYEAR='".UserSyear()."'");
//                            DBQuery("INSERT INTO program_user_config (USER_ID,PROGRAM,TITLE,VALUE) SELECT s.STAFF_ID,puc.PROGRAM,puc.TITLE,puc.VALUE FROM staff s,program_user_config puc WHERE puc.USER_ID=s.ROLLOVER_ID AND puc.PROGRAM='Preferences' AND s.SYEAR='$next_syear'");
//                            DBQuery("INSERT INTO staff_exceptions (USER_ID,MODNAME,CAN_USE,CAN_EDIT) SELECT STAFF_ID,MODNAME,CAN_USE,CAN_EDIT FROM staff,staff_exceptions WHERE USER_ID=ROLLOVER_ID AND SYEAR='$next_syear'");
//                            DBQuery("INSERT INTO students_join_users (STUDENT_ID,STAFF_ID) SELECT j.STUDENT_ID,s.STAFF_ID FROM staff s,students_join_users j WHERE j.STAFF_ID=s.ROLLOVER_ID AND s.SYEAR='$next_syear'");                   
//                        }
//                        $parent=DBGet(DBQuery("SELECT * FROM staff WHERE PROFILE='parent' AND SYEAR='$next_syear' AND CURRENT_SCHOOL_ID='".UserSchool()."'"));
//                        foreach($parent as $key)
//                        { 
//                            $join_students=DBGet(DBQuery("SELECT * FROM students_join_users WHERE STAFF_ID='".$key[ROLLOVER_ID]."'"));
//                            foreach($join_students as $stu_info)
//                            {   
//                                  $enrollment_record=DBGet(DBQuery("SELECT * FROM student_enrollment WHERE STUDENT_ID='$stu_info[STUDENT_ID]' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
//                                  
//                                  foreach($enrollment_record as $enroll_next_school)
//                                  {
//                                          if($enroll_next_school['NEXT_SCHOOL']=='-1')
//                                          {
//                                              $arr[]='true';
//                                          }
//                                         else {
//                                            $arr[]='false';
//                                        }
//                                  }
//                            }
//                            
//                            
//                            if(!in_array('false', $arr))
//                            {
//                                DBQuery("DELETE FROM staff WHERE STAFF_ID='".$key['STAFF_ID']."'");
//                            }
//                        }
                        DBQuery('DELETE FROM staff_school_relationship WHERE school_id=\''.  UserSchool().'\' AND syear=\''.$next_syear.'\'');
                        DBQuery('INSERT INTO staff_school_relationship (staff_id,school_id,syear) SELECT staff_id,school_id,syear+1 FROM staff_school_relationship WHERE school_id=\''.  UserSchool().'\' AND syear=\''. UserSyear().'\'');

                        $exists_RET[$table] = DBGet(DBQuery('SELECT count(*) AS COUNT from staff_school_relationship WHERE SYEAR=\''.$next_syear.'\' AND SCHOOL_ID=\''.UserSchool().'\''));
                        $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                        echo $tables['staff'].'|'.'(|'.$total_rolled_data.'|)';
                    break;

		case 'school_periods':
                            
                        DBQuery('DELETE FROM school_periods WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.$next_syear.'\'');
                        DBQuery('INSERT INTO school_periods (SYEAR,SCHOOL_ID,SORT_ORDER,TITLE,SHORT_NAME,LENGTH,ATTENDANCE,ROLLOVER_ID,START_TIME,END_TIME) SELECT SYEAR+1,SCHOOL_ID,SORT_ORDER,TITLE,SHORT_NAME,LENGTH,ATTENDANCE,PERIOD_ID,START_TIME,END_TIME FROM school_periods WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\'');
                        $exists_RET[$table] = DBGet(DBQuery('SELECT count(*) AS COUNT from '.$table.' WHERE SYEAR=\''.$next_syear.'\''.(!$no_school_tables[$table]?' AND SCHOOL_ID=\''.UserSchool().'\'':'')));
                        $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                        echo $tables['school_periods'].'|'.'(|'.$total_rolled_data.'|)';
                    break;
		
		case 'attendance_calendars':
                        
			DBQuery('DELETE FROM attendance_calendars WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.$next_syear.'\'');
			DBQuery('INSERT INTO attendance_calendars (SYEAR,SCHOOL_ID,TITLE,DEFAULT_CALENDAR,ROLLOVER_ID) SELECT SYEAR+1,SCHOOL_ID,TITLE,DEFAULT_CALENDAR,CALENDAR_ID FROM attendance_calendars WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\'');
                        //------------------newly added-------------------
                        DBQuery('DELETE FROM attendance_calendar WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.$next_syear.'\'');
                        DBQuery('DELETE FROM calendar_events WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.$next_syear.'\'');

                        $calendars_RET = DBGet(DBQuery('SELECT CALENDAR_ID,ROLLOVER_ID FROM attendance_calendars WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.$next_syear.'\''));
                        foreach($calendars_RET as $calendar)
                        {
                            roll_calendar($calendar['CALENDAR_ID'],$calendar['ROLLOVER_ID']);
                        }
//                      DBQuery("INSERT INTO attendance_calendar (SYEAR,SCHOOL_ID,SCHOOL_DATE,MINUTES,BLOCK,CALENDAR_ID) SELECT SYEAR+1,SCHOOL_ID,SCHOOL_DATE+INTERVAL '1' YEAR,MINUTES,BLOCK,(SELECT CALENDAR_ID FROM attendance_calendars WHERE attendance_calendar.CALENDAR_ID=attendance_calendars.ROLLOVER_ID) AS CAL_ID FROM attendance_calendar WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
                        DBQuery('INSERT INTO calendar_events (SYEAR,SCHOOL_ID,SCHOOL_DATE,TITLE,DESCRIPTION) SELECT SYEAR+1,SCHOOL_ID,SCHOOL_DATE+INTERVAL \'1\' YEAR,TITLE,DESCRIPTION FROM calendar_events WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\'');
                        $exists_RET[$table] = DBGet(DBQuery('SELECT count(*) AS COUNT from '.$table.' WHERE SYEAR=\''.$next_syear.'\''.(!$no_school_tables[$table]?' AND SCHOOL_ID=\''.UserSchool().'\'':'')));
                        $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                        echo $tables['attendance_calendars'].'|'.'(|'.$total_rolled_data.'|)';                                      //-------------------end--------------------------------
                    break;

		case 'school_years':
                        
			DBQuery('DELETE FROM school_progress_periods WHERE SYEAR=\''.$next_syear.'\' AND SCHOOL_ID=\''.UserSchool().'\'');
			DBQuery('DELETE FROM school_quarters WHERE SYEAR=\''.$next_syear.'\' AND SCHOOL_ID=\''.UserSchool().'\'');
			DBQuery('DELETE FROM school_semesters WHERE SYEAR=\''.$next_syear.'\' AND SCHOOL_ID=\''.UserSchool().'\'');
			DBQuery('DELETE FROM school_years WHERE SYEAR=\''.$next_syear.'\' AND SCHOOL_ID=\''.UserSchool().'\'');
			$r = DBGet(DBQuery('select max(m.marking_period_id) as marking_period_id from (select max(marking_period_id) as marking_period_id from school_years union select max(marking_period_id) as marking_period_id from school_semesters union select max(marking_period_id) as marking_period_id from school_quarters) m'));
			$mpi = $r[1]['MARKING_PERIOD_ID'] + 1;
		        DBQuery('ALTER TABLE marking_period_id_generator AUTO_INCREMENT = '.$mpi.'');
                         // DBQuery('INSERT INTO marking_period_id_generator (id)VALUES (NULL)');
                            //$MARKING_PERIOD_SEQ_VALUE_ARRAY= DBGet(DBQuery('SELECT  max(id) AS ID from marking_period_id_generator' ));
                           // $MARKING_PERIOD_SEQ_VALUE=$MARKING_PERIOD_SEQ_VALUE_ARRAY[1]['ID'];
			DBQuery('INSERT INTO school_years (MARKING_PERIOD_ID,SYEAR,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,ROLLOVER_ID) SELECT '.db_seq_nextval('marking_period_seq').',SYEAR+1,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE + INTERVAL 1 YEAR,END_DATE + INTERVAL 1 YEAR,POST_START_DATE + INTERVAL 1 YEAR,POST_END_DATE +INTERVAL 1 YEAR,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,MARKING_PERIOD_ID FROM school_years WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\'');
                        DBQuery('INSERT INTO school_semesters (MARKING_PERIOD_ID,YEAR_ID,SYEAR,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,ROLLOVER_ID) SELECT '.db_seq_nextval('marking_period_seq').',(SELECT MARKING_PERIOD_ID FROM school_years y WHERE y.SYEAR=s.SYEAR+1 AND y.ROLLOVER_ID=s.YEAR_ID),SYEAR+1,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE + INTERVAL 1 YEAR,END_DATE + INTERVAL 1 YEAR,POST_START_DATE + INTERVAL 1 YEAR,POST_END_DATE + INTERVAL 1 YEAR,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,MARKING_PERIOD_ID FROM school_semesters s WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\'');
                        DBQuery('INSERT INTO school_quarters (MARKING_PERIOD_ID,SEMESTER_ID,SYEAR,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,ROLLOVER_ID) SELECT '.db_seq_nextval('marking_period_seq').',(SELECT MARKING_PERIOD_ID FROM school_semesters s WHERE s.SYEAR=q.SYEAR+1 AND s.ROLLOVER_ID=q.SEMESTER_ID),SYEAR+1,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE+INTERVAL 1 YEAR,END_DATE+INTERVAL 1 YEAR,POST_START_DATE+INTERVAL 1 YEAR,POST_END_DATE+INTERVAL 1 YEAR,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,MARKING_PERIOD_ID FROM school_quarters q WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\'');
                        DBQuery('INSERT INTO school_progress_periods (MARKING_PERIOD_ID,QUARTER_ID,SYEAR,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE,END_DATE,POST_START_DATE,POST_END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,ROLLOVER_ID) SELECT '.db_seq_nextval('marking_period_seq').',(SELECT MARKING_PERIOD_ID FROM school_quarters q WHERE q.SYEAR=p.SYEAR+1 AND q.ROLLOVER_ID=p.QUARTER_ID),SYEAR+1,SCHOOL_ID,TITLE,SHORT_NAME,SORT_ORDER,START_DATE+INTERVAL 1 YEAR,END_DATE+INTERVAL 1 YEAR,POST_START_DATE+INTERVAL 1 YEAR,POST_END_DATE+INTERVAL 1 YEAR,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,MARKING_PERIOD_ID FROM school_progress_periods p WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\'');
                        $exists_RET[$table] = DBGet(DBQuery("SELECT count(*) AS COUNT from $table WHERE SYEAR='$next_syear'".(!$no_school_tables[$table]?" AND SCHOOL_ID='".UserSchool()."'":'')));             
                        $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                        echo $tables['school_years'].'|'.'(|'.$total_rolled_data.'|)';
                    break;

                    case 'course_subjects':
                    DBQuery('DELETE FROM course_subjects WHERE SYEAR=\''.$next_syear.'\' AND SCHOOL_ID=\''.UserSchool().'\'');
                    DBQuery('INSERT INTO course_subjects (SYEAR,SCHOOL_ID,TITLE,SHORT_NAME,ROLLOVER_ID) SELECT SYEAR+1,SCHOOL_ID,TITLE,SHORT_NAME,SUBJECT_ID FROM course_subjects WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\'');
                    $exists_RET[$table] = DBGet(DBQuery('SELECT count(*) AS COUNT from '.$table.' WHERE SYEAR=\''.$next_syear.'\''.(!$no_school_tables[$table]?' AND SCHOOL_ID=\''.UserSchool().'\'':'')));
                    $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                    echo $tables['course_subjects'].'|'.'(|'.$total_rolled_data.'|)';
                    break;

		case 'courses':
                    DBQuery('DELETE FROM courses WHERE SYEAR=\''.$next_syear.'\' AND SCHOOL_ID=\''.UserSchool().'\'');
                    DBQuery('INSERT INTO courses (SYEAR,SUBJECT_ID,SCHOOL_ID,GRADE_LEVEL,TITLE,SHORT_NAME,ROLLOVER_ID) SELECT SYEAR+1,(SELECT SUBJECT_ID FROM course_subjects s WHERE s.SYEAR=c.SYEAR+1 AND s.ROLLOVER_ID=c.SUBJECT_ID),SCHOOL_ID,GRADE_LEVEL,TITLE,SHORT_NAME,COURSE_ID FROM courses c WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\'');
                    $exists_RET[$table] = DBGet(DBQuery('SELECT count(*) AS COUNT from '.$table.' WHERE SYEAR=\''.$next_syear.'\''.(!$no_school_tables[$table]?' AND SCHOOL_ID=\''.UserSchool().'\'':'')));
                    $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                    echo $tables['courses'].'|'.'(|'.$total_rolled_data.'|)';
                    break;
                   
                    case 'course_periods':


			/*DBQuery("DELETE FROM COURSE_WEIGHTS WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'");*/

			DBQuery('DELETE FROM course_periods WHERE SYEAR=\''.$next_syear.'\' AND SCHOOL_ID=\''.UserSchool().'\'');
			// ROLL course_subjects

			// ROLL COURSE WEIGHTS

			// ROLL courses
			/*DBQuery("INSERT INTO COURSE_WEIGHTS (SYEAR,SCHOOL_ID,COURSE_ID,GPA_MULTIPLIER,COURSE_WEIGHT) SELECT SYEAR+1,SCHOOL_ID,(SELECT COURSE_ID FROM courses c WHERE c.SYEAR=w.SYEAR+1 AND c.ROLLOVER_ID=w.COURSE_ID),GPA_MULTIPLIER,COURSE_WEIGHT FROM COURSE_WEIGHTS w WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");*/
			// ROLL course_periods
			DBQuery('INSERT INTO course_periods (SYEAR,SCHOOL_ID,COURSE_ID,COURSE_WEIGHT,TITLE,SHORT_NAME,PERIOD_ID,MP,MARKING_PERIOD_ID,TEACHER_ID,SECONDARY_TEACHER_ID,ROOM,TOTAL_SEATS,FILLED_SEATS,DOES_ATTENDANCE,GRADE_SCALE_ID,DOES_HONOR_ROLL,DOES_CLASS_RANK,DOES_BREAKOFF,GENDER_RESTRICTION,HOUSE_RESTRICTION,CREDITS,AVAILABILITY,DAYS,HALF_DAY,PARENT_ID,CALENDAR_ID,ROLLOVER_ID) SELECT SYEAR+1,SCHOOL_ID,(SELECT COURSE_ID FROM courses c WHERE c.SYEAR=p.SYEAR+1 AND c.ROLLOVER_ID=p.COURSE_ID),COURSE_WEIGHT,TITLE,SHORT_NAME,(SELECT PERIOD_ID FROM school_periods n WHERE n.SYEAR=p.SYEAR+1 AND n.ROLLOVER_ID=p.PERIOD_ID),MP,'.db_case(array('MP',"'FY'",'(SELECT MARKING_PERIOD_ID FROM school_years n WHERE n.SYEAR=p.SYEAR+1 AND n.ROLLOVER_ID=p.MARKING_PERIOD_ID)',"'SEM'",'(SELECT MARKING_PERIOD_ID FROM school_semesters n WHERE n.SYEAR=p.SYEAR+1 AND n.ROLLOVER_ID=p.MARKING_PERIOD_ID)',"'QTR'",'(SELECT MARKING_PERIOD_ID FROM school_quarters n WHERE n.SYEAR=p.SYEAR+1 AND n.ROLLOVER_ID=p.MARKING_PERIOD_ID)')).',TEACHER_ID,SECONDARY_TEACHER_ID,ROOM,TOTAL_SEATS,0 AS FILLED_SEATS,DOES_ATTENDANCE,(SELECT ID FROM report_card_grade_scales n WHERE n.ROLLOVER_ID=p.GRADE_SCALE_ID),DOES_HONOR_ROLL,DOES_CLASS_RANK,DOES_BREAKOFF,GENDER_RESTRICTION,HOUSE_RESTRICTION,CREDITS,AVAILABILITY,DAYS,HALF_DAY,PARENT_ID,(SELECT CALENDAR_ID FROM attendance_calendars n WHERE n.ROLLOVER_ID=p.CALENDAR_ID),COURSE_PERIOD_ID FROM course_periods p WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\'');
                        
                        //$rowq=DBGet(DBQUERY("SELECT COURSE_PERIOD_ID FROM course_periods  WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'"));
                        //rollid=DBGet(DBQUERY("SELECT ROLLOVER_ID FROM course_periods  WHERE SYEAR='$next_syear' AND SCHOOL_ID='".UserSchool()."'"));
                        
                        DBQuery('UPDATE course_periods SET PARENT_ID=COURSE_PERIOD_ID WHERE SYEAR=\''.$next_syear.'\' AND SCHOOL_ID=\''.UserSchool().'\'');
                              

                        
                        $exists_RET[$table] = DBGet(DBQuery('SELECT count(*) AS COUNT from '.$table.' WHERE SYEAR=\''.$next_syear.'\''.(!$no_school_tables[$table]?' AND SCHOOL_ID=\''.UserSchool().'\'':'')));
                        $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                    echo $tables['course_periods'].'|'.'(|'.$total_rolled_data.'|)';
                        break;

		case 'student_enrollment':
                   
//                        $next_start_date = DBDate();
//			DBQuery("DELETE FROM student_enrollment WHERE SYEAR='$next_syear' AND LAST_SCHOOL='".UserSchool()."'");
//			// ROLL STUDENTS TO NEXT GRADE
//			DBQuery("INSERT INTO student_enrollment (SYEAR,SCHOOL_ID,STUDENT_ID,GRADE_ID,START_DATE,END_DATE,ENROLLMENT_CODE,DROP_CODE,CALENDAR_ID,LAST_SCHOOL) SELECT SYEAR+1,SCHOOL_ID,STUDENT_ID,(SELECT NEXT_GRADE_ID FROM school_gradelevels g WHERE g.ID=e.GRADE_ID),'$next_start_date' AS START_DATE,NULL AS END_DATE,(SELECT ID FROM student_enrollment_codes WHERE SYEAR=$next_syear AND TYPE='Roll') AS ENROLLMENT_CODE,NULL AS DROP_CODE,(SELECT CALENDAR_ID FROM attendance_calendars WHERE ROLLOVER_ID=e.CALENDAR_ID),SCHOOL_ID FROM student_enrollment e WHERE e.SYEAR='".UserSyear()."' AND e.SCHOOL_ID='".UserSchool()."' AND (('".DBDate()."' BETWEEN e.START_DATE AND e.END_DATE OR e.END_DATE IS NULL) AND '".DBDate()."'>=e.START_DATE) AND e.NEXT_SCHOOL='".UserSchool()."'");
//			// ROLL STUDENTS WHO ARE TO BE RETAINED
//			DBQuery("INSERT INTO student_enrollment (SYEAR,SCHOOL_ID,STUDENT_ID,GRADE_ID,START_DATE,END_DATE,ENROLLMENT_CODE,DROP_CODE,CALENDAR_ID,LAST_SCHOOL) SELECT SYEAR+1,SCHOOL_ID,STUDENT_ID,GRADE_ID,'$next_start_date' AS START_DATE,NULL AS END_DATE,(SELECT ID FROM student_enrollment_codes WHERE SYEAR=$next_syear AND TYPE='Roll') AS ENROLLMENT_CODE,NULL AS DROP_CODE,(SELECT CALENDAR_ID FROM attendance_calendars WHERE ROLLOVER_ID=e.CALENDAR_ID),SCHOOL_ID FROM student_enrollment e WHERE e.SYEAR='".UserSyear()."' AND e.SCHOOL_ID='".UserSchool()."' AND (('".DBDate()."' BETWEEN e.START_DATE AND e.END_DATE OR e.END_DATE IS NULL) AND '".DBDate()."'>=e.START_DATE) AND e.NEXT_SCHOOL='0'");
//			// ROLL STUDENTS TO NEXT SCHOOL
//			DBQuery("INSERT INTO student_enrollment (SYEAR,SCHOOL_ID,STUDENT_ID,GRADE_ID,START_DATE,END_DATE,ENROLLMENT_CODE,DROP_CODE,CALENDAR_ID,LAST_SCHOOL) SELECT SYEAR+1,NEXT_SCHOOL,STUDENT_ID,(SELECT g.ID FROM school_gradelevels g WHERE g.SORT_ORDER=1 AND g.SCHOOL_ID=e.NEXT_SCHOOL),'$next_start_date' AS START_DATE,NULL AS END_DATE,(SELECT ID FROM student_enrollment_codes WHERE SYEAR=$next_syear AND TYPE='Roll') AS ENROLLMENT_CODE,NULL AS DROP_CODE,(SELECT CALENDAR_ID FROM attendance_calendars WHERE ROLLOVER_ID=e.CALENDAR_ID),SCHOOL_ID FROM student_enrollment e WHERE e.SYEAR='".UserSyear()."' AND e.SCHOOL_ID='".UserSchool()."' AND (('".DBDate()."' BETWEEN e.START_DATE AND e.END_DATE OR e.END_DATE IS NULL) AND '".DBDate()."'>=e.START_DATE) AND e.NEXT_SCHOOL NOT IN ('".UserSchool()."','0','-1')");
    //new                                                
                                                    //DBQuery("DELETE FROM student_enrollment WHERE SYEAR='$next_syear' AND LAST_SCHOOL='".UserSchool()."'");
                                                    DBQuery('INSERT INTO student_enrollment (SYEAR,NEXT_SCHOOL,SCHOOL_ID,STUDENT_ID,GRADE_ID,START_DATE,END_DATE,ENROLLMENT_CODE,DROP_CODE,CALENDAR_ID,LAST_SCHOOL) SELECT SYEAR+1,NEXT_SCHOOL,SCHOOL_ID,STUDENT_ID,(SELECT NEXT_GRADE_ID FROM school_gradelevels g WHERE g.ID=e.GRADE_ID),\''.$next_start_date.'\' AS START_DATE,NULL AS END_DATE,(SELECT ID FROM student_enrollment_codes WHERE SYEAR=\''.$next_syear.'\' AND TYPE=\'Roll\') AS ENROLLMENT_CODE,NULL AS DROP_CODE,(SELECT CALENDAR_ID FROM attendance_calendars WHERE ROLLOVER_ID=e.CALENDAR_ID),SCHOOL_ID FROM student_enrollment e WHERE e.SYEAR=\''.UserSyear().'\' AND e.SCHOOL_ID=\''.UserSchool().'\' AND ((\''.DBDate().'\' BETWEEN e.START_DATE AND e.END_DATE OR e.END_DATE IS NULL) AND \''.DBDate().'\'>=e.START_DATE) AND e.NEXT_SCHOOL=\''.UserSchool().'\'');
			// ROLL STUDENTS WHO ARE TO BE RETAINED
                                                    DBQuery('INSERT INTO student_enrollment (SYEAR,NEXT_SCHOOL,SCHOOL_ID,STUDENT_ID,GRADE_ID,START_DATE,END_DATE,ENROLLMENT_CODE,DROP_CODE,CALENDAR_ID,LAST_SCHOOL) SELECT SYEAR+1,NEXT_SCHOOL,SCHOOL_ID,STUDENT_ID,GRADE_ID,\''.$next_start_date.'\' AS START_DATE,NULL AS END_DATE,(SELECT ID FROM student_enrollment_codes WHERE SYEAR=\''.$next_syear.'\' AND TYPE=\'Roll\') AS ENROLLMENT_CODE,NULL AS DROP_CODE,(SELECT CALENDAR_ID FROM attendance_calendars WHERE ROLLOVER_ID=e.CALENDAR_ID),SCHOOL_ID FROM student_enrollment e WHERE e.SYEAR=\''.UserSyear().'\' AND e.SCHOOL_ID=\''.UserSchool().'\' AND ((\''.DBDate().'\' BETWEEN e.START_DATE AND e.END_DATE OR e.END_DATE IS NULL) AND \''.DBDate().'\'>=e.START_DATE) AND e.NEXT_SCHOOL=\'0\'');
			// ROLL STUDENTS TO NEXT SCHOOL
                                                    DBQuery('INSERT INTO student_enrollment (SYEAR,SCHOOL_ID,GRADE_ID,STUDENT_ID,START_DATE,END_DATE,NEXT_SCHOOL,ENROLLMENT_CODE,DROP_CODE,CALENDAR_ID,LAST_SCHOOL) SELECT SYEAR+1,NEXT_SCHOOL,(SELECT g.ID FROM school_gradelevels g WHERE g.SORT_ORDER=1 AND g.SCHOOL_ID=e.NEXT_SCHOOL),STUDENT_ID,\''.$next_start_date.'\' AS START_DATE,NULL AS END_DATE,NEXT_SCHOOL,(SELECT ID FROM student_enrollment_codes WHERE SYEAR=\''.$next_syear.'\' AND TYPE=\'Roll\') AS ENROLLMENT_CODE,NULL AS DROP_CODE,NULL,NEXT_SCHOOL FROM student_enrollment e WHERE e.SYEAR=\''.UserSyear().'\' AND e.SCHOOL_ID=\''.UserSchool().'\' AND ((\''.DBDate().'\' BETWEEN e.START_DATE AND e.END_DATE OR e.END_DATE IS NULL) AND \''.DBDate().'\'>=e.START_DATE) AND e.NEXT_SCHOOL NOT IN (\''.UserSchool().'\',\'0\',\'-1\')');
                                                    
                                                    DBQuery('UPDATE student_enrollment SET NEXT_SCHOOL=\'-1\' WHERE GRADE_ID=(SELECT MAX(NEXT_GRADE_ID)FROM school_gradelevels) AND SYEAR=\''.$next_syear.'\' AND LAST_SCHOOL=\''.UserSchool().'\'');
//                                                    DBQuery("UPDATE student_enrollment SET DROP_CODE=(SELECT ID FROM student_enrollment_codes WHERE SYEAR='".UserSyear()."' AND TYPE='Roll') WHERE SYEAR=".  UserSyear()." AND SCHOOL_ID=".  UserSchool());
                                                    
                        $exists_RET[$table] = DBGet(DBQuery('SELECT count(*) AS COUNT from '.$table.' WHERE SYEAR=\''.$next_syear.'\''.(!$no_school_tables[$table]?' AND SCHOOL_ID=\''.UserSchool().'\'':'')));
                        $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                        echo $tables['student_enrollment'].'|'.'(|'.$total_rolled_data.'|)';
                    break;
        
		case 'report_card_grade_scales':
                         
			DBQuery('DELETE FROM report_card_grade_scales WHERE SYEAR=\''.$next_syear.'\' AND SCHOOL_ID=\''.UserSchool().'\'');
			DBQuery('DELETE FROM report_card_grades WHERE SYEAR=\''.$next_syear.'\' AND SCHOOL_ID=\''.UserSchool().'\'');
			// DBQuery("INSERT INTO report_card_grade_scales (ID,SYEAR,SCHOOL_ID,TITLE,COMMENT,SORT_ORDER,ROLLOVER_ID) SELECT ".db_seq_nextval('REPORT_CARD_GRADE_SCALES_SEQ')."+ID,SYEAR+1,SCHOOL_ID,TITLE,COMMENT,SORT_ORDER,ID FROM report_card_grade_scales WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'");
                        DBQuery('INSERT INTO report_card_grade_scales (SYEAR,SCHOOL_ID,TITLE,COMMENT,SORT_ORDER,ROLLOVER_ID,GP_SCALE) SELECT SYEAR+1,SCHOOL_ID,TITLE,COMMENT,SORT_ORDER,ID,GP_SCALE FROM report_card_grade_scales WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\'');
			DBQuery('INSERT INTO report_card_grades (SYEAR,SCHOOL_ID,TITLE,COMMENT,BREAK_OFF,GPA_VALUE,GRADE_SCALE_ID,UNWEIGHTED_GP,SORT_ORDER) SELECT SYEAR+1,SCHOOL_ID,TITLE,COMMENT,BREAK_OFF,GPA_VALUE,(SELECT ID FROM report_card_grade_scales WHERE ROLLOVER_ID=GRADE_SCALE_ID AND SCHOOL_ID=report_card_grades.SCHOOL_ID),UNWEIGHTED_GP,SORT_ORDER FROM report_card_grades WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\'');
                        $exists_RET[$table] = DBGet(DBQuery('SELECT count(*) AS COUNT from '.$table.' WHERE SYEAR=\''.$next_syear.'\''.(!$no_school_tables[$table]?' AND SCHOOL_ID=\''.UserSchool().'\'':'')));
                        $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                        echo $tables['report_card_grade_scales'].'|'.'(|'.$total_rolled_data.'|)';
                    break;
       
		case 'report_card_comments':
                   
			DBQuery('DELETE FROM report_card_comments WHERE SYEAR=\''.$next_syear.'\' AND SCHOOL_ID=\''.UserSchool().'\'');
			DBQuery('INSERT INTO report_card_comments (SYEAR,SCHOOL_ID,TITLE,SORT_ORDER,COURSE_ID) SELECT SYEAR+1,SCHOOL_ID,TITLE,SORT_ORDER,'.db_case(array('COURSE_ID',"''",'NULL','(SELECT COURSE_ID FROM courses WHERE ROLLOVER_ID=rc.COURSE_ID)')).' FROM report_card_comments rc WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\'');
                        $exists_RET[$table] = DBGet(DBQuery('SELECT count(*) AS COUNT from '.$table.' WHERE SYEAR=\''.$next_syear.'\''.(!$no_school_tables[$table]?' AND SCHOOL_ID=\''.UserSchool().'\'':'')));
                        $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                        echo $tables['report_card_comments'].'|'.'(|'.$total_rolled_data.'|)';
                     break;
                                    case 'honor_roll':
		//case 'eligibility_activities':

			DBQuery('DELETE FROM '.$table.' WHERE SYEAR=\''.$next_syear.'\' AND SCHOOL_ID=\''.UserSchool().'\'');
			$table_properties = db_properties($table);
			$columns = '';
			foreach($table_properties as $column=>$values)
			{
				if($column!='ID' && $column!='SYEAR')
					$columns .= ','.$column;
			}
                        DBQuery('INSERT INTO '.$table.' (SYEAR'.$columns.') SELECT SYEAR+1'.$columns.' FROM '.$table.' WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\'');
                        
                        $exists_RET[$table] = DBGet(DBQuery('SELECT count(*) AS COUNT from '.$table.' WHERE SYEAR=\''.$next_syear.'\''.(!$no_school_tables[$table]?' AND SCHOOL_ID=\''.UserSchool().'\'':'')));
                        $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                        echo $tables['honor_roll'].'|'.'(|'.$total_rolled_data.'|)';
                      break;

		case 'attendance_codes':
                         
			DBQuery('DELETE FROM '.$table.' WHERE SYEAR=\''.$next_syear.'\' AND SCHOOL_ID=\''.UserSchool().'\'');
			$table_properties = db_properties($table);
			$columns = '';
			foreach($table_properties as $column=>$values)
			{
				if($column!='ID' && $column!='SYEAR')
					$columns .= ','.$column;
			}
                        DBQuery('INSERT INTO '.$table.' (SYEAR'.$columns.') SELECT SYEAR+1'.$columns.' FROM '.$table.' WHERE SYEAR=\''.UserSyear().'\' AND SCHOOL_ID=\''.UserSchool().'\'');
                        
                        $exists_RET[$table] = DBGet(DBQuery('SELECT count(*) AS COUNT from '.$table.' WHERE SYEAR=\''.$next_syear.'\''.(!$no_school_tables[$table]?' AND SCHOOL_ID=\''.UserSchool().'\'':'')));
                        $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                        echo $tables['attendance_codes'].'|'.'(|'.$total_rolled_data.'|)';
                      break;

		// DOESN'T HAVE A SCHOOL_ID
		case 'student_enrollment_codes':
                        
			//DBQuery("DELETE FROM $table WHERE SYEAR='$next_syear'");
                        $student_enroll_rolled=DBGet(DBQuery('SELECT ID FROM '.$table.' WHERE SYEAR=\''.$next_syear.'\''));
                        $total_student_enroll_rolled=count($student_enroll_rolled);
			$table_properties = db_properties($table);
			$columns = '';
			foreach($table_properties as $column=>$values)
			{
				if($column!='ID' && $column!='SYEAR')
					$columns .= ','.$column;
			}
                        if($total_student_enroll_rolled==0){
			DBQuery('INSERT INTO '.$table.' (SYEAR'.$columns.') SELECT SYEAR+1'.$columns.' FROM '.$table.' WHERE SYEAR=\''.UserSyear().'\'');
                                $roll_RET=DBGet(DBQuery('SELECT ID FROM '.$table.' WHERE TYPE=\'Roll\' AND SYEAR=\''.$next_syear.'\''));
                                if(!$roll_RET){
                                    DBQuery('INSERT INTO '.$table.' (SYEAR'.$columns.') VALUES(\''.$next_syear.'\',\'Rolled Over\',\'ROLL\',\'Roll\')');
                                }
                        }
                        $exists_RET[$table] = DBGet(DBQuery('SELECT count(*) AS COUNT from '.$table.' WHERE SYEAR=\''.$next_syear.'\''.(!$no_school_tables[$table]?' AND SCHOOL_ID=\''.UserSchool().'\'':'')));
                        $total_rolled_data=$exists_RET[$table][1]['COUNT'];
                        echo $tables['student_enrollment_codes'].'|'.'(|'.$total_rolled_data.'|)';
                      break;

                    case 'NONE' :
                        DBQuery('DELETE FROM program_config WHERE (program=\'eligibility\' OR program=\'Currency\') AND syear=\''.$next_syear.'\' AND syear IS NOT NULL AND school_id IS NOT NULL AND school_id=\''.UserSchool().'\'');
                        DBQuery('INSERT INTO program_config(syear,school_id,program,title,value) SELECT syear+1,\''.UserSchool().'\',program,title,value FROM program_config WHERE (program=\'eligibility\' OR program=\'Currency\') AND syear=\''.UserSyear().'\' AND syear IS NOT NULL AND school_id IS NOT NULL AND school_id=\''.UserSchool().'\'');
                        echo '<div style="padding-top:90px; text-align:center;"><span style="font-size:14px; font-weight:bold;">The school year has been rolled.</span><br/><br/><input type=button onclick=document.location.href="index.php?modfunc=logout" value="Please login again" class=btn_large ></div>';
						
                        unset($_SESSION['_REQUEST_vars']['tables']);
                        unset($_SESSION['_REQUEST_vars']['delete_ok']);
                        
}

function roll_calendar($calendar_id,$rollover_id)
{
    $next_y=UserSyear()+1;
    $cal_RET=DBGet(DBQuery('SELECT DATE_FORMAT(MIN(SCHOOL_DATE),\'%c\') AS START_MONTH,DATE_FORMAT(MIN(SCHOOL_DATE),\'%e\') AS START_DAY,DATE_FORMAT(MIN(SCHOOL_DATE),\'%Y\') AS START_YEAR,
                                    DATE_FORMAT(MAX(SCHOOL_DATE),\'%c\') AS END_MONTH,DATE_FORMAT(MAX(SCHOOL_DATE),\'%e\') AS END_DAY,DATE_FORMAT(MAX(SCHOOL_DATE),\'%Y\') AS END_YEAR FROM attendance_calendar WHERE CALENDAR_ID='.$rollover_id.''));
    $min_month=$cal_RET[1]['START_MONTH'];
    $min_day=$cal_RET[1]['START_DAY'];
    $min_year=$cal_RET[1]['START_YEAR']+1;
    $max_month=$cal_RET[1]['END_MONTH'];
    $max_day=$cal_RET[1]['END_DAY'];
    $max_year=$cal_RET[1]['END_YEAR']+1;
    $begin=mktime(0,0,0,$min_month,$min_day,$min_year)+ 43200;
    $end=mktime(0,0,0,$max_month,$max_day,$max_year)+ 43200;
    $day_RET=DBGet(DBQuery('SELECT SCHOOL_DATE FROM attendance_calendar WHERE CALENDAR_ID=\''.$rollover_id.'\' ORDER BY SCHOOL_DATE LIMIT 0, 7'));
    foreach ($day_RET as $day)
    {
        $weekdays[date('w',strtotime($day['SCHOOL_DATE']))]=date('w',strtotime($day['SCHOOL_DATE']));
    }
    $weekday = date('w',$begin);
    for($i=$begin;$i<=$end;$i+=86400)
    {
            if($weekdays[$weekday]!=''){
                if(is_leap_year($next_y)){
                   $previous_year_day=$i-31622400;
                }else{
                     $previous_year_day=$i-31536000;
                }
                $previous_RET=DBGet(DBQuery('SELECT COUNT(SCHOOL_DATE) AS SCHOOL FROM attendance_calendar WHERE SCHOOL_DATE=\''.date('Y-m-d',$previous_year_day).'\' AND CALENDAR_ID=\''.$rollover_id.'\''));
                if($previous_RET[1]['SCHOOL']==0){
                    $prev_weekday=date('w',$previous_year_day);
                    if($weekdays[$prev_weekday]==''){
                        DBQuery('INSERT INTO attendance_calendar (SYEAR,SCHOOL_ID,SCHOOL_DATE,MINUTES,CALENDAR_ID) values(\''.$next_y.'\',\''.UserSchool().'\',\''.date('Y-m-d',$i).'\',\'999\',\''.$calendar_id.'\')');
                    }
                }else{
                    DBQuery('INSERT INTO attendance_calendar (SYEAR,SCHOOL_ID,SCHOOL_DATE,MINUTES,CALENDAR_ID) values(\''.$next_y.'\',\''.UserSchool().'\',\''.date('Y-m-d',$i).'\',\'999\',\''.$calendar_id.'\')');
                }
            }
            $weekday++;
            if($weekday==7)
                    $weekday = 0;
    }
}

function is_leap_year($year)
{
	return ((($year % 4) == 0) && ((($year % 100) != 0) || (($year %400) == 0)));
}
?>
