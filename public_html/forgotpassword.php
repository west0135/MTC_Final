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


    <title>March Tennis Club - Forgot Password</title>

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
	
	<script type="text/javascript" src="js/forgotpassword.js"></script>


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

                <?php include('nav.php');?>

                <h1 class="tagline">Become a Member Today!</h1>
                <p class="under-tagline"> March tennis club welcomes you to be a proud member of our community. Sign up now to have access to all club member benefits.</p>
                <div id="join-btn-wrapper"><a href="register.php" class="btn btn-lg btn-success join-btn">Sign up today!</a></div>
            </div>
        </div>
    </div>
</header>

<!-- Page Content -->

<div class="container">

	<br />
	<div class="centered" style="margin: auto; max-width: 500px;">
    <div class="row">
       <div class="col-md-12">
            <h2 id="forgotYourPassword">Forgot your Password?</h2>
            <form id="forgotPassword-form">
              <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" class="form-control" id="email" placeholder="Enter email">
                <p id="emailControl" class="error-message"></p>
              </div>
              <button type="submit" class="btn btn-success">Submit</button>
            </form>
            <form id="secretAnswer-form" style="display:none;">
            	<h3>Secret Question</h3>
              <div class="form-group">
                
                <label id="secretQuestion" for="answer"></label>
                <input type="text" class="form-control" name="answer" id="answer" placeholder="Answer...">
                <p id="answerControl" class="error-message"></p>
              </div>
              <button type="submit" class="btn btn-success">Submit</button>
            </form>
            <form id="newPassword-form" style="display:none;">
            	<h3>Enter A New Password</h3>
              <div class="form-group">
                <label for="password1">Password</label>
                <input type="password" class="form-control" name="password1" id="password1" placeholder="Password...">
                <p id="password1Control" class="error-message"></p>
              </div>
              <div class="form-group">
                <label for="password2">Confirm Password</label>
                <input type="password" class="form-control" name="password2" id="password2" placeholder="Confirm Password...">
                <p id="password2Control" class="error-message"></p>
              </div>
              <button type="submit" class="btn btn-success">Submit</button>
            </form>
        </div>
        </div>
    </div>
    
</div><!-- /.container -->

<br/>

<?php include('footer.php'); ?>
</body>

</html>
