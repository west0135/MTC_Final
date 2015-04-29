<?php

require_once "Log4me.class.php";
require_once "db_info.php";

///////////////////////////////////////////////////////////////////
// File: DbHelper.php
// Author: Thomas Wiegand
// Copyright: 2014, Thomas Wiegand
// Desc: This is a database helper file
// 			It includes the The IdbTable Interface common helper functions for database operations
//			PDOStandardTable - Standard SQL functions that are common to both MYSQL and SQLLite
//			MySQLTable the MYSQL implementation of the IdbTable interface
//			SQLLiteTable the SQLLite implementation of the IdbTable interface
//			RecordSetHelper class for initializing and using IdbTable implementations 
///////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////
// interface: IdbTable
// Desc: This interface contains definitions for common database operations
///////////////////////////////////////////////////////////////////
interface IdbTable{
	
	//parameters: $createTable boolean set true to create the table on initialization
	//$pdo 
	//return: true on success
	public function init($createTable, $pdo = NULL);
	
	//return initialized PDO object or null
	public function getPDO();
	
	//return number of rows modified or deleted by the SQL statement. If no rows were affected returns 0.
	//On error returns -1
	public function executeSQL($sql);
	
	//Returns the table specification array
	public function getTableSpecs();
	
	//inserts one row of data into the table
	//parameters: $row an associative array containg name value pairs to insert
	//$primaryKeyName the primary key. If value is NULL insert id will be returned
	//returns primary key value(MYSQL only) or true on success, false on failure.
	//NOTE primary key must be auto_increment for MYSQL
	public function insertRow($row,$primaryKeyName);
	
	//Inserts a mutiple number of rows into the table using the most efficient method for the implemented Datbase
	//parameters $recordset array of arrays or a PDOStatement object
	//The object contains an array of associative arrays containg name value pairs to be inserted
	//returns the number of rows inserted or -1 on failure.
	public function insertFullRecordSet($recordset);
	
	//update one row of data
	//$row an associative array containg name value pairs to insert
	//Uses the where clause for the update - otherwise uses the value of the primary key
	//return true on success
	public function updateRow($row, $where_clause = NULL);
	
	//Updatess a mutiple number of rows into the table using the most efficient method for the implemented Datbase
	//parameters $recordset array of arrays or a PDOStatement object
	//The object contains an array of associative arrays containg name value pairs to be inserted
	//returns the number of rows updated or -1 on failure.
	public function updateFullRecordSet($recordset, $where_clause);

	//Vacuum a SQLLite database table
	//This has no meaning for MYSQL
	//returns true on success
	public function vacuum();
	
	//Delete a single Row
	//returns true on success
	//parameter $id value
	//parameter $key_name name of the column
	public function deleteRow($id, $key_name);
	
	//executes an SQL query
	//returns PDOStatement object or false on failure
	public function selectDataSet($sql);
	
	//executes an SQL query
	//returns associative array or false on failure
	public function getRowSet($sql);
	
	//executes a prepared statement using SQL query and parameters for query
	//returns associative array or false on failure
	public function getRowSetUsing($sql, $params);

	//returns the number of rows in the table or -1 on failure
	public function getCount();
	
	//Truncate the table
	//returns the number of rows deleted or -1 on falure
	public function truncateTable();
	
	//returns error messages from last failed call
	public function getErrorMsg();
	
	//Test if table exist returns true if exist false if not
	public function doesTableExist();
	
	//Log error Messgage
	public function logErrMsg($msg);
}
///////////////////////////////////////////////////////////////////
// class: PDOStandardTable
// Desc: This class contains SQL methods that are common to both
//			MYSQL and SQLLite
///////////////////////////////////////////////////////////////////
class PDOStandardTable
{

