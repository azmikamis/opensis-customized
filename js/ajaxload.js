function loadajax(frmname)
{
  this.formobj=document.forms[frmname];
	if(!this.formobj)
	{
	  alert("BUG: couldnot get Form object "+frmname);
		return;
	}
	if(this.formobj.onsubmit)
	{
	 this.formobj.old_onsubmit = this.formobj.onsubmit;
	 this.formobj.onsubmit=null;
	}
	else
	{
	 this.formobj.old_onsubmit = null;
	}
	this.formobj.onsubmit=ajax_handler;
	
}

function ajax_handler()
{
	if(ajaxform(this, this.action) =='failed')
	return true;
	
	return false;
}

function formload_ajax(frm){
		var frmloadajax  = new loadajax(frm);
}



var hand = function(str){
	window.document.getElementById('response_span').innerHTML=str;
}
/*function validateUsername(user){
	var strDomain='';
	window.document.getElementById('response_span').innerHTML="Validating username...";
	var valajax = new ValAjax();
	valajax.doGet(strDomain+'validator.php?action=validateUsername&username='+user,hand,'text');
}*/


function ajax_call (url, callback_function, error_function) {
	var xmlHttp = null;
	try {
		// for standard browsers
		xmlHttp = new XMLHttpRequest ();
	} catch (e) {
		// for internet explorer
		try {
			xmlHttp = new ActiveXObject ("Msxml2.XMLHTTP");
	    } catch (e) {
			xmlHttp = new ActiveXObject ("Microsoft.XMLHTTP");
	    }
	}
	xmlHttp.onreadystatechange = function () {
		if (xmlHttp.readyState == 4)
			try {
				if (xmlHttp.status == 200) {
					
					callback_function (xmlHttp.responseText);
				}
			} catch (e) {
				
				error_function (e.description);
			}
	 }
	
	 xmlHttp.open ("GET", url);
	 xmlHttp.send (null);
 }
 // --------------------------------------------------- USER ----------------------------------------------------------------------------------- //
 
 function usercheck_init(i) {
	var obj = document.getElementById('ajax_output');
	obj.innerHTML = ''; 
	
	if (i.value.length < 1) return;
	
 	var err = new Array ();
	if (i.value.match (/[^A-Za-z0-9_]/)) err[err.length] = 'Username can only contain letters, numbers and underscores';
 	if (i.value.length < 3) err[err.length] = 'Username too short';
 	if (err != '') {
	 	obj.style.color = '#ff0000';
	 	obj.innerHTML = err.join ('<br />');
	 	return;
 	}
 	
	var pqr = i.value;
	
	
	ajax_call('validator.php?u='+i.value+'user', usercheck_callback, usercheck_error); 
 }
 
  function usercheck_callback (data) {
 	var response = (data == '1');

 	var obj = document.getElementById('ajax_output');
 	obj.style.color = (response) ? '#008800' : '#ff0000';
 	obj.innerHTML = (response == '1') ? 'Username OK' : 'Username already taken';
 }
 
  function usercheck_error (err) {
 	alert ("Error: " + err);
 }

// ------------------------------------------------------ USER ---------------------------------------------------------------------------------- //

// ------------------------------------------------------ Student ------------------------------------------------------------------------------ //

 function usercheck_init_student(i) {
	var obj = document.getElementById('ajax_output_st');
	obj.innerHTML = ''; 
	
	if (i.value.length < 1) return;
	
 	var err = new Array ();
	if (i.value.match (/[^A-Za-z0-9_]/)) err[err.length] = 'Username can only contain letters, numbers and underscores';
 	if (i.value.length < 3) err[err.length] = 'Username too short';
 	if (err != '') {
	 	obj.style.color = '#ff0000';
	 	obj.innerHTML = err.join ('<br />');
	 	return;
 	}
	ajax_call('validator.php?u='+i.value+'stud', usercheck_callback_student, usercheck_error_student); 
 }

 function usercheck_callback_student (data) {
 	var response = (data == '1');

 	var obj = document.getElementById('ajax_output_st');
 	obj.style.color = (response) ? '#008800' : '#ff0000';
 	obj.innerHTML = (response == '1') ? 'Username OK' : 'Username already taken';
 }

 function usercheck_error_student (err) {
 	alert ("Error: " + err);
 }

