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


    <title>March Tennis Club - Home</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/agency.css" rel="stylesheet">

    <!-- Google Maps API  -->
    <script src="https://maps.googleapis.com/maps/api/js"></script>
    <script src="js/map.js" type="text/javascript"></script>

	<!-- jQuery -->
	<script src="js/jquery.js"></script>

	<!-- Bootstrap Core JavaScript -->
	<script src="js/bootstrap.min.js"></script>


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

				?>

                <h1 class="tagline">Become a Member Today!</h1>
                <p class="under-tagline"> March tennis club welcomes you to be a proud member of our community. Sign up now to have access to all club member benefits.</p>
                <div id="join-btn-wrapper"><a href="register.php" class="btn btn-lg btn-success join-btn">Sign up today!</a></div>
            </div>
        </div>
    </div>
</header>
<!-- Page Content -->

<!-- About Section -->
        <div class="container">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading">About</h2>
                </div>
            <div class="row">
                <div class="col-lg-12">
                </br>
                    <ul class="timeline">
                        <li>
                            <div class="timeline-image">
                                <img class="img-circle img-responsive" src="img/mtc_logo.svg" alt="">
                            </div>
                            <div class="timeline-panel">
                                <div class="timeline-heading">
                                </br>
                                    <h4 class="subheading">MTC Mission Statement</h4>
<p>The March Tennis Club’s mission is to provide the entire community the opportunity to enjoy the game of tennis. Steadfast in its focus of offering programs and events geared toward all ages and abilities, the club offers everyone in the community the opportunity to share in the pleasures of this lifelong sport. With an active programs schedule, the club strives to reach out to participants of all ages and abilities, and always encourages community involvement</p>
                                </div>
                        <li class="timeline-inverted">
                            <div class="timeline-image">
                                <img class="img-circle img-responsive" src="img/cloud" alt="">
                            </div>
                            <div class="timeline-panel">
                                <div class="timeline-heading">
                                    <h4>2015 Club Open Hours </h4>
                                    <h5 class="subheading">Club is going to open on May 21st</h5>
                                    <p> For more information check the club home website</p>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="timeline-image">
                                <img class="img-circle img-responsive" src="img/ball" alt="">
                            </div>
                            <div class="timeline-panel">
                                <div class="timeline-heading">
                                    <h4>Board of Directors </h4>
                                    <h5> Jonathan Adamson</h5>
                                    <p> Manager </p>
                                    <p><a href="mailto:jon@adamsonsacademy.com" target="_top">jon@adamsonsacademy.com</a></p>
</p>
                                    <h5> Braden Penner</h5>
                                    <p> Senior Court Caption</p> 
                                </div>
                            </div>
                        </li>
                        <li class="timeline-inverted">
                            <div class="timeline-image">
                                <img class="img-circle img-responsive" src="img/ata.png" alt="">
                            </div>
                            <div class="timeline-panel">
                                <div class="timeline-heading">
                                    <h4>CLUB PRO's</h4>
                                </div>
                                <div class="timeline-body">
                                    <p>The Pro’s at the March Tennis Club are from Adamson’s Tennis Academy.  The Tennis Director is Jonathan Adamson. For more information about the pro’s please visit www.adamsonstennisacademy.com.
</p>
                                </div>
                            </div>
                        </li>
                        <li class="timeline-inverted">
                            <div class="timeline-image">
                              <img class="img-circle img-responsive" src="img/Be part.png" alt="">
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
   </div>
</section>

<?php include('footer.php'); ?>     

</body>

</html>
