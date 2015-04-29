<?php

/**
 * login-signup.php
 * 
 * Allows a user to login or sign up to __________
 * Allows a user to sign up for newsletter subscription
 *
 * @author Kirk Davies
 *
 */

//Tom Here added header information 
//Important if returning json
header('Content-type: application/json; charset=utf-8'); 
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
//Allow mobile ajax calls
header('Access-Control-Allow-Origin: *');

require "../../private/global_functions.php";
require "../../private/config.php";

//require "../private/password.php";

// get POST variables
$action = !empty($_POST["func"]) ? trim($_POST["func"]) : fail(-1);

switch ($action) {
    case 'addSubscription':
        $email = !empty($_POST["email"]) ? strtolower(trim($_POST["email"])) : fail(-1);
        addSubscription($db, $email);
        break;
    case 'loginUser':
        $email = !empty($_POST["email"]) ? strtolower(trim($_POST["email"])) : fail(-1);
        $password = !empty($_POST["password"]) ? trim($_POST["password"]) : fail(-1);
		loginUser($db, $email, $password);
        break;
	case 'logoutUser':
		$userid = !empty($_POST["userid"]) ? trim($_POST["userid"]) : fail(-1);
       $ukey = !empty($_POST["ukey"]) ? trim($_POST["ukey"]) : fail(-1);
	   logout($db, $userid, $ukey);
	case 'forgotPassword1':
		
		$email = !empty($_POST["email"]) ? strtolower(trim($_POST["email"])) : fail(-1);
	   forgotPassword1($db, $email);
	case 'forgotPassword2':
		$answer = !empty($_POST["answer"]) ? trim($_POST["answer"]) : fail(-1);
		$email = !empty($_POST["email"]) ? strtolower(trim($_POST["email"])) : fail(-1);
	   forgotPassword2($db, $email, $answer);
	case 'forgotPassword3':
		$answer = !empty($_POST["answer"]) ? trim($_POST["answer"]) : fail(-1);
		$password = !empty($_POST["password"]) ? trim($_POST["password"]) : fail(-1);
		$email = !empty($_POST["email"]) ? strtolower(trim($_POST["email"])) : fail(-1);
	   forgotPassword3($db, $email, $answer, $password);
	   
	case 'clearReservations':
		$userid = !empty($_POST["userid"]) ? trim($_POST["userid"]) : fail(-1);
       $ukey = !empty($_POST["ukey"]) ? trim($_POST["ukey"]) : fail(-1);
	   clearReservations($db, $userid, $ukey);
    default:
        fail(-1);
        break;
}

// close the db connection
$db->CloseConnection();

// if the script reaches this far, end the script
fail(-1);

function loginUser ($db, $email, $password) {
	
    if (checkEmail($email)) {
        if (validatePassword($password)) {
			$db->bindMore(array("email" => $email));
			$arr = $db->row("SELECT member_id, email, password FROM mtc_member WHERE email = :email");
           $password1 = $arr['password'];
		   $cool = md5($password) === $password1;
		   //$cool = password_verify($password, $password1);
			if(!empty($arr) && $cool) {
				// successful login
				$userid = $arr['member_id'];
				//Get Permissions for User
				$db->bindMore(array("member_id" => $userid));
				//TOM here added permissions to return
				$perms = $db->single("SELECT `permissions` FROM `mtc_permissions` WHERE member_id = :member_id");
                $ukey = addSession($db, $userid);
				  if ($ukey) {
					# set the array with the new user's info
					$arg = array('status' => 1, 'userid' => $userid, 'email' => $email, 'ukey' => $ukey, 'permissions' => $perms);
					respond($arg); 
				  }
            	  
            } else {
 				// failed login
                //record_failed_login($username);
                //$message = "Username/password combination not found.";
				  $arg = array('status' => 0, 'message' => 'Username/password combination not found.');
            	  respond($arg); 
            }
            

                $db->rollBack();
                fail(-1);
			
		
		}else {
           // set the array with the message confirming an invalid password 
            $arg = array('status' => 0, 'message' => 'That password is invalid');
            respond($arg); 
        }


     
    }else {
        // set the array with the message confirming an invalid email 
        $arg = array('status' => 0, 'message' => 'That email address is invalid');
        respond($arg);
    }


}

