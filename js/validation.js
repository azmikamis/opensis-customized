
		function formcheck_school_setup_school()
		{
			var sel = document.getElementsByTagName('input');
			for(var i=1; i<sel.length; i++)
			{
				var inp_value = sel[i].value;
				if(inp_value == "")
				{
					var inp_name = sel[i].name;
					if(inp_name == 'values[TITLE]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter school name")+"</font></b>";
						return false;
					}
					else if(inp_name == 'values[ADDRESS]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter address")+"</font></b>";
						return false;
					}
					else if(inp_name == 'values[CITY]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter city")+"</font></b>";
						return false;
					}
					else if(inp_name == 'values[STATE]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter state")+"</font></b>";
						return false;
					}
					else if(inp_name == 'values[ZIPCODE]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter zip/postal code")+"</font></b>";
						return false;
					}
					else if(inp_name == 'values[PHONE]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter phone number")+"</font></b>";
						return false;
					}
					else if(inp_name == 'values[PRINCIPAL]')
					{
						document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter principal")+"</font></b>";
						return false;
					}
					else if(inp_name == 'values[REPORTING_GP_SCALE]')
					{
							document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter base grading scale")+"</font></b>";
						return false;
					}
					
				}
				else if(inp_value != "")
				{
					var val = inp_value;
					var inp_name1 = sel[i].name;
					
					if(inp_name1 == 'values[ZIPCODE]')
					{
					
						var charpos = val.search("[^0-9-\(\)\, ]");								 
						if (charpos >= 0)
						{
							document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter a valid zip/postal code.")+"</font></b>";
							return false;
						}
					}
					if(inp_name1 == 'values[PHONE]')
					{
					
						var charpos = val.search("[^0-9-\(\)\, ]");								 
						if (charpos >= 0)
						{
							document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter a valid phone number.")+"</font></b>";
							return false;
						}
					}
					else if(inp_name1 == 'values[REPORTING_GP_SCALE]')
					{
					
						var charpos = val.search("[^0-9.]");
						if (charpos >= 0)
						{
							document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter decimal value only.")+"</font></b>";
							return false;
						}
					}
					else if(inp_name1 == 'values[E_MAIL]')
					{
						var emailRegxp = /^(.+)@(.+)$/;
						if (emailRegxp.test(val) != true)
						{
							document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please enter a valid email address.")+"</font></b>";
							return false;
						}
					}
					/*else if(inp_name1 == 'values[WWW_ADDRESS]')
					{
						var urlRegxp = /^(http:\/\/www.|https:\/\/www.|ftp:\/\/www.|www.){1}([\w]+)(.[\w]+){1,2}$/;
						if (urlRegxp.test(val) != true)
						{
							document.getElementById('divErr').innerHTML="<b><font color=red>"+unescape("Please Enter a Valid url.")+"</font></b>";
							return false;
						}
					}*/
				}
			}
                        return true;
//			document.school.submit();
		}

	function formcheck_school_setup_portalnotes()
	{
	
		var frmvalidator  = new Validator("F2");
		
		frmvalidator.addValidation("values[new][TITLE]","alphanumeric", "Title allows only alphanumeric value");
		frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for title is 50 characters");
		
		frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort order allows only numeric value");
		frmvalidator.addValidation("values[new][SORT_ORDER]","maxlen=5", "Max length for sort order is 5 digits");
		
		frmvalidator.setAddnlValidationFunction("ValidateDate_Portal_Notes");

	
	}
	
	
	function formcheck_student_advnc_srch()
	{
	
	var day_to=  $('day_to_birthdate');
    var month_to=  $('month_to_birthdate');
	var day_from=  $('day_from_birthdate');
    var month_from=  $('month_from_birthdate');
	if(!day_to.value && !month_to.value && !day_from.value && !month_from.value ){
		return true;
		}
    if(!day_to.value || !month_to.value || !day_from.value || !month_from.value )
		{ 
		strError="Please provide birthday to day, to month, from day, from month.";
	document.getElementById('divErr').innerHTML="<b><font color=red>"+strError+"</font></b>";return false;
		}	
				 				strError="To date must be equal to or greater than from date.";	

								if(month_from.value > month_to.value ){
document.getElementById('divErr').innerHTML="<b><font color=red>"+strError+"</font></b>";                   
                                return false;
    							}else if(month_from.value == month_to.value && day_from.value > day_to.value ){
document.getElementById('divErr').innerHTML="<b><font color=red>"+strError+"</font></b>";
                                return false;
    							}return true;
                                    
	
	}
	
		
	function ValidateDate_Portal_Notes()
	{
		var sm, sd, sy, em, ed, ey, psm, psd, psy, pem, ped, pey ;
		var frm = document.forms["F2"];
		var elem = frm.elements;
		for(var i = 0; i < elem.length; i++)
		{
			if(elem[i].name=="month_values[new][START_DATE]")
			{
				sm=elem[i];
			}
			
			if(elem[i].name=="day_values[new][START_DATE]")
			{
				sd=elem[i];
			}
			
			if(elem[i].name=="year_values[new][START_DATE]")
			{
				sy=elem[i];
			}
			
			if(elem[i].name=="month_values[new][END_DATE]")
			{
				em=elem[i];
			}
			
			if(elem[i].name=="day_values[new][END_DATE]")
			{
				ed=elem[i];
			}
			
			if(elem[i].name=="year_values[new][END_DATE]")
			{
				ey=elem[i];
			}
		}
		
		try
		{
		   if (false==CheckDate(sm, sd, sy, em, ed, ey))

		   {
			   em.focus();
			   return false;
		   }
		}
		catch(err)
		{
		
		}

		try
		{  
		   if (false==isDate(psm, psd, psy))
		   {
			   alert("Please enter the grade posting start date");
			   psm.focus();
			   return false;
		   }
		}   
		catch(err)
		{
		
		}
		
		try
		{  
		   if (true==isDate(pem, ped, pey))
		   {
			   if (false==CheckDate(psm, psd, psy, pem, ped, pey))
			   {
				   pem.focus();
				   return false;
			   }
		   }
		}   
		catch(err)
		{
		
		}
		   
		   return true;
		
	}



	function formcheck_school_setup_marking(){

  	var frmvalidator  = new Validator("marking_period");
  	frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the title");
  	frmvalidator.addValidation("tables[new][TITLE]","maxlen=50", "Max length for title is 50 characters");
	
	frmvalidator.addValidation("tables[new][SHORT_NAME]","req","Please enter the short name");
  	frmvalidator.addValidation("tables[new][SHORT_NAME]","maxlen=10", "Max length for short name is 10 characters");
	
	frmvalidator.addValidation("tables[new][SORT_ORDER]","maxlen=5", "Max length for sort order is 5 digits");
  	frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "Enter only numeric value");
	
	frmvalidator.setAddnlValidationFunction("ValidateDate_Marking_Periods");
}

