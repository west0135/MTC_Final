<?php
include_once "general/GenericData.class.php";

class MtcOpenDatesHelper extends MtcOpenDates
{
	public function __construct()
   	{
      	parent::__construct();
   	}
	
	public static $weekdays = array(0=>'Sunday', 1=>'Monday', 2=>'Tuesday', 3=>'Wednesday', 4=>'Thursday', 5=>'Friday', 6=>'Saturday');
	
	private function getDayNumber($strDay)
	{
		for($i=0; $i < count(self::$weekdays); $i++)
		{
			if(self::$weekdays[$i] == $strDay) return $i;
		}
		return -1;
	}
	
	//Add a block of MtcOpenDates entries specifying an open from date to an end date
	//Contains entries for a specified block of days ie: mon-fri
	public function create($postArray)
	{
		 $fields = array();
		 $params = array('open_dates_id'=>NULL, 'name'=>NULL, 'day'=>NULL, 'start_date'=>NULL, 'end_date'=>NULL,
		 'start_time'=>NULL, 'end_time'=>NULL, 'type' => NULL, 'comments'=>NULL);

		//unset($params['userid']);
		//unset($params['ukey']);
		if($this->loadParams($postArray, $params))
		{
			if(isset($postArray['end_day']))
			{
				//Multi day
				//determine 
				//l (lowercase 'L')	A full textual representation of the day of the week Sunday through Saturday
				$start_number = $this->getDayNumber($params['day']);
				$end_number = $this->getDayNumber($postArray['end_day']);
				if($start_number == -1 || $end_number == -1)
				{
					$extErrMsg = $this->className;
					$strMsg = " Spelling Error: " . $params['day'] . " End Day: " . $postArray['end_day'];
					return $this->returnErrorArray($strMsg, RETVAL::DB_FAILED_INSERT, $extErrMsg);
				}
				if($start_number >= $end_number)
				{
					$extErrMsg = $this->className . " start_number: " . $start_number . " end_number: " . $end_number;
					$strMsg = " The value for Day cannot be larger than or equal to End Day. Leave End Day value empty to enter a single day value.";
					return $this->returnErrorArray($strMsg, RETVAL::DB_FAILED_INSERT, $extErrMsg);
				}
				$param_list = $this->assocArrayToParamList($params);
				
				
				$sql = "INSERT INTO `to_be`.`mtc_open_dates` (`open_dates_id`, `name`, `day`, `start_date`, `end_date`, `start_time`, `end_time`, `type`, `comments`) VALUES (:open_dates_id, :name, :day, :start_date, :end_date, :start_time, :end_time, :type, :comments);";
				
				$select_clause = 'SELECT LAST_INSERT_ID() AS `open_dates_id`;';
				
				$pdo = $this->dbHelper->getPDO();
				
				$pdo->beginTransaction();
				try
				{
					$ins = $pdo->prepare($sql);
					for($i=$start_number; $i <= $end_number; $i++)
					{
						$params['day'] = self::$weekdays[$i];
						if($ins->execute($params))
						{
							$id = current($pdo->query($select_clause)->fetch());
							$fields[] = array($this->primaryKeyName => $id);
						}
						else
						{
							$pdo->rollBack();
							$extErrMsg = $this->table_name . ": " . $this->dbHelper->getErrMsg() . " Row Values: " . print_r($params, true);
							return $this->returnErrorArray("Failed to insert Data", RETVAL::DB_FAILED_INSERT, $extErrMsg);
						}
					}
					$pdo->commit();
					return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, "fields" => $fields);
				}catch(PDOException $e)
				{
					$pdo->rollBack();
					$extErrMsg = $this->table_name . ": " . $e->getMessage() . " index: " . $i . " Row Values: " . print_r($params, true);
					return $this->returnErrorArray("Failed to insert Data", RETVAL::DB_FAILED_INSERT, $extErrMsg);
				}
			}
			else
			{
				$number = $this->getDayNumber($params['day']);
				if($number == -1)
				{
					$extErrMsg = $this->className;
					$strMsg = " Spelling Error: " . $params['day'];
					return $this->returnErrorArray($strMsg, RETVAL::DB_FAILED_INSERT, $extErrMsg);
				}
				//Single day
				$id = $this->dbHelper->insertRow($params, $this->primaryKeyName);
				if($id == false)
				{
					$extErrMsg = $this->table_name . ": " . $this->dbHelper->getErrMsg() . " Row Values: " . print_r($params, true);
					return $this->returnErrorArray("Failed to insert Data", RETVAL::DB_FAILED_INSERT, $extErrMsg);
				}
				else
				{
					$fields[] = array($this->primaryKeyName => $id);
					return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, "fields" => $fields);
				}
			}
		}
		else
		{
			$extErrMsg = $this->className . ".reservations:  Row Values: " . print_r($params, true);
			return $this->returnErrorArray("Missing or invalid parameter values.", RETVAL::DB_FAILED_INSERT, $extErrMsg);
		}
	}

	/*
	public function getOpenHours($postArray)
	{
		$params = array('date' => NULL, 'day' => NULL);
		if($this->loadParams($postArray, $params))
		{
			$sql = 'SELECT * FROM `mtc_open_dates` WHERE `start_date` <= :date AND `end_date` >= :date AND `day` = :day';
			$rowset = $this->dbHelper->getRowSetUsing($sql, $params);
			if($rowset == false)
			{
				$extErrMsg = $this->className . ".getOpenHours:  Row Values: " . print_r($params, true);
				return $this->returnErrorArray("Not Open.", RETVAL::DB_FAILED_INSERT, $extErrMsg);
			}
			else
			{
				return array(RETVAL::STATUS => RETVAL::DB_SUCCESS,"field" => $rowset);
			}
		}
		else
		{
			$extErrMsg = $this->className . ".getOpenHours:  Row Values: " . print_r($params, true);
			return $this->returnErrorArray("Missing or invalid parameter values.", RETVAL::DB_FAILED_INSERT, $extErrMsg);
		}
	}
	*/
	
	public function getOpenHours($postArray)
	{
		$params = array('date' => NULL, 'day' => NULL);
		if($this->loadParams($postArray, $params))
		{
			$sql = 'SELECT * FROM `mtc_open_dates` WHERE `start_date` <= :date AND `end_date` >= :date AND `day` = :day AND `type` = -1';
			$rowset = $this->dbHelper->getRowSetUsing($sql, $params);
			if($rowset == false)
			{
				$sql = 'SELECT * FROM `mtc_open_dates` WHERE `start_date` <= :date AND `end_date` >= :date AND `day` = :day AND `type` = 0';
				$rowset = $this->dbHelper->getRowSetUsing($sql, $params);
				if($rowset == false)
				{
					$extErrMsg = $this->className . ".getOpenHours:  Row Values: " . print_r($params, true);
					return $this->returnErrorArray("Not Open.", RETVAL::DB_FAILED_INSERT, $extErrMsg);
				}
				else
				{
					return array(RETVAL::STATUS => RETVAL::DB_SUCCESS,"field" => $rowset);
				}
			}
			else
			{
				return array(RETVAL::STATUS => RETVAL::DB_SUCCESS,"field" => $rowset);
			}
		}
		else
		{
			$extErrMsg = $this->className . ".getOpenHours:  Row Values: " . print_r($params, true);
			return $this->returnErrorArray("Missing or invalid parameter values.", RETVAL::DB_FAILED_INSERT, $extErrMsg);
		}
	}

}

 