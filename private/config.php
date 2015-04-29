<?php
/**
 * config.php
 * 
 * Set configuration settings to be used in all php files.
 * Set application-wide constants.
 * Initialize the database object
 * Handle uncaught exception.
 *
 * @author Kirk Davies
 *
 */

# set error reporting to report all errors
error_reporting(E_ALL);

# set the php.ini file to display errors
ini_set('display_errors','On');

# set functions to handle all uncaught exceptions and error

register_shutdown_function("check_for_fatal");
set_error_handler("log_error");
set_exception_handler("log_exception");

# get the user's timezone (default is eastern)
$timezone = empty($_POST["timezone"]) ? "America/New_York" : trim($_POST["timezone"]);

# set the timezone as a constant
define("TIMEZONE", $timezone);

# set the current URL
define("SURL", "http://www.clients.edumedia.ca/to_be/");

# include the database class
require "Db.class.php";

# create a database object
$db = new Db();


/**
* Error handler, passes flow over the exception logger with new ErrorException.
*/
function log_error($num, $str, $file, $line, $context = null) {

    log_exception(new ErrorException($str, 0, $num, $file, $line));
    
}

/**
 * Handle all uncaught exceptions
 * @param Exception $e
 * @author Kirk Davies
 */
function log_exception($e) {

	# log the exception message
	error_log("Type: " . get_class($e) . ", Message: " . $e->getMessage() . ", File: " . $e->getFile() . ", Line: " . $e->getLine());

	# end the script with an failure status code
	fail(-1);

}

/**
 * Checks for a fatal error, work around for set_error_handler not working on fatal errors.
 */
function check_for_fatal() {

    $error = error_get_last();

    if ($error["type"] == E_ERROR)
        log_error($error["type"], $error["message"], $error["file"], $error["line"]);

}

?>