function ValidateDate_Marking_Periods()
{
var sm, sd, sy, em, ed, ey, psm, psd, psy, pem, ped, pey, grd ;
var frm = document.forms["marking_period"];
var elem = frm.elements;
for(var i = 0; i < elem.length; i++)
{

if(elem[i].name=="month_tables[new][START_DATE]")
{
sm=elem[i];
}
if(elem[i].name=="day_tables[new][START_DATE]")
{
sd=elem[i];
}
if(elem[i].name=="year_tables[new][START_DATE]")
{
sy=elem[i];
}


if(elem[i].name=="month_tables[new][END_DATE]")
{
em=elem[i];
}
if(elem[i].name=="day_tables[new][END_DATE]")
{
ed=elem[i];
}
if(elem[i].name=="year_tables[new][END_DATE]")
{
ey=elem[i];
}


if(elem[i].name=="month_tables[new][POST_START_DATE]")
{
psm=elem[i];
}
if(elem[i].name=="day_tables[new][POST_START_DATE]")
{
psd=elem[i];
}
if(elem[i].name=="year_tables[new][POST_START_DATE]")
{
psy=elem[i];
}


if(elem[i].name=="month_tables[new][POST_END_DATE]")
{
pem=elem[i];
}
if(elem[i].name=="day_tables[new][POST_END_DATE]")
{
ped=elem[i];
}
if(elem[i].name=="year_tables[new][POST_END_DATE]")
{
pey=elem[i];
}

if(elem[i].name=="tables[new][DOES_GRADES]")
{
grd=elem[i];
}

}


try
{
if (false==isDate(sm, sd, sy))
   {
   document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter the start date."+"</font></b>";
   sm.focus();
   return false;
   }
}
catch(err)
{

}
try
{  
   if (false==isDate(em, ed, ey))
   {
  document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter the end date."+"</font></b>";
   em.focus();
   return false;
   }
}   
catch(err)
{

}
try
{
   if (false==CheckDate(sm, sd, sy, em, ed, ey))
   {
   em.focus();
   return false;
   }
}
catch(err)
{

}

if (true==validate_chk(grd))
{

try
{  
   if (false==isDate(psm, psd, psy))
   {
  document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter the grade posting start date."+"</font></b>";
   psm.focus();
   return false;
   }
}   
catch(err)
{

}

try
{  
   if (true==isDate(pem, ped, pey))
   {
   if (false==CheckDate(psm, psd, psy, pem, ped, pey))
   {
   pem.focus();
   return false;
   }
   }

}   
catch(err)
{

}






try
{
   if (false==CheckDateMar(sm, sd, sy, psm, psd, psy))
   {
	   psm.focus();
	   return false;
   }
}
catch(err)
{

}



}




   return true;
}



function formcheck_school_setup_copyschool()
{
	var frmvalidator  = new Validator("prompt_form");
	frmvalidator.addValidation("title","req","Please enter the new school's title");
	frmvalidator.addValidation("title","maxlen=100", "Max length for title is 100 characters");
}



function formcheck_school_setup_calender()
{
	var frmvalidator  = new Validator("prompt_form");
	frmvalidator.addValidation("title","req","Please enter the title");
	frmvalidator.addValidation("title","maxlen=100", "Max length for title is 100");
}