// ------------------------------------------------------ Student ------------------------------------------------------------------------------ //

// ------------------------------------------------------ Student ID------------------------------------------------------------------------------ //

 function usercheck_student_id(i) {
	var obj = document.getElementById('ajax_output_stid');
	obj.innerHTML = ''; 
	
	if (i.value.length < 1) return;
	
 	var err = new Array ();
	if (i.value.match (/[^0-9_]/)) err[err.length] = 'Student ID can only contain numbers';
 	
 	if (err != '') {
	 	obj.style.color = '#ff0000';
	 	obj.innerHTML = err.join ('<br />');
	 	return;
 	}
 	ajax_call ('validator_int.php?u='+i.value+'stid', usercheck_callback_student_id, usercheck_error_student_id); 
 }

 function usercheck_callback_student_id (data) {
 	var response = (data == '1');

 	var obj = document.getElementById('ajax_output_stid');
 	obj.style.color = (response) ? '#008800' : '#ff0000';
 	obj.innerHTML = (response == '1') ? 'Student ID OK' : 'Student ID already taken';
 }

 function usercheck_error_student_id (err) {
 	alert ("Error: " + err);
 }

// ------------------------------------------------------ Student ID------------------------------------------------------------------------------ //


//-----------------Take attn depends on period------------------------------------------------------

function formcheck_periods_attendance_F2(attendance)
{
           if(document.getElementById('cp_period'))
           {
                period_id = document.getElementById('cp_period').value;
           }
           else
           {
                period_id = 0;
           }
    var err = new Array ();
    if(attendance.checked)
        {
           var obj = document.getElementById('ajax_output');
           var period_id;
           
           var cp_id=document.getElementById('cp_id').value;
           obj.innerHTML = '';

           if (attendance.value.length < 1) return;

                if (period_id.length ==0)
                    {
                    err[err.length] = 'Select Period';
                    document.getElementById('get_status').value = 'false';
                    }
                    else
                        err[err.length] ='';
                if (err != '') {
                        obj.style.color = '#ff0000';
                        obj.innerHTML = err.join ('<br />');
                        return;
                }
                var pqr = attendance.value;
                ajax_call('validator_attendance.php?u='+attendance.value+'&p_id='+period_id+'&cp_id='+cp_id, attendance_callback, attendance_error);
        }
        else
            {
                if (period_id.length ==0)
                    {
                        err[err.length] = 'Select Period';
                        document.getElementById('get_status').value = 'false';
                    }
                    else
                        err[err.length] ='';
                if (err != '') {
                        obj.style.color = '#ff0000';
                        obj.innerHTML = err.join ('<br />');
                        return;
                }
                if(err =='')
                {
           document.getElementById('ajax_output').innerHTML = '';
           document.getElementById('get_status').value ='';
            }
}
}

  function attendance_callback (data)
  {
       var response = (data == '1');
 	var obj = document.getElementById('ajax_output');
 	obj.style.color = (response) ? '#008800' : '#ff0000';
        obj.innerHTML = (response == '1' ? '' : 'Turn on attendance for the<br>period in School Setup &gt;&gt; Periods');
       if(response==false)
         document.getElementById('get_status').value = response;
       else
        document.getElementById('get_status').value ='';
           }

  function attendance_error (err) {
 	alert ("Error: " + err);
 }