function addSubscription ($db, $email) {
    
    if (validateSubscription($db, $email)) {
        // begin the databse transaction
            $db->beginTransaction();

            # add the user to the database
            $db->bindMore(array("email" => $email));
            $insert = $db->query("INSERT INTO mtc_subscriptions (email) VALUES (:email)");
            
            if ($insert > 0) {
                # send the user a confirmation email
                $first_name = 'Kirk';
		sendEmailConfirmation($db, $email, $first_name);

                # set the array with the new user's info
                $arr = array('status' => 1, 'message' => 'Thank you for your subscription');
                $db->commit();
                
                respond($arr);

            } else {    

                $db->rollBack();
                fail(-1);

            }

    }else {
        // Security through obscurity, even though the email has already signed up; let user know it worked 
        $arg = array('status' => 1, 'message' => 'Thank you for your subscription');
        respond($arg);
    }
    /*
    // Enter the email where you want to receive the notification when someone subscribes
    $emailTo = 'kirkdavies@rogers.com';
    $your_feedbackmail = "to_be@tobe.com";
    $subscriber_email = $email;

    if(!checkEmail($email)) {
        // set the array with the message confirming a valid subscription 
        $arg = array('status' => 0, 'message' => 'Insert a valid email address!');

        respond($arg);
    }
    else {
        // set the array with the message confirming a valid subscription 
        $arg = array('status' => 1, 'message' => 'Thanks for your subscription!');

        respond($arg);

        // Send email
        $subject = 'New Subscriber!';
        $body = "You have a new To Be || ! To Be subscriber!\n\nEmail: " . $email;
        $headers = "From: $your_feedbackmail" . "\r\n" . "Reply-To: $email" . "\r\n" ;
        mail($emailTo, $subject, $body, $headers);
    }
    */
}
function clearReservations($db, $userid, $ukey) {
	
	$db->bindMore(array("userid" => $userid, "ukey" => $ukey));
	
		$exists = $db->single("SELECT EXISTS(SELECT 1 FROM mtc_user_session WHERE userid = :userid AND ukey = :ukey)");

		# ensure a record exists for the email and first_name
		if (!empty($exists)) {
			
			error_log("\nDELETE FROM mtc_court_reservation WHERE member1_id = '$userid'", 3, "my-errors.log");
			
			$db->bindMore(array("userid" => $userid));
			$db->query("DELETE FROM mtc_court_reservation WHERE member1_id = :userid");
			
				$arr = array("status" => 1);
				respond($arr);

			
		}
		

}

function logout($db, $userid, $ukey) {

	$db->bindMore(array("userid" => $userid, "ukey"=>$ukey));
	$db->query("DELETE FROM mtc_user_session WHERE userid = :userid AND ukey = :ukey");
	
	# set the array with the new user's info
   $arr = array('status' => 1, 'message' => "Logout Successfull");
	respond($arr);
}

function forgotPassword1 ($db, $email) {
	
    if (emailExists($db, $email)) {
		$db->bindMore(array("email" => $email));
		$exists = $db->single("SELECT EXISTS(SELECT 1 FROM mtc_member WHERE email = :email)");

		# ensure a record exists for the email and first_name
		if (!empty($exists)) {
			
			$db->bindMore(array("email" => $email));
			$passwordHint = $db->single("SELECT password_hint FROM mtc_member WHERE email = :email");
			
			if ($passwordHint) {
				$arr = array("status" => 1, "passwordHint" => $passwordHint);
				respond($arr);
			}
			
		}

	}	

   
}

function forgotPassword2 ($db, $email, $answer) {
	
    if (emailExists($db, $email)) {
		
		$db->bindMore(array("email" => $email, "answer" => md5($answer)));
		$exists = $db->single("SELECT EXISTS(SELECT 1 FROM mtc_member WHERE email = :email AND password_hint_answer = :answer)");

		# ensure a record exists for the email and first_name
		if (!empty($exists)) {
			
			$arr = array("status" => 1);
			respond($arr);
			
			
		}else {
			$arr = array("status" => 0, "message" => "That is incorrect");
			respond($arr);
		}

	}	

   
}