function formcheck_school_setup_periods()
{
  	var frmvalidator  = new Validator("F1");

	var p_name = document.getElementById('values[new][TITLE]');
	var p_name_val = p_name.value;
	
	if(p_name_val != "")
	{
		
		frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for title is 50 characters");
		
		frmvalidator.addValidation("values[new][SHORT_NAME]","maxlen=50", "Max length for short name is 50 characters");
		
		frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort order allows only numeric value");
		frmvalidator.addValidation("values[new][SORT_ORDER]","maxlen=5", "Max length for sort order is 5 digits");
		
		frmvalidator.addValidation("values[new][START_HOUR]","req","Please select start time");
		frmvalidator.addValidation("values[new][START_MINUTE]","req","Please select start time");
		frmvalidator.addValidation("values[new][START_M]","req","Please select start time");
		
		frmvalidator.addValidation("values[new][END_HOUR]","req","Please select end time");
		frmvalidator.addValidation("values[new][END_MINUTE]","req","Please select end time");
		frmvalidator.addValidation("values[new][END_M]","req","Please select end time");
	} 
	
}


function formcheck_school_setup_grade_levels()
{
		var frmvalidator  = new Validator("F1");
		
		
		frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for title is 50 characters");
		
		
		frmvalidator.addValidation("values[new][SHORT_NAME]","maxlen=50", "Max length for short name is 50 characters");
		
		frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort order allows only numeric value");
		frmvalidator.addValidation("values[new][SORT_ORDER]","maxlen=5", "Max length for sort order is 5 digits");
		
}


function formcheck_student_student()
{

  	var frmvalidator  = new Validator("student");
  	frmvalidator.addValidation("students[FIRST_NAME]","req","Please enter the first name");
	frmvalidator.addValidation("students[FIRST_NAME]","maxlen=100", "Max length for school name is 100 characters");
	
	frmvalidator.addValidation("students[LAST_NAME]","req","Please enter the last name");
	frmvalidator.addValidation("students[LAST_NAME]","maxlen=100", "Max length for address is 100 characters");
    frmvalidator.addValidation("students[GENDER]","req","Please select gender");
    frmvalidator.addValidation("students[ETHNICITY]","req","Please select ethnicity");
	
	frmvalidator.addValidation("assign_student_id","num", "Student ID allows only numeric value");



  	frmvalidator.addValidation("values[student_enrollment][new][GRADE_ID]","req","Please select a grade");
	
	frmvalidator.addValidation("students[USERNAME]","maxlen=50", "Max length for Username is 50");
        frmvalidator.addValidation("students[PASSWORD]","password=8", "Password should be minimum 8 characters with atleast one special character and one number");
	frmvalidator.addValidation("students[PASSWORD]","maxlen=20", "Max length for password is 20 characters");
	frmvalidator.addValidation("students[EMAIL]","email");
	frmvalidator.addValidation("students[PHONE]","phone","Invalid phone number");
	
	
  		
	
	
	frmvalidator.addValidation("values[student_enrollment][new][NEXT_SCHOOL]","req","Please select rolling / retention options");
	
	frmvalidator.addValidation("values[address][ADDRESS]","req","Please enter address");
	
	frmvalidator.addValidation("values[address][CITY]","req","Please enter city");
	
	frmvalidator.addValidation("values[address][STATE]","req","Please enter state");
		
	frmvalidator.addValidation("values[address][ZIPCODE]","req","Please enter zipcode");	
	
	frmvalidator.addValidation("values[address][PRIM_STUDENT_RELATION]","req","Relation");

	frmvalidator.addValidation("values[address][PRI_FIRST_NAME]","req","Please enter first name");	
	
	frmvalidator.addValidation("values[address][PRI_LAST_NAME]","req","Please enter last name");	
	
	frmvalidator.addValidation("values[address][SEC_STUDENT_RELATION]","req","Please enter secondary relation");
	
	frmvalidator.addValidation("values[address][SEC_FIRST_NAME]","req","Please enter secondary emergency contact frist name ");	
	
	frmvalidator.addValidation("values[address][SEC_LAST_NAME]","req","Please enter  secondary emergency contact last name");	
	
	frmvalidator.addValidation("values[students_join_people][STUDENT_RELATION]","req","Relation");
	
	
	
	frmvalidator.addValidation("values[people][FIRST_NAME]","req","Please enter first name");		
	
	frmvalidator.addValidation("values[people][LAST_NAME]","req","Please enter last name");		



 	frmvalidator.addValidation("values[address][ADDRESS]","req","Please enter address");
	frmvalidator.addValidation("values[address][PHONE]","ph","Please enter a valid phone number");
	
	frmvalidator.addValidation("values[people][FIRST_NAME]","alphabetic","first name allows only alphabetic value");
	frmvalidator.addValidation("values[people][LAST_NAME]","alpha","last name allows only alphabetic value");
	
	frmvalidator.addValidation("students[PHYSICIAN]","req","Please enter the physician name");
	
	frmvalidator.addValidation("students[PHYSICIAN_PHONE]","ph","Phone number cannot not be alphabetic.");
	
	
 	frmvalidator.addValidation("tables[goal][new][GOAL_TITLE]","req","Please enter goal title");
        frmvalidator.addValidation("tables[goal][new][GOAL_TITLE]","req","Please enter goal title");

	frmvalidator.addValidation("tables[goal][new][GOAL_DESCRIPTION]","req","Please enter goal description");
	
	
 	frmvalidator.addValidation("tables[progress][new][PROGRESS_NAME]","req","Please enter progress period name");
	frmvalidator.addValidation("tables[progress][new][PROFICIENCY]","req","Please select proficiency scale");
	frmvalidator.addValidation("tables[progress][new][PROGRESS_DESCRIPTION]","req","Please enter progress assessment");
	
	
            frmvalidator.setAddnlValidationFunction("ValidateDate_Student");
        

}