function formcheck_periods_F2()
{
    if(!document.getElementById('cp_does_attendance') || (!document.getElementById('cp_does_attendance').checked))
    {
       var obj = document.getElementById('ajax_output');
       var period_id=document.getElementById('cp_period').value;
       var cp_id=document.getElementById('cp_id').value;
       var err = new Array ();
       if (period_id.length ==0)
       {
            err[err.length] = 'Select Period';
            document.getElementById('get_status').value = 'false';
       }
       else
           err[err.length]='';
       if(err =='')
           {
           document.getElementById('ajax_output').innerHTML = '';
           document.getElementById('get_status').value ='';
           }
       if (err != '')
       {
            obj.style.color = '#ff0000';
            obj.innerHTML = err.join ('<br />');
            return;
       }
       if(!document.getElementById('cp_does_attendance'))
       ajax_call('validator_attendance.php?u=N&p_id='+period_id+'&cp_id='+cp_id, attendance_callback, attendance_error);
    }
    else
    {
      if(document.getElementById('cp_does_attendance').checked)
      {
        formcheck_periods_attendance_F2(document.getElementById('cp_does_attendance'));
      }
      else
        document.getElementById('get_status').value ='';
}
}

//----------------------------------------------------------------------


function ajax_call_modified(url,callback_function,error_function)
{
    var xmlHttp = null;
    try {
        xmlHttp = new XMLHttpRequest ();
    } catch (e) {
    try {
        xmlHttp = new ActiveXObject ("Msxml2.XMLHTTP");
    } catch (e) {
        xmlHttp = new ActiveXObject ("Microsoft.XMLHTTP");
    }
    }
    xmlHttp.onreadystatechange = function () {
        if (xmlHttp.readyState == 1){
            try {
                document.getElementById('calculating').style.display="block";
            } catch (e) {
                error_function (e.description);
            }
        }
        if (xmlHttp.readyState == 4){
            try {
                if (xmlHttp.status == 200) {
                    callback_function(xmlHttp.responseText);
                }
            } catch (e) {
                error_function (e.description);
            }
        }
    }
    xmlHttp.open ("GET", url);
    xmlHttp.send (null);
}

//=========================================Missing Attendance===========================
function mi_callback(mi_data)
{
                    document.getElementById("resp").innerHTML=mi_data;
                    document.getElementById("calculating").style.display="none";
                    if(mi_data.search('NEW_MI_YES')!=-1)
                    {
                        document.getElementById("attn_alert").style.display="block"
                    }
}
function calculate_missing_atten()
{
     var url = "calculate_missing_attendance.php";
     ajax_call_modified(url,mi_callback,missing_attn_error);
}

function missing_attn_error(err)
{
    alert ("Error: " + err);
}
//-------------------------------------Missing Attendance end ------------------------------------------------

