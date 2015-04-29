<!doctype html>
<html>
<head>
<meta charset="UTF-8">

<title>Report Types</title>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="CSS/style.css">
<!-- Tablesorter: required -->
<link rel="stylesheet" href="CSS/theme.blue.css">

<style type="text/css">
.show{
	display:block;
}
.hide{
	display:none;
}
</style>
</head>
<body>

<div id="navbar">
	<a class="top-navbar-item admin" href="index.php">Admin Portal</a>
    <a class="top-navbar-item utils nav-active" href="report.php">Reports</a>
    <a class="top-navbar-item reserve" href="reserve.php">Reservation Manager</a>
    <a class="top-navbar-item log_in_out" href="" id="cheapLogOut" data-status="logged_out">Log In</a>
</div>

<div id="lists_container">
    <table id="tablesorter-list" class="tablesorter" border="0" cellpadding="0" cellspacing="1"></table>
</div>

<div id="login_dialog" title="Login To List Utilities" class="hide">
    <form id="login_form" name="login_form" method="post" action="#">		
    	<label>email</label>
    	<input id="my_email" type="text" autocomplete="on" >
    	<label>password</label>
    	<input id="pswd" type="password">
    </form>
</div>


<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<!-- Tablesorter: required -->
<script type="text/javascript" src="js/jquery.tablesorter.js"></script>

<script type="text/javascript" src="js/utils.js?ver=1.000.000"></script>
<script type="text/javascript" src="js/profile.js?ver=1.000.000"></script>
<script type="text/javascript" src="js/report.js?ver=1.000.000"></script>

</body>
</html>