	protected $logger;
	protected $err_log;
	protected $errMsgs = "";
	protected $pdo = NULL;
	protected $dbName;
	protected $tableName;
	protected $tablespecs = NULL;
	function __construct($dbName, $tableName, $tablespecs)
	{
		$this->dbName = $dbName;
		$this->tableName = $tableName;
		if($tablespecs)
		{
			$this->tablespecs = $tablespecs;
		}
		$this->errlog = new Log4Me(PDO_STANDARD_TABLE_ERROR_LOG_STATUS,"error.txt");
		$this->errlog->setContext("PDOStandardTable", $_SERVER['PHP_SELF']);
		//To log debug messages set the level to Log4Me::DEBUG as shown below
		//$this->logger = new Log4Me(Log4Me::DEBUG,"log.txt");
		$this->logger = new Log4Me(PDO_STANDARD_TABLE_LOG_STATUS,"log.txt");
		$this->logger->setContext("PDOStandardTable", $_SERVER['PHP_SELF']);
	}
	function __destruct()
	{
		$this->pdo = NULL;
	}
	
	public function close()
	{
		$this->pdo = NULL;
	}
	
	protected function array_to_pdo_params($array) {
  		$temp = array();
  		foreach (array_keys($array) as $name) {
    		$temp[] = "`" . $name . "` = ?";
  		}
		$param_string = implode(', ', $temp);
		//$this->logger->debug("PDO param string: " . $param_string);
  		return $param_string;
	}
	
	protected function array_to_associative_pdo_params($array) {
  		$temp = array();
  		foreach (array_keys($array) as $name) {
    		$temp[] = "`" . $name . "` = :" . $name;
  		}
		$param_string = implode(', ', $temp);
		//$this->logger->debug("PDO param string: " . $param_string);
  		return $param_string;
	}

	protected function err($msg)
	{
		$this->errMsgs = $msg;
		$this->errlog->error($msg);
	}
	
	//Log error Messgage
	public function logErrMsg($msg)
	{
		$this->err($msg);
	}
	
	//return initialized PDO object or null
	public function getPDO()
	{
		return $this->pdo;
	}
	//Returns the table specification array
	public function getTableSpecs()
	{
		return $this->tablespecs;
	}
	
	//Returns the simple data type for MYSQL data type
	public function simpleDataType($type)
	{
		$pattern = '/\(/';
		$ar1 = preg_split($pattern,$type);
		$test = strtolower(trim($ar1[0]));
		switch($test)
		{
			case 'varchar':
			case 'text':
				return 'VARCHAR';
			case 'bigint':
			case 'int':
			case 'smallint':
			case 'tinyint':
				return 'INTEGER';
		}
		return $type;
	}
	
	//return number of rows modified or deleted by the SQL statement. If no rows were affected returns 0.
	//On error returns -1
	public function executeSQL($sql)
	{
		try{
			return $this->pdo->exec($sql);
		}catch(PDOException $e){
			$this->err("EXECUTE: " . $e->getMessage() . " using " . $this->tableName . " query [" . $sql . "]");
			return -1;
		}
	}
	
