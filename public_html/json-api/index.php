<?php
	//Important if returning json
	header('Content-type: application/json; charset=utf-8'); 
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
 	//Allow mobile ajax calls
 	header('Access-Control-Allow-Origin: *');
	
	//All defines and includes are located here
	//note $logger is also initialized here - use it anywhere in the global scope
	include_once("boot.php");
	
	$logger->debug("DEBUG: [" . print_r($_REQUEST,true) . "]END");

	if(isset($_REQUEST[RETVAL::METHOD])) //method can be called by POST or GET - TODO is this a good idea?
	{
		$method = $_REQUEST[RETVAL::METHOD];
		//Tom changed Feb 16 2015
		$arr = explode(".",$method);
		$class = $arr[0];
		$mthd = $arr[1];
		
		//$logger->debug("call[" . $class . "." . $mthd . "]");
		$ok = 1;
		if($class == "MtcMemberSecure" && $mthd == "create")
		{
		}
		else
		{
			switch($mthd)
			{
				case "create":
				case "update":
				case "delete":
				case "safeCreate":
				case "setMemberPermission":
				case "runCannedQuery":
					$security = new Security($class, $mthd);
					if($security->checkPermissions())
					{
						$ok = $security->checkSession();
						if($ok == -1)
						{
							$retVal = array(RETVAL::STATUS => RETVAL::DB_ERROR, RETVAL::ERR_MSG => 'Failed Authentication.', 
											RETVAL::EXTND_ERR_MSG => $class . "." . $mthd);
						}
						elseif($ok == 0)
						{
							$retVal = array(RETVAL::STATUS => RETVAL::DB_ERROR, RETVAL::ERR_MSG => 'Bad Authentication Params.', 
											RETVAL::EXTND_ERR_MSG => $class . "." . $mthd);
						}
					}
					else
					{
						$ok = -2;
						$retVal = array(RETVAL::STATUS => RETVAL::DB_ERROR, RETVAL::ERR_MSG => 'Permission Denied.',
											RETVAL::EXTND_ERR_MSG => $class . "." . $mthd);
					}
				break;
				
			}
		}
		if($ok == 1)
		{
			$object = new $class ();
			$retVal = $object->{$mthd}($_POST);
		}
	}
	else
	{
		$retVal = array(RETVAL::STATUS => RETVAL::DB_ERROR,
				RETVAL::ERR_MSG => 'No "method" value in request.', RETVAL::EXTND_ERR_MSG => "Check POST data content.");
	}
	$json = json_encode($retVal);
	$logger->debug("return json: " . $json);
	echo $json;	
?>