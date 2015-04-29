<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro|Oxygen' rel='stylesheet' type='text/css'>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
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
                
            </div>
        </div>
    </div>
</header>

<!-- Page Content -->
<!----Sign up Form -->
<div class="container-fluid">
    <section class="container">
		<div class="container-page">				
			<div class="col-md-6">
				<h3 class="dark-grey">Registration</h3>
				<div class="form-group col-lg-6">
                 <form id="RegistrationForm">
					<label>First Name </label>
					<input type="text" name="firstName" class="form-control" id="firstName" value="" required>
                    <p id="firstNameControl" class="error-message"></p>
				</div>
                
               <div class="form-group col-lg-6">
					<label>Last Name</label>
					<input type="text" name="lastName" class="form-control" id="lastName" value="" required>
                    <p id="lastNameControl" class="error-message"></p>
				</div>
				
                 <div class="form-group col-lg-6">
					<label>Address</label>
					<input type="text" name="address" class="form-control" id="address" value="">
					<p id="addressControl" class="error-message"></p>
                </div>
				
                 <div class="form-group col-lg-6">
					<label>City</label>
					<input type="text" name="city" class="form-control" id="city"  value="">
					<p id="cityControl" class="error-message"></p>
                </div>
                 <div class="form-group col-lg-6">
					<label>Postal Code</label>
					<input type="" name="postalCode" class="form-control" id="postalCode" value="">
					<p id="postalCodeControl" class="error-message"></p>
                </div>
                 <div class="form-group col-lg-6">
					<label>Home Phone</label>
					<input type="" name="homePhone" class="form-control" id="homePhone" value="">
					<p id="homePhoneControl" class="error-message"></p>
                </div>
                 <div class="form-group col-lg-6">
					<label>Business Phone</label>
					<input type="" name="businessPhone" class="form-control" id="businessPhone" value="" >
			</div>				
				<div class="form-group col-lg-6">
					<label>Email</label>
					<input type="" name="email" class="form-control" id="email" value="" required>
					<p id="emailControl" class="error-message"></p>
                </div>
              
			   <div class="form-group col-lg-6">
					<label>Password</label>
					<input type="password" name="passoword1" class="form-control" id="password1" value="" required>
                    <p id="password1Control" class="error-message"></p>
				</div>
                
                <div class="form-group col-lg-6">
					<label>Confirm Password</label>
					<input type="password" name="passoword2" class="form-control" id="password2" value="" required>
                    <p id="password2Control" class="error-message"></p>
				</div>
				
				<div class="form-group col-lg-6">
					<label>Password Reset Secret Question</label>
					<input type="" name="passwordHint" class="form-control" id="passwordHintQuestion" value="">
                  <p id="passwordHintQuestionControl" class="error-message"></p>
				</div>	
                
               <div class="form-group col-lg-6">
					<label>Secret Question Answer</label>
					<input type="" name="passwordHintAnswer" class="form-control" id="passwordHintAnswer" value="">
                   <p id="passwordHintAnswerControl" class="error-message"></p>
				</div>
                
                <div class="form-group col-lg-12 dependantLabel hide">
		          <label>Names of all family members covered by this application</label>
                  <p>(Please provide date of birth for all juniors and students)</p>
				</div>
                    
                <div class="form-group col-lg-6">
					<label class="dependantLabel hide">First Dependant Name</label>
					<input type="" class="dependantName dn1 form-control hide" name="firstDependantName" class="form-control" id="firstDependantName" value="">
				</div>	
                
               <div class="form-group col-lg-6">
					<label class="dependantLabel hide">First Dependant DOB</label>
					<input type="date" class="dependantDOB ddob1 form-control hide" name="firstDependantDOB" class="form-control" id="firstDependantDOB" value="">
				</div>
                    
                <div class="form-group col-lg-6">
					<label class="dependantLabel hide">Second Dependant Name</label>
				    <input type="" class="dependantName dn2 form-control hide" name="secondDependantName" class="form-control" id="secondDependantName" value="">
				</div>	
                
               <div class="form-group col-lg-6">
					<label class="dependantLabel hide">Second Dependant DOB</label>
					<input type="date" class="dependantDOB ddob2 form-control hide" name="secondDependantDOB" class="form-control" id="secondDependantDOB" value="">
				</div>
                    
                <div class="form-group col-lg-6">
					<label class="dependantLabel hide">Third Dependant Name</label>
				    <input type="" class="dependantName dn3 form-control hide" name="thirdDependantName" class="form-control" id="thirdDependantName" value="">
				</div>	
                
               <div class="form-group col-lg-6">
					<label class="dependantLabel hide">Third Dependant DOB</label>
					<input type="date" class="dependantDOB ddob3 form-control hide" name="thirdDependantDOB" class="form-control" id="thirdDependantDOB" value="">
				</div>
                    
                <div class="form-group col-lg-6">
					<label class="dependantLabel hide">Fourth Dependant Name</label>
				    <input type="" class="dependantName dn4 form-control hide" name="fourthDependantName" class="form-control" id="fourthDependantName" value="">
                
				</div>	
                
               <div class="form-group col-lg-6">
					<label class="dependantLabel hide">Fourth Dependant DOB</label>
					<input type="date" class="dependantDOB ddob4 form-control hide" name="fourthDependantDOB" class="form-control" id="fourthDependantDOB" value="">
				</div>
                    
                <div class="form-group col-lg-6">
					<label class="dependantLabel hide">Fifth Dependant Name</label>
					<input type="" class="dependantName dn5 form-control hide" name="fifthDependantName" class="form-control" id="fifthDependantName" value="">
                
				</div>	
                
               <div class="form-group col-lg-6">
					<label class="dependantLabel hide">Fifth Dependant DOB</label>
					<input type="date" class="dependantDOB ddob5 form-control hide" name="fifthDependantDOB" class="form-control" id="fifthDependantDOB" value="">
				</div>
                    
                <div class="form-group col-lg-6">
					<label class="dependantLabel hide">Sixth Dependant Name</label>
					<input type="" class="dependantName dn6 form-control hide" name="fifthDependantName" class="form-control" id="fifthDependantName" value="">
                
				</div>	
                
               <div class="form-group col-lg-6">
					<label class="dependantLabel hide">Sixth Dependant DOB</label>
					<input type="date" class="dependantDOB ddob6 form-control hide" name="fifthDependantDOB" class="form-control" id="fifthDependantDOB" value="">
				</div>			
			
			</div>