function change_pass()
 {	
 	
	var frmvalidator  = new Validator("change_password");
	frmvalidator.addValidation("old","req","Please enter old password");
	frmvalidator.addValidation("new","req","Please enter new password");
	frmvalidator.addValidation("retype","req","Please retype password");
        frmvalidator.addValidation("new","password=8","Password should be minimum 8 characters with atleast one special character and one number");
	
		
 }

function ValidateDate_Student()
{
    

var bm, bd, by ;
var frm = document.forms["student"];
var elem = frm.elements;
for(var i = 0; i < elem.length; i++)
{

if(elem[i].name=="month_students[BIRTHDATE]")
{
bm=elem[i];
}
if(elem[i].name=="day_students[BIRTHDATE]")
{
bd=elem[i];
}
if(elem[i].name=="year_students[BIRTHDATE]")
{
by=elem[i];
}


}

for(var i = 0; i < elem.length; i++)
{

if(elem[i].name=="month_tables[new][START_DATE]")
{
sm=elem[i];
}
if(elem[i].name=="day_tables[new][START_DATE]")
{
sd=elem[i];
}
if(elem[i].name=="year_tables[new][START_DATE]")
{
sy=elem[i];
}


if(elem[i].name=="month_tables[new][END_DATE]")
{
em=elem[i];
}
if(elem[i].name=="day_tables[new][END_DATE]")
{
ed=elem[i];
}
if(elem[i].name=="year_tables[new][END_DATE]")
{
ey=elem[i];
}



}


try
{
if (false==isDate(sm, sd, sy))
   {
   document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter date."+"</font></b>";
   sm.focus();
   return false;
   }
}
catch(err)
{

}
try
{  
   if (false==isDate(em, ed, ey))
   {
  document.getElementById("divErr").innerHTML="<b><font color=red>"+"Please enter end date."+"</font></b>";
   em.focus();
   return false;
   }
}   
catch(err)
{

}
try
{
   if (false==CheckDateGoal(sm, sd, sy, em, ed, ey))
   {
   em.focus();
   return false;
   }
}
catch(err)
{

}
//-----
try
{
   if (false==CheckValidDateGoal(sm, sd, sy, em, ed, ey))
   {
   sm.focus();
   return false;
   }
}
catch(err)
{

}


try
{
if (false==CheckBirthDate(bm, bd, by))
   {
   bm.focus();
   return false;
   }
}
catch(err)
{

}

//return true;
//alert("Press a button!");

for(var z = 0; z < elem.length; z++)
{
if(elem[z].name=="students[FIRST_NAME]")
{
var firstnameobj = elem[z];
var firstname =elem[z].value;
}
if(elem[z].name=="students[MIDDLE_NAME]")
{
var middlenameobj  = elem[z];   
var middlename =elem[z].value;
}
if(elem[z].name=="students[LAST_NAME]")
{
 var lastnameobj =    elem[z];
var lastname =elem[z].value;
}
if(elem[z].name=="values[student_enrollment][new][GRADE_ID]")
{
  var gradeobj=  elem[z];
var grade =elem[z].value;
}
var studentbirthday_year = by.value;
var studentbirthday_month = bm.value;
var studentbirthday_day = bd.value;
}
//alert(studentname);
//return false;
if(firstnameobj && middlenameobj && lastnameobj && gradeobj && by && bm && bd)
{    
ajax_call('check_duplicate_student.php?fn='+firstname+'&mn='+middlename+'&ln='+lastname+'&gd='+grade+'&byear='+studentbirthday_year+'&bmonth='+studentbirthday_month+'&bday='+studentbirthday_day, studentcheck_match, studentcheck_unmatch); 
   return false;
}
else
   return true;  
}

function studentcheck_match(data) {
 	var response = data;
if(response!=0)
{    
 var result = confirm("Duplicate student found. There is already a student with the same information. Do you want to proceed?");
if(result==true)
  {
  document.getElementById("student_isertion").submit();
  return true;
  }
else
  {
  return false;
  }
 }
 else
 {    
   document.getElementById("student_isertion").submit();
   return true;
 }
 }
 
 function studentcheck_unmatch (err) {
 	alert ("Error: " + err);
 }
   




	function formcheck_student_studentField_F2()
	{
		var frmvalidator  = new Validator("F2");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the title");
		frmvalidator.addValidation("values[TITLE]","maxlen=100", "Max length for school name is 100 characters");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order code allows only numeric value");
	}
	
	



	function formcheck_student_studentField_F1()
	{
		var frmvalidator  = new Validator("F1");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the field name");
		
		
		frmvalidator.addValidation("tables[new][TYPE]","req","Please select the data type");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order allows only numeric value");
	}
	
	
                    function formcheck_student_studentField_F1_defalut()
                    {
                           var type=document.getElementById('type');
                           if(type.value=='textarea')
                               document.getElementById('tables[new][DEFAULT_SELECTION]').disabled=true;
                           else
                               document.getElementById('tables[new][DEFAULT_SELECTION]').disabled=false;
                    }

