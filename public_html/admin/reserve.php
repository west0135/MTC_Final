<?
ini_set('display_errors','On'); error_reporting(E_ALL);
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Reservation Manager</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="CSS/style.css">
<style type="text/css">
.grey{
	background-color:#D3D3D3;
}
.ochre{
	background-color:#FED800
}
.white{
	background-color:#FFFFFF;
}
.dark_grey{
	padding-top:0.5em;
	padding-bottom:0.6em;
	color:#FFFFFF;
	background-color:#656565;
	text-align:center;
}
.cell{
	width:100%;
	border:solid black thin;
}
.container{
	display:inline-block;
	width:10%;
	border:solid black thin;
	margin-right:4px;
}
label{
	margin-right:20px;
}
.block input{
	margin:0px;
	padding:0px;
}
#res-container{
    padding:20px;
}
#test_area{
	margin:0px;
	padding:0px;
	border:solid black thin;
}

.resMsg{
	display:block;
	margin: 0px 4px;
	padding:0em;
}
.time_str{
	display:block;
	margin: 0px 4px;
	padding:0em;
	font-size:.9em;
}
.name_str{
	display:block;
	margin: 0px 4px;
	padding:0em;
	font-size:.9em;
	text-overflow: ellipsis;
}

</style>
</head>
<body>
<div id="navbar">
	<a class="top-navbar-item admin" href="index.php">Admin Portal</a>
    <a class="top-navbar-item utils" href="report.php">Reports</a>
    <a class="top-navbar-item reserve nav-active" href="reserve.php">Reservation Manager</a>
    <a class="top-navbar-item log_in_out" href="" id="cheapLogOut" data-status="logged_out">Log In</a>
</div>

<br/>
    <div id="res-container">
    	<div id="actionButtons">
            <button id="btnToday">Today</button>
            <button id="btnTomorrow">Tomorrow</button>
            <span class="date_lookup"><label> Set Date to Reserve:</label><input type="text" id="date_for_registration"></span>
            <button id="btnReservation_Settings">Settings</button>
            <!--<button id="btnEvents_Test">Test Events</button>-->
        </div>
        <h1 id="dateString"></h1>
        <div id="lists_container"></div>
    </div>

    <div id="login_dialog" title="Login To Reservation Administrator">
        <form id="login_form" name="login_form" method="post" action="#">
        <label>email</label>
        <input id="my_email" type="text" autocomplete="on">
        <label>password</label>
        <input id="pswd" type="password">
        </form>
    </div>

    <div id="look_up_dialog" title="Look Up" class="hide">
    	<div id="search_input_container" class="hide">
    		<caption>First Name Like: </caption><input id="search_input_first_name_input" type="text" maxlength="64">
        	<button id="search_by_first_name"  class="imageLookupBtn" >Search</button>
    	</div>
    	<form id="look_up_form" method="post" action="#">
    	<div id="look_up_fields_list"></div>		
    	</form>
	</div>
    
    <div id="reservation_dialog" title="Reservation" class="hide">
    	<div id="reservation_dialog_actions">
        	<div class="reservation_input">
            	<label>Member Name:</label><span id="member_name_text"></span>
            	<button id="search_by_first_name_lookup_btn"  class="imageLookupBtn" >Lookup</button>
			</div>
        </div>
        <form id="court_reservation_form" method="post" action="#">
			<input type="hidden" id="court_reservation_id">
            <input type="hidden" id="member1_id">
            <input type="hidden" id="court_id">
            <div class="reservation_input"><label>Court:</label><span id="court_name_txt"></span>
            	<span id="court_lookup_button_area">
                	<button id="court_lookup_button"  class="imageLookupBtn" >Lookup</button>
                </span>
            </div>
            <div class="reservation_input"><label>Day:</label><span id="day_name_string"></span></div>
            <div class="reservation_input"><label>Date:</label><span id="date_string"></span></div>
            <div class="reservation_input"><label>Start Time:</label><input type="time" id="start_time"></div>
            <div class="reservation_input"><label>End Time:</label><input type="time" id="end_time"></div>
            <div class="reservation_input"><label>Status:</label>
            	<select id="status">
                	<option value="RESERVED">RESERVED</option>
                    <option value="CONFIRMED">CONFIRMED</option>
                    <option value="COMPLETE">COMPLETE</option>
                    <option value="EXTRA">EXTRA</option>
                </select>
            </div>
            <div class="reservation_input"><label>Notes:</label>
            	<textarea id="notes" style="width:100%; height:5em;" maxlength="255"></textarea>
            </div>    
            <div class="reservation_input">
            	<label>Advanced Settings:</label><button id="toggle_advanced_settings">open</button>
            </div>    
         	<div id="advanced_settings" class="hide">
                <div class="reservation_input"><label>Timestamp:</label><span id="time_stamp"></span></div>
				<div class="reservation_input">
                	<label id="recurring_days_msg">Reserve all ?s until:</label>
                    <input id="recurring_reservation_end_date" type="date">
                    <sub>Contact development for this upgrade</sub>
                </div>
			</div>    
		</form>
    </div>
        
	<div id="reservation_settings_dialog" title="Settings" class="hide">
    	<div class="reservation_input">
        	<label>Can Update or Delete Any Reservation</label><input id="can_delete_res" type="checkbox" checked>
        </div>
        <div class="reservation_input">
            <label>Reservation Limit by Time Period:</label>
            <select id="reservations_per_time_period_select">
                <option value="-1" selected>Unlimited</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
            </select>
        </div>
        <div class="reservation_input">
            <label>Reservation Limit per Member:</label>
            <select id="reservations_per_member_select">
                <option value="-2" selected>Unlimited on Mutiple Courts</option>
                <option value="-1">Unlimited</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
            </select>
        </div>
    	
    </div>
    
    <div id="dialog-confirm"></div>
    
    <div id="dialog-message"></div>
    
    <div id="edit_select" class="hide"></div>

<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script type="text/javascript" src="js/utils.js?ver=1.000.000"></script>
<script type="text/javascript" src="js/profile.js?ver=1.000.000"></script>
<script type="text/javascript" src="js/reservation_utils.js?ver=1.000.002"></script>

</body>
</html>