	//Delete a single Row
	//returns true on success
	//parameter $id the value of key
	public function deleteRow($id, $key_name)
	{
		$this->errMsgs = "";
		//construct the parameter array for the prepared statement
		$param = array($key_name => $id);
		$sql = "DELETE FROM `" . $this->tableName . "` WHERE " . $key_name . "=:" . $key_name;
		try{
			$sth = $this->pdo->prepare($sql);
			return $sth->execute($param);
		}catch(PDOException $e){
			$this->err("DELETE from: " . $e->getMessage() . " using " . $this->tableName . " query [" . $sql . "]");
			return false;
		}
	}
	//executes an SQL query
	//returns PDOStatement object or false on failure
	public function selectDataSet($sql)
	{
		$this->errMsgs = "";
		try{
			return $this->pdo->query($sql);
		}catch(PDOException $e){
			$this->err("SELECT: " . $e->getMessage() . " using " . $this->tableName . " query [" . $sql . "]");
			return false;
		}
	}
	//executes an SQL query
	//returns associative array or false on failure
	public function getRowSet($sql)
	{
		$this->errMsgs = "";
		try{
			$statement = $this->pdo->prepare($sql);
			$statement->execute();
			return $statement->fetchAll(PDO::FETCH_ASSOC);
			
		}catch(PDOException $e){
			//$this->logger->debug("DEBUG getMessage(" . $e->getMessage() . ")");
			$this->err("SELECT: " . $e->getMessage() . " using " . $this->tableName . " query [" . $sql . "]");
			return false;
		}
	}
	//executes a prepared statement using SQL query and parameters for query
	//returns associative array or false on failure
	public function getRowSetUsing($sql, $params)
	{
		$this->errMsgs = "";
		try{
			$statement = $this->pdo->prepare($sql);
			//$this->logger->debug("PARAMS:" . print_r($params, true));
			$statement->execute($params);
			return $statement->fetchAll(PDO::FETCH_ASSOC);
			
		}catch(PDOException $e){
			//$this->logger->debug("DEBUG getMessage(" . $e->getMessage() . ")");
			$this->err("SELECT: " . $e->getMessage() . " using " . $this->tableName . " query [" . $sql . "]");
			return false;
		}
	}
	//returns the number of rows in the table or -1 on failure
	public function getCount()
	{
		$this->errMsgs = "";
		try{
			$sql = "SELECT count(*) FROM " . $this->tableName;
			return current($this->pdo->query($sql)->fetch());
		}catch(PDOException $e){
			$this->err("SELECT: " . $e->getMessage() . " using " . $this->tableName . " query [" . $sql . "]");
			return -1;
		}
	}
	//Truncate the table
	//returns the number of rows deleted or -1 on falure
	public function truncateTable()
	{
		$this->errMsgs = "";
		try{
			$sql = "DELETE FROM " . $this->tableName;
			$count = $this->pdo->exec($sql);
			return $count;
		}catch(PDOException $e){
			$this->err("DELETE: " . $e->getMessage() . " using " . $this->tableName . " query [" . $sql . "]");
			return -1;
		}
	}
	//returns error messages from last failed call
	public function getErrorMsg()
	{
		return $this->errMsgs;
	}
}
///////////////////////////////////////////////////////////////////
// class: MySQLTable
// extends PDOStandardTable
// implements IdbTable
// Desc: This class implements SQL methods that are unique to MYSQL
///////////////////////////////////////////////////////////////////
class MySQLTable extends PDOStandardTable implements IdbTable
{
	//If $this->tablespecs == NULL init function will generate tablespecs for you
	//TODO implement this for SQLLite
	function __construct($dbName, $tableName, $tablespecs = NULL)
	{
		parent::__construct($dbName, $tableName, $tablespecs);
		//$this->logger->debug("MySQLTable Constructor");
	}
	