///////////////////////////////////////// Student Field End ////////////////////////////////////////////////////////////

///////////////////////////////////////// Address Field Start //////////////////////////////////////////////////////////



	function formcheck_student_addressField_F2()
	{
		var frmvalidator  = new Validator("F2");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the title");
		frmvalidator.addValidation("values[TITLE]","maxlen=100", "Max length for school name is 100 characters");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order code allows only numeric value");
	}
	
	


	function formcheck_student_addressField_F1()
	{
		var frmvalidator  = new Validator("F1");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the field name");
		
		
		frmvalidator.addValidation("tables[new][TYPE]","req","Please select the Data type");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order allows only numeric value");
	}
	
	



///////////////////////////////////////// Address Field End ////////////////////////////////////////////////////////////

///////////////////////////////////////// Contact Field Start //////////////////////////////////////////////////////////


	
	function formcheck_student_contactField_F2()
	{
		var frmvalidator  = new Validator("F2");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the title");
		frmvalidator.addValidation("values[TITLE]","maxlen=100", "Max length for school name is 100 characters");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order code allows only numeric value");
	}
	
	


	function formcheck_student_contactField_F1()
	{
		var frmvalidator  = new Validator("F1");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the field name");
		
		
		frmvalidator.addValidation("tables[new][TYPE]","req","Please select the data type");
		
		frmvalidator.addValidation("tables[new][SORT_ORDER]","num", "sort order allows only numeric value");
	}
	
	



	function formcheck_user_user(staff_school_chkbox_id){
            
        //alert(par);
  	var frmvalidator  = new Validator("staff");
        //frmvalidator.addValidation("month_values[START_DATE]["+1+"]","req","Please Enter start date");
  	frmvalidator.addValidation("staff[FIRST_NAME]","req","Please enter the first name");
//	frmvalidator.addValidation("staff[FIRST_NAME]","alphabetic", "First name allows only alphabetic value");
  	frmvalidator.addValidation("staff[FIRST_NAME]","maxlen=100", "Max length for first name is 100 characters");
	
		
	frmvalidator.addValidation("staff[LAST_NAME]","req","Please enter the Last Name");
//	frmvalidator.addValidation("staff[LAST_NAME]","alphabetic", "Last name allows only alphabetic value");
  	frmvalidator.addValidation("staff[LAST_NAME]","maxlen=100", "Max length for Address is 100");
        frmvalidator.addValidation("staff[PASSWORD]","password=8", "Password should be minimum 8 characters with one special character and one number");
	frmvalidator.addValidation("staff[PROFILE]","req","Please select the user profile");
	

	frmvalidator.addValidation("staff[PHONE]","ph","Please enter a valid telephone number");
        return school_check(staff_school_chkbox_id);	
        
}
function school_check(staff_school_chkbox_id)
		{
                    //alert(par);
                    //alert(document.getElementById('daySelect11').value);
			chk='n';
                        var err='T';
			if(staff_school_chkbox_id)
			{
                                    for(i=1;i<=staff_school_chkbox_id;i++)
                                    {
                                        
                                            if(document.getElementById('staff_SCHOOLS'+i).checked==true)
                                            {
                                                    chk='y';
                                                    //alert(document.staff.day_values['START_DATE'][i].value);
                                                    //alert(document.staff)
                                                   sd=document.getElementById('daySelect1'+i).value;
                                                   sm=document.getElementById('monthSelect1'+i).value;
                                                   sy=document.getElementById('yearSelect1'+i).value;

                                                   ed=document.getElementById('daySelect2'+i).value;
                                                   em=document.getElementById('monthSelect2'+i).value;
                                                   ey=document.getElementById('yearSelect2'+i).value;
                                                    //ed=
                                                    //alert(sd+sm+sy);
                                                    //alert(ed+em+ey);
//                                                    if(sm=='' || sd=='' || sy=='')
//                                                        {
//                                                         err='F';
//                                                         break;
//                                                        }
//                                                        else
//                                                        {
                                                            var starDate = new Date(sd+"/"+sm+"/"+sy);
                                                            var endDate = new Date(ed+"/"+em+"/"+ey);
                                                             if (starDate > endDate && endDate!='')
                                                            {
                                                                err='S';

                                                            }
//                                                        }
                                            } 
                                         
                                    }
                              
                                
                        }
				if(chk!='y')
				{
					var d = $('divErr');
					var err = "Please assign at least one school to this new user.";
					d.innerHTML="<b><font color=red>"+err+"</font></b>";
					return false;
				}
                                else if(chk=='y')
                                {
//                                    if(err=='F')
//                                    {
//                                      var d = $('divErr');
//                                       var err_date = "Please enter start date to selected school.";
//                                        d.innerHTML="<b><font color=red>"+err_date+"</font></b>";
//                                        return false;
//                                    }
                                    if(err=='S')
                                    {
                                      var d = $('divErr');
                                       var err_stardate = "Start date cannot be greater than end date.";
                                        d.innerHTML="<b><font color=red>"+err_stardate+"</font></b>";
                                        return false;
                                    }
                                    else
                                    {
                                        return true;
                                    }
                                            
                                }
				else
				{
					return true;
				}
			
//                        else
//                            {
//                            var d = $('divErr');
//			    var err = "Please assign at least one school to this new user asd.";
//                            d.innerHTML="<b><font color=red>"+err+"</font></b>";
//                            return false;
//                            }
	    }
