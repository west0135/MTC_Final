//globals
var g_const;
var g_json;
var g_courts_json;
var g_json_open_hours;
var g_last_end_time = 0;

var g_userid;
var g_ukey;
var g_permissions;

//TODO set using configuration
var g_max_reservations_per_member;// = 4;
var g_max_rereservations_per_time_period;// = 2;
var g_can_delete_res;

var const_cell_height = 80;
var const_line_height = 2;
var const_FREE = "FREE";
var const_MEMBER_RESERVATION = "MEMBER_RESERVATION";
var const_MY_RESERVATION = "MY_RESERVATION";

var g_date;
var g_day;

var const_START_TIME;
var const_END_TIME;
var g_current_time_dec;

var g_is_advanced_settings = false;

var MIN_MSG_HEIGHT = .3;

$(document).ready(function ()
{
	$.support.cors = true;
	
	$( "#login_dialog" ).dialog(
	{
		autoOpen: false,
		buttons: [
		{
		  text: "Ok",
		  icons: {
			primary: "ui-icon-heart"
		  },
		  type: "submit",
          form: "login_form", // <-- Make the association
		  click: function(ev)
		  {
			ev.preventDefault();
			$( this ).dialog( "close" );
			var myEmail = $("#my_email").val();
			var myPassword = $("#pswd").val(); 
			var data = {func: 'loginUser',email: myEmail,password: myPassword};
			loginServer(LOGIN_SERVER, data, function(json)
			{
				g_userid = json.userid;
				g_ukey = json.ukey;
				g_permissions = json.permissions;
				
				drawTimeColums();
				
				localStorage.setItem("g_userid", g_userid);
				localStorage.setItem("g_ukey", g_ukey);
				localStorage.setItem("g_permissions", g_permissions);
				
				$("#cheapLogOut").attr("data-status", "logged_in");
				$("#cheapLogOut").html("Log Out");
	
			}, login_error);
		  }
		  // Uncommenting the following line would hide the text,
		  // resulting in the label being used as a tooltip
		  //showText: false
		}
	  ]
	});

	// Retrieve
	g_userid = localStorage.getItem("g_userid");
	g_ukey = localStorage.getItem("g_ukey");
	g_permissions = localStorage.getItem("g_permissions");
	
	makeReservationSettings();
													
	//Get the reservation settings from local storage or from the form if not set
	g_can_delete_res = localStorage.getItem("g_can_delete_res") || $("#can_delete_res").prop('checked');
	$('#can_delete_res').prop('checked', g_can_delete_res);
	
	g_date = getTodaysDatePlus(0);
	g_day = getTodaysDayPlus(0);
	
	//alert(g_date);
	function setGlobals(json)
	{
		//alert(JSON.stringify(json).substring(0, 2500)); //Limit the string size
		var field = json.field[0];
		$("#dateString").html(getDayByDateString(g_date) + " " + g_date + " open: " +
			formatMinutes(field.start_time) + " closed: " + formatMinutes(field.end_time));

		const_START_TIME = minutesToDecimal(field.start_time);
		const_END_TIME = minutesToDecimal(field.end_time);
	}
	
	//Get the open hours
	var data = {method:"MtcOpenDatesHelper.getOpenHours", date:g_date, day: g_day};
	postData(JSON_API_URL, data, function(json)
	{
		g_json_open_hours = json;
		setGlobals(json);
		//Get Current time in half hour increments 
		//Plus n hours
		g_current_time_dec = getTimeByHalfs(1);
		//Draw the containers representing courts
		var data = {method:"MtcCourt.getList"};
		postData(JSON_API_URL, data, function(json)
		{
			g_courts_json = json;
			$("#lists_container").html("");
			var html = "";
			for(var i=0; i < json.fields.length; i++)
			{
				var field = json.fields[i];
				html += '<div id="c-' + field.court_id + '" class="container"></div>';
			}
			$("#lists_container").html(html);
			//Draw the time columns
			drawTimeColums();	
		}, error);
	},error);

	//////////////////////////////// initialize after $("#login_dialog") /////////////////
	if(!g_ukey)
	{
		$("#cheapLogOut").attr("data-status", "logged_out");
		$("#cheapLogOut").html("Log In");
		show("#login_dialog");
		$("#login_dialog").dialog( "open" );
	}
	else
	{
		$("#cheapLogOut").attr("data-status", "logged_in");
		$("#cheapLogOut").html("Log Out");
	}
	
	//Log Out
	$("#cheapLogOut").click(function(e)
	{
		e.preventDefault();
		var status = e.target.dataset.status;
		if("logged_out" == status)
		{
			$("#login_dialog").dialog( "open" );
		}
		else
		{
			var data = {func: 'logoutUser',ukey: g_ukey, userid: g_userid};
			loginServer(LOGIN_SERVER, data, function(json)
			{
				if(json.status == 1)
				{
					localStorage.removeItem("g_userid");
					localStorage.removeItem("g_ukey");
					localStorage.removeItem("g_permissions");
					g_userid = null;
					g_ukey = null;
					g_permissions = null;
					//openMessageBox("Logged Out");
					$("#cheapLogOut").attr("data-status", "logged_out");
					$("#cheapLogOut").html("Log In");
					$("#pswd").val("");
				}
				else
				{
					openMessageBox("ERROR Logging Out");
				}
				
			}, function(e)
			{
				//alert("ERROR logging in");
				openMessageBox("ERROR Logging Out");
			});
		}
	});

	/////////////////////////////////////////////  events  //////////////////////////////////////
	
	//Initialize date to today
	$("#date_for_registration").val(g_date);
	//Draw graph for selected date
	//date_for_registration
	$("#date_for_registration").datepicker(
	{
		dateFormat: "yy-mm-dd",
		onSelect: function(dateText)
		{
			//alert("Selected date: " + dateText + "; input's current value: " + this.value);
			g_date = dateText;
			g_day = getDayByDateString(dateText);
			$("#date_for_registration").val(g_date);
			var data = {method:"MtcOpenDatesHelper.getOpenHours", date:g_date, day: g_day};
			postData(JSON_API_URL, data, function(json)
			{
				setGlobals(json);
				//Get Current time in half hour increments 
				//Plus n hours - used to test if reservations time is over
				//Not for any reservation in the future this will always be less than const_START_TIME
				g_current_time_dec = getTimeByHalfs(1);
				drawTimeColums();
			},error);
		}
	});
	//Draw Tommorrows graph
	$("#btnTomorrow").click(function(e)
	{
		//Get tommorows date and day
		g_date = getTodaysDatePlus(1);
		g_day = getTodaysDayPlus(1);
		$("#date_for_registration").val(g_date);
		var data = {method:"MtcOpenDatesHelper.getOpenHours", date:g_date, day: g_day};
		postData(JSON_API_URL, data, function(json)
		{
			setGlobals(json);
			//Get Current time in half hour increments 
			//Plus n hours - used to test if reservations time is over
			//Not for any reservation in the future this will always be less than const_START_TIME
			g_current_time_dec = getTimeByHalfs(1);
			drawTimeColums();
		},error);
	});

	$("#btnToday").click(function(e)
	{
		//Get todays date and day
		g_date = getTodaysDatePlus(0);
		g_day = getTodaysDayPlus(0);
		$("#date_for_registration").val(g_date);
		var data = {method:"MtcOpenDatesHelper.getOpenHours", date:g_date, day: g_day};
		postData(JSON_API_URL, data, function(json)
		{
			setGlobals(json);
			//Get Current time in half hour increments 
			//Plus n hours - used to test if reservations time is over
			g_current_time_dec = getTimeByHalfs(1);
			drawTimeColums();
		},error);
	});
	
	$("#lists_container").click(function(e)
	{
		if(!g_ukey)
		{
			$( "#login_dialog" ).dialog( "open" );
			return;
		}
		if(g_permissions < COURT_CAPTAIN)
		{
			var todaysDate = getTodaysDatePlus(0);
			if(g_date < todaysDate)
			{
				alert("Reservations Over. Ask Court Captain for help.");
				return;
			}
		}
		var id = e.target.id;
		var status = e.target.dataset.status;
		var next_id = e.target.dataset.next_id;
		var court_reservation_id = e.target.dataset.court_reservation_id;
		switch(status)
		{
			case const_FREE:
				var selector = fixSelectorWithPeriod("#" + next_id);
				next_status = $(selector).data('status');
				if(next_status == const_FREE)
				{
					//$("#reservation_dialog").dialog("open");
					makeReservationAt(id);
				}
				else
				{
					//alert("This time slot is not available, try a different time or location.");
					$("#reservation_dialog").dialog("open");
				}
				break;
			case const_MEMBER_RESERVATION:
				//if($("#can_delete_res").is(':checked'))
				if(g_can_delete_res)
				{
					showActionDialog(court_reservation_id, id);
				}
				else
				{
					alert("Not allowed to modify someone elses reservation. Check Settings.");
					drawTimeColums();
				}
				break;
			case const_MY_RESERVATION:
					showActionDialog(court_reservation_id, id);
				break;
		}
	});
	
	//TODO Testing only
	/*
	$("#btnTestReservations").click(function(e)
	{
		//Get the list of reservations listed by court_id
		var data = { method:"MtcCourtReservationHelper.reservations", date:g_date,
					start_time:decimalToMinutes(const_START_TIME),
					end_time:decimalToMinutes(const_END_TIME)};
		postData(JSON_API_URL, data, function(json)
		{
			alert(JSON.stringify(json).substring(0, 2500)); //Limit the string size	
		}, error);
	});
	*/
	
	//look_up_dialog
	$( "#look_up_dialog" ).dialog(
	{
		autoOpen: false,
		maxWidth: 800,
		minWidth: 200,
		buttons: [
		{
		  text: "Cancel",
		  icons: {
			primary: "ui-icon-cancel"
		  },
		  click: function(ev)
		  {
			$( this ).dialog( "close" );
		  }
		}
	  ]
	});
	
	//Default settings
	$("#toggle_advanced_settings").html("open");
	g_is_advanced_settings = false;
	
	$("#reservation_dialog").dialog(
	{
		autoOpen: false,
		maxWidth: 800,
		minWidth: 640,
		buttons: [
		{
		  text: "Save",
		  icons: {
			primary: "ui-icon-heart"
		  },
		  click: function(ev)
		  {
			ev.preventDefault();
			saveReservation();
			//$( this ).dialog( "close" );
		  }
		},
		{
		  text: "Cancel",
		  icons: {
			primary: "ui-icon-cancel"
		  },
		  click: function(ev)
		  {
			$( this ).dialog( "close" );
		  }
		}
	  ]
	});
	
	$("#toggle_advanced_settings").click(function(e)
	{
		e.preventDefault();
		if(g_is_advanced_settings)
		{
			hide("#advanced_settings");
			$("#toggle_advanced_settings").html("open");
			g_is_advanced_settings = false;
		}
		else
		{
			show("#advanced_settings");
			$("#toggle_advanced_settings").html("close");
			g_is_advanced_settings = true;
		}
	});

	$("#look_up_fields_list").click(function(e)
	{
		if("look_up_fields_list" != e.target.id)
		{
			if(e.target.dataset.target == "first_name")
			{
				$("#member_name_text").html(e.target.innerHTML);
				$("#member1_id").val(e.target.id);
			}
			else if(e.target.dataset.target == "court_id")
			{
				$("#court_id").val(e.target.id);
				$("#court_name_txt").html(getCourtNameForId(e.target.id));
			}
			$( "#look_up_dialog" ).dialog( "close" );
		}
	});

	//Pop up a Look Up dialog from an input Form
	$("#court_lookup_button").click(function(e)
	{
		e.preventDefault();
		$( "#look_up_dialog" ).dialog( "open" );
		drawLookUpList("MtcCourt" , "court_id");		
	});
	
	//Pop up a look Up Dialog from a button
	$("#search_by_first_name_lookup_btn").click(function(e)
	{
		e.preventDefault();
		$( "#look_up_dialog" ).dialog( "open" );
		drawLookUpList("MtcMemberSecure", "first_name");		
	});

	$("#search_by_first_name").click(function(e)
	{
		drawFirstNameList(name);
	});
	
	$("#btnReservation_Settings").click(function(e)
	{
		openRegistrationSettingsDialog(function()
		{
			g_max_reservations_per_member = parseInt($("#reservations_per_member_select").val());
			g_max_rereservations_per_time_period = parseInt($("#reservations_per_time_period_select").val());
			g_can_delete_res = $("#can_delete_res").prop('checked');
			if(g_can_delete_res)
			{
				localStorage.setItem("str_can_delete_res", "true");
			}
			else
			{
				localStorage.setItem("str_can_delete_res", "false");
			}
			localStorage.setItem("g_max_reservations_per_member", g_max_reservations_per_member);
			localStorage.setItem("g_max_rereservations_per_time_period", g_max_rereservations_per_time_period);
		});
	});
	
	/*
	$("#btnEvents_Test").click(function(e)
	{
		//var data = { method:"EventsHelper.getLatestEvents"}; 
		var data = { method:"EventsHelper.getEventsByDate", order:"ASC"};
		postData(JSON_API_URL, data, function(json)
		{
			var str = json;
		}, error);

	});
	*/
});

