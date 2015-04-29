<?php
/**
 * global_functions.php
 * 
 * Miscellaneous functions to be used throughout all php files
 *
 * @author Kirk Davies
 *
 */


/**
 * // Get the user's ip address
 * @return String
 */
function getIPAddress()
{

	$ip = NULL;

	# get the ip address
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      	$ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      	$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    elseif (!empty($_SERVER['REMOTE_ADDR'])) {
      	$ip=$_SERVER['REMOTE_ADDR'];
    }

    return $ip;

}

/**
 * Respond with a status code and exit the script
 * @param Integer $code
 */
function fail($code) {
	
	if ($code >= 0)
		$code = -1;

    $arr = array('status' => $code);
    echo json_encode($arr);
    exit;

}

/**
 * Respond to the request by encoding the provided array
 * @param Array $arr
 */
function respond($arr) {
    
    if (isset($arr)) {
        echo json_encode($arr);
        exit;
    }
    else {
        fail();
    }

}

/**
 * Fix a string so that it can be displayed in an HTML file withou any issues
 * @param String $string 
 * @return String
 */
function outputHTML($string) {

	return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');

}

/**
 * Validate an email address
 * @param String $email 
 * @return Boolean
 * @author Kirk Davies
 */
function checkEmail($email) {

    # check the email format, empty email and email length
    if (filter_var($email, FILTER_VALIDATE_EMAIL) && $email !== '' && strlen($email) < 100) {

        return true;

    }

    return false;

}

/**
 * Validate an email address, including a check to make sure it
 * doesn't exist.
 * @param String $email 
 * @return Boolean
 */
function validateEmail($db, $email) {

    # check the email format, empty email and email length
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $email === '' || strlen($email) >= 100) {

        return false;

    }

    $db->bind("email", $email);
    $count = $db->single("SELECT COUNT(1) FROM mtc_member WHERE email = :email");
    
    # if count is false or 0, return false, otherwise return true
    if (isset($count)) {

        if ((int)$count === 0)
            return true;

    }

    return false;

}

/**
 * Validate an email address, including a check to make sure it
 * doesn't exist.
 * @param String $email 
 * @return Boolean
 */
function emailExists($db, $email) {

    # check the email format, empty email and email length
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $email === '' || strlen($email) >= 100) {

        return false;

    }

    $db->bind("email", $email);
    $count = $db->single("SELECT COUNT(1) FROM mtc_member WHERE email = :email");
    
    # if count is false or 0, return false, otherwise return true
    if (isset($count)) {

        if ((int)$count === 1)
            return true;

    }

    return false;

}
/**
 * Validate an email address, including a check to make sure it
 * doesn't exist.
 * @param String $email 
 * @return Boolean
 */
function validateSubscription($db, $email) {

    # check the email format, empty email and email length
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $email === '' || strlen($email) >= 100) {

        return false;

    }

    $db->bind("email", $email);
    $count = $db->single("SELECT COUNT(1) FROM mtc_subscriptions WHERE email = :email");
    
    # if count is false or 0, return false, otherwise return true
    if (isset($count)) {

        if ((int)$count === 0)
            return true;

    }

    return false;

}

/**
 * Validate a password
 * @param String $password
 * @return Boolean
 */
function validatePassword($password) {

	# check for empty password and length
	if ($password !== '' && strlen($password) < 100) {

		return true;

	}

	return false;

}

?>