/////////////////////////////////////////  Add User End  ////////////////////////////////////////////////////////////

/////////////////////////////////////////  User Fields Start  //////////////////////////////////////////////////////////

	function formcheck_user_userfields_F2()
	{
		var frmvalidator  = new Validator("F2");
		frmvalidator.addValidation("tables[new][TITLE]","req","Please enter the title");
		frmvalidator.addValidation("tables[new][TITLE]","alphabetic", "Title allows only alphabetic value");
		frmvalidator.addValidation("tables[new][TITLE]","maxlen=50", "Max length for title is 100");
	}
	
	function formcheck_user_userfields_F1()
	{
		var frmvalidator1  = new Validator("F1");
		frmvalidator1.addValidation("tables[new][TITLE]","req","Please enter the field Name");
		frmvalidator1.addValidation("tables[new][TITLE]","alnum", "Field name allows only alphanumeric value");
		frmvalidator1.addValidation("tables[new][TITLE]","maxlen=50", "Max length for Field Name is 100");
                //frmvalidator1.addValidation("tables[new][SORT_ORDER]","req","Please enter the sort order");
                frmvalidator1.addValidation("tables[new][SORT_ORDER]","num", "sort order allows only numeric value");
	}

/////////////////////////////////////////  User Fields End  ////////////////////////////////////////////////////////////

/////////////////////////////////////////  User End  ////////////////////////////////////////////////////////////

//////////////////////////////////////// Scheduling start ///////////////////////////////////////////////////////

//////////////////////////////////////// Course start ///////////////////////////////////////////////////////

function formcheck_scheduling_course_F4()
{
	var frmvalidator  = new Validator("F4");
  	frmvalidator.addValidation("tables[course_subjects][new][TITLE]","req","Please enter the title");
  	frmvalidator.addValidation("tables[course_subjects][new][TITLE]","maxlen=100", "Max length for title is 100");
}

function formcheck_scheduling_course_F3()
{
	var frmvalidator  = new Validator("F3");
  	frmvalidator.addValidation("tables[courses][new][TITLE]","req","Please enter the title");
  	frmvalidator.addValidation("tables[courses][new][TITLE]","maxlen=50", "Max length for title is 50");
	
  	frmvalidator.addValidation("tables[courses][new][SHORT_NAME]","req","Please enter the short name");
  	frmvalidator.addValidation("tables[courses][new][SHORT_NAME]","maxlen=10", "Max length for short name is 10");
}

function formcheck_scheduling_course_F2()
{
    var count;
    var check=0;
    for(count=1;count<=7;count++)
    {
       if(document.getElementById("DAYS"+count).checked==true)
         check++;  
    }
    if(check==0)
    {    
     document.getElementById("display_meeting_days_chk").innerHTML='<font color="red">Please select atleast one day</font>';
     document.getElementById("DAYS1").focus();
     return false;
    } 
    else
    {    
	var frmvalidator  = new Validator("F2");
  	frmvalidator.addValidation("tables[course_periods][new][SHORT_NAME]","req","Please enter the short name");
  	frmvalidator.addValidation("tables[course_periods][new][SHORT_NAME]","maxlen=20", "Max length for short name is 20");

  	frmvalidator.addValidation("tables[course_periods][new][TEACHER_ID]","req","Please select the teacher");

  	frmvalidator.addValidation("tables[course_periods][new][ROOM]","req","Please enter the Room");
  	frmvalidator.addValidation("tables[course_periods][new][ROOM]","maxlen=10", "Max length for room is 10");
	
  	frmvalidator.addValidation("tables[course_periods][new][PERIOD_ID]","req","Please select the period");
	frmvalidator.addValidation("tables[course_periods][new][MARKING_PERIOD_ID]","req","Please select marking period");
	frmvalidator.addValidation("tables[course_periods][new][TOTAL_SEATS]","req","Please input total seats");
	frmvalidator.addValidation("tables[course_periods][new][TOTAL_SEATS]","maxlen=10","Max length for seats is 10");
       
        frmvalidator.addValidation("get_status","attendance=0","Cannot take attendance in this period");
     //   alert(document.forms["F2"]["tables[course_periods][new][DAYS][M]"].value);
    }  
}


///////////////////////////////////////// Course End ////////////////////////////////////////////////////////

//////////////////////////////////////// Scheduling End ///////////////////////////////////////////////////////

//////////////////////////////////////// Grade Start ///////////////////////////////////////////////////////


function formcheck_grade_grade()
{
    var frmvalidator  = new Validator("F1");
    frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for title is 50");
    frmvalidator.addValidation("values[new][SHORT_NAME]","maxlen=50", "Max length for short name is 50");
    frmvalidator.addValidation("values[new][SORT_ORDER]","num", "Sort order allows only numeric value");
    frmvalidator.addValidation("values[new][SORT_ORDER]","maxlen=5", "Max length for sort order is 5");

}
function formcheck_honor_roll()
{
    var frmvalidator  = new Validator("F1");
 
    frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for Title is 50");
 
    frmvalidator.addValidation("values[new][VALUE]","maxlen=50", "Max length for Short Name is 50");
}

//////////////////////////////////////// Report Card Comment Start ///////////////////////////////////////////////////////