function rollover_callback(roll_data)
{
    //alert(roll_data);
    var total_data;
    total_data=roll_data.split('|');
	var value=total_data[2];
	if(value==0)
	{
		var rollover_class='rollover_no';
	}
	else
	{
		var rollover_class='rollover_yes';		
	}
	
	if(total_data[0]=='Users'){
            document.getElementById("staff").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("staff").setAttribute("class", rollover_class);
            document.getElementById("staff").setAttribute("className", rollover_class);
            if(document.getElementById("chk_school_periods").value=='Y')
            {
            ajax_rollover('school_periods');
        }
            else
            {
                ajax_rollover('school_years');
            }
        }
        else if(total_data[0]=='School Periods')
        {
            document.getElementById("school_periods").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("school_periods").setAttribute("class", rollover_class);
            document.getElementById("school_periods").setAttribute("className", rollover_class);
            ajax_rollover('school_years');
        }

       else if(total_data[0]=='Marking Periods')
       {
            document.getElementById("school_years").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("school_years").setAttribute("class", rollover_class);
            document.getElementById("school_years").setAttribute("className", rollover_class);
            if(document.getElementById("chk_attendance_calendars").value=='Y')
            {
            ajax_rollover('attendance_calendars');
       }
            else if(document.getElementById("chk_report_card_grade_scales").value=='Y')
            {
                ajax_rollover('report_card_grade_scales');
            }
            else if(document.getElementById("chk_course_subjects").value=='Y')
            {
                ajax_rollover('course_subjects');
            }
            else if(document.getElementById("chk_courses").value=='Y')
            {
                ajax_rollover('courses');
            }
           else if(document.getElementById("chk_course_periods").value=='Y')
            {
                ajax_rollover('course_periods');
            }
            else
            {
                ajax_rollover('student_enrollment_codes');
            }
            
       }
       else if(total_data[0]=='Calendars')
       {
            document.getElementById("attendance_calendars").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("attendance_calendars").setAttribute("class", rollover_class);
            document.getElementById("attendance_calendars").setAttribute("className", rollover_class);
            ajax_rollover('report_card_grade_scales');
       }
       else if(total_data[0]=='Report Card Grade Codes')
       {
            document.getElementById("report_card_grade_scales").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("report_card_grade_scales").setAttribute("class", rollover_class);
            document.getElementById("report_card_grade_scales").setAttribute("className", rollover_class);
            if(document.getElementById('chk_course_subjects').value=='Y')
                ajax_rollover('course_subjects');
            else if(document.getElementById('chk_courses').value=='Y')
            ajax_rollover('courses');
            else if(document.getElementById('chk_course_periods').value=='Y')
                ajax_rollover('course_periods');
            else
                ajax_rollover('student_enrollment_codes');
       }
       else if(total_data[0]=='Subjects')
       {
            document.getElementById("course_subjects").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("course_subjects").setAttribute("class", rollover_class);
            document.getElementById("course_subjects").setAttribute("className", rollover_class);
            if(document.getElementById('chk_courses').value=='Y')
                ajax_rollover('courses');
            else if(document.getElementById('chk_course_periods').value=='Y')
                ajax_rollover('course_periods');
            else
                ajax_rollover('student_enrollment_codes');
       }
       else if(total_data[0]=='Courses')
       {
            document.getElementById("courses").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("courses").setAttribute("class", rollover_class);
            document.getElementById("courses").setAttribute("className", rollover_class);
            if(document.getElementById('chk_course_periods').value=='Y')
                ajax_rollover('course_periods');
            else
            ajax_rollover('student_enrollment_codes');
       }
        else if(total_data[0]=='Course Periods')
       {
            document.getElementById("course_periods").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("course_periods").setAttribute("class", rollover_class);
            document.getElementById("course_periods").setAttribute("className", rollover_class);
            ajax_rollover('student_enrollment_codes');
       }
        else if(total_data[0]=='Student Enrollment Codes')
       {
            document.getElementById("student_enrollment_codes").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("student_enrollment_codes").setAttribute("class", rollover_class);
            document.getElementById("student_enrollment_codes").setAttribute("className", rollover_class);
            ajax_rollover('student_enrollment');
       }
       else if(total_data[0]=='Students')
       {
            document.getElementById("student_enrollment").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("student_enrollment").setAttribute("class", rollover_class);
            document.getElementById("student_enrollment").setAttribute("className", rollover_class);
            if(document.getElementById("chk_honor_roll").value=='Y')
            {
            ajax_rollover('honor_roll');
       }
            else if(document.getElementById("chk_attendance_codes").value=='Y')
            {
                ajax_rollover('attendance_codes');
            }
            else if(document.getElementById("chk_report_card_comments").value=='Y')
            {
                ajax_rollover('report_card_comments');
            }
            else
            {
                ajax_rollover('NONE');
            }
       }
       else if(total_data[0]=='Honor Roll Setup')
       {
            document.getElementById("honor_roll").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("honor_roll").setAttribute("class", rollover_class);
            document.getElementById("honor_roll").setAttribute("className", rollover_class);
            if(document.getElementById("chk_attendance_codes").value=='Y')
            {
            ajax_rollover('attendance_codes');
       }
            else if(document.getElementById("chk_report_card_comments").value=='Y')
            {
                ajax_rollover('report_card_comments');
            }
            else
            {
                ajax_rollover('NONE');
            }
            
       }
       else if(total_data[0]=='Attendance Codes')
       {
            document.getElementById("attendance_codes").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("attendance_codes").setAttribute("class", rollover_class);
            document.getElementById("attendance_codes").setAttribute("className", rollover_class);
            
            if(document.getElementById("chk_report_card_comments").value=='Y')
            {
            ajax_rollover('report_card_comments');
       }
            else
            {
                ajax_rollover('NONE');
            }
       }

       else if(total_data[0]=='Report Card Comment Codes')
       {
            document.getElementById("report_card_comments").innerHTML=total_data[0]+" "+total_data[1]+" "+total_data[2]+" "+total_data[3];
            document.getElementById("report_card_comments").setAttribute("class", rollover_class);
            document.getElementById("report_card_comments").setAttribute("className", rollover_class);
            ajax_rollover('NONE');
       }
        else 
        {
            document.getElementById("response").innerHTML=roll_data;
            document.getElementById("calculating").style.display="none";
        }
}

