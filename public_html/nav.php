<?php 
	ini_set('display_errors','On');
    error_reporting('E_ALL');
	require_once "json-api/lib/PublicData.class.php";
	$ok = false;
	if(true)
	{
	 
		$stringURI = $_SERVER['REQUEST_URI'];
		//echo $stringURI;
		
		$parseDot = explode('.', $stringURI);
		//echo $parseDot[0];
		$parseSlash = explode('/', $parseDot[0]);
		
		$script = '<script>
		
		$(document).ready(function(){
		
		switch("'.$parseSlash[count($parseSlash) - 1].'"){

			case "index":
			$("#index").addClass("active");
			break;
			case "courts":
			$("#courts").addClass("active");
			break;
			case "about":
			$("#about").addClass("active");
			break;
			case "register":
			$("#register").addClass("active");
			break;
			case "stringing":
			$("#stringing").addClass("active");
			$("#info").addClass("active");
			break;
			case "events":
			$("#events").addClass("active");
			$("#info").addClass("active");
			break;
			case "notices":
			$("#notices").addClass("active");
			$("#info").addClass("active");
			break;
			case "":
			$("#index").addClass("active");
			break;
		}
		
		});
		</script> 
		
		';
		
		echo $script;
		
	}
?>
<nav class="navbar navbar-default float-left" role="navigation">

	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
			<span class="sr-only">Toggle navigation</span><span class="icon-bar"></span>
			<span class="icon-bar"></span><span class="icon-bar"></span>
		</button>


	</div>

	<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		<ul class="nav navbar-nav">
			<li id="index">
				<a href="index.php" class="nav-link">Home</a>
			</li>
			<li id="courts">
				<a href="courts.php" class="nav-link">Courts</a>
			</li>
			<li>
				<a href="http://www.adamsonstennisacademy.com/programs.php" target="_blank" class="nav-link">Programs</a>
			</li>
			<li>
				<a href="http://www.adamsonstennisacademy.com/lessons.php" target="_blank" class="nav-link">Lessons</a>
			</li>
			<li id="about">
				<a href="about.php" class="nav-link">About us</a>
			</li>
			<li id="register">
				<a href="register.php" class="nav-link">Register</a>
			</li>
			<li class="dropdown" id="info">
				<a id="info" href="" class="dropdown-toggle nav-link" data-toggle="dropdown" role="button" aria-expanded="false">Info <span class="caret"></span></a>
				<ul class="dropdown-menu" role="menu" style="background-color: #E8E8E8 !important;">
					<li id="stringing">
						<a href="stringing.php" class="">Stringing</a>
					</li>
					<li id="events">
						<a href="events.php" class="">Events</a>
					</li>
					<li id="notices">
						<a href="notices.php" class="">Notices</a>
					</li>
				</ul>
			</li>

		</ul>

		<!--
		<ul class="nav navbar-nav navbar-right">
			<li>
				<a href="#">Members</a>
			</li>
		</ul>
		-->
	</div>
</nav>