function formcheck_grade_comment()
{

		var frmvalidator  = new Validator("F1");
		
		frmvalidator.addValidation("values[new][SORT_ORDER]","num", "ID allows only numeric value");
		
		frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for Comment is 50");
	
}

////////////////////////////////////////  Report Card Comment End  ///////////////////////////////////////////////////////


//////////////////////////////////////// Grade End ///////////////////////////////////////////////////////

///////////////////////////////////////// Eligibility Start ////////////////////////////////////////////////////

///////// Activities Start/////////////////////////////

function formcheck_eligibility_activies()
{
	
	var frmvalidator  = new Validator("F1");
	
	frmvalidator.addValidation("values[new][TITLE]","maxlen=50", "Max length for Title is 50");
	
	frmvalidator.setAddnlValidationFunction("ValidateDate_eligibility_activies");

}


	
	function ValidateDate_eligibility_activies()
	{
		var sm, sd, sy, em, ed, ey, psm, psd, psy, pem, ped, pey ;
		var frm = document.forms["F1"];
		var elem = frm.elements;
		for(var i = 0; i < elem.length; i++)
		{
			if(elem[i].name=="month_values[new][START_DATE]")
			{
				sm=elem[i];
			}
			
			if(elem[i].name=="day_values[new][START_DATE]")
			{
				sd=elem[i];
			}
			
			if(elem[i].name=="year_values[new][START_DATE]")
			{
				sy=elem[i];
			}
			
			if(elem[i].name=="month_values[new][END_DATE]")
			{
				em=elem[i];
			}
			
			if(elem[i].name=="day_values[new][END_DATE]")
			{
				ed=elem[i];
			}
			
			if(elem[i].name=="year_values[new][END_DATE]")
			{
				ey=elem[i];
			}
		}
		
		try
		{
		   if (false==CheckDate(sm, sd, sy, em, ed, ey))
		   {
			   em.focus();
			   return false;
		   }
		}
		catch(err)
		{
		
		}

		try
		{  
		   if (false==isDate(psm, psd, psy))
		   {
			   alert("Please enter the grade posting start date");
			   psm.focus();
			   return false;
		   }
		}   
		catch(err)
		{
		
		}
		
		try
		{  
		   if (true==isDate(pem, ped, pey))
		   {
			   if (false==CheckDate(psm, psd, psy, pem, ped, pey))
			   {
				   pem.focus();
				   return false;
			   }
		   }
		}   
		catch(err)
		{
		
		}
		   
		   return true;
		
	}




///////////////////////////////////////// Activies End ////////////////////////////////////////////////////



///////////////////////////////////////// Entry Times Start ////////////////////////////////////////////////

function formcheck_eligibility_entrytimes()
{
  	var frmvalidator  = new Validator("F1");
	frmvalidator.setAddnlValidationFunction("ValidateTime_eligibility_entrytimes");
}

	function ValidateTime_eligibility_entrytimes()
	{
		var sd, sh, sm, sp, ed, eh, em, ep, psm, psd, psy, pem, ped, pey ;
		var frm = document.forms["F1"];
		var elem = frm.elements;
		for(var i = 0; i < elem.length; i++)
		{
			if(elem[i].name=="values[START_DAY]")
			{
				sd=elem[i];
			}
			if(elem[i].name=="values[START_HOUR]")
			{
				sh=elem[i];
			}
			if(elem[i].name=="values[START_MINUTE]")
			{
				sm=elem[i];
			}
			if(elem[i].name=="values[START_M]")
			{
				sp=elem[i];
			}
			if(elem[i].name=="values[END_DAY]")
			{
				ed=elem[i];
			}
			if(elem[i].name=="values[END_HOUR]")
			{
				eh=elem[i];
			}
			if(elem[i].name=="values[END_MINUTE]")
			{
				em=elem[i];
			}
			if(elem[i].name=="values[END_M]")
			{
				ep=elem[i];
			}
		}
		
		try
		{
		   if (false==CheckTime(sd, sh, sm, sp, ed, eh, em, ep))
		   {
			   sh.focus();
			   return false;
		   }
		}
		catch(err)
		{
		}
		try
		{  
		   if (true==isDate(pem, ped, pey))
		   {
			   if (false==CheckDate(psm, psd, psy, pem, ped, pey))
			   {
				   pem.focus();
				   return false;
			   }
		   }
		}   
		catch(err)
		{
		}
		
		   return true;
	}




///////////////////////////////////////// Entry Times End //////////////////////////////////////////////////
       
function formcheck_mass_drop()
{
    if(document.getElementById("course_div").innerHTML=='')
    {    
        alert("Please choose a course period to drop");
        return false;
    }
    else
        return true;
}



function formcheck_attendance_category()
{
        var frmvalidator  = new Validator("F1");
        frmvalidator.addValidation("new_category_title","req","Please enter attendance category Name");
        frmvalidator.addValidation("new_category_title","maxlen=50", "Max length for category name is 50");
			
}

