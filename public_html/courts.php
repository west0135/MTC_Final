<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro|Oxygen' rel='stylesheet' type='text/css'>

    <link rel="icon"
          type="image/png"
          href="img/favicon.png">


    <title>March Tennis Club - Court Booking</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">

    <!-- Google Maps API  -->
    <script src="https://maps.googleapis.com/maps/api/js"></script>
    <script src="js/map.js" type="text/javascript"></script>

	<script src="js/jquery.js"></script>

	<!-- Bootstrap Core JavaScript -->
	<script src="js/bootstrap.min.js"></script>
	
	<script type="text/javascript" src="js/Reservation-Tester.js"></script>
	<style>

.modal-dialog {
    width: 300px;
}
.modal-footer {
    height: 70px;
    margin: 0;
}
.modal-footer .btn {
    font-weight: bold;
}

	</style>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

<!-- Navigation -->


<!-- Image Background Page Header -->
<!-- Note: The background image is set within the business-casual.css file. -->
<header class="header">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">

                <div id="logo-wrapper">
                    <img class="float-left" id="logo" src="img/mtc_logo.svg"/>
                </div>

                <?php
			
					include('nav.php');

					if (isset($_GET['today'])){

						if ($_GET['today']=='false'){
							echo "<script type='text/javascript'>var daySelection = '1';</script>";
						}

						else{
							echo "<script type='text/javascript'>var daySelection = '0';</script>";
						}
					}

					else{
						echo "<script type='text/javascript'>var daySelection = '0';</script>";
					}


				?>

				
				
				
				
				
                <h1 class="tagline">Become a Member Today!</h1>
                <p class="under-tagline"> March tennis club welcomes you to be a proud member of our community. Sign up now to have access to all club member benefits.</p>
                <div id="join-btn-wrapper"><a href="register.php" class="btn btn-lg btn-success join-btn">Sign up today!</a></div>
            </div>
        </div>
    </div>
</header>

<!-- Page Content -->



<div class="container">


    <br/>
    <button class="btn btn-login" id="loginBtn" data-toggle="modal" data-target="#myModal">
    Log in
</button>

<div id="logoutContainer" style="display:none;">
	
	<button class="btn btn-primary" id="logoutBtn">
    Log out
</button>
<button class="btn btn-primary" id="clearReservationsBtn" style="margin-right:1rem;">
    Clear My Reservations
</button>
</div>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title" id="myModalLabel">Log in</h4>
			</div> <!-- /.modal-header -->

			<div class="modal-body">
				<form role="form" id="loginForm">
					<div class="form-group">
						<div class="input-group">
							<input type="text" class="form-control" id="uLogin" placeholder="Email">
							<label for="uLogin" class="input-group-addon glyphicon glyphicon-user"></label>
						</div>
					</div> <!-- /.form-group -->

					<div class="form-group">
						<div class="input-group">
							<input type="password" class="form-control" id="uPassword" placeholder="Password">
							<label for="uPassword" class="input-group-addon glyphicon glyphicon-lock"></label>
                         
						</div> <!-- /.input-group -->
					</div> <!-- /.form-group -->

					<div class="checkbox">
						<a href="forgotpassword.php">Forgot Password?</a>
					</div> <!-- /.checkbox -->
					<div class="checkbox">
						<a href="register.php">Not a Member?</a>
					</div> <!-- /.checkbox -->
					<p id="loginControl" class="error-message"></p>
			</div> <!-- /.modal-body -->

			<div class="modal-footer">
				<button type="submit" class="form-control btn btn-primary">Log In</button>
                </form>
			</div> <!-- /.modal-footer -->

		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

    <div class="row align-text">
       <div class="col-md-12">
       
            <h2 class="">Current Live Court Availability</h2>
            <h3 id="numCourts">0 Courts Available</h3>
            <div class="col-sm-12">
                <img class="court-rs" id="court-4" src="img/court_icon_red.svg">
                <img class="court-rs" id="court-3" src="img/court_icon_red.svg">
                <img class="court-rs" id="court-2" src="img/court_icon_red.svg">
                <img class="court-rs" id="court-1" src="img/court_icon_red.svg">
                <br/>
                <img class="court-ls" id="court-8" src="img/court_icon.svg">
                <img class="court-ls" id="court-7" src="img/court_icon.svg">
                <img class="court-ls" id="court-6" src="img/court_icon.svg">
                <img class="court-ls" id="court-5" src="img/court_icon.svg">

				
				
				
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 court-reserve">
            <h3 class="center" style="text-align:center">View Reservations for:</h3>
			<div class="col-xs-6" style="padding: 10px;" > <button class="btn btn-primary" id="btnToday"  style="float: right;">Today</button></div>
			<div class="col-xs-6"  style="padding: 10px;"> <button class="btn btn-primary" id="btnTomorrow"  style="float: left;">Tomorrow</button></div>
			
            <h1 id="dateString"></h1>
            <p>Please Note: We keep 6 courts open for walk-ins. Only two courts may be booked at any given time. Each member has a maximum of 1 reservation per day. You can clear your reservation using the Clear My Reservations button above.</p>
            <div id="table1"></div>
            <div id="lists_container"></div>
            <dialog id="login_dialog">
                <h3>Hello World!</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Earum, inventore!</p>
                <button id="exit">Exit</button>
            </dialog>
        </div>
    </div><!-- /.row -->
</div><!-- /.container -->

<br/>

<?php include('footer.php'); ?>
</body>

</html>
