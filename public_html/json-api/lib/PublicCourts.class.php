<?php
include_once "general/GenericData.class.php";

class MtcCourtReservationHelper extends MtcCourtReservation
{
	const RESERVED = "RESERVED"; //the reservation has been created but the individual has not been placed on a court yet
	const CONFIRMED = "CONFIRMED"; //A reservation has been created and the individual has been placed on a court
	const COMPLETE = "COMPLETE"; //Individual who was on a court with a reservation has finished and been removed from the court by the court captain
	
	const RESERVATIONS_PER_TIME_PERIOD = "reservations_per_time_period";
	const RESERVATIONS_PER_PERSON = "reservations_per_person";
	
	const RES_STATUS = "RES_STATUS";
	const RES_COUNT = "RES_COUNT";
	
	public function __construct()
   	{
      	parent::__construct();
   	}
	
	public function getConstants($postArray = NULL)
	{
		$const = array(self::RESERVED => self::RESERVED, self::COMPLETE => self::COMPLETE, self::CONFIRMED => self::CONFIRMED, 
					self::RES_STATUS => self::RES_STATUS, self::RES_COUNT => self::RES_COUNT);
		return array(RETVAL::STATUS => RETVAL::DB_SUCCESS,"constants" => $const);
	}
	
	//Used to set member permission - note maximum permission level is RESERVATION
	public function setMemberPermission($postArray)
	{
		$params = array('member_id' => NULL, 'permissions' => NULL);
		if($this->loadParams($postArray, $params))
		{
			if($params['permissions'] > Security::RESERVATION)
			{
				$extErrMsg = " Permissions: " . $params['permissions'] . " member_id: " . $params['member_id'];
				return $this->returnErrorArray("Cannot Promote a member above RESERVATION status. See Administrator", RETVAL::DB_FAILED_UPDATE, $extErrMsg);
			}
			$sql = 'UPDATE `mtc_permissions` SET `permissions` = "' . $params['permissions'] . '" WHERE `member_id` = ' . $params['member_id'];
			$ret = $this->dbHelper->executeSQL($sql);
			if($ret != -1)
			{
				return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, "member_id" => $params['member_id'],
							 "permissions" => $params['permissions']);
			}
			else
			{
				$extErrMsg = $this->table_name . ": " . $this->dbHelper->getErrMsg() . " Row Values: " . print_r($params, true);
				return $this->returnErrorArray("Failed to update Data", RETVAL::DB_FAILED_UPDATE, $extErrMsg);
			}
		}
		else
		{
			$extErrMsg = $this->className . ".reservations:  Row Values: " . print_r($params, true);
			return $this->returnErrorArray("Missing or invalid parameter values.", RETVAL::DB_FAILED_INSERT, $extErrMsg);
		}
	}


	public function reservations($postArray)
	{
		$params = array('date' => NULL, 'start_time' => NULL, 'end_time' => NULL);
		if($this->loadParams($postArray, $params))
		{
			$sql = "SELECT court_id, court_name FROM `mtc_court` WHERE 1 ORDER BY court_id ASC";
			$courts = $this->dbHelper->getRowSet($sql);
			if($courts == false)
			{
				$xtErrMsg =  "Failed Query for " . $this->className . " " . $this->dbHelper->getErrMsg();
				return $this->returnErrorArray("Database Error", RETVAL::DB_FAILED_QUERY, $xtErrMsg);
			}
			elseif(empty($courts))
			{
				return $this->returnErrorArray("No records for this item",
					RETVAL::EMPTY_RESULT_SET, "No records for " . $this->className . ".get using " .  $sql . 
					" postArray['date'] = " . $postArray['date']);
			}
			else
			{
				$courts_array = array();
				$pdo = $this->dbHelper->getPDO();
				$sql = 'SELECT r . * , m . first_name , m . last_name FROM `mtc_court_reservation` AS r INNER JOIN `mtc_member` AS m ON r.member1_id = m.member_id WHERE r.date = :date AND r.start_time >= :start_time AND r.start_time < :end_time AND r.court_id = :court_id ORDER BY r.start_time ASC ';
				$statement = $pdo->prepare($sql);
				//Loop through the courts
				foreach($courts as $court) 
				{
					$params['court_id'] = $court['court_id'];
					$court_array = array('court_id' => $court['court_id'], 'court_name' => $court['court_name']);
					try
					{
						//$this->logger->debug("PARAMS:" . print_r($params, true));
						$statement->execute($params);
						$court_array['reservations'] = $statement->fetchAll(PDO::FETCH_ASSOC);
						$courts_array[] = $court_array;
						
					}catch(PDOException $e){
						//$this->logger->debug("DEBUG getMessage(" . $e->getMessage() . ")");
						$xtErrMsg =  "Failed Query for " . $this->className . " " . $e->getMessage(). " query [" . $sql . "]";
						return $this->returnErrorArray("Database Error", RETVAL::DB_FAILED_QUERY, $xtErrMsg);
					}
				}
				return array(RETVAL::STATUS => RETVAL::DB_SUCCESS,"courts" => $courts_array);
			}
		}
		else
		{
			$extErrMsg = $this->className . ".reservations:  Row Values: " . print_r($params, true);
			return $this->returnErrorArray("Missing or invalid parameter values.", RETVAL::DB_FAILED_INSERT, $extErrMsg);
		}
	}

	public function quick_res($postArray)
	{
		$params = array('date' => NULL, 'start_time' => NULL, 'end_time' => NULL);
		if($this->loadParams($postArray, $params))
		{
			$sort_order = "ASC";
			$status_select = "";
			$start_date_select = "date = :date";
			$end_date_select = "";
			if(isset($postArray['sort_order']))
			{
				//This should protect from injection attacks here
				$order = $postArray['sort_order'];
				if($order == "DESC")
				{ 
					$sort_order = "DESC";
				}
				elseif($order == "ASC")
				{
					$sort_order = "ASC";
				}
			}
			if(isset($postArray['status']))
			{
				$status = $postArray['status'];
				$status_select = "AND status = :status";
				$params['status'] = $status;
			}
			if(isset($postArray['end_date']))
			{
				$start_date_select = "date >= :date";
				$end_date = $postArray['end_date'];
				if($end_date != -1)
				{
					$end_date_select = "AND date <= :end_date";
					$params['end_date'] = $end_date;
				}
			}
			
			$sql = 'SELECT date, court_reservation_id, start_time , end_time, member1_id, status, court_id, '
    			. 'm.first_name as first_name, m.last_name as last_name '
    			. 'FROM `mtc_court_reservation` AS mc '
    			. 'INNER JOIN `mtc_member` AS m '
    			. 'ON mc.member1_id = m.member_id '
				. 'WHERE ' . $start_date_select. ' AND start_time >= :start_time AND start_time < :end_time '
				. $status_select
				. ' ' . $end_date_select . ' ORDER BY date ' . $sort_order . ', start_time ASC';
			
			//OBSOLETE as of 2015-04-18
			/*
			$sql = 'SELECT date, court_reservation_id, start_time , end_time, member1_id, status, court_id FROM `' . $this->table_name .
			'` WHERE ' . $start_date_select. ' AND start_time >= :start_time AND start_time < :end_time ' . $status_select .
			' ' . $end_date_select . ' ORDER BY date ' . $sort_order . ', start_time ASC';
			*/
			
			$this->logger->debug("TEST:" . $sql);
			
			$rowset = $this->dbHelper->getRowSetUsing($sql, $params);
			if($rowset == false)
			{
				$rowset = array();
				return array(RETVAL::STATUS => RETVAL::DB_SUCCESS,"reservations" => $rowset);
			}
			else
			{
				return array(RETVAL::STATUS => RETVAL::DB_SUCCESS,"reservations" => $rowset);
			}
		}
		else
		{
			$extErrMsg = $this->className . ".reservations:  Row Values: " . print_r($params, true);
			return $this->returnErrorArray("Missing or invalid parameter values.", RETVAL::DB_FAILED_INSERT, $extErrMsg);
		}
	}
	
	public function safeCreate($postArray)
	{
		//This array specifies the field names that are required to execute the method
      	$params = BaseMtc_court_reservation::getParams();
		
		$this->logger->debug("DEBUG " . print_r($params, true));
		
		$this->isInsert = true;
		if($this->loadParams($postArray, $params))
		{
			$this->logger->debug("DEBUG " . print_r($params, true));
			
			$errTest = $this->validateFields($params);
			if($errTest === false)
			{
				//After standard fields have been validated
				//add the reservation rules fields
				$reservations_per_time_period = -1; //-1 indicates no rule
				$reservations_per_person = -1; //-1 indictaes no rule
				$allow_multiple_courts = false;
				if(isset($_POST[self::RESERVATIONS_PER_TIME_PERIOD]))
				{
					$reservations_per_time_period = $_POST[self::RESERVATIONS_PER_TIME_PERIOD];
				}
				if(isset($_POST[self::RESERVATIONS_PER_PERSON]))
				{
					$reservations_per_person = $_POST[self::RESERVATIONS_PER_PERSON];
				}
				
				//Allows an administrator to book multiple courts
				if($reservations_per_person == -2)
				{
					$reservations_per_person = -1;
					$allow_multiple_courts = true;
				}
				
				//Lock the table so no new changes can go into effect
				$ret = $this->dbHelper->executeSQL("LOCK TABLES `" . $this->table_name . "` WRITE");
				if($ret == -1)
				{
					$extErrMsg = $this->table_name . ": " . $this->dbHelper->getErrMsg() . " Row Values: " . print_r($params, true);
					return $this->returnErrorArray("Failed LOCK Tables", RETVAL::DB_FAILED_INSERT, $extErrMsg);
				}
				$start_time = $params['start_time'];
				$end_time = $params['end_time'];
				$date = $params['date'];
				$member1_id = $params['member1_id'];

				$pdo = $this->dbHelper->getPDO();
				//Test the number of reservation for this user
				$sql = 'SELECT COUNT(*) '
				. ' FROM `mtc_court_reservation` '
				. ' WHERE `date` = "' . $date . '"'
				. ' AND `status` = "RESERVED"'
				. ' AND `member1_id` = "' . $member1_id . '" ';
				try
				{
					$count = current($pdo->query($sql)->fetch());
					if($count === FALSE) //Bail out on failure
					{
						$extErrMsg = $this->table_name . ": " . $this->dbHelper->getErrMsg() . " Row Values: " . print_r($params, true);
						$this->dbHelper->executeSQL("UNLOCK TABLES");
						return $this->returnErrorArray("Failed to Count Data", RETVAL::DB_FAILED_INSERT, $extErrMsg);
					}
					if($reservations_per_person >= 0 && $count >= $reservations_per_person)
					{
						$str = $reservations_per_person > 1 ? "reservations" : "reservation";
						$strMsg = "Maximum of " . $reservations_per_person . " " . $str . " per day.";
						$this->dbHelper->executeSQL("UNLOCK TABLES");
						return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, /*$this->primaryKeyName => $id,*/ self::RES_STATUS => $strMsg, self::RES_COUNT => $count);
					}
				}catch(PDOException $e)
				{
					$extErrMsg = $this->table_name . ": " . $this->dbHelper->getErrMsg() . " Row Values: " . print_r($params, true);
					$this->dbHelper->executeSQL("UNLOCK TABLES");
					return $this->returnErrorArray("Failed Query to test Reservation Count", RETVAL::DB_FAILED_INSERT, $extErrMsg);
				}
				//Search for number of reservation for this time slot
				$sql = 'SELECT *'
				. ' FROM `' . $this->table_name . '` '
				. ' WHERE `date` = "' . $date . '"'
				. ' AND `status` = "RESERVED"'
				. ' AND ((`start_time` <= "' . $start_time . '" AND `end_time` > "' . $start_time 
				. '") OR (`start_time` >= "' . $start_time . '" AND `start_time`< "' . $end_time . '"))'
				. ' ';
				$rows = $this->dbHelper->getRowSet($sql);
				$count = 0;
				if($rows)
				{
					$count = count($rows);
					
					$this->logger->debug("count:" . $count . " " . self::RESERVATIONS_PER_TIME_PERIOD . ": " . $reservations_per_time_period);
					if($reservations_per_time_period >= 0 && $count >= $reservations_per_time_period) 
					{
						$strMsg = "Maximum of " . $reservations_per_time_period . " Reservations for this time: " . $start_time . " to " . $end_time;
						$this->dbHelper->executeSQL("UNLOCK TABLES");
						return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, /*$this->primaryKeyName => $id,*/ self::RES_STATUS => $strMsg, self::RES_COUNT => $count);
					}
					$court_id = $params['court_id'];
					//Can't have more than one reservation in the same court
					foreach($rows as $row) 
					{
						if($court_id == $row['court_id'])
						{
							$strMsg = "This court already has a reservation try another court";
							$this->dbHelper->executeSQL("UNLOCK TABLES");
							return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, /*$this->primaryKeyName => $id,*/ self::RES_STATUS => $strMsg, self::RES_COUNT => $count);
						}
						if($member1_id == $row['member1_id'])
						{
							if(!$allow_multiple_courts)
							{
								$strMsg = "Cannot reserve more than one court at a time";
								$this->dbHelper->executeSQL("UNLOCK TABLES");
								return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, /*$this->primaryKeyName => $id,*/ self::RES_STATUS => $strMsg, self::RES_COUNT => $count);
							}
						}
					}
				}
				$id = $this->dbHelper->insertRow($params, $this->primaryKeyName);
				if($id == false)
				{
					$this->dbHelper->executeSQL("UNLOCK TABLES");
					$extErrMsg = $this->table_name . ": " . $this->dbHelper->getErrMsg() . " Row Values: " . print_r($params, true);
					return $this->returnErrorArray("Failed to insert Data", RETVAL::DB_FAILED_INSERT, $extErrMsg);
				}
				else
				{
					$this->dbHelper->executeSQL("UNLOCK TABLES");
					return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, $this->primaryKeyName => $id, self::RES_STATUS => RETVAL::DB_SUCCESS, self::RES_COUNT => $count);
				}
			}
			else
			{
				return $errTest;
			}
		}
		else
		{
			$extErrMsg = $this->className . ".create:  Row Values: " . print_r($params, true);
			return $this->returnErrorArray("Missing or invalid parameter values.", RETVAL::DB_FAILED_INSERT, $extErrMsg);
		}
	}

	public function safeUpdate($postArray)
	{
		//This array specifies the field names that are required to execute the method
      	$params = BaseMtc_court_reservation::getParams();
		$this->isInsert = true;
		if($this->loadParams($postArray, $params))
		{
			$errTest = $this->validateFields($params);
			if($errTest === false)
			{
				//After standard fields have been validated
				//add the reservation rules fields
				$reservations_per_time_period = -1; //-1 indicates no rule
				$reservations_per_person = -1; //-1 indictaes no rule
				$allow_multiple_courts = false;
				if(isset($_POST[self::RESERVATIONS_PER_TIME_PERIOD]))
				{
					$reservations_per_time_period = $_POST[self::RESERVATIONS_PER_TIME_PERIOD];
				}
				if(isset($_POST[self::RESERVATIONS_PER_PERSON]))
				{
					$reservations_per_person = $_POST[self::RESERVATIONS_PER_PERSON];
				}
				
				//Allows an administrator to book multiple courts
				if($reservations_per_person == -2)
				{
					$reservations_per_person = -1;
					$allow_multiple_courts = true;
				}
				
				//Lock the table so no new changes can go into effect
				$ret = $this->dbHelper->executeSQL("LOCK TABLES `" . $this->table_name . "` WRITE");
				if($ret == -1)
				{
					$extErrMsg = $this->table_name . ": " . $this->dbHelper->getErrMsg() . " Row Values: " . print_r($params, true);
					return $this->returnErrorArray("Failed LOCK Tables", RETVAL::DB_FAILED_INSERT, $extErrMsg);
				}
				$start_time = $params['start_time'];
				$end_time = $params['end_time'];
				$date = $params['date'];
				$member1_id = $params['member1_id'];
				$status = $params['status'];
				$court_reservation_id = $params['court_reservation_id'];

				$pdo = $this->dbHelper->getPDO();
				
				//Search for number of reservation for this time slot
				$sql = 'SELECT *'
				. ' FROM `' . $this->table_name . '` '
				. ' WHERE `date` = "' . $date . '"'
				. ' AND (`status` = "RESERVED" OR `status` = "CONFIRMED" )'
				. ' AND ((`start_time` <= "' . $start_time . '" AND `end_time` > "' . $start_time 
				. '") OR (`start_time` >= "' . $start_time . '" AND `start_time`< "' . $end_time . '"))'
				. ' AND `court_reservation_id` != ' . $court_reservation_id .' ';
				$rows = $this->dbHelper->getRowSet($sql);
				$count = 0;
				if($rows)
				{
					$count = count($rows);
					
					$this->logger->debug("count:" . $count . " " . self::RESERVATIONS_PER_TIME_PERIOD . ": " . $reservations_per_time_period);
					if($reservations_per_time_period >= 0 && $count >= $reservations_per_time_period) 
					{
						$strMsg = "Maximum of " . $reservations_per_time_period . " Reservations for this time: " . $start_time . " to " . $end_time;
						$this->dbHelper->executeSQL("UNLOCK TABLES");
						return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, self::RES_STATUS => $strMsg, self::RES_COUNT => $count);
					}
					$court_id = $params['court_id'];
					//Can't have more than one reservation in the same court
					foreach($rows as $row) 
					{
						if($court_id == $row['court_id'])
						{
							$strMsg = "This court already has a reservation try another court";
							$this->dbHelper->executeSQL("UNLOCK TABLES");
							return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, self::RES_STATUS => $strMsg, self::RES_COUNT => $count);
						}
						if($member1_id == $row['member1_id'])
						{
							if(!$allow_multiple_courts)
							{
								$strMsg = "Cannot reserve more than one court at a time";
								$this->dbHelper->executeSQL("UNLOCK TABLES");
								return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, self::RES_STATUS => $strMsg, self::RES_COUNT => $count);
							}
						}
					}
				}
				$id = $params[$this->primaryKeyName];
				$whereClause = "WHERE `" . $this->primaryKeyName . "` = :" . $this->primaryKeyName; 
				//update returns true on success
				if($this->dbHelper->updateRow($params, $whereClause))
				{
					$this->dbHelper->executeSQL("UNLOCK TABLES");
					return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, $this->primaryKeyName => $id, self::RES_STATUS => RETVAL::DB_SUCCESS, self::RES_COUNT => $count);
					//,"log" => "where: " . $whereClause . " params: " . print_r($params, true));
				}
				else
				{
					$this->dbHelper->executeSQL("UNLOCK TABLES");
					$extErrMsg = $this->table_name . ": " . $this->dbHelper->getErrMsg() . " Row Values: " . print_r($params, true);
					return $this->returnErrorArray("Failed to update Data", RETVAL::DB_FAILED_UPDATE, $extErrMsg);
				}
			}
			else
			{
				return $errTest;
			}
		}
		else
		{
			$extErrMsg = $this->className . ".create:  Row Values: " . print_r($params, true);
			return $this->returnErrorArray("Missing or invalid parameter values.", RETVAL::DB_FAILED_INSERT, $extErrMsg);
		}
	}
	
	//Used to get member permission
	public function getMemberPermission($postArray)
	{
		if(isset($postArray['member_id']))
		{
			//$param = $postArray['member_id'];
			$param = array('member_id' => $postArray['member_id']);
			$sql = 'SELECT * FROM `mtc_permissions` WHERE `member_id` = :member_id';
			$rowSet = $this->dbHelper->getRowSetUsing($sql, $param);
			if($rowSet == false)
			{
				$xtErrMsg =  "Failed Query for mtc_permissions " . $this->dbHelper->getErrMsg();
				return $this->returnErrorArray("Reservation NOT allowed check permission status.", RETVAL::DB_FAILED_QUERY, $xtErrMsg);
			}
			elseif(empty($rowSet))
			{
				$xtErrMsg = "No records using " .  $sql . " postArray[" . print_r($postArray, true);
				return $this->returnErrorArray("No records for this item", RETVAL::EMPTY_RESULT_SET, $xtErrMsg);
			}
			else
			{
				return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, self::FIELD => $rowSet[0]);
			}
		}
		else
		{
			$extErrMsg = " Row Values: " . print_r($postArray, true);
			return $this->returnErrorArray("Missing or invalid parameter values.", RETVAL::DB_FAILED_INSERT, $extErrMsg);
		}
	}

}

 