function forgotPassword3 ($db, $email, $answer, $password) {
	
    if (emailExists($db, $email)) {
		$answer = md5($answer);
		$db->bindMore(array("email" => $email, "answer" => $answer));
		$exists = $db->single("SELECT EXISTS(SELECT 1 FROM mtc_member WHERE email = :email AND password_hint_answer = :answer)");

		# ensure a record exists for the email and first_name
		if (!empty($exists)) {
			
			$newPassword = md5($password);	//G

			$db->bindMore(array("password" => $newPassword, "email" => $email, "answer" => $answer));
			
			$update = $db->query("UPDATE mtc_member SET password = :password WHERE email = :email AND password_hint_answer = :answer");
			
			error_log("\n UPDATE mtc_member SET password = '$newPassword' WHERE email = '$email' AND password_hint_answer = '$answer'", 3, "my-errors.log");
			
			
			if ($update >= 0) {
				
				/*
				require "Email.class.php";

				# send welcome email
				$newPasswordEmail = new email();
				$sent = $newPasswordEmail->sendNewPassword($first_name, $email, $newPassword);
				*/

					$arr = array("status" => 1, "email" => $email);
					respond($arr);

				
			}
			else {
				# email and/or first name not found
				fail(-2);
			}
			
			
		}else {
			$arr = array("status" => 0, "message" => "That is incorrect");
			respond($arr);
		}

	}	

   
}

/**
 * 1. Generate a confirmation code
 * 2. Insert the code with an associated email into the DB
 * 3. Send the confirmation email with a link to confirm
 * @param DB $db 
 * @param String $email 
 * @param String $first_name 
 * @return Boolean
 */
function sendEmailConfirmation($db, $email, $first_name) {

	# generate a unique confirmation code
    $confirmation_code = md5(uniqid(rand()));

	$db->bindMore(array("email" => $email, "confirmation_code" => $confirmation_code));
	$insert = $db->query("INSERT INTO mtc_email_confirm (email, confirmation_code) VALUES (:email, :confirmation_code)");

	if ($insert > 0) {

		require "../private/Email.class.php";

		# send welcome email
		$welcomeEmail = new email();
		return $welcomeEmail->sendWelcomeEmail($first_name, $email, $confirmation_code);

	}

	return false;

}

//Functions for this page
/**
 * Insert a session into the database for the user
 * @param DB $db 
 * @param Integer $userid 
 * @return String
 */
function addSession($db, $userid) {

    # get a unique key to store in the sessions table
    $ukey = generateRandomKey();

    # get the user's IP address
    $ip_address = getIPAddress();

    # get the user agent
    $user_agent = NULL;
    if (!empty($_POST["user_agent"])) {
        $user_agent = substr($_POST["user_agent"], 0, 200);
    }

    $db->bindMore(array("userid" => $userid, "ukey" => $ukey, "ip_address" => $ip_address, "device" => $user_agent));
    $insert = $db->query("INSERT INTO mtc_user_session (userid, ukey, ip_address, device) VALUES (:userid, :ukey, :ip_address, :device)");
    
    # if the session is successfully inserted, return the ukey
    return ($insert > 0) ? $ukey : NULL;

}

function checkSession ($userid, $ukey) {
	
	$auth = false;

	$db->bindMore(array("userid" => $userid, "ukey" => $ukey));
	$user_sessionid = $db->single("SELECT userid FROM mtc_user_sessions WHERE userid = :userid AND ukey = :ukey");

	if (!empty($user_sessionid)) {

		# update the last accesssed time to now
		$db->bind("userid", $user_sessionid);
		$db->query("UPDATE mtc_user_sessions SET modified = CURRENT_TIMESTAMP WHERE userid = :userid");
		$auth = true;

	}
	
	return $auth;
}
/**
 * Generate a random key to be stored for the user's session
 * @return String ukey
 */
function generateRandomKey() {

    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $key = '';

    for ($i = 0; $i < 13; $i++) {
        $key .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $key;

}
?>