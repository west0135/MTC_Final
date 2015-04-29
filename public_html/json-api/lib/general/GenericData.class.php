<?php

require_once "base/G12Object.class.php";
require_once "base/G12SchemaData.class.php";

class Generic extends G12Object
{
	protected $dbHelper;
	protected $className;
	protected $table_name;
	protected $primaryKeyName;
	//public function __construct($className, $table_name, $primaryKeyName, $schema)
	public function __construct($schemaDataClass)
	{
		parent::__construct($schemaDataClass::SCHEMA);
		$this->className = $schemaDataClass::CLASS_NAME;
		$this->table_name = $schemaDataClass::TABLE_NAME;
		$this->primaryKeyName = $schemaDataClass::PRIMARY_KEY;
		$this->dbHelper = new ToBeRecordSetHelper(DB_NAME, $this->table_name);
		
		$this->logger->setContext("Generic", $_SERVER['PHP_SELF']);
		//$this->logger->debug("TEST[" . $this->table_name . "]"); 
	
		//parent::__construct($schema);
		//$this->dbHelper = new ToBeRecordSetHelper(DB_NAME, $table_name);
		//$this->className = $className;
		//$this->table_name = $table_name;
		//$this->primaryKeyName = $primaryKeyName;
	}
	
	public function getPrimaryKeyName()
	{
		return $this->primaryKeyName;
	}
	
	public function insertRow($postArray, $params)
	{
		$this->isInsert = true;
		if($this->loadParams($postArray, $params))
		{
			$errTest = $this->validateFields($params);
			if($errTest === false)
			{
				$id = $this->dbHelper->insertRow($params, $this->primaryKeyName);
				if($id == false)
				{
					$extErrMsg = $this->table_name . ": " . $this->dbHelper->getErrMsg() . " Row Values: " . print_r($params, true);
					return $this->returnErrorArray("Failed to insert Data", RETVAL::DB_FAILED_INSERT, $extErrMsg);
				}
				else
				{
					return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, $this->primaryKeyName => $id);
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
	
	public function updateRow($postArray, $params) 
	{
		$this->isInsert = false;
		if($this->loadParams($postArray, $params))
		{
			$errTest = $this->validateFields($params);
			if($errTest === false)
			{
				$id = $params[$this->primaryKeyName];
				$whereClause = "WHERE `" . $this->primaryKeyName . "` = :" . $this->primaryKeyName; 
				//update returns true on success
				if($this->dbHelper->updateRow($params, $whereClause))
				{
					return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, $this->primaryKeyName => $id);
					//,"log" => "where: " . $whereClause . " params: " . print_r($params, true));
				}
				else
				{
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
			$extErrMsg = $this->className . ".update:  Row Values: " . print_r($params, true);
			return $this->returnErrorArray("Missing or invalid parameter values.", RETVAL::DB_FAILED_UPDATE, $extErrMsg);
		}
	}

	protected function makeLimitsStatement($postArray)
	{
		$limit = "";
		if($postArray)
		{
			if(isset($postArray['start']))
			{
				if(is_numeric($postArray['start']))
				{
					$limit = " LIMIT " . $postArray['start'];
					if(isset($postArray['count']))
					{
						if(is_numeric($postArray['count']))
						{
							$limit .= " , " . $postArray['count'];
						}
					}
				}
			}
		}
		return $limit;
	}
	
	public function selectItems($postArray)
	{
		$limit = $this->makeLimitsStatement($postArray);
		
		$sql = 'SELECT * FROM `' . $this->table_name . '`' . $limit . ' ;';
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
	
	public function selectItemsUsing($postArray, $params)
	{
		$limit = $this->makeLimitsStatement($postArray);
		
		$select_fields = $this->assocArrayToSelectFields($params);
		$sql = 'SELECT ' . $select_fields . ' FROM `' . $this->table_name . '`' . $limit . ' ;';
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

	public function getItemById($id)
	{
		if(is_array($id)) //get the primary key value
		{
			$id = $id[$this->primaryKeyName];
		}
		//construct the parameter array for the prepared statement
		$param = array($this->primaryKeyName => $id);
		
		$sql = 'SELECT * FROM `' . $this->table_name . '` WHERE `' . $this->primaryKeyName . '` = :' . $this->primaryKeyName;
		
		$rowSet = $this->dbHelper->getRowSetUsing($sql, $param);
		if($rowSet == false)
		{
			$xtErrMsg =  "Failed Query for " . $this->className . " " . $this->dbHelper->getErrMsg();
			return $this->returnErrorArray("Database Error", RETVAL::DB_FAILED_QUERY, $xtErrMsg);
		}
		elseif(empty($rowSet))
		{
			return $this->returnErrorArray("No records for this item",
				RETVAL::EMPTY_RESULT_SET, "No records for " . $this->className . ".get using " .  $sql . 
				" postArray[" . $this->primaryKeyName . "] = " . $postArray[$this->primaryKeyName]);
		}
		else
		{
			return array(RETVAL::STATUS => RETVAL::DB_SUCCESS,
					self::FIELD => $rowSet[0]);
		}
	}

	public function getItemByIdUsing($id, $params)
	{
		$select_fields = $this->assocArrayToSelectFields($params);
		if(is_array($id)) //get the primary key value
		{
			$id = $id[$this->primaryKeyName];
		}
		//construct the parameter array for the prepared statement
		$param = array($this->primaryKeyName => $id);
		
		$sql = 'SELECT ' . $select_fields . ' FROM `' . $this->table_name . '` WHERE `' . $this->primaryKeyName . '` = :' . $this->primaryKeyName;
		
		$rowSet = $this->dbHelper->getRowSetUsing($sql, $param);
		if($rowSet == false)
		{
			$xtErrMsg =  "Failed Query for " . $this->className . " " . $this->dbHelper->getErrMsg();
			return $this->returnErrorArray("Database Error", RETVAL::DB_FAILED_QUERY, $xtErrMsg);
		}
		elseif(empty($rowSet))
		{
			return $this->returnErrorArray("No records for this item",
				RETVAL::EMPTY_RESULT_SET, "No records for " . $this->className . ".get using " .  $sql . 
				" postArray[" . $this->primaryKeyName . "] = " . $postArray[$this->primaryKeyName]);
		}
		else
		{
			return array(RETVAL::STATUS => RETVAL::DB_SUCCESS,
					self::FIELD => $rowSet[0]);
		}
	}

	public function deleteItemById($id)
	{
		if(is_array($id)) //get the primary key value
		{
			$id = $id[$this->primaryKeyName];
		}
		if(is_numeric($id))
		{
			if($this->dbHelper->deleteRow($id, $this->primaryKeyName))
			{
				return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, $this->primaryKeyName => $id);
			}
			else
			{
				$xtErrMsg =  "Failed Delete for " . $this->className . " " . $this->dbHelper->getErrMsg();
				return $this->returnErrorArray("Database Error", RETVAL::DB_FAILED_DELETE, $xtErrMsg);
			}
		}
		else
		{
			$xtErrMsg =  "Failed Delete for " . $this->className . " " . $id . " is NOT a numeric value";
			return $this->returnErrorArray("Delete Error", RETVAL::DB_FAILED_DELETE, $xtErrMsg);
		}
	}
}