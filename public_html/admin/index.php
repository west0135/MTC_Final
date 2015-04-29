<?
ini_set('display_errors','On'); error_reporting(E_ALL);
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>March Tennis Club Admin Portal</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="CSS/style.css">
</head>
<body>

<div id="navbar">
	<a class="top-navbar-item admin nav-active" href="index.php">Admin Portal</a>
    <a class="top-navbar-item utils" href="report.php">Reports</a>
    <a class="top-navbar-item reserve" href="reserve.php">Reservation Manager</a>
    <a class="top-navbar-item log_in_out" href="" id="cheapLogOut" data-status="logged_out">Log In</a>
</div>

<div id="test_area">
    <!--
    <caption>TEST AREA do not include in Admin Page</caption>
    <div class="block">
    <caption><strong>IMPORTANT - Uncheck this to test without validation:</strong></caption>
    <input type="checkbox" id="validationStatus" checked><br>
    </div>
    -->
    <!--
    <div class="block">
    <button id="showTestResults">Show Test Results</button><button id="AtaProgramCollection">Test AtaProgramCollection</button>
    <button id="AtaProgramCategory">ATA Program Category</button>
    </div>
    <div class="block">
    <label>member_id</label>
    <input id="test_member_id" type="number">
    <label>permissions</label>
    <input id="test_permissions" type="number">
    <button id="setMemberPermission">Set Member Permission Test</button><br> 
    </div>
    -->
</div>

<div id="option_area" class="hide">    
    <input type="checkbox" id="showFieldValuesStatus" checked>
    <label for="showFieldValuesStatus"><strong>Client Mode</strong></label>
</div>

<div id="pageTypes" class="hide">
	<!--
    <button class="mainButtons" id="AtaProgram">ATA Program</button>
    <button class="mainButtons" id="AtaLesson">ATA Lesson</button>
    -->
    <button class="mainButtons" id="Event">Event</button>
    <button class="mainButtons" id="MtcNotice">MTC Notice</button>
    <button class="mainButtons" id="MtcCourtReservation">MTC Court Reservation</button>
    <button class="mainButtons" id="MtcCourt">MTC Court</button>
    <button class="mainButtons" id="MtcMemberSecure">MTC Member</button>
    <button class="mainButtons" id="MtcMembershipCategory">MTC Category</button>
    <button class="mainButtons" id="MtcPermissions">MTC Permissions</button>
    <button class="mainButtons" id="CannedQuery">Canned Queries</button>
    <button class="mainButtons" id="MtcOpenDatesHelper">MTC Open Dates</button>
</div>
<h3 id="itemTypeLabel" class="hide"></h3>
<button id="createItem" class="hide saveItem">Create a New Item</button>
<div id="editArea" class="hide">
    <form>	
        <div id="actionButtons">
        	<button type="submit" id="addItem" class="saveItem">Save</button>
            <button id="cancelButton">Cancel</button>
            <div id="image_look_up_container" class="hide">
                <button id="image_look_up" class="imageLookupBtn">Look Up Images</button>
            </div>
        </div>
    	<div id="inputForm"></div>
    </form>
    <div id="tinyMice">
        <textarea id="txtContent" name="content" class="htmlEditTextArea tinymce-enabled">
        </textarea>
    </div>
</div>

<div id="fields_list_container" class="hide">
<div class="box">
	<button id="getNextBlock" class="nextButton">Show</button><input id="block_count" type="number" size="4" maxlength="4" ><label><strong><span id="item_type_label"></span> starting from number: </strong></label>
	<input id="block_start" type="number" value="0" size="4" maxlength="4">
    <span id="query_form_input_container" class="hide"><label>Search for member Where First Name is Like: </label>
    <input type="text" maxlength="32" id="mtc_member_first_name_input"></span><span id="mtc_member_first_name_btn" class="hide"><button id="canned_query_submit" class="hide">Search</button></span>
</div>       
<div id="fields_list"></div>
</div> 

<div id="login_dialog" title="Login To Administrator" class="hide">
    <form id="login_form" name="login_form" method="post" action="#">		
    	<label>email</label>
    	<input id="my_email" type="text" autocomplete="on" >
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

<!--
<div id="searchable_look_up_dialog" title="Look Up" class="hide">
    <form id="searchable_look_up_form" method="post" action="#">
    <div id="searchable_look_up_fields_list"></div>		
    </form>
</div>
-->

<div id="dialog-confirm"></div>

<div id="dialog-message"></div>

<div id="image_look_up_dialog" class="hide"></div>

<!--<script type="text/javascript" src="js/jquery-2.1.3.min.js"></script>-->
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script type="text/javascript" src="js/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="js/utils.js?ver=1.000.000"></script>
<script type="text/javascript" src="js/profile.js?ver=1.000.000"></script>
<script type="text/javascript" src="js/admin_utils.js?ver=1.000.000"></script>

</body>
</html>