//////////////////////////////////// Reservation Settings Dialog
//

function openRegistrationSettingsDialog(editCallBack)
{
	//settings_dialog
	// Define the Dialog and its properties.
    $("#reservation_settings_dialog").dialog({
		resizable: false,
		modal: true,
		title: "Reservation Settings",
		resize:"auto",
		//height: 200,
		width: 400,
		buttons:{
				"Save": function () {
				$(this).dialog('close');
				editCallBack();
			},
				"Cancel": function () {
				$(this).dialog('close');
			}
		}
	});
}

function makeReservationSettings()
{
	//Get the reservation settings from local storage or from the form if not set
	g_max_reservations_per_member = localStorage.getItem("g_max_reservations_per_member");
	if(!g_max_reservations_per_member)//use value from default form
	{
		g_max_reservations_per_member = parseInt($("#reservations_per_member_select").val());
		localStorage.setItem("g_max_reservations_per_member", g_max_reservations_per_member);
	}
	else
	{
		g_max_reservations_per_member = parseInt(localStorage.getItem("g_max_reservations_per_member"));
	}
	//Make sure to set input in form
	$("#reservations_per_member_select").val(g_max_reservations_per_member);
	
	//Get the reservation settings from local storage or from the form if not set
	g_max_rereservations_per_time_period = localStorage.getItem("g_max_rereservations_per_time_period");
	if(!g_max_rereservations_per_time_period)//use value from default form
	{
		g_max_rereservations_per_time_period = parseInt($("#reservations_per_time_period_select").val());
		localStorage.setItem("g_max_rereservations_per_time_period", g_max_rereservations_per_time_period);
	}
	else
	{
		g_max_rereservations_per_time_period = parseInt(localStorage.getItem("g_max_rereservations_per_time_period"));
	}
	//Make sure to set input in form
	$("#reservations_per_time_period_select").val(g_max_rereservations_per_time_period);
													
	//Get the reservation settings from local storage or from the form if not set
	var str_can_delete_res = localStorage.getItem("str_can_delete_res");
	if(!str_can_delete_res)
	{
		if($("#can_delete_res").prop('checked'))
		{
			localStorage.setItem("str_can_delete_res", "true");
			g_can_delete_res = true;
		}
		else
		{
			localStorage.setItem("str_can_delete_res", "false");
			g_can_delete_res = false;
		}
	}
	else
	{
		if(str_can_delete_res == "true")
		{
			g_can_delete_res = true;
		}
		else
		{
			g_can_delete_res = false;
		}
	}
	$('#can_delete_res').prop('checked', g_can_delete_res);
}

