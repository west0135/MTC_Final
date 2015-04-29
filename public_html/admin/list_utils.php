<?php
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
?>

<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Report Results</title>

<!--<link rel="stylesheet" href="CSS/jq.css" type="text/css" media="print, projection, screen" />-->
<!--<link rel="stylesheet" href="themes/blue/style.css" type="text/css" media="print, projection, screen" />-->

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="CSS/style.css">

<!-- Tablesorter: required -->
<link rel="stylesheet" href="CSS/theme.blue.css">

<!--
<link rel="stylesheet" href="CSS/jq.css">
<link href="CSS/prettify.css" rel="stylesheet">
-->

<style type="text/css">
.show{
	display:block;
}
.hide{
	display:none;
}
body{
}
.saveItem{
	background-color:#1BD900;
}
.cancelItem{
	background-color:#B6B6B6;
}

#query_form_container{
	margin:20px;
	padding:20px;
	border:solid black 2px;
}

.styled-select {
	overflow: hidden;
	height: 3em;
	/*float: left;
	width: 365px;
	margin:1.5em;*/
	margin-right: 10px;
	background:#B4B4B4;
}

.styled-select select {
	font-size: 1.5em;
	border-radius: 0;
	border: none;
	background: transparent;
	width:100%;
	overflow: hidden;
	padding-top: 0em;
	height: 3em;
	text-indent: 1em;
	color:#000000;
	-webkit-appearance: none;
}

.styled-select optgroup {
    font-size: 1.5em;
}

.styled-select option.service-small {
	font-size: 1.5em;
	padding: 5px;
	background:#B4B4B4;
}

</style>
</head>
<body>

<div id="navbar">
	<a class="top-navbar-item admin" href="index.php">Admin Portal</a>
    <a class="top-navbar-item utils" href="report.php">Reports</a>
    <a class="top-navbar-item reserve" href="reserve.php">Reservation Manager</a>
    <a class="top-navbar-item log_in_out" href="" id="cheapLogOut" data-status="logged_out">Log In</a>
</div>

<!--
<div id="test_area">
    <caption>TEST AREA do not include in Admin Page</caption>
    <div class="block">
    </div>
</div>
-->

<!--
<button id="loginBtn">Log In</button>
<button id="cheapLogOut">Cheap Logout</button><br>
-->

<div id="main_container">
	<div id="query_form_container" class="hide">
	    <form id="query_form" name="query_form" method="post" action="#">
            <button type="submit" id="listItems_submit" class="saveItem">Submit</button><br><br>
            <div id="query_form_input_container">
            </div>
    	</form>
	</div>
    <div id="lists_container">
    	<table id="tablesorter-list" class="tablesorter hide" border="0" cellpadding="0" cellspacing="1"></table>
    </div>
</div>

<div id="login_dialog" title="Login To List Utilities" class="hide">
    <form id="login_form" name="login_form" method="post" action="#">		
    	<label>email</label>
    	<input id="my_email" type="text" autocomplete="on" >
    	<label>password</label>
    	<input id="pswd" type="password">
    </form>
</div>

<div id="dialog-confirm"></div>

<div id="dialog-message"></div>

<div id="edit_select" class="hide"></div>

<div id="edit_profile" class="hide">
    <form id="profile_edit_form">
        <div id="actionButtons">
            <button type="submit" id="addItem" class="saveItem">Save</button>
            <button id="cancelButton">Cancel</button>
        </div>
		<div id="inputForm"></div>
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
<script type="text/javascript">
<?php
	if(isset($_GET['id']))
	{
		$id = $_GET['id'];
		echo 'var g_canned_query_id = "' . $id . '";';
	}
	else
	{
		echo "var g_canned_query_id = null;";
	}
?>
</script>
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<!-- Tablesorter: required -->
<script type="text/javascript" src="js/jquery.tablesorter.js"></script>

<script type="text/javascript" src="js/utils.js?ver=1.000.000"></script>
<script type="text/javascript" src="js/profile.js?ver=1.000.000"></script>
<script type="text/javascript" src="js/list_utils.js?ver=1.000.000"></script>

</body>
</html>