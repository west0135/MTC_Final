//globals
var g_const;
var g_json;
var g_courts_json;
var g_confirmed_json;
var g_last_end_time = 0;
//ADMIN IS 45

//Dan is 166
var g_userid = null;

//Admin is "goaxjwur9g6QD"
var g_ukey = null;

//TODO set using configuration
var g_max_reservations_per_person = 4;
var g_max_rereservations_per_time_period = 2;

//var g_my_reservation_count = 0;

var const_cell_height = 80;
var const_line_height = 2;
var const_FREE = "FREE";
var const_MEMBER_RESERVATION = "MEMBER_RESERVATION";
var const_MY_RESERVATION = "MY_RESERVATION";

//TODO fake member id
//var g_fake_member_id = 1;
var g_date;
var g_todays_date;
var hoursDateObject = new Date();


//TODO get this value from mtc_open_dates table
var const_START_TIME = 7;
var g_start_time_dec = 7;
var const_END_TIME = 22;

//var DOMAIN = "http://geopad.ca";
//var DOMAIN = "http://clients.edumedia.ca";
//var JSON_API_URL = DOMAIN + "/to_be/json-api/";
//var LOGIN_SERVER = DOMAIN + "/to_be/kirk/assets/server.php";

DOMAIN = "http://marchtennisclub.com";
var JSON_API_URL = DOMAIN + "/json-api/";
var LOGIN_SERVER = DOMAIN + "security/server.php";


var globalJSON;