///////////////////////////////////// Open Edit Select Dialog  /////////////////////////////
//
function openEditSelectBox(obj, editCallBack, deleteCallBack, cloneCallBack)
{
	//var html = '<span>' + msg + '</span>';
	//$("#edit_select").html(html);

    // Define the Dialog and its properties.
    $("#edit_select").dialog({
		resizable: false,
		modal: true,
		title: "Select Action",
		resize:"auto",
		//height: 200,
		width: 400,
		buttons:{
				"Edit": function () {
				$(this).dialog('close');
				editCallBack();
			},
				"Delete": function () {
				$(this).dialog('close');
				deleteCallBack();
			},
				"Cancel": function () {
				$(this).dialog('close');
			}
		}
	});
}

function drawTimeColums()
{
	//Don't draw
	//obsolete
	
	//Get the list of reservations and confirmed time slots
	//var data = { method:"MtcCourtReservationHelper.reservations", date:g_date, start_time:"14:00:00", end_time:"18:30:00"};
	var data = { method:"MtcCourtReservationHelper.quick_res", date:g_date, 
		start_time:decimalToMinutes(const_START_TIME), end_time:decimalToMinutes(const_END_TIME)};
	postData(JSON_API_URL, data, function(json)
	{
		//g_my_reservation_count = 0;
		g_json = json;
		for(var i=0; i < g_courts_json.fields.length; i++)
		{
			var field = g_courts_json.fields[i];
			makeTimeRows(field.court_id, field.court_name);
		}
	}, error);
}

