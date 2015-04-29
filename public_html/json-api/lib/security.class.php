<?php

class Security
{
	const NONE = 0;
	const MEMBERSHIP = 1; 		
	const RESERVATION = 2; 		
	const COURT_CAPTAIN = 3;	
	const EDITOR = 4; 	
	const ADMIN = 5;				

	private $my_class;
	private $mthd;
	private $db;
	
	private $logger;
	
	public function __construct($class, $mthd)
	{
		$this->my_class = $class;
		$this->mthd = $mthd;
		$this->db = 	new Db();
		
		$this->logger = new Log4Me(OBJECT_LOG_STATUS,"log.txt");
		$this->logger->setContext("OBJECT", $_SERVER['PHP_SELF']);
	}
	
	//returns true if sufficient permissions
	public function checkPermissions()
	{
		$userid = !empty($_POST["userid"]) ? strtolower(trim($_POST["userid"])) : NULL;
		if(!$userid)
		{
			return false;
		}
		$this->db->bindMore(array("member_id" => $userid));
		$perms = $this->db->single("SELECT `permissions` FROM `mtc_permissions` WHERE member_id = :member_id");
		$this->logger->debug("member_id: " . $userid . " PERMISSIONS:" . print_r($perms, true));
		if(empty($perms)) //if 0 or not set
		{
			return false;
		}
		else
		{
			$this->logger->debug("class:" . $this->my_class . " mthd:" . $this->mthd);
			$retval = false;
			if($this->my_class == "MtcCourtReservationHelper")
			{
				//$this->logger->debug("DEBUG1");
				switch($this->mthd)
				{
					case "safeCreate":
						//$this->logger->debug("DEBUG2");
						//Need reservation and member permissions
						$retVal = $perms >= self::RESERVATION;
						break;
					default:
						$retVal = $perms >= self::COURT_CAPTAIN; 
						break;
				}
			}
			elseif($this->my_class == "CannedQueryHelper" && $this->mthd == "runCannedQuery")
			{
				//$this->logger->debug("retval = " . ($perms >= self::EDITOR) . " perms: " . $perms);
				$retVal = $perms >= self::EDITOR; //Editors and admins only
			}
			elseif($this->my_class == "MtcCourtReservation" && $this->mthd == "delete") //Reservers can delete their own reservations NOTE
			{
				//$this->logger->debug("DEBUG2.5");
				$retVal = $perms >= self::RESERVATION; 
			}
			elseif($this->my_class == "MtcPermissions") //Only Admins can change permission
			{
				//$this->logger->debug("DEBUG3");
				$perm_level = 4; //above Court Captain by default
				if(isset($_POST['permissions']))
				{
					$perm_level = $_POST['permissions'];
				}
				if($perm_level <= 3) //Court Captains can change up to level 3
				{
					$retVal = $perms >= self::COURT_CAPTAIN;
				}
				else
				{
					$retVal = $perms >= self::ADMIN;
				}
			}
			elseif($this->my_class == "MtcMember") //Court Captains can change members
			{
				//$this->logger->debug("DEBUG4");
				$retVal = $perms >= self::COURT_CAPTAIN; 
			}
			elseif($this->my_class == "MtcMemberSecure" && $this->mthd == "create")
			{
				$this->logger->debug("MtcMemberSecure");
				return true;
			}
			else
			{
				//$this->logger->debug("DEBUG5");
				$retVal = $perms >= self::EDITOR;
			}
			//$this->logger->debug("DEBUG retVal:" . $retVal);
			return $retVal;
		}
	}
	
	//return 1 on success 0 on bad parameters -1 on failed authenticaion
	public function checkSession()
	{
        $userid = !empty($_POST["userid"]) ? strtolower(trim($_POST["userid"])) : NULL;
		$ukey = !empty($_POST["ukey"]) ? strtolower(trim($_POST["ukey"])) : NULL;
		if(!$userid || !$ukey)
		{
			return 0;
		}
		$auth = -1;
		$this->db->bindMore(array("userid" => $userid, "ukey" => $ukey));
		$user_sessionid = $this->db->single("SELECT userid FROM mtc_user_session WHERE userid = :userid AND ukey = :ukey");
		if (!empty($user_sessionid)) {
			# update the last accesssed time to now
			$this->db->bind("userid", $user_sessionid);
			$this->db->query("UPDATE mtc_user_session SET modified = CURRENT_TIMESTAMP WHERE userid = :userid");
			$auth = 1;
		}
		return $auth;
	}

	public function closeConnection()
	{
		// close the db connection
		$this->db->CloseConnection();
	}
}