function rollover_error(err)
{
    alert ("Error: " + err);
}

function ajax_rollover(roll_table)
{   
     var url = 'Rollover_shadow.php?table_name='+roll_table;
     ajax_call_modified(url,rollover_callback,rollover_error);
}
function formcheck_rollover()
{
    var start_month_len=document.getElementById("monthSelect1").value;
    var start_day_len=document.getElementById("daySelect1").value;
    var start_year_len=document.getElementById("yearSelect1").value;
    if(start_month_len=="" || start_day_len=="" || start_year_len=="")
    {     
        document.getElementById("start_date").innerHTML="Please Enter Start Date ";
        return false;
    }
    if(start_month_len!="" && start_day_len!="" && start_year_len!="")
     {
        return true;
     }
    
}

function validate_rollover(thisFrm,thisElement)
{
    if(thisElement.name=='courses')
    {
        if(thisElement.checked==true)
        {
            thisFrm.course_subjects.checked=true;
        }
    }
    
    if(thisElement.name=='course_periods')
    {
        if(thisElement.checked==true)
        {
            thisFrm.school_periods.checked=true;
            thisFrm.attendance_calendars.checked=true;
            thisFrm.course_subjects.checked=true;
            thisFrm.courses.checked=true;
            
        }
        if(thisFrm.report_card_comments.checked==true && thisElement.checked==false)
        {
            thisElement.checked=true;
        }
    }
    if(thisElement.name=='report_card_comments' && thisElement.checked==true)
    {
        thisFrm.school_periods.checked=true;
        thisFrm.attendance_calendars.checked=true;
        thisFrm.course_subjects.checked=true;
        thisFrm.courses.checked=true;
        thisFrm.course_periods.checked=true;
    }
    if(thisFrm.course_periods.checked==true && thisElement.checked==false && (thisElement.name=='school_periods' || thisElement.name=='attendance_calendars' || thisElement.name=='course_subjects'|| thisElement.name=='courses'))
    {
        thisElement.checked=true;
    }
    if(thisFrm.courses.checked==true && thisElement.checked==false && thisElement.name=='course_subjects')
    {
        thisElement.checked=true;
    }
}

function validate_password(password,stid)
{   
 
     var url = "validator.php?validate=pass&password=" + password +"&stfid="+ stid;
     ajax_call(url,pass_val_callback,pass_val_error);
}

function pass_val_callback(data)
{
 	var obj = document.getElementById('passwordStrength');
        
            if(data!='1')
            {
 	obj.style.color ='#ff0000';
                  obj.style.backgroundColor =  "#cccccc" ;
 	obj.innerHTML = 'Invalid password';
            }
         
}
function pass_val_error(err)
{
    alert ("Error: " + err);
}



//-------------------------------------------------- historical grade school name pickup --------------------------------------//
function pick_schoolname (data) {
 	
        document.getElementById('SCHOOL_NAME').value = data;
 }

// ------------------------------------------------------ Student ------------------------------------------------------------------------------ //

// ------------------------------------------------------ Student ID------------------------------------------------------------------------------ //

 function GetSchool(i) {
	var obj = document.getElementById('SCHOOL_NAME');
	obj.innerHTML = ''; 
	
 	ajax_call ('GetSchool.php?u='+i, pick_schoolname); 
 }
