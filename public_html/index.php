<?php
    ini_set('display_errors','On');
    error_reporting('E_ALL');
	require_once "json-api/lib/PublicData.class.php";
	//Tom Here April 22 1:47
	require_once "json-api/lib/PublicEventsHelper.class.php";
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


    <title>March Tennis Club - Home</title>

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
                
                <?php include('nav.php'); ?>

                <h1 class="tagline">Become a Member Today!</h1>
                <p class="under-tagline">March tennis club welcomes you to be a proud member of our community. Sign up now to have access to all club member benefits.</p>
                <div id="join-btn-wrapper"><a href="register.php" class="btn btn-lg btn-success join-btn">Sign up today!</a></div>
            </div>
        </div>
    </div>
</header>

<!-- Page Content -->

<div class="container">
	<div class="row">
        <div class="col-sm-9">
        	<div class="row">
            <img id="court-pics" src="img/court_pics.png">
            <h2 class="">What We Do</h2>
            <p>Established in 1975, the March Tennis Club is dedicated in providing an atmosphere concentrated on community participation and development of the active family. The club offers everyone of all ages in the community to share in the pleasures of tennis by offering various social events, round robins , and instructional programs. For more details please browse our website. If you have any questions or concerns please email info@marchtennisclub.com</p>

            <p>
                <a class="btn btn-primary btn-lg float-right" href="register.php">Sign up today!</a>
            </p>
            </div>
            <div class="row">
                <?PHP
				//$ata_event = new Event();
				//$array = $ata_event->getList();
				
				//Tom Here
				$ata_event = new EventsHelper();
				$array = $ata_event->getLatestEvents();
				
				
				if($array[RETVAL::STATUS] == RETVAL::DB_SUCCESS)
				{
					$content = $array["fields"][0]->content;
					//Only allow 2 content blocks TODO Events should be ordered by date
					$count = count($array["fields"]);
                    //Tom here
					$limit = $count < 2 ? $count : 2;
					
 					/*
					if ($count < 2){
                         $countLess2 = $count;
                    }
                    else{
                        $countLess2 = $count - 2;

                    }
					*/
 
					//for ($x=$count-1; $x >= $countLess2; $x--)
					//Tom here
					for($x=0; $x < $limit; $x++)
					{
						$field = $array["fields"][$x];
						echo '<div class="col-md-6 event index-events">';
                        echo '<h3>'.$field["event_name"].'</h3>
						<div class="thumbnail-php mtc_events">';
						
						echo $field["content"];
						//if ($x==$countLess2)
						//Tom here
						if($x == $limit-1)
						{
							echo '<a class="btn btn-primary" href="events.php" style="float:right;">All Events &raquo;</a><br/>';
                    	}
                    	echo '</div></div>';
                	}
				}
				else
				{
					$err = $array[RETVAL::ERR_MSG];
					$xtErrMsg = $array[RETVAL::EXTND_ERR_MSG];
				}
                ?>
                <div class="row">
                <div class="col-md-12 notice-col">
                        <?PHP
                        $mtc_notice = new MtcNotice();
                        $array = $mtc_notice->getList();
                        if($array[RETVAL::STATUS] == RETVAL::DB_SUCCESS)
                        {
                            for ($x=0; $x<(count($array["fields"]))&&$x<3; $x++)
                            {
                                $field = $array["fields"][$x];
                                echo '<div class="col-md-4 notice">';
								echo '<h3>'.$field["title"].'</h3>';
                                echo $field["content"];
                                echo '</div>';
                            }
                        }
                        else
                        {
                            $err = $array[RETVAL::ERR_MSG];
                            $xtErrMsg = $array[RETVAL::EXTND_ERR_MSG];
                        }
                        ?>
                        <a class="btn btn-primary" href="notices.php" style="float:right;">All Notices &raquo;</a><br/><br/>
   					</div>
                   </div> 
             </div>
        </div>
        
        <div class="col-md-3">
     		<div class="row">
        	<div class="side-info">
				<img class="thumb-icon" src="img/court_icon.svg">
				<h3>Reserve your court now!</h3>
      			<p>At the March Tennis Club, members can book a court online 24 hours in advance. Click the link below to book your court today!</p>
           	<a class="btn btn-primary" href="courts.php">Book a court</a><br/>
                <br/>
           </div>
           
           <div class="side-info">
             	<img class="thumb-icon" src="img/racquet_icon.svg">
				<h3>Want to improve your game?</h3>
				<p>We offer private and semi-private lessons as well as various programs to help you become a better player. These training programs are hosted by the Adamson Tennis Academy.</p>
				<a class="btn btn-primary" href="http://adamsonstennisacademy.com/lessons.php">Learn more &raquo;</a><br/><br/>
                </div>
			</div>
        </div>
            
            
    </div>
	
</div>

<?php include('footer.php'); ?>    

</body>

</html>