function formcheck_attendance_codes()
{
        if(document.getElementById("title").value!='')
        {
            var frmvalidator  = new Validator("F1");
            frmvalidator.addValidation("values[new][STATE_CODE]","req","Please select state code");
        }
}
//-------------------------------------------------assignments Title Validation Starts---------------------------------------------
function formcheck_assignments()
{

           var frmvalidator  = new Validator("F3");
           frmvalidator.addValidation("tables[new][TITLE]","req","Title cannot be blank");
           frmvalidator.addValidation("month_tables[new][ASSIGNED_DATE]","req","Month cannot be blank");
           frmvalidator.addValidation("day_tables[new][ASSIGNED_DATE]","req","Day cannot be blank");
           frmvalidator.addValidation("year_tables[new][ASSIGNED_DATE]","req","Year cannot be blank");
            frmvalidator.addValidation("month_tables[new][DUE_DATE]","req","Month cannot be blank");
           frmvalidator.addValidation("day_tables[new][DUE_DATE]","req","Day cannot be blank");
           frmvalidator.addValidation("year_tables[new][DUE_DATE]","req","Year cannot be blank");
}
//-------------------------------------------------assignments Title Validation Ends---------------------------------------------


function passwordStrength(password)

{
    document.getElementById("passwordStrength").style.display = "none";

        var desc = new Array();

        desc[0] = "Very Weak";

        desc[1] = "Weak";

        desc[2] = "Good";

        desc[3] = "Strong";

        desc[4] = "Strongest";


        //if password bigger than 7 give 1 point

        if (password.length > 0) 
        {   
            document.getElementById("passwordStrength").style.display = "block" ;
            document.getElementById("passwordStrength").style.backgroundColor = "#cccccc" ;
            document.getElementById("passwordStrength").innerHTML = desc[0] ;
            
            
        }


        //if password has at least one number give 1 point

        if (password.match(/\d+/) && password.length > 5) 
        {
            document.getElementById("passwordStrength").style.display = "block" ;
            document.getElementById("passwordStrength").style.backgroundColor = "#ff0000" ;
            document.getElementById("passwordStrength").innerHTML = desc[1] ;
        }



        //if password has at least one special caracther give 1 point

        if (password.match(/\d+/) && password.length > 7 && password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) )
        {
            document.getElementById("passwordStrength").style.display = "block" ;
            document.getElementById("passwordStrength").style.backgroundColor = "#ff5f5f" ;
            document.getElementById("passwordStrength").innerHTML = desc[2] ;
        }

        
        //if password has both lower and uppercase characters give 1 point      

        if (password.match(/\d+/) && password.length > 10 && password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) && ( password.match(/[A-Z]/) ) ) 
        {
            document.getElementById("passwordStrength").style.display = "block" ;
            document.getElementById("passwordStrength").style.backgroundColor = "#56e500" ;
            document.getElementById("passwordStrength").innerHTML = desc[3] ;
        }


        //if password bigger than 12 give another 1 point

        if (password.match(/\d+/) &&  password.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) && ( password.match(/[a-z]/) ) && ( password.match(/[A-Z]/) ) && password.length > 12)
        {
            document.getElementById("passwordStrength").style.display = "block" ;
            document.getElementById("passwordStrength").style.backgroundColor = "#4dcd00" ;
            document.getElementById("passwordStrength").innerHTML = desc[4] ;
        }

}
function passwordMatch()
{
    document.getElementById("passwordMatch").style.display = "none" ;
    var new_pass = document.getElementById("new_pass").value;
    var vpass = document.getElementById("ver_pass").value;
    if(new_pass || vpass)
    {
        if(new_pass==vpass)
        {
            document.getElementById("passwordMatch").style.display = "block" ;
            document.getElementById("passwordMatch").style.backgroundColor = "#4dcd00" ;
            document.getElementById("passwordMatch").innerHTML = "Password Match" ;
        }
        if(new_pass!=vpass)
        {
            document.getElementById("passwordMatch").style.display = "block" ;
            document.getElementById("passwordMatch").style.backgroundColor = "#ff0000" ;
            document.getElementById("passwordMatch").innerHTML = "Password MisMatch" ;    
        }
    }
    
}
function pass_check()
{
    if(document.getElementById("new_pass").value==document.getElementById("ver_pass").value)
    {
        var new_pass = document.getElementById("new_pass").value;
//      var ver_pass = document.getElementById("ver_pass").value;
        if(new_pass.length < 7 || (new_pass.length > 7 && !new_pass.match((/\d+/))) || (new_pass.length > 7 && !new_pass.match((/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/))))
        {
            document.getElementById('divErr').innerHTML="<b><font color=red>Password should be minimum 8 characters with atleast one number and one special character</font></b>";
            return false;
        }
        
        return true;
    }
    else
    {
        document.getElementById('divErr').innerHTML="<b><font color=red>New Password MisMatch</font></b>";
        return false;
    }
}

function reenroll()
{
    if(document.getElementById("monthSelect1").value=='' || document.getElementById("daySelect1").value=='' || document.getElementById("yearSelect1").value=='')
    {    
        document.getElementById('divErr').innerHTML="<b><font color=red>Please Enter a Proper Date</font></b>";
        return false;
    }
    if(document.getElementById("grade_id").value=='')
    {    
        document.getElementById('divErr').innerHTML="<b><font color=red>Please Select a Grade Level</font></b>";
        return false;
    }
    if(document.getElementById("en_code").value=='')
    {    
        document.getElementById('divErr').innerHTML="<b><font color=red>Please Select an Enrollment Code</font></b>";
        return false;
    }
    
    else
        return true;
}