$(document).ready(function (){
	
	if(localStorage.getItem('userid') && localStorage.getItem('ukey')) {
	  g_userid = localStorage.getItem('userid');
	  g_ukey = localStorage.getItem('ukey');
	  $('#loginBtn').hide();
	  $('#logoutContainer').show();
	} else {
		$('#myModal').modal('show');
	}
    //alert(daySelection);
    
    //This date is used for court availability
	g_todays_date = getTodaysDatePlus(0);
	
	if (daySelection == 0){
        g_date = getTodaysDatePlus(0);
        var today = new Date();
        // add number of days
        today.setDate(today.getDate());
    }
    else if (daySelection == 1){
        g_date = getTodaysDatePlus(1);
        var today = new Date();
        // add number of days
        today.setDate(today.getDate()+1);
        hoursDateObject = today;
    }
    
    //alert(g_date);
    getClubhouseHours();
    
	$( "#clearReservationsBtn" ).click(function(e){
		$.post("http://marchtennisclub.com/security/server.php", {
							"func": 'clearReservations',
							"userid": g_userid,
							 "ukey": g_ukey
			},
			function(data) {
				//console.log (data);
		
				if(data.status === 1) {
					//alert ("All Your Reservations have been deleted");
					location.reload();
				  	//setTimeout(function(){ window.location = "index.php"; }, 2000);
					 //Login form should go to homepage
				//Either email has already been used or pwd is invalid
				}else {
					//console.log ("Delete Uncessfull");
				}
			}, "json");  

		
	});
	
	$( "#logoutBtn" ).click(function(e){
		$.post("http://marchtennisclub.com/security/server.php", {
							"func": 'logoutUser',
							"userid": g_userid,
							 "ukey": g_ukey
			},
			function(data) {
				//console.log (data);
		
				if(data.status === 1) {
					g_userid = null;
					g_uker = null;
					localStorage.clear();
					$('#logoutContainer').hide();
					$('#loginBtn').show();
				  	//setTimeout(function(){ window.location = "index.php"; }, 2000);
					 //Login form should go to homepage
				//Either email has already been used or pwd is invalid
				}else {
					//console.log ("LOGOUT UNSUCCESSFUL");
				}
			}, "json");  

		
	});
	
	$( "#loginForm" ).submit(function(e){
	$('#loginControl').hide();
	//$('#myModal').modal('hide');
	var uLogin = $('#uLogin').val();
	var uPassword = $('#uPassword').val();
	
	if (uLogin.trim() !== '' && uPassword.trim() !== '') { 
		$.post("http://marchtennisclub.com/security/server.php", {
								"func": 'loginUser',
								"email": uLogin,
								  "password": uPassword
				},
				function(data) {
					//console.log (data);
			
					if(data.status === 1) {
						$('#uLogin').val('');
						$('#uPassword').val('');
						$('#myModal').modal('hide');
						$('#loginBtn').hide();
						$('#logoutContainer').show();
						g_userid = data.userid;
					  g_ukey = data.ukey;
						localStorage.setItem('userid', data.userid);
						localStorage.setItem('ukey', data.ukey);
						//setTimeout(function(){ window.location = "index.php"; }, 2000);
						 //Login form should go to homepage
					//Either email has already been used or pwd is invalid
					}else {
						$('#loginControl').html("The email and password you entered did not match our records. Please double-check and try again.");
						$('#loginControl').fadeIn();
					}
				}, "json");  
	}else {
		$('#loginControl').html("An email and password is required to login. Please double-check and try again.");
		$('#loginControl').fadeIn();
		$('#uLogin').val('');
		$('#uPassword').val('');
		$('#uLogin').focus();
	}
    return false;
	});
	
    document.querySelector("#btnTomorrow").addEventListener('click',tomorrowPressed);
    document.querySelector("#btnToday").addEventListener('click',todayPressed);
    var data = {method:"MtcCourtReservationHelper.getConstants"}
    postData(JSON_API_URL, data, function(json)
    {
        //save the constants to a global
        g_const = json.constants;

        g_start_time_dec = getTimeByHalfs(1);
        //Adjust if time is before opening
        g_start_time_dec = g_start_time_dec < const_START_TIME ? const_START_TIME : g_start_time_dec;
        
        if (daySelection == 0){
            $("#dateString").html("Reservations for Today: " + g_date);
			$("#btnToday").addClass("darken");
			$("#btnTomorrow").removeClass("darken");
        }
        
        else if (daySelection == 1){
            $("#dateString").html("Reservations for Tomorrow: " + g_date);
			$("#btnToday").removeClass("darken");
			$("#btnTomorrow").addClass("darken");
        }
        
        else{
            $("#dateString").html("Reservations for Today: " + g_date);
        }


        //Draw the containers representing courts
        var data = {method:"MtcCourt.getList"};
        postData(JSON_API_URL, data, function(json)
        {
            g_courts_json = json;
            //$("#lists_container").html("");
            var html = "";
            for(var i=0; i < json.fields.length; i++)
            {
                var field = json.fields[i];
                //html += '<div id="c-' + field.court_id + '" class="container"></div>';
            }
            //$("#lists_container").html(html);

            //Draw the time columns
            //drawTimeColums();
            
            getResStatus();
            getAllCourts();

        }, error);
    });

    function getClubhouseHours(){
        //alert("Inside clubhours");
        //var today = new Date();
        // add number of days
        var dayOfWeek = hoursDateObject.getDay();
        var dayOfWeekString = "";
        
        switch(dayOfWeek){
            case 0:
                dayOfWeekString = "Sunday";
                break;
            case 1:
                dayOfWeekString = "Monday";
                break;
            case 2:
                dayOfWeekString = "Tuesday";
                break;
            case 3:
                dayOfWeekString = "Wednesday";
                break;
            case 4:
                dayOfWeekString = "Thursday";
                break;
            case 5:
                dayOfWeekString = "Friday";
                break;
            case 6:
                dayOfWeekString = "Saturday";
                break;
            default:
                dayOfWeekString = "Monday";
                break;
        }
        //alert(dayOfWeekString);
        //alert("Finished switching");
    //postData(http://marchtennisclub.com/json-api/)
             //method:MtcOpenDatesHelper.getOpenHours
            var data = 
                {           
                    method: "MtcOpenDatesHelper.getOpenHours",
                    date: g_date,
                    day: dayOfWeekString
                };
            postData(JSON_API_URL, data, function (json) {
            //alert(JSON.stringify(json).substring(0, 2500)); //Limit the string size

                if(json.status == "SUCCESS")
                {
                    //alert("Got Today's Hours");
                    console.log("============================HOURS==============================");
                    console.log(json);
                    //alert(json.field[0].start_time);
                    var startTime = json.field[0].start_time;
                    startTime = startTime.split(":");
                    //alert(startTime[0]);
                    var startInt = parseInt(startTime[0],10);
                    
                    var endTime = json.field[0].end_time;
                    endTime = endTime.split(":");
                    var endInt = parseInt(endTime[0],10);
                    //alert(startInt);
                    const_START_TIME = startInt;
                    const_END_TIME = endInt;
                    //alert(const_START_TIME);
                    //alert(const_END_TIME);
                }
                else{
                    alert("ERROR");
                    alert(json.RES_STATUS);
                }
            
            });
    }


    
    function getResStatus() {
        //Get the list of confirmations listed by court_id
        var data = {
            method: "MtcCourtReservationHelper.quick_res",
            date: g_todays_date, status: "CONFIRMED",
            start_time: decimalToMinutes(const_START_TIME),
            end_time: decimalToMinutes(const_END_TIME),
            end_date: g_todays_date, sort_order: "ASC"
        };
        postData(JSON_API_URL, data, function (json) {
            //alert(JSON.stringify(json).substring(0, 2500)); //Limit the string size

            if(json.status == "SUCCESS")
            {
                var numCourts = 8;
                g_confirmed_json = json;
                for(var i=0; i < g_confirmed_json.reservations.length; i++)
                {
                    var g_court_id = 'court-' + g_confirmed_json.reservations[i].court_id;
                    numCourts--;
                    if(g_confirmed_json.reservations[i].court_id < 5) {
                        document.getElementById(g_court_id).src="img/court_icon_red_used.svg";
                    }
                    else{
                        document.getElementById(g_court_id).src="img/court_icon_used.svg";
                    }
                }
                if(numCourts != 1) {
                    document.getElementById("numCourts").innerHTML = numCourts + ' Courts Available';
                }else{
                    document.getElementById("numCourts").innerHTML = '1 Court Available';
                }
            }
            else
            {
                //console.log(json.RES_STATUS);
            }


        }, error);
    }
    
    function getAllCourts() {
        
        //g_date = getTodaysDatePlus(0);
        //alert("Getting courts");
        //console.log("FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF");
        var data = {
            method: "MtcCourtReservationHelper.quick_res",
            date: g_date, status: "RESERVED",
            start_time: decimalToMinutes(const_START_TIME),
            end_time: decimalToMinutes(const_END_TIME),
            sort_order: "ASC"
        };
        
        postData(JSON_API_URL, data, function (json) {
            if(json.status == "SUCCESS")
                {
                    
                    if (const_START_TIME == const_END_TIME){
//                        var warning = document.createElement("h1");
//                        warning.innerHTML = 
                        document.querySelector("#dateString").innerHTML = "Online reservations are currently unavailable";
                    }
                    
                    else{
                        var listDiv = document.querySelector("#table1");
                        var table1 = document.createElement("table");
                        //data-toggle="table" data-url="data1.json" data-cache="false" data-height="299"
                        table1.setAttribute('data-toggle', 'table');
                        table1.setAttribute('data-url', 'data1.json');
                        table1.setAttribute('data-cache', 'false');
                        table1.setAttribute('data-height','299');
                        table1.className = 'table table-bordered';

                        var table2 = document.createElement("table");
                        //data-toggle="table" data-url="data1.json" data-cache="false" data-height="299"
                        table2.setAttribute('data-toggle', 'table');
                        table2.setAttribute('data-url', 'data1.json');
                        table2.setAttribute('data-cache', 'false');
                        table2.setAttribute('data-height','299');
                        table2.className = 'table table-bordered';

                        var scrollableDiv = document.createElement('div');
                        scrollableDiv.className = 'scrollable';

                        table1.id = "morningTable"
                        listDiv.appendChild(table2);
                        listDiv.appendChild(scrollableDiv);
                        scrollableDiv.appendChild(table1);

                        var thead = document.createElement("thead");
                        var tr1 = document.createElement("tr");
                        var th1 = document.createElement("th");
                        th1.innerHTML = "Time";
                        var th2 = document.createElement("th");
                        th2.innerHTML = "Current Reservations";
                        var th3 = document.createElement("th");
                        th3.innerHTML = "Availability";
                        table2.appendChild(thead);
                        thead.appendChild(tr1);
                        tr1.appendChild(th1);
                        tr1.appendChild(th2);
                        tr1.appendChild(th3);
                        //table1.appendChild(thead);

                        var dateRanges = (const_END_TIME - const_START_TIME)*2;
                        //alert(dateRanges);
                        var iterationTime = const_START_TIME;
                        //alert(getTimeByHalfs(0));
                        //alert("iteration time: "+ iterationTime);
                        for(var i=0; i<dateRanges; i++){
                            var tbody = document.createElement("tbody");
                            var tableRow = document.createElement("tr");
                            var td1 = document.createElement("td");

                            switch (iterationTime){
                                case 7:
                                    td1.innerHTML = "7:00";
                                    break;
                                case 7.5:
                                    td1.innerHTML = "7:30";
                                    break;
                                case 8:
                                    td1.innerHTML = "8:00";
                                    break;
                                case 8.5:
                                    td1.innerHTML = "8:30";
                                    break;
                                case 9:
                                    td1.innerHTML = "9:00";
                                    break;
                                case 9.5:
                                    td1.innerHTML = "9:30";
                                    break;
                                case 10:
                                    td1.innerHTML = "10:00";
                                    break;
                                case 10.5:
                                    td1.innerHTML = "10:30";
                                    break;
                                case 11:
                                    td1.innerHTML = "11:00";
                                    break;
                                case 11.5:
                                    td1.innerHTML = "11:30";
                                    break;
                                case 12:
                                    td1.innerHTML = "12:00";
                                    break;
                                case 12.5:
                                    td1.innerHTML = "12:30";
                                    break;
                                case 13:
                                    td1.innerHTML = "13:00";
                                    break;
                                case 13.5:
                                    td1.innerHTML = "13:30";
                                    break;
                                case 14:
                                    td1.innerHTML = "14:00";
                                    break;
                                case 14.5:
                                    td1.innerHTML = "14:30";
                                    break;
                                case 15:
                                    td1.innerHTML = "15:00";
                                    break;
                                case 15.5:
                                    td1.innerHTML = "15:30";
                                    break;
                                case 16:
                                    td1.innerHTML = "16:00";
                                    break;
                                case 16.5:
                                    td1.innerHTML = "16:30";
                                    break;
                                case 17:
                                    td1.innerHTML = "17:00";
                                    break;
                                case 17.5:
                                    td1.innerHTML = "17:30";
                                    break;
                                case 18:
                                    td1.innerHTML = "18:00";
                                    break;
                                case 18.5:
                                    td1.innerHTML = "18:30";
                                    break;
                                case 19:
                                    td1.innerHTML = "19:00";
                                    break;
                                case 19.5:
                                    td1.innerHTML = "19:30";
                                    break;
                                case 20:
                                    td1.innerHTML = "20:00";
                                    break;
                                case 20.5:
                                    td1.innerHTML = "20:30";
                                    break;
                                case 21:
                                    td1.innerHTML = "21:00";
                                    break;
                                case 21.5:
                                    td1.innerHTML = "21:30";
                                    break;

                                default:
                                    td1.innerHTML = "";
                                    break;

                            }
                            //td1.innerHTML = iterationTime;
                            var td2 = document.createElement("td");
                            var result = countResForSlot(json,iterationTime);
                            td2.innerHTML = result;
                            var td3 = document.createElement("td");

                            if (getTimeByHalfs(0)>iterationTime && daySelection == 0){
                                //alert("Time is in the past");
                                td3.innerHTML = "Past Reservation";
                                td1.className = "td-reserved";
                                td2.className = "td-reserved";
                                td3.className = "td-reserved";
                            }

                            else{
                                    if(result >= 2){

                                        td3.innerHTML = "No Reservations Available";
                                        td1.className = "td-reserved";
                                        td2.className = "td-reserved";
                                        td3.className = "td-reserved";
                                    }
                                    else{
                                        td3.innerHTML = "<button type='button' id='" + iterationTime + "' class='btn btn-sml btn-success'>Reserve Now</button>";
                                        //td3.innerHTML = "YES";
                                    }

                                    table1.appendChild(tbody);
                                    tableRow.appendChild(td1);
                                    tableRow.appendChild(td2);
                                    tableRow.appendChild(td3);
                                    tbody.appendChild(tableRow);
                                }

                            iterationTime += 0.5;
                        }
                        table1.addEventListener("click", makeReservationAt);
                        //table1.addEventListener("click",showLogin);
                    }
                    
                    
                    
                    //console.log("-----------------------------------------");
                }
                else
                {
                    console.log(json.RES_STATUS);
                    //alert("couldn't do it");
                }
        
        }, error);
    }
    
    function showLogin(){
        //alert("Shown");
        document.querySelector("#login_dialog").show;
    }
    
    function countResForSlot(data,slot){
    
        globalJSON = data;
        slot = parseFloat(slot);
        var numReservations = 0;
        //console.log("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
        for(var i=0; i<data.reservations.length; i++){
            var resStartTime = data.reservations[i].start_time.split(":");
            var parsed = parseInt(resStartTime[1],10);
            resStartTime[1] = ((parsed/60)*100);
            var fullStart = "" + resStartTime[0] + "." + resStartTime[1];
            var resEndTime = data.reservations[i].end_time.split(":");
            var parsed2 = parseInt(resEndTime[1],10);
            resEndTime[1] = ((parsed2/60)*100);
            var fullEnd = "" + resEndTime[0] + "." + resEndTime[1];
            
            //console.log("START ARRAY NOW!!!!!");
            //console.log(resStartTime);
            //console.log(fullStart);
            //console.log(fullEnd);
            
            //alert(fullStart + " < " + slot + " < " + fullEnd);
            //alert((fullStart <= slot) && (slot <= fullEnd));
            if ((fullStart <= slot) && (slot < fullEnd)){
                numReservations++;
            }
        }
        return numReservations;
    }
    
    function makeReservationAt(ev)
    {
        //alert(ev.target.id);
        if (ev.target.id.length>0){
            
            //alert("target id: "+ev.target.id);
            var currentRes = ev.target.id.split(".");
            var endRes;
            var startRes;
            
            //alert(currentRes[1]);
            if (currentRes[1]!=null && currentRes[1]!=undefined){
                //alert("Half Hour");
                //alert("clicked half hour");
                startRes = "" + currentRes[0] + ":30";
                //alert("currentres: "+startRes);
                //console.log("===================HALFHOUR BUG==================================");
                //console.log(""+currentRes[0]+"");
                var endresHour = (parseInt(currentRes[0],10)) + 1;
                endRes = "" + endresHour + ":30";
                //alert("endRes: "+endRes);
            }
            
            else{
                //alert("Hour");
                startRes = "" + currentRes + ":00";
                //console.log("===================================================================");
                //console.log(currentRes);
                endRes = "" + (parseInt(startRes,10)+1) + ":00";
            }
            //alert("currentRes = "+currentRes+", endRes = "+endRes);
        }
        
        else{
            //alert("clicked nothing");
        }

        var courtToReserve = 1;
        var flag = false;
        
        for (var i=1; i<8; i++){
            
            for (var j=0; j<globalJSON.reservations.length; j++){
                
                if (globalJSON.reservations[j].court_id == i){
                    flag = true;
                }
                
            }
            
            if (!flag){
                courtToReserve = i;
            }
            
            
        }
    
        //console.log("//////////////COURT TO RESERVE////////////////");
        //console.log(courtToReserve);
        var data = {method:"MtcCourtReservationHelper.safeCreate",
            reservations_per_time_period: -1,
            reservations_per_person: -1,
            reservations_per_time_period:g_max_rereservations_per_time_period,
            reservations_per_person: g_max_reservations_per_person,
            date:g_date, start_time:startRes,
            end_time:endRes, court_id:courtToReserve, member1_id:g_userid,
            status:g_const.RESERVED, notes:"Reserved from Web site.", court_reservation_id:"NULL"};

        data['userid'] = g_userid;
        data['ukey'] = g_ukey;

        postData(JSON_API_URL, data, function(json)
        {
            if(json.RES_STATUS == "SUCCESS")
            {
                //drawTimeColums();
                //alert("Worked");
                alert("Reservation Successful");
                location.reload(true);
            }
            else
            {
                //alert("Failed");
                alert(json.RES_STATUS);
                //drawTimeColums();
            }
        }, error);
    }

    function tomorrowPressed(){
        window.location = "courts.php?today=false";
        
    }
    
    function todayPressed(){
        window.location = "courts.php";
    }

    function decimalToMinutes(n)
    {
        var test = Math.floor(n);
        return test == n ? n + ":00" : test + ":30";
    }

    function minutesToDecimal(str)
    {
        var arr = str.split(":");
        var hrs = parseFloat(arr[0]);
        var mints = parseFloat(arr[1]);
        mints = mints/60;
        return hrs + mints;
    }

    function postData(strUrl, data, success, error)
    {
        var my_str = "method:" + data.method;
        //console.log("postData(" + strUrl + ")");
        //console.log(my_str);
        //console.log("data = {");
        for(var name in data) {
            //console.log(name + ": " + data[name]);
            // propertyName is what you want
            // you can get the value like this: myObject[propertyName]
        }

        var jqxhr = $.post(strUrl, data);
        // results
        jqxhr.done(function(json)
        {
            //console.log("RESPONSE");
            //console.log(JSON.stringify(json));

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
            //console.log("jqxhr.fail");
            //console.log("Error status: " + e.status + " Error: " + e.statusText);

            $("#error_responseText").html(e.responseText);
        });

    }

    function success(json)
    {
        //alert(json.status);
    }

    function error(json)
    {
        var errMsg = json.status + " " + json.errMsg + " " + json.xtndErrMsg;
        alert(errMsg.substring(0, 2500)); //Limit the string size
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

    function getTimeByHalfs(n)
    {
        var today = new Date();
        var current_hour = today.getHours();
        var current_minutes = today.getMinutes();
        var minutes = current_minutes > 30 ? 1 : 0.5;
        return current_hour + n + minutes;
    }

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
//            console.log("jqxhr.always");
//            console.log(JSON.stringify(json));
        });

        jqxhr.fail(function(e)
        {
//            console.log("jqxhr.fail");
//            console.log("Error status: " + e.status + " Error: " + e.statusText);
            $("#error_responseText").html(e.responseText);
        });

    }

    function login_error(json)
    {
        alert("Failed Login: " + JSON.stringify(json).substring(0, 2500)); //Limit the string size
    }

});

    /*
$(document).ready(function (){
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
                            var msg = "SUCCESS" + JSON.stringify(json);
                            alert(msg.substring(0, 2500)); //Limit the string size
                            g_userid = json.userid;
                            g_ukey = json.ukey;

                            drawTimeColums();

                        }, login_error);
                    }
                    // Uncommenting the following line would hide the text,
                    // resulting in the label being used as a tooltip
                    //showText: false
                }
            ]
        });

    //Draw the courts containers
    //<div id="c-1" class="container">
    //</div>

    //First get the constants we need
    var data = {method:"MtcCourtReservationHelper.getConstants"}
    postData(JSON_API_URL, data, function(json)
    {
        //save the constants to a global
        g_const = json.constants;

        g_start_time_dec = getTimeByHalfs(1);
        //Adjust if time is before opening
        g_start_time_dec = g_start_time_dec < const_START_TIME ? const_START_TIME : g_start_time_dec;
        g_date = getTodaysDatePlus(0);
        $("#dateString").html("Reservations for Today: " + g_date);


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
    });

    //Get the list of users
    //getUserList();

    /////////////////////////////////////////////  events  //////////////////////////////////////
    $("#cheapLogOut").click(function(e)
    {
        localStorage.removeItem("g_userid");
        localStorage.removeItem("g_ukey");
        localStorage.removeItem("g_permissions");
        g_userid = null;
        g_ukey = null;
        g_permissions = null;
        alert("Kirk We need a Logout Function");
    });


    //Test Code
    $("#btnTest").click(function(e)
    {
        var my_end_date = $("#end_date").val();
        if(my_end_date == "")
        {
            my_end_date = -1;
        }
        //Get the list of reservations listed by court_id
        var data = { method:"MtcCourtReservationHelper.quick_res",
            date:g_date, status: "CONFIRMED",
            start_time:decimalToMinutes(const_START_TIME),
            end_time:decimalToMinutes(const_END_TIME),
            end_date: my_end_date, sort_order: "ASC"};
        postData(JSON_API_URL, data, function(json)
        {
            alert(JSON.stringify(json).substring(0, 2500)); //Limit the string size
        }, error);
    });

    //Change parameters
    $("#btnChange").click(function(e)
    {
        g_max_reservations_per_person = $("#res_per_member").val();
        g_max_rereservations_per_time_period = $("#res_per_hour").val();
    });

    //Get initial parameters
    $("#btnChange").click();

    /*
     $("#user_list").change(function()
     {
     g_fake_member_id = $(this).children(":selected").attr("id");
     drawTimeColums();
     });


    $("#btnTomorrow").click(function(e)
    {
        g_start_time_dec = const_START_TIME;
        g_date = getTodaysDatePlus(1);
        $("#dateString").html("Reservations for Tomorrow: " + g_date);
        drawTimeColums();
    });

    $("#btnToday").click(function(e)
    {
        //getUserList();
        g_start_time_dec = getTimeByHalfs(1);
        g_start_time_dec = g_start_time_dec < const_START_TIME ? const_START_TIME : g_start_time_dec;
        g_date = getTodaysDatePlus(0);
        $("#dateString").html("Reservations for Today: " + g_date);
        drawTimeColums();
    });

    $("#lists_container").click(function(e)
    {
        if(!g_ukey)
        {
            $( "#login_dialog" ).dialog( "open" );
            return;
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
                    makeReservationAt(id);
                }
                else
                {
                    alert("This time slot is not available, try a different time or location.");
                }
                break;
            case const_MEMBER_RESERVATION:
                alert("Checking reservation status for this time.");
                drawTimeColums();
                break;
            case const_MY_RESERVATION:
                if(confirm("Delete Reservation: " + court_reservation_id))
                {
                    deleteReservation(court_reservation_id);
                }
                break;
        }
    });

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

});

function drawTimeColums()
{
    //Don't draw
    if(g_start_time_dec >= const_END_TIME)
    {
        alert("Closed for Reservations.");
        return;
    }

    //Get the list of reservations and confirmed time slots
    //var data = { method:"MtcCourtReservationHelper.reservations", date:g_date, start_time:"14:00:00", end_time:"18:30:00"};
    var data = { method:"MtcCourtReservationHelper.quick_res", date:g_date, start_time:decimalToMinutes(g_start_time_dec), end_time:decimalToMinutes(const_END_TIME)};
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

function deleteReservation(my_court_reservation_id)
{
    var data = {method:"MtcCourtReservation.delete", court_reservation_id:my_court_reservation_id};
    data['userid'] = g_userid;
    data['ukey'] = g_ukey;

    postData(JSON_API_URL, data, function(json)
    {
        //g_my_reservation_count--;
        drawTimeColums();
    }, error);

}

function makeReservationAt(id)
{
    var arr = id.split("_");
    var t = arr[1];
    var h = Math.floor(t);
    var m = (t-h)*60;

    eh = h+1;
    h = h<10 ? "0" + h : h;
    eh = eh<10 ? "0" + eh : eh;
    m = m<10 ? "0" + m : m;

    var my_start_time = h + ":" + m;
    var my_end_time = eh + ":" + m;

    //Get the court number
    var arr2 = arr[0].split("-");
    var my_court_id = arr2[1];

    var data = {method:"MtcCourtReservationHelper.safeCreate",
        reservations_per_time_period:g_max_rereservations_per_time_period,
        reservations_per_person: g_max_reservations_per_person,
        date:g_date, start_time:my_start_time,
        end_time:my_end_time, court_id:my_court_id, member1_id:g_userid,
        status:g_const.RESERVED, notes:"Reserved from Web site.", court_reservation_id:"NULL"};

    data['userid'] = g_userid;
    data['ukey'] = g_ukey;

    postData(JSON_API_URL, data, function(json)
    {
        if(json.RES_STATUS == "SUCCESS")
        {
            drawTimeColums();
        }
        else
        {
            alert(json.RES_STATUS);
            drawTimeColums();
        }
    }, error);
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

    for(var i = g_start_time_dec; i < const_END_TIME; )
    {
        var color = "white";
        var height;
        var msg = decimalToMinutes(i);
        var id = court + "_" + i;
        var res = setReservationAt(i, court_id);
        var court_reservation_id = "";
        var data = 'data-status="' + const_FREE + '"';
        if(res)
        {
            court_reservation_id = res.court_reservation_id;
            i += res.time_span;
            height = const_cell_height * res.time_span + const_line_height * res.time_span;
            g_last_end_time = i;
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
        }
        else
        {
            i += 0.5
            height = const_cell_height * 0.5;
        }
        //record the next id now that we know what it will be
        var next_id = court + "_" + i;
        var $new = $('<div id="' + id + '" class="cell ' + color + '" ' + data + ' data-next_id="' + next_id + '" ' +
        'data-court_reservation_id="' + court_reservation_id + '">' + msg + '</div>').appendTo($div);
        $new.height(height);
    }
    //Put empty row at the bottom fix
    height = const_cell_height * 0.5;
    var $new = $('<div class="cell dark_grey">&nbsp;</div>').appendTo($div);
    //$new.height(height);

}

function setReservationAt(dec_time, court_id)
{
    var count = 0;
    for(var i=0; i < g_json.reservations.length; i++)
    {
        var res = g_json.reservations[i];
        if(res.status != "MAPPED" && res.court_id == court_id && res.status != "PENDING")
        {
            var res_start = minutesToDecimal(res.start_time);
            var res_end = minutesToDecimal(res.end_time);
            //find out if this res starts in this time slot
            var slot_end_time = dec_time + 0.5;
            if((res_start <= dec_time || res_start < slot_end_time) && res_start >= g_last_end_time)
            {
                console.log("time slot:" + dec_time + " slot_end_time:" + slot_end_time + " res_start:" + res_start + " res_end:" + res_end);
                res.status = "MAPPED";
                res.time_span = res_end - res_start;
                return res;
            }
        }
    }
    return null;
}

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
            res.status = "MAPPED";
            res.time_span = res_end - res_start;
            return res;
        }
    }
    return null;
}


function fixSelectorWithPeriod(id)
{
    var arr = id.split(".");
    if(arr.length > 1)
    {
        id = arr[0] + "\\." + arr[1];
    }
    return id;
}

function decimalToMinutes(n)
{
    var test = Math.floor(n);
    return test == n ? n + ":00" : test + ":30";
}

function minutesToDecimal(str)
{
    var arr = str.split(":");
    var hrs = parseFloat(arr[0]);
    var mints = parseFloat(arr[1]);
    mints = mints/60;
    return hrs + mints;
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
    var errMsg = json.status + " " + json.errMsg + " " + json.xtndErrMsg;
    alert(errMsg.substring(0, 2500)); //Limit the string size
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

function getTimeByHalfs(n)
{
    var today = new Date();
    var current_hour = today.getHours();
    var current_minutes = today.getMinutes();
    var minutes = current_minutes > 30 ? 1 : 0.5;
    return current_hour + n + minutes;
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

*/