<!---Registration Submission-->
            <div class="col-md-6">
            <div class="col-md-12">
                <h3 class="dark-grey">Membership Category</h3>
                    <h4>Payment is not available online, please visit the clubhouse to make your payment after completing registration.</h4>
                <div class="leftPadding">
                <div id="membershipCategoryList">
                <div id="1" class="lookUpItem">
                <input type="radio" name="membership_category" value="1" data-price="225"> Family With Children Under 18: <strong>$225</strong>
                </div>		           
                <div id="2" class="lookUpItem">
                <input type="radio" name="membership_category" value="2" data-price="295"> Family With Children Over 18: <strong>$295</strong>
                </div>
                <div id="3" class="lookUpItem">
                  <input type="radio" name="membership_category" value="3" data-price="130"> Senior : <strong>$130</strong>
                 </div>
                <div id="4" class="lookUpItem">
                  <input type="radio" name="membership_category" value="4" data-price="145"> Adult : <strong>$145</strong>
                </div>
                <div id="5" class="lookUpItem">
                 <input type="radio" name="membership_category" value="5" data-price="200">  Couple : <strong>$200</strong>
                </div>
                <div id="6" class="lookUpItem">
                <input type="radio" name="membership_category" value="6" data-price="95"> Student (18 - 23) : <strong>$95</strong>
                </div>
                <div id="7" class="lookUpItem">
                 <input type="radio" name="membership_category" value="7" data-price="60"> Junior (under 18) : <strong>$60</strong>
                </div>
                <p id="membershipCategoryControl" class="error-message"></p>
                </div>
            </div>
                <div class="col-sm-6">
                    <input type="checkbox" id="donation" class="checkbox" />
                    <label>Yes, I will include an additional $10 donation to the Club House Improvement Fund</label> 
                </div>
				
                <div class="col-sm-6">
				    <input type="checkbox" id="newMember" class="checkbox" />
                    <label>New Member? (If yes, include $60 Initiation Fee)</label>
                </div>
		
                <br/><br/>
                <div class="col-md-12">
					
					<input type="checkbox" id="termsAgree" class="checkbox" />
                    <label>Terms and Conditions</label>
                    <p>I/We hereby agree that the March Tennis Club will not be held responsible for any loss or injury incurred while on the premises.  I/We hearby agree to abide by the club rules and code of conduct and give permission for my/our picture to be taken for website and promotional purposes.</p>
			
				<button id="submit" type="submit" class="btn btn-primary">Register</button>
                <label id="confirmation"></label>
                    </div>
		</div>
        </div>
        </form>
	</section>
</div>
<?php
			
					include('footer.php');

				?>
        <!-- jQuery -->
<script src="js/jquery.js"></script>
<script src="js/signup.js"></script>
<script src="js/jquery-1.11.2.min"></script> 
<!-- load jquery
<!-- Bootstrap Core JavaScript -->
<script src="js/bootstrap.min.js"></script>
</body>
</html>
