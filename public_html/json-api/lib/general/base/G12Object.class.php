<?php

require_once "Log4me.class.php";
require_once "ToBeRecordSetHelper.class.php";

class RETVAL
{
	const STATUS = "status";
	const METHOD = "method";
	const ERR_MSG = "errMsg";
	const EXTND_ERR_MSG = "xtndErrMsg";
	const DB_SUCCESS = "SUCCESS";
	const DB_ERROR = "ERROR";

	//deprecated
	//const PARAM_ERROR = "PARAM_ERROR";
	const NO_METHOD_SPECIFIED = "NO_METHOD_SPECIFIED";
	const UNKNOWN_METHOD = "UNKNOWN_METHOD";
	const UNKNOWN_DISPATCH_ERROR = "UNKNOWN_DISPATCH_ERROR";
	const EMPTY_RESULT_SET = "EMPTY_RESULT_SET";
	
	const ERROR_MISSING_PARAM = 1;
	const ERROR_INVALID_PARAM = 2;
	
	const DB_FAILED_INSERT = 3;
	const DB_FAILED_UPDATE = 4;
	const DB_FAILED_QUERY = 5;
	const DB_FAILED_DELETE = 6;
	
	const FILE_FAILED_SCAN = 7;

}

class G12Object
{
	//TODO TODO TODO --- For Production Release Turn extended error messages OFF
	const EXTENTED_ERR_MSG = false;
	
	protected $isInsert;
	protected $logger;
	protected $errlog;
	protected $primary_key;
	protected $schema;
	protected $schema_array = NULL;
	const FIELDS = "fields";
	const FIELD = "field";
	
	public function __construct($schema=NULL)
	{
		$this->logger = new Log4Me(OBJECT_LOG_STATUS,"log.txt");
		$this->logger->setContext("OBJECT", $_SERVER['PHP_SELF']);
		$this->errlog = new Log4Me(OBJECT_ERROR_LOG_STATUS,"error.txt");
		$this->errlog->setContext("G12 OBJECT ERROR", $_SERVER['PHP_SELF']);

		if($schema)
		{
			$this->schema = $schema;
			//$this->logger->debug("JSON SCHEMA:" . $this->schema);
			$this->schema_array = json_decode($this->schema);
			//$this->logger->debug("ARRAY[" . print_r($this->schema_array, true) . "]");
		}
	}

	protected function returnErrorArray($message, $code = 0, $xtndMessage = "") 
	{
		$this->errlog->error($message . " code: " . $code . Log4Me::NWLN_CHAR . $xtndMessage);
		
		//Don't return extended error messages for production version
		$xtndMessage = self::EXTENTED_ERR_MSG ? $xtndMessage : ""; 
		return array(RETVAL::STATUS => RETVAL::DB_ERROR, RETVAL::ERR_MSG => $message, RETVAL::EXTND_ERR_MSG => $xtndMessage);
	}
	
	protected function arrayToSelectFields($field_names_array)
	{
		$str = "";
		for($i = 0; $i < count($field_names_array); $i++)
		{
			$str .= "`" . $field_names_array[$i] . "`, ";
		}
		$str = rtrim($str, ", ") . " ";
		return $str;
	}
	
	protected function assocArrayToSelectFields($param_array)
	{
		//firstname = :firstname AND id = :id
		$str = "";
		foreach($param_array as $key => $value)
		{
			$str .= "`" . $key . "`, ";
		}
		$str = rtrim($str, ", ") . " ";
		return $str;
	}

	protected function assocArrayToParamList($param_array)
	{
		//firstname = :firstname AND id = :id
		$str = "";
		$count = 0;
		foreach($param_array as $key => $value)
		{
			$prefix = ($count != 0) ? " AND " : " ";
			$str .= $prefix . $key . " = :" . $key;
			$count++;	
		}
		return $str;
	}
	
	protected function getSchemaFields()
	{
		return $this->schema_array->fields;
	}
	
	//Loads parameter array from the $_POST array. returns true if no problems false otherwise
	//Parameters
	//$postArray - An array containing the values needed to execute the method 
	//&$paramArray - An array containing the specific names of fields to receive values to execute the method
	protected function loadParams($postArray, &$paramArray, $context="") 
	{
		//$testArray = array();
		foreach($paramArray as $key => $value)
		{
			if(isset($postArray[$key])) //$_POST[$key]))
			{
				$paramArray[$key] = $postArray[$key]; //$_POST[$key];
				//$this->logger->debug($paramArray[$key] . " = paramArray[" . $key . "] = " . $postArray[$key]);
			}
			else
			{
				unset($paramArray[$key]);
				//array_push($testArray, $key);
			}
		}
		return true;
		/*
		if(count($testArray) > 0)
		{
			$msg = $context . " is Missing Parameter(s)";
			$xtndMsg = "You are missing the following parameters: ";
			for($i = 0; $i < count($testArray); $i++)
			{
				$xtndMsg .= $testArray[$i] . ",";
			}
			$xtndMsg = rtrim($xtndMsg, ",");
			$xtndMsg .=  " from this array " . print_r($paramArray, true);
			$this->errlog->error($msg . " " . $xtndMsg);
			$this->logger->debug($msg . " " . $xtndMsg);
			$this->returnErrorArray($msg, RETVAL::ERROR_MISSING_PARAM, $xtndMsg); 
		}
		else
		{
			return true;
		}
		*/
	}
	
	protected function makeSchemaArray()
	{
		return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, "schema" => $this->schema_array);
	}
	
	private function getSchemaFieldByName($key)
	{
		$schemaFields = $this->getSchemaFields();
		foreach($schemaFields as $field)
		{
			if($field->name == $key)
			{
				return $field;
			}
		}
		return false;
	}
	
	//Returns false on no errors else error array explaining error
	protected function validateFields($paramArray)
	{
		foreach($paramArray as $key => $value)
		{
			$field = $this->getSchemaFieldByName($key);
			if(!$field)
			{
				return $this->returnErrorArray("Missing Parameter ", $code = RETVAL::ERROR_MISSING_PARAM,
					"validateFields Failed - Missing: Paremeter " . $key . " in " . print_r($paramArray,true));
			}
			//If field is Mandatory
			if($field->optional == 'NO')
			{
				if(empty($value) && $value != 0) //We must allow 0 for int fields
				{
					//Primary key can be empty on insert
					if(!($this->isInsert && $key == $this->primary_key))
					{
						return $this->returnErrorArray("Missing Mandatory Value for: " . $key, $code = RETVAL::ERROR_INVALID_PARAM,
						Log4Me::NWLN_CHAR . "validateFields Failed - Missing Mandatory Value for " . $key . " in " . print_r($paramArray,true));
					}
				}
			}
		}
		return false;
	}

}
