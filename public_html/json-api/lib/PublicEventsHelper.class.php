<?php
include_once "general/GenericData.class.php";

class EventsHelper extends Event
{
	public function __construct()
   	{
      	parent::__construct();
   	}
	
	public function getLatestEvents($postArray = NULL)
	{
		$sql = "SELECT * from `event` WHERE `event_date_time` > NOW() ORDER BY `event_date_time`";			
		$events = $this->dbHelper->getRowSet($sql);
		if($events == false)
		{
			$xtErrMsg =  "Failed Query for " . $this->className . " " . $this->dbHelper->getErrMsg();
			return $this->returnErrorArray("Database Error", RETVAL::DB_FAILED_QUERY, $xtErrMsg);
		}
		elseif(empty($events))
		{
			return $this->returnErrorArray("No records for this item",
				RETVAL::EMPTY_RESULT_SET, "No records for " . $this->className . ".get using " .  $sql);
		}
		else
		{
			return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, "fields" => $events); 
		}
	}

	public function getEventsByDate($postArray = NULL)
	{
		$order = "ASC";
		//cheap sanitizer
		if(isset($postArray['order']))
		{
			if($postArray['order'] == "ASC")
			{
				$order = "ASC";
			}
			if($postArray['order'] == "DESC")
			{
				$order = "DESC";
			}
		}
		$sql = "SELECT * from `event` ORDER BY `event_date_time` " . $order;			
		$events = $this->dbHelper->getRowSet($sql);
		if($events == false)
		{
			$xtErrMsg =  "Failed Query for " . $this->className . " " . $this->dbHelper->getErrMsg();
			return $this->returnErrorArray("Database Error", RETVAL::DB_FAILED_QUERY, $xtErrMsg);
		}
		elseif(empty($events))
		{
			return $this->returnErrorArray("No records for this item",
				RETVAL::EMPTY_RESULT_SET, "No records for " . $this->className . ".get using " .  $sql);
		}
		else
		{
			return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, "fields" => $events); 
		}
	}

}

 