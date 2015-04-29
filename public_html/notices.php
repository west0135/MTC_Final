<?php
    ini_set('display_errors','On');
    error_reporting('E_ALL');
	require_once "json-api/lib/PublicData.class.php";
	$ok = false;
?>
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


    <title>March Tennis Club - Current Notices</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">

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

<div class="container">
    <h1>Current Notices</h1>
    <?PHP

				if(true)
				{
					$mtc_notice = new MtcNotice();
					$array = $mtc_notice->getList();
					//print_r($array["fields"][0]);
					if($array[RETVAL::STATUS] == RETVAL::DB_SUCCESS)
					{

						$content = $array["fields"][0]->content;
						$ok = true;
						//print_r($array["fields"][1]);
						//echo (count($array["fields"]));
					}
					else
					{
						$err = $array[RETVAL::ERR_MSG];
						$xtErrMsg = $array[RETVAL::EXTND_ERR_MSG];
					}
				}


            for ($x=0; $x<(count($array["fields"])); $x++){
                $field = $array["fields"][$x];
            	//echo '
              
                    //<div class="col-lg-12 contentContainer well">';
            //print_r($array["fields"][$x]);
				
				echo '<div style="margin:10px">';
				echo '<h3>'.$field["title"].'</h3>';
                echo $field["content"];
				//echo date("Y-M-d  H:i:s", strtotime( $field["event_date_time"] ));
            	//echo $field["content"];
				//echo '  '.$field["event_date_time"].'';
            	//echo '<a class="pull-right btn btn-success btn-sm" href="#" role="button">Register</a>
                echo '</div>';
            
            }
            ?>
    
</div><!-- /.container -->

<br/>

<?php include('footer.php'); ?>  

</body>

</html>