	//parameters: $createTable boolean set true to create the table on initialization
	//return: true on success
	//NOTE createTable NOT implemented
	public function init($createTable, $pdo = NULL)
	{
		$this->errMsgs = "";
		if($this->pdo == NULL){
			try{
				if($pdo)
				{
					$this->pdo = $pdo;
					//$this->logger->debug("USING existing PDO");
				}
				else
				{
					$this->pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . $this->dbName ,DB_USER ,DB_PASSWORD);
					//set the error mode to throw exceptions
					$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				}
				//If a tablespec was not included then generate one from sql query
				if(!$this->tablespecs)
				{
					$sql = 'SHOW COLUMNS FROM `' . $this->tableName . '`';
					$rs = $this->selectDataSet($sql);
					if($rs)
					{
						$str = "tablespec: ";
						$this->tablespecs =  array();
						foreach($rs as $row)
						{
							$suffix = "";
							if($row['Key'] == 'PRI')
							{
								$suffix = " PRIMARY KEY";
							}
							$val = $this->simpleDataType($row['Type']) . $suffix;
							$this->tablespecs[$row['Field']] = $val;
							$str .= $row['Field'] . "[" . $val . "] ";
						}
						//$this->logger->debug(get_class($this) . " GENERATED:" . $str);
					}
					else
					{
						$this->err = "Error getting Column information for table:" . $this->tableName;
						return false;
					}
				}
				else
				{
					//$this->logger->debug(get_class($this) . " STATIC:" . print_r($this->tablespecs,true));
				}
				return true;
			}catch(PDOException $e){
				$this->err("Error: " . $e->getMessage() . " contact developer.");
				return false;
			}
		}else
		{
			$this->pdo = $pdo;
		}
		return true;
	}
	
	//Returns array of all primary keys for table
	//false on error
	//Note test record set for length to determine if more than one primary key
	//Note SHOW FULL COLUMNS FROM to get COMMENTS etc.
	private function getPrimaryKeySet($table_name)
	{
		//$sql = 'SHOW KEYS FROM `' . $table_name; // . '` WHERE `key_name` = "PRIMARY"';
		$sql = 'SHOW COLUMNS FROM `' . $table_name . '` WHERE `key` = "PRI"';
		$rs = $this->selectDataSet($sql);
		if($rs)
		{
			$arr =  array();
			foreach($rs as $row)
			{
				//$arr[] = $row['key_name']; 
				$arr[] = $row['Field'];
			}
			return $arr;
		}
		else
		{
			$this->err("Error in MySQLTable.getPrimaryKeys(" . $table_name . ") PDO recordset: " . print_r($rs, true));
			return false;
		}
	}
	
	private function getWhereClause($table_name, $row, $where_clause)
	{
		$primaryKey = NULL;
		$rs = $this->getPrimaryKeySet($this->tableName);
		$str = "";
		if($rs)
		{
			//Only get primary key if more than one primary key
			if(count($rs) == 1)
			{
				$primaryKey = $rs[0];
			}
 		}
		if($where_clause == NULL && $primaryKey != NULL)
		{
			$where_clause = "WHERE `" . $primaryKey . "` = '" . $row[$primaryKey] . "'";  
		}
		else if($primaryKey == NULL && $where_clause == NULL)
		{
			//$this->err("r = " . print_r($r,true) . " rs = " . print_r($rs, true));
			$this->err("[" . $str . "] PRIMARY KEY count = " . count($rs) . " You must use a WHERE clause to update records containing more than one (or none) PRIMARY KEYs:" . print_r($rs, true) . ". Note: calculated key value[" . $primaryKey . "] and WHERE CLAUSE: " . $where_clause);
			return false;
		}
		//remove (if we can) primary key from row since we don't want it included in the pdo params array
		if($primaryKey)
		{
			unset($row[$primaryKey]);
		}
		return true;
	}
	
	//update one row of data
	//$row an associative array containg name value pairs to update
	//Uses the where clause for the update - otherwise uses the value of the primary key
	//return true on success
	public function updateRow($row, $where_clause = NULL)
	{
		$this->errMsgs = "";
		//NOTE this method returns false if problems exist
		if($this->getWhereClause($this->tableName, $row, $where_clause))
		{
			// Build the query string
			$query = "UPDATE `" . $this->tableName . "` SET ". $this->array_to_associative_pdo_params($row) . " " . $where_clause . ";";
			try{
				$sth = $this->pdo->prepare($query);
				$cols = $sth->execute($row);
				return $cols;
			}catch(PDOException $e){
				$this->err("UPDATE: " . $e->getMessage() . " using query [" . $query . "]");
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	//Updatess a mutiple number of rows into the table using the most efficient method for the implemented Datbase
	//parameters $recordset array of arrays or a PDOStatement object
	//The object contains an array of associative arrays containg name value pairs to be updated
	//returns the number of rows updated or -1 on failure.
	public function updateFullRecordSet($recordset, $where_clause)
	{
		$this->errMsgs = "";
		//NOTE this method returns false if problems exist
		try
		{
			$i = 0;
			foreach($recordset as $row)
			{
				if($i == 0)
				{
					// Build the query string
					$query = "UPDATE `" . $this->tableName . "` SET ". $this->array_to_associative_pdo_params($row) . " " . $where_clause . ";";
					$sth = $this->pdo->prepare($query);
					$i++;
				}
				$sth->execute($row);
			}
			return true; 
	
		}catch(PDOException $e){
			$this->err("UPDATE: " . $e->getMessage() . " using query [" . $query . "]");
			return false;
		}
	}

	//inserts one row of data into the table
	//parameters: $row an associative array containg name value pairs to insert
	//$primaryKeyName the primary key.
	//returns primary key value on success, false on failure.
	public function insertRow($row, $primaryKeyName)
	{
		$select_clause = "";
		$primary_key_val = false;
		$this->errMsgs = "";
		$sql = "INSERT INTO " . $this->tableName . "("; 
		foreach($row as $key => $value)
		{
			$sql .= "`" . $key . "`,";
		}
		$sql = trim($sql,",") . ")VALUES(";
		foreach ($row as $key => $value)
		{
			if($key == $primaryKeyName)
			{
				$select_clause = 'SELECT LAST_INSERT_ID() AS `' . $key . '`;';
			}
			$sql .= ":" . $key . ",";
		}
		$sql = trim($sql,",") . ");";
		try{
			//$this->logger->debug("insertRow sql: " . $sql);
			$ins = $this->pdo->prepare($sql);
			if($ins->execute($row))
			{
				//$this->logger->debug("select_clause" . $select_clause);
				//Return the primary key value or false
				return current($this->pdo->query($select_clause)->fetch());
			}
			else
			{
				return false;
			}
		}catch(PDOException $e)
		{
			$this->err("INSERT INTO: " . $e->getMessage() . " on " . $this->tableName . " query [" . $sql . "]");
			return false;
		}
		return true;
	}
	
	//Inserts a mutiple number of rows into the table using the most efficient method for the implemented Datbase
	//parameters $recordset array of arrays or a PDOStatement object
	//The object contains an array of associative arrays containg name value pairs to be inserted
	//returns the number of rows inserted or -1 on failure.
	public function insertFullRecordSet($recordset)
	{
		$sql = "";
		$ins = NULL;
		$count = 0;
		foreach($recordset as $row)
		{
			if($count == 0) //create the query
			{
				$sql = "INSERT INTO " . $this->tableName . "("; 
				foreach($row as $key => $value)
				{
					$sql .= $key . ",";
				}
				$sql = trim($sql,",") . ")VALUES(";
				foreach ($row as $key => $value)
				{
					$sql .= ":" . $key . ",";
				}
				$sql = trim($sql,",") . ");";
				//$this->logger->debug("insertRow sql: " . $sql);
				$ins = $this->pdo->prepare($sql);
			}
			$count++;
			try{
				//$this->logger->debug("EXECUTE(" . print_r($row, true));
				$ins->execute($row);
			}catch(PDOException $e)
			{
				$this->err("INSERT INTO: " . $e->getMessage() . " on " . $this->tableName . " query [" . $sql . "]");
				return -1;
			}
		}
		return $count;
	}














	//Inserts a mutiple number of rows into the table using the most efficient method for the implemented Datbase
	//parameters $recordset array of arrays or a PDOStatement object
	//The object contains an array of associative arrays containg name value pairs to be inserted
	//returns the number of rows inserted or -1 on failure.
	public function insertFullRecordSetOld($recordset)
	{
		$this->errMsgs = "";
		$sql = 'INSERT INTO `' . $this->tableName . "` (";
		foreach($this->tablespecs as $key => $value)
		{
			$sql .= "`" . $key . "`,";
		}
		$sql = trim($sql,",") . ")VALUES";
		foreach($recordset as $row)
		{
			$sql .= "(";
			foreach ($this->tablespecs as $key => $value)
			{
				$val = $row[$key];
				$val = "'" . $val . "'";
				$sql .= $val . ",";
			}
			$sql = trim($sql,",") . "),";
		}
		$sql = trim($sql,",") . ";";
		try{
			//$this->logger->debug($sql);
			$count = $this->pdo->exec($sql);
			return $count;
		}catch(PDOException $e){
			$this->err("INSERT INTO: " . $e->getMessage() . " using " . $this->tableName . " query [" . $sql . "]");
			return -1;
		}
	}
	//Vacuum a SQLLite database table
	//This has no meaning for MYSQL
	//returns true on success
	public function vacuum()
	{
		return true;
	}
	//Test if table exist returns true if exist false if not
	public function doesTableExist()
	{
		// Select 1 from table_name will return false if the table does not exist.
		$this->errMsgs = "";
		try{
			return $this->pdo->query("SELECT 1 FROM `"  . $this->tableName . "` LIMIT 0,1");
		}catch(PDOException $e){
			$this->err("SELECT: " . $e->getMessage() . " using " . $this->tableName . " query [" . $sql . "]");
			return false;
		}

	}
}
///////////////////////////////////////////////////////////////////
// class: MySQLTable
// extends PDOStandardTable
// implements IdbTable
// Desc: This class implements SQL methods that are unique to SQLLite
///////////////////////////////////////////////////////////////////
class SQLLiteTable extends PDOStandardTable implements IdbTable
{
	//parameters: $createTable boolean set true to create the table on initialization
	//return: true on success
	public function init($createTable, $pdo = NULL) //returns true on success
	{
		$this->errMsgs = "";
		try{
			$this->pdo = new PDO("sqlite:" . $this->dbName . ".sqlite3");
			//Throw exceptions
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			//Try this to fix SQLLite delete problem SOLVED just a stupid coding error
			//$this->pdo->setAttribute(PDO::ATTR_TIMEOUT,11);
		}catch(PDOException $e)
		{
			$this->err("new PDO: " . $e->getMessage() . " contact developer.");
			return false;
		}
		if($createTable)
		{
			try
			{
				$sql = "CREATE TABLE IF NOT EXISTS " .  $this->tableName . " (";
				foreach ($this->tablespecs as $key => $value)
				{
					$sql .= $key . " " . $value . ",";
				}
				$sql = trim($sql,",") . ");";
				$this->pdo->exec($sql);
			}catch(PDOException $e){
				$this->err("CREATE TABLE: " . $e->getMessage() . " CREATE TABLE IF NOT EXISTS on table: " . $this->tableName . " query [" . $sql . "]");
				return false;
			}
		}
		return true;
	}
	//Vacuum a SQLLite database table
	//This has no meaning for MYSQL
	//returns true on success
	public function vacuum()
	{
		try{
			$this->pdo->exec("VACUUM");
			return true;
		}catch(PDOException $e){
			$this->err("VACUUM: " . $e->getMessage());
			return false;
		}
	}
	
	//TODO SQLLite not tested
	//update one row of data
	//$row an associative array containg name value pairs to update
	//Uses the where clause for the update - otherwise uses the value of the primary key
	//return true on success
	public function updateRow($row, $where_clause = NULL)
	{
		$this->err("Not Implemented see class SQLLiteTable in  DbHelper.php");
		return false;
	}
	
	//Updates a mutiple number of rows into the table using the most efficient method for the implemented Datbase
	//parameters $recordset array of arrays or a PDOStatement object
	//The object contains an array of associative arrays containg name value pairs to be inserted
	//returns the number of rows updated or -1 on failure.
	public function updateFullRecordSet($recordset, $where_clause = NULL)
	{
		$this->err("Not Implemented see class SQLLiteTable in  DbHelper.php");
		return false;
	}

	//inserts one row of data into the table
	//parameters: $row an associative array containg name value pairs to insert
	//$primaryKeyName the primary key. If value is NULL insert id will be returned
	//returns true on success, false on failure.
	public function insertRow($row, $primaryKeyName)
	{
		$this->errMsgs = "";
		$sql = "INSERT INTO " . $this->tableName . "("; 
		foreach($this->tablespecs as $key => $value)
		{
			$sql .= $key . ",";
		}
		$sql = trim($sql,",") . ")VALUES(";
		foreach ($this->tablespecs as $key => $value)
		{
			$val = $row[$key];
			$val = "'" . $val . "'";
			$sql .= $val . ",";
		}
		$sql = trim($sql,",") . ");";
		try{
			//$this->logger->debug($sql);
			$ins = $this->pdo->prepare($sql);
			$ins->execute();
			//Return number of rows that were inserted
			return $ins->rowCount();
		}catch(PDOException $e)
		{
			$this->err("INSERT INTO: " . $e->getMessage() . " on " . $this->tableName . " query [" . $sql . "]");
			return false;
		}
		return true;
	}

	//Inserts a mutiple number of rows into the table using the most efficient method for the implemented Datbase
	//parameters $recordset array of arrays or a PDOStatement object
	//The object contains an array of associative arrays containg name value pairs to be inserted
	//			$nullKey boolean if true set the primary key value to NULL
	//returns the number of rows inserted or -1 on failure.
	public function insertFullRecordSet($recordset)
	{
		$this->errMsgs = "";
		$sql = 'INSERT INTO `' . $this->tableName . "` ";
		$i = 0;
		foreach($recordset as $row)
		{
			if($i == 0)
			{
				$sql .= "SELECT ";
				$i++;
				foreach ($this->tablespecs as $key => $value)
				{
					if(strpos($value, "PRIMARY KEY"))
					{
						$sql .= "NULL AS '" . $key . "',";
					}
					else
					{
						$sql .= "'" . $row[$key] . "' AS '" . $key . "',";
					}
				}
				$sql = trim($sql,",") . " ";
			}
			else
			{
				$sql .= " UNION SELECT ";
				foreach ($this->tablespecs as $key => $value)
				{
					if(strpos($value, "PRIMARY KEY"))
					{
						$sql .= "NULL ,";
					}
					else
					{
						$sql .= " '" . $row[$key] . "',";
					}
				}
				$sql = trim($sql,",");
			}
		}
		try{
			//$this->logger->debug($sql);
			$count = $this->pdo->exec($sql);
			return $count;
		}catch(PDOException $e){
			$this->err("INSERT INTO: " . $e->getMessage() . " using " . $this->tableName . " query [" . $sql . "]");
			return -1;
		}
	}
	//Test if table exist returns true if exist false if not
	public function doesTableExist()
	{
		$this->errMsgs = "";
		// Check if the table exists by doing a select on the SQLite 
		// table called 'sqlite_master' to check if the table name 
		$sql = "SELECT count(*) as count FROM `sqlite_master` WHERE `tbl_name` = '" . $this->tableName . "';"; 
		try{
			$sth = $this->pdo->prepare($sql);
			$sth->execute();
			$result = $sth->fetchColumn();
			return $result == 1;
		}catch(PDOException $e){
			$this->err("SELECT: " . $e->getMessage() . " using query [" . $sql . "]");
			return false;
		}
	}
}
///////////////////////////////////////////////////////////////////
// class: RecordSetHelper
// Desc: This class has methods for callin the IdbTable interface
//			implementations
///////////////////////////////////////////////////////////////////
class RecordSetHelper{
	
	protected $logger;
	protected $idbTable;
	protected $errMsgs;
	protected $lastCount;
	protected $lastTotal;
	//Construct an object containing an instantiated idbTable implementation
	//$createTable boolean set to true to create the table if not exist in
	//the init method of the idbTable interface
	//NOTE createTable not implemented for MYSQL yet
	//use executeSQL to create a table in MYSQL
	function __construct(IdbTable $idbTable, $createTable, $pdo = NULL)
	{
		$this->logger = new Log4Me(RECORD_SET_HELPER_LOG_STATUS,"log.txt");
		$this->logger->setContext("RecordSetHelper", $_SERVER['PHP_SELF']);

		$this->errMsgs = "";
		$this->idbTable = $idbTable;
		if(!$this->idbTable->init($createTable, $pdo)) //returns true on success
		{
			$this->errMsgs = "idbTable init: " . $this->idbTable->getErrorMsg();
		}
	}
	//Call first before any updates
	//TODO very weak architecture fix this
	private function initUpdates()
	{
		$this->lastCount = 0;
		$this->lastTotal = 0;
	}
	public function getTableSpecs()
	{
		return $this->idbTable->getTableSpecs();
	}
	
	public function getErrMsg()
	{
		//return $this->errMsgs; BAD logic here
		return $this->idbTable->getErrorMsg();
	}
	
	public function getErrMsgList()
	{
		return $this->errMsgs;
	}
	
	public function logErrMsg($msg)
	{
		$this->idbTable->logErrMsg($msg);
	}
	
	//return number of rows modified or deleted by the SQL statement. If no rows were affected returns 0.
	//On error returns -1
	public function executeSQL($sql)
	{
		return $this->idbTable->executeSQL($sql);
	}
	//executes an SQL query
	//returns PDOStatement object or false on failure
	public function getRecordSet($sql)
	{
		if($recordSet = $this->idbTable->selectDataSet($sql))
		{
			return $recordSet;
		}
		//else
		//{
		//	$this->errMsgs = $this->idbTable->getErrorMsg();
		//}
		return false;
	}
	//executes an SQL query
	//returns associative array or false on failure
	public function getRowSet($sql)
	{
		if($rowSet = $this->idbTable->getRowSet($sql))
		{
			return $rowSet;
		}
		return false;
	}
	//executes a prepared statement using SQL query and parameters for query
	//returns associative array or false on failure
	public function getRowSetUsing($sql, $params)
	{
		if($rowSet = $this->idbTable->getRowSetUsing($sql, $params))
		{
			return $rowSet;
		}
		return false;
	}
	//returns the number of rows in the table or -1 on failure
	public function getRowCount()
	{
		return $this->idbTable->getCount();
	}
	//Truncate the table
	//returns the number of rows deleted or -1 on falure
	public function truncateTable()
	{
		$retVal = $this->idbTable->truncateTable();
		//if($retVal == -1)
		//{
		//	$this->errMsgs = $this->idbTable->getErrorMsg();
		//}
		return $retVal;
	}
	
	//inserts one row of data into the table
	//parameters: $row an associative array containg name value pairs to insert
	//$primaryKeyName
	//For MYSQL returns primary key value on success SQLite returns true, All return false on failure.
	public function insertRow($row, $primaryKeyName)
	{
		return $this->idbTable->insertRow($row, $primaryKeyName);
	}
	
	//Vacuum a SQLLite database table
	//This has no meaning for MYSQL
	//returns true on success
	public function vacuum()
	{
		return $this->idbTable->vacuum();
	}
	
	//
	public function deleteRow($id, $key_name)
	{
		return $this->idbTable->deleteRow($id, $key_name);
	}
	
	//Delete multiple Rows by primary key values
	//returns true on success
	//parameter $idArray an array of key values
	public function deleteRecordSet($idArray, $key_name)
	{
		$this->initUpdates();
		$this->errMsgs = "";
		$retVal = true;
		foreach($idArray as $id)
		{
			if(!$test = $this->idbTable->deleteRow($id, $key_name)){
				$retVal = false;
				$this->errMsgs .= $this->idbTable->getErrorMsg();
			}
			else
			{
				$this->lastCount += $test;
			}
			$this->lastTotal++;
		}
		return $retVal;
	}
	//Inserts a mutiple number of rows into the table using the most efficient method for the implemented Datbase
	//parameters $recordset array of arrays or a PDOStatement object
	//The object contains an array of associative arrays containg name value pairs to be inserted
	//returns the number of rows inserted or -1 on failure.
	public function insertFullRecordSet($recordset)
	{
		$retVal = $this->idbTable->insertFullRecordSet($recordset);
		//if($retVal == -1)
		//{
		//	$this->errMsgs = $this->idbTable->getErrorMsg();
		//}
		return $retVal;
	}
	//Test if table exist returns true if exist false if not
	public function doesTableExist()
	{
		return $this->idbTable->doesTableExist();
	}
	//update one row of data
	//$row an associative array containg name value pairs to insert
	//Uses the where clause for the update - otherwise uses the value of the primary key
	//return true on success
	public function updateRow($row, $where_clause = NULL)
	{
		$retVal = $this->idbTable->updateRow($row, $where_clause);
		//if($retVal === false)
		//{
		//	$this->errMsgs = $this->idbTable->getErrorMsg();
		//}
		return $retVal;
	}
	
	//Updatess a mutiple number of rows into the table using the most efficient method for the implemented Datbase
	//parameters $recordset array of arrays or a PDOStatement object
	//The object contains an array of associative arrays containg name value pairs to be updated
	//returns the number of rows updated or -1 on failure.
	public function updateFullRecordSet($recordset, $where_clause)
	{
		$retVal = $this->idbTable->updateFullRecordSet($recordset, $where_clause);
		//if($retVal === false)
		//{
		//	$this->errMsgs = $this->idbTable->getErrorMsg();
		//}
		return $retVal;
	}
	
	//return initialized PDO object or null
	public function getPDO()
	{
		return $this->idbTable->getPDO();
	}
	
	//return the database table name
	public function getTableName()
	{
		return $this->idbTable->tableName;
	}
}
?>