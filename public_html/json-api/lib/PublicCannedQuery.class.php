<?php
include_once "general/GenericData.class.php";

class CannedQueryHelper extends CannedQuery
{
	
	public function __construct()
   	{
      	parent::__construct();
   	}
	
	public function runCannedQuery($postArray)
	{
		if(isset($postArray['key']))
		{
			$key = $postArray['key'];
			//construct the parameter array for the prepared statement
			$param = array('key' => $key);
			$sql = 'SELECT * FROM `canned_query` WHERE `key` = :key';
			$rowSet = $this->dbHelper->getRowSetUsing($sql, $param);
			if($rowSet == false)
			{
				$xtErrMsg =  "Failed Canned Query: " . $key . " " . $this->dbHelper->getErrMsg();
				return $this->returnErrorArray("Database Error", RETVAL::DB_FAILED_QUERY, $xtErrMsg);
			}
			elseif(empty($rowSet))
			{
				$xtErrMsg =  "No Records for Canned Query: " . $key;
				return $this->returnErrorArray("Database Error", RETVAL::DB_FAILED_QUERY, $xtErrMsg);
			}
			else
			{
				$fields = $rowSet[0];
				$query = $fields['query'];
				$class = $fields['class_list'];
				//Remove unused fields
				unset($postArray['method']);
				unset($postArray['key']);
				unset($postArray['userid']);
				unset($postArray['ukey']);
				
				$delete_test = strpos($query, "DELETE FROM");
								
				$pdo = $this->dbHelper->getPDO();
				try{
					$statement = $pdo->prepare($query);
					
					$test = strpos($query, "LIKE");
					if($test === false)
					{
						///No op
					}
					else
					{
						//Find all LIKE statements and sub in "%" after - NOTE Only supports post fix wild card LIKE "EXAMPLE%"
						$arr = explode("LIKE :", $query);
						if(count($arr) > 1)
						{
							for($i=1; $i < count($arr); $i++)
							{
								$arr2 = explode(" ", $arr[$i]);
								$the_key = ltrim($arr2[0]);
								$val = $postArray[$the_key];
								//Append the wild card to value
								$postArray[$the_key] = $val . "%";
							}
						}
					}
					
					$this->logger->debug("DEBUG query: " . $query);
					$this->logger->debug("DEBUG postArray: " . print_r($postArray,true));
					 
					$retVal = $statement->execute($postArray);
					if($retVal)
					{
						//$schema = "";
						$primary_key_name = "";
						$class = trim($class);
						if(!empty($class))
						{ 
							$object = new $class();
							//$schema = $object->getSchema();
							$primary_key_name = $object->getPrimaryKeyName();
						}

						$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
						if($rows)
						{
							return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, "fields" => $rows, 
										"primary_key_name" => $primary_key_name, "class_name" => $class);
						}
						elseif(empty($rows))
						{
							$xtErrMsg =  "No Records for Canned Query: " . $key . " POST ARRAY: " . print_r($postArray, true) .
							" using query: " . $query;
							return $this->returnErrorArray("No Return. Check input values.", RETVAL::DB_FAILED_QUERY, $xtErrMsg);
						}
						else
						{
							$op_success = array("operation_success"=>$key);
							return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, "fields" => $op_success, 
										"primary_key_name" => $primary_key_name, "class_name" => $class);
						}
					}
					else
					{
						$xtErrMsg = "False Return Value: " . $key . " " . $this->dbHelper->getErrMsg();
						return $this->returnErrorArray("Database Error", RETVAL::DB_FAILED_QUERY, $xtErrMsg);
					}
				}catch(PDOException $e){
					//$xtErrMsg =  "Canned Query Exception: " . $key . " " . $this->dbHelper->getErrMsg();
					//return $this->returnErrorArray("Database Exception", RETVAL::DB_FAILED_QUERY, $xtErrMsg);
					//NOTE on Delete an exception is thrown even though delete succeeds
					if ($delete_test === false) {
							$xtErrMsg =  "Failed Statement Execution: " . $key . " " .
											 $e->getMessage() . " POST ARRAY: " . print_r($postArray, true);
								return $this->returnErrorArray("Database Error", RETVAL::DB_FAILED_QUERY, $xtErrMsg);
					} else {
						$op_success = array("update_success_tentative"=>$key);
						return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, "fields" => $op_success);
					}

				}
			}
		}
	}
	
	public function getSortedList($postArray = NULL)
	{
		$limit = $this->makeLimitsStatement($postArray);
		
		$sql = 'SELECT * FROM `' . $this->table_name . '` ORDER BY `class_list` DESC ' . $limit . ' ;';
		$rowSet = $this->dbHelper->getRowSet($sql);
		if(empty($rowSet))
		{
			return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, self::FIELDS => array()); //return empty array
		}
		else if($rowSet == false)
		{
			$extErrMsg = $this->className . ".getList: " . $this->dbHelper->getErrMsg();
			return $this->returnErrorArray("Failed to retrieve Items", RETVAL::DB_FAILED_QUERY, $extErrMsg);
		}
		else
		{
			//$this->logger->debug("LIST TEST" . print_r($rowSet, true));
			return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, self::FIELDS => $rowSet);
		}
	}

/*
				if ($delete_test === false)
				{
					$order_by_test = strpos($query, "ORDER BY");
					if($order_by_test === false)
					{
						$query .= " ORDER BY `class_list`";
					}
				}
*/

}

 