function showActionDialog(my_court_reservation_id, my_id)
{
	var obj = {court_reservation_id: my_court_reservation_id, id: my_id};
	openEditSelectBox(obj, function(e)
	{
		//Edit Content
		editReservationAt(my_court_reservation_id);
	}, function(e)
	{
		deleteReservation(my_court_reservation_id, my_id)
		//Delete Item
	});
}

function deleteReservation(my_court_reservation_id, my_id)
{
	var obj = getTimesForId(my_id);
	var my_start_time = obj.start_time;

	var todaysDate = getTodaysDatePlus(0);
	var my_decimal_time = minutesToDecimal(my_start_time);
	var reservation_cutoff_time = getTimeByHalfs(1);
	var strTime = decimalToMinutes(reservation_cutoff_time);
	if(g_date == todaysDate && my_decimal_time < reservation_cutoff_time)
	{
		if(g_permissions >= COURT_CAPTAIN)
		{
			if(!confirm("This reservation is before cutoff time of " + strTime + ". Do you want to continue?"))
			{
				return;
			}
		}
		else
		{
			alert("Reservation can be deleted only for times after " + strTime + ".");
			return;
		}
	}
	
	var data = {method:"MtcCourtReservation.delete", court_reservation_id:my_court_reservation_id};
	data['userid'] = g_userid;
	data['ukey'] = g_ukey;

	postData(JSON_API_URL, data, function(json)
	{
		//g_my_reservation_count--;
		drawTimeColums();
	}, error);

}

//Returns {start_time:my_start_time, end_time:my_end_time}
function getTimesForId(id)
{
	var arr = id.split("_");
	var t = arr[1];
	var h = Math.floor(t);
	var m =  Math.round((t-h)*60);
	
	eh = h+1;
	h = h<10 ? "0" + h : h;
	eh = eh<10 ? "0" + eh : eh;
	m = m<10 ? "0" + m : m;
	
	var my_start_time = h + ":" + m;
	var my_end_time = eh + ":" + m;

	return {start_time:my_start_time, end_time:my_end_time};
}

function getMemberNameByCourtReservationId(my_court_reservation_id)
{
	for(var i=0; i < g_json.reservations.length; i++)
	{
		var res = g_json.reservations[i];
		if(my_court_reservation_id == res.court_reservation_id)
		{
			return res.first_name + ' ' + res.last_name;
		}
	}
	return "";
}

function editReservationAt(my_court_reservation_id)
{
	var data = {
		method: "MtcCourtReservation.get",
		court_reservation_id: my_court_reservation_id
	};
	postData(JSON_API_URL, data, function(json)
	{
		//Show Court Lookup button
		showInline("#court_lookup_button_area");
		var field = json.field;
		var member_name_string = getMemberNameByCourtReservationId(field.court_reservation_id);
		var day_name_string = getDayByDateString(field.date);
		$("#member_name_text").html(member_name_string);
		$("#court_reservation_id").val(field.court_reservation_id);
		$("#member1_id").val(field.member1_id);
		$("#court_id").val(field.court_id);
		$("#court_name_txt").html(getCourtNameForId(field.court_id));
		$("#day_name_string").html(day_name_string);
		$("#date_string").html(field.date);
		$("#start_time").val(field.start_time);
		$("#end_time").val(field.end_time);
		$("#notes").html(field.notes);
		$("#recurring_days_msg").html("Reserve all " + day_name_string + "s until:");
		$("#reservation_dialog").dialog("open");
		$("#time_stamp").html(field.time_stamp);
	}, error);
//{"status":"SUCCESS","field":{"court_reservation_id":"810","court_id":"4","time_stamp":"2015-04-17 09:47:34","status":"CONFIRMED","date":"2015-04-17","start_time":"12:30:00","end_time":"13:30:00","member1_id":"110","notes":"Reserved from Web site."}}
}

function makeReservationAt(id)
{
	var obj = getTimesForId(id);
	var my_start_time = obj.start_time;
	var my_end_time = obj.end_time;

	//Get the court number
	var arr = id.split("_");
	var arr2 = arr[0].split("-");
	var my_court_id = arr2[1];
	
	var todaysDate = getTodaysDatePlus(0);
	var my_decimal_time = minutesToDecimal(my_start_time);
	var reservation_cutoff_time = getTimeByHalfs(1);
	var strTime = decimalToMinutes(reservation_cutoff_time);
	if(g_date == todaysDate && my_decimal_time < reservation_cutoff_time)
	{
		if(g_permissions >= COURT_CAPTAIN)
		{
			if(!confirm("This reservation is before cutoff time of " + strTime + ". Do you want to continue?"))
			{
				return;
			}
		}
		else
		{
			alert("Reservation can be made only for times after " + strTime + ".");
			return;
		}
	}
	//Hide court lookup button
	hideInline("#court_lookup_button_area");
	//var day_name_string = getTodaysDayPlus(0);
	var day_name_string = getDayByDateString(g_date);
	$("#court_reservation_id").val(null);
	$("#court_id").val(my_court_id);
	$("#court_name_txt").html(getCourtNameForId(my_court_id));
	$("#day_name_string").html(day_name_string);
	$("#date_string").html(g_date);
	$("#start_time").val(my_start_time);
	$("#end_time").val(my_end_time);
	$("#notes").html("Reserved by admin: " + g_userid);
	$("#recurring_days_msg").html("Reserve all " + day_name_string + "s until:");
	$("#reservation_dialog").dialog("open");
}

function saveReservation()
{
	//Check the members permission
	var my_member1_id = $("#member1_id").val();
	if(my_member1_id == "")
	{
		alert("Missing Member Name");
		return;
	}
	var data = {method:"MtcCourtReservationHelper.getMemberPermission", member_id:my_member1_id};
	postData(JSON_API_URL, data, function(json)
	{
		var permissions = json.field.permissions;
		if(permissions < RESERVATION)
		{
			alert("This Member does not have permission to reserve.");
			return;
		}
		var my_court_reservation_id = $("#court_reservation_id").val();
		var my_member1_id = $("#member1_id").val();
		var my_court_id = $("#court_id").val();
		var my_date = $("#date_string").html();
		var my_start_time = $("#start_time").val();
		var my_end_time = $("#end_time").val();
		var my_notes = $("#notes").html();
		var my_status = $("#status").val();

		if(my_start_time == "" || my_end_time == "")
		{
			alert("Invalid time value");
			return;
		}
		
		//var field = g_json_open_hours.field;
		var d_start_time = minutesToDecimal(my_start_time);
		var d_end_time =  minutesToDecimal(my_end_time);
		if((d_start_time < const_START_TIME) || (d_end_time > const_END_TIME))
		{
			alert("Cannot Book outside of open hours");
			return;
		}
		
		if(d_end_time <= d_start_time)
		{
			alert("End time cannot be less than or equal to start time");
			return;
		}

		var my_method;
		if(my_court_reservation_id)
		{
			my_method = "MtcCourtReservationHelper.safeUpdate"
		}
		else
		{
			my_method = "MtcCourtReservationHelper.safeCreate"
			my_court_reservation_id = "NULL";
		}
		
		var data = {method:my_method,
			reservations_per_time_period:g_max_rereservations_per_time_period,
			reservations_per_person: g_max_reservations_per_member, 
			date:my_date, start_time:my_start_time,
			end_time:my_end_time, court_id:my_court_id, member1_id:my_member1_id, 
			status:my_status, notes:my_notes, court_reservation_id:my_court_reservation_id};
		
		data['userid'] = g_userid;
		data['ukey'] = g_ukey;
	
		postData(JSON_API_URL, data, function(json)
		{
			if(json.RES_STATUS == "SUCCESS")
			{
				$("#reservation_dialog").dialog("close");
				drawTimeColums();
			}
			else
			{
				alert(json.RES_STATUS);
				drawTimeColums();
			}
		}, error);
	}, error);
}

function calcNewHeight(d_start_time)
{
	var time_span = getTimeSpan(d_start_time)
	return const_cell_height * time_span + const_line_height * time_span;
}

function getTimeSpan(d_start_time)
{
	var mins = d_start_time - Math.floor(d_start_time);
	if(mins >= .5)
	{
		mins = mins - .5;
	}
	var time_span = .5 + mins;
	return time_span;
}

//Height of the partial cell above or below a non standard (not on hour or half hour) reservation
function getSlotTimeSpan(d_start_time)
{
	var span;
	var mins = d_start_time - Math.floor(d_start_time);
	if(mins == 0)
	{
		span = .5; //standard span
	}
	else if(mins < .5)
	{
		span = .5 - mins;
	}
	else
	{
		span = 1 - mins;
	}
	return span;
}

function makeTimeRows(court_id, court_name)
{
	court = "c-" + court_id;
	g_last_end_time = 0;
	$div = $("#" + court);
	//clear it out
	$div.html("");
	var odd = false;
	
	//Put empty row at the bottom fix
	var height = const_cell_height * 0.5;
	var $new = $('<div class="cell dark_grey">' + court_name + '</div>').appendTo($div);
	//$new.height(height);

	for(var i = const_START_TIME; i < const_END_TIME; )
	{
		var is_res = false;
		var next_id;
		var color = "white";
		var height;
		//var msg = decimalToMinutes(i); //decimalToMinutes(i);
		var msg = ""; //decimalToString(i);
		var id = court + "_" + i;
		var slot_time_span = getSlotTimeSpan(i);
		var slot_end_time = i + slot_time_span;
		var res = setReservationAt(i, slot_end_time, court_id);
		var court_reservation_id = "";
		var data = 'data-status="' + const_FREE + '"';
		var msg = "";
		if(slot_time_span > MIN_MSG_HEIGHT)
		{
			msg = decimalToString(i);
		}
		if(res)
		{
			is_res = true;
			
			//msg = res.status + '<sub>' + formatT(res.start_time) + ' - ' + formatT(res.end_time) + '</sub>'; 
			//msg += '<br>' + res.first_name + ' ' + res.last_name;
			
			court_reservation_id = res.court_reservation_id;
			
			var d_start_time = minutesToDecimal(res.start_time);
			var d_end_time = minutesToDecimal(res.end_time);
			var time_span = d_end_time - d_start_time;
			id = court + "_" + d_start_time;
			i = d_end_time;
			
			var newHeight = calcNewHeight(d_start_time);
			
			//i += d_end_time - d_start_time;
			//i += res.time_span;
			
			//height = const_cell_height * res.time_span + const_line_height * res.time_span;
			height = const_cell_height * time_span + const_line_height * time_span;
			
			if(res.member1_id == g_userid)
			{
				color = "ochre";
				data = 'data-status="' + const_MY_RESERVATION + '"';
				//g_my_reservation_count++;
			}
			else
			{
				color = "grey";
				data = 'data-status="' + const_MEMBER_RESERVATION + '"';
			}
			
			if(time_span > MIN_MSG_HEIGHT)
			{
				msg = '<span class="resMsg" id="' + id + '" data-court_reservation_id="' + 
					court_reservation_id + '" ' + data + ' >' + res.status + '</span>';
				msg += '<span class="time_str" id="' + id + '" data-court_reservation_id="' + 
					court_reservation_id + '" ' + data + ' >' +
					formatT(res.start_time) + ' - ' + formatT(res.end_time) + '</span>';
				msg += '<span class="name_str" id="' + id + '" data-court_reservation_id="' + 
					court_reservation_id + '" ' + data + ' >' + 
					res.first_name + ' ' + res.last_name + '</span>';
				
				next_id = court + "_" + i;
			}
		}
		else
		{
			i += slot_time_span; //0.5
			height = const_cell_height * slot_time_span;
			next_id = court + "_" + i;
		}
		g_last_end_time = i;
		var $new = $('<div id="' + id + '" class="cell ' + color + '" ' + data + ' data-next_id="' + next_id + '" ' +
					'data-court_reservation_id="' + court_reservation_id + '">' + msg + '</div>').appendTo($div);
		$new.height(height);
 	}
	//Put empty row at the bottom fix
	height = const_cell_height * 0.5;
	var $new = $('<div class="cell dark_grey">&nbsp;</div>').appendTo($div);
	//$new.height(height);
	
}

//dec_time,
function setReservationAt(start_time, end_time, court_id)
{
	var count = 0;
	for(var i=0; i < g_json.reservations.length; i++)
	{
		var res = g_json.reservations[i];
		if(res.display_status != "MAPPED" && res.court_id == court_id && res.display_status != "PENDING")
		{
			var res_start = minutesToDecimal(res.start_time);
			var res_end = minutesToDecimal(res.end_time);
			
			//GIGO Garbage In Garbage Out - can't map bad values
			if(res_end <= res_start) return null;
			
			//find out if this res starts in this time slot
			//if((res_start <= getSlotTimeSpan || res_start < slot_end_time) && res_start >= g_last_end_time)
			if((res_start == start_time || res_start < end_time) && res_start >= g_last_end_time)
			{
				var slot_time_span = res_start - start_time;
				if(slot_time_span > 0)
				{
					//Create the slug to fill in the remainder
					var id = court + "_" + start_time;
					var color = "white";
					var data = 'data-status="' + const_FREE + '"';
					next_id = court + "_" + end_time;
					var msg = "";
					if(slot_time_span > MIN_MSG_HEIGHT)
					{
						msg = decimalToString(start_time);
					}
					var court_reservation_id = "";
					var $new = $('<div id="' + id + '" class="cell ' + color + '" ' + data + ' data-next_id="' + next_id + '" ' +
						'data-court_reservation_id="' + court_reservation_id + '">' + msg + '</div>').appendTo($div);
					var height = const_cell_height * slot_time_span;
					$new.height(height);
					//End create slug
				}
				
				res.display_status = "MAPPED";
				return res;
			}
		}
	}
	return null;
}

/*
function checkForReservationAt(id)
{
	for(var i=0; i < g_json.reservations.length; i++)
	{
		var res = g_json.reservations[i];
		var res_start = minutesToDecimal(res.start_time);
		var res_end = minutesToDecimal(res.end_time);
		if((res_start <= dec_time || res_start < slot_end_time) && res_start >= g_last_end_time)
		{
			console.log("time slot:" + dec_time + " slot_end_time:" + slot_end_time + " res_start:" + res_start + " res_end:" + res_end);
			res.display_status = "MAPPED";
			res.time_span = res_end - res_start;  
			return res;
		}
	}
	return null;
}
*/

/////  if id value has a period in it we need to write the selector in a special way

function fixSelectorWithPeriod(id)
{
	var arr = id.split(".");
	if(arr.length > 1)
	{
		id = arr[0] + "\\." + arr[1];
	}
	return id;
}

///////////////////////////////////////  Court List Functions /////////////////////
function getCourtNameForId(id)
{
	for(var i=0; i < g_courts_json.fields.length; i++)
	{
		var field = g_courts_json.fields[i];
		if(field.court_id == id)
		{
			return field.court_name;
		}
	}
	return "";
}

///////////////////////////////////  Time Functions //////////////////////

//Special function for returning formated string time by half hour increments
function decimalToTimeString(n)
{
	var hours = Math.floor(n);
	var mints = hours == n ? 0 : 30;
	var suffix = hours >= 12 ? "PM" : "AM";
	hours = hours > 12 ? hours - 12 : hours;
	hours = hours < 10 ? "0" + hours : hours;
	mints = mints < 10 ? "0" + mints : mints;
	return hours + ":" + mints + " " + suffix;
}

//Special function for returning string time by half hour increments
function decimalToMinutes(n)
{
	var test = Math.floor(n);
	return test == n ? n + ":00" : test + ":30";
}

function decimalToString(n)
{
	var hours = Math.floor(n);
	var mints = Math.floor((n - hours)*60);
	hours = hours < 10 ? "0" + hours : hours;
	mints = mints < 10 ? "0" + mints : mints;
	return hours + ":" + mints;
}

function minutesToDecimal(str)
{
	var arr = str.split(":");
	var hrs = parseFloat(arr[0]);
	var mints = parseFloat(arr[1]);
	mints = mints/60;
	return hrs + mints;
}

function formatMinutes(str)
{
	var arr = str.split(":");
	var hrs = parseFloat(arr[0]);
	var suffix = hrs >= 12 ? "PM" : "AM";
	if(suffix == "PM" && hrs != 12)
	{
		hrs = hrs - 12;
	}
	hrs = hrs < 10 ? "0" + hrs : "" + hrs;
	return hrs + ":" + arr[1] + " " + suffix;
}

function formatT(str)
{
	var arr = str.split(":");
	return arr[0] + ":" + arr[1];
}

function getTodaysDayPlus(n)
{
	var today = new Date();
	// add number of days
	today.setDate(today.getDate() + n);
	//today.getDay();
	var weekday = new Array(7);
	weekday[0]=  "Sunday";
	weekday[1] = "Monday";
	weekday[2] = "Tuesday";
	weekday[3] = "Wednesday";
	weekday[4] = "Thursday";
	weekday[5] = "Friday";
	weekday[6] = "Saturday";
	return weekday[today.getDay()];
}

function getDayByDateString(date_string)
{
	//make a date string that is fool proof
	var arr = date_string.split("-");
	var year = parseInt(arr[0]);
	//JavaScript counts months from 0 to 11. January is 0. December is 11.
	var month = parseInt(arr[1]) - 1;
	var day = parseInt(arr[2]);
	
	//var d = new Date(date_string);
	var d = new Date(year, month, day);

	var n = d.getDay();
	var weekday = new Array(7);
	weekday[0]=  "Sunday";
	weekday[1] = "Monday";
	weekday[2] = "Tuesday";
	weekday[3] = "Wednesday";
	weekday[4] = "Thursday";
	weekday[5] = "Friday";
	weekday[6] = "Saturday";
	return weekday[n];
	//return weekday[d.getDay()];
}

function getTodaysDatePlus(n)
{
	var today = new Date();
	// add number of days
	today.setDate(today.getDate() + n);
	var dd = today.getDate();
	var mm = today.getMonth()+1; //January is 0!
	var yyyy = today.getFullYear();
	if(dd<10) {
		dd='0'+dd
	} 
	if(mm<10) {
		mm='0'+mm
	} 
	//2015-03-13,
	today = yyyy + "-" + mm + "-" + dd;
	return today;
}

//Get Current time in half hour increments 
//Plus n hours
function getTimeByHalfs(n)
{
	var today = new Date();
	var current_hour = today.getHours();
	var current_minutes = today.getMinutes();
	var minutes = current_minutes > 30 ? 1 : 0.5;
	return current_hour + n + minutes;	
}

//Get Current time in half hour increments 
//Plus n hours
function cutoffTime(reservation_time)
{
	var today = new Date();
	var current_hour = today.getHours();
	var current_minutes = today.getMinutes();
}

///////////////////////////////////   Utiliy Functions   //////////////////////////////////////

function postData(strUrl, data, success, error)
{
  	var my_str = "method:" + data.method;
	console.log("postData(" + strUrl + ")");
	console.log(my_str);
	console.log("data = {");
	for(var name in data) {
   		console.log(name + ": " + data[name]);
		// propertyName is what you want
   		// you can get the value like this: myObject[propertyName]
	}
	
	var jqxhr = $.post(strUrl, data);
	// results
	jqxhr.done(function(json)
	{
		console.log("RESPONSE");
		console.log(JSON.stringify(json));

		var str = "Status: " + json.status; 
		if(json.status == "SUCCESS")
		{
			success(json);
		}
		else
		{
			error(json);
		}
	});
	
	jqxhr.always(function(json)
	{
		//Don't Log here due to latency
		//console.log("RESPONSE");
		//console.log(JSON.stringify(json));

  		//$("#jqxhr_always").val(JSON.stringify(json));
	});
	
	jqxhr.fail(function(e)
	{
		console.log("jqxhr.fail");
		console.log("Error status: " + e.status + " Error: " + e.statusText);

		$("#error_responseText").html(e.responseText);
	});

}

function success(json)
{
	alert(json.status);
}

function error(json)
{
	var errMsg = json.errMsg + " " + json.xtndErrMsg;
	alert(errMsg.substring(0, 2500)); //Limit the string size
	console.log(json.status + " " + json.errMsg + " " + json.xtndErrMsg);
}

function show(id)
{
	$(id).removeClass("hide");
	$(id).addClass("show");
}

function hide(id)
{
	$(id).removeClass("show");
	$(id).addClass("hide");
}

function showInline(id)
{
	$(id).removeClass("hide");
	$(id).addClass("show_inline");
}

function hideInline(id)
{
	$(id).removeClass("show_inline");
	$(id).addClass("hide");
}

////////////////////////////////////////////////////    Login Server  /////////////////////////////////////////////
function loginServer(strUrl, data, success, login_error)
{
  	var jqxhr = $.post(strUrl, data);
	// results
	jqxhr.done(function(json)
	{
		var str = "Status: " + json.status; 
		if(json.status == "1")
		{
			success(json);
		}
		else
		{
			login_error(json);
		}
	});
	
	jqxhr.always(function(json)
	{
		console.log("jqxhr.always");
		console.log(JSON.stringify(json));
	});
	
	jqxhr.fail(function(e)
	{
		console.log("jqxhr.fail");
		console.log("Error status: " + e.status + " Error: " + e.statusText);
		$("#error_responseText").html(e.responseText);
	});

}

function login_error(json)
{
	alert("Failed Login: " + JSON.stringify(json).substring(0, 2500)); //Limit the string size
}

/*
function getUserList()
{
	var data = {method:"MtcMemberSecure.getList"};
	postData(JSON_API_URL, data, function(json)
	{
		$("#user_list").html("");
		var html = "";
		for(var i=0; i < json.fields.length; i++)
		{
			var field = json.fields[i];
			html += '<option id="' + field.member_id + '">' + field.first_name + ' ' + field.last_name + '</option>';
			//if(i == 0) g_userid = field.member_id;
		}
		$("#user_list").html(html);
	}, error);
}
*/
