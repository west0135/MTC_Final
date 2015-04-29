<?php

require_once "base/G12Object.class.php";
require_once "base/G12SchemaData.class.php";

class Collections
{
	const CATAGORIES = "catagories";
	const CATAGORY_ITEMS = "catagory_items";
	
	protected $logger;
	protected $errLog;
	private $category_baseClass;
	private $items_baseClass;
	private $dbCategory;
	private $objectItem;
	private $categoryTableName;
	private $itemTableName;
	
	protected $category_primary_key_name;
	protected $item_primary_key_name;
	protected $category_schema_array;
	protected $items_schema_array;
	
	public function __construct($category_baseClass, $items_baseClass)
	{
		$this->logger = new Log4Me(OBJECT_LOG_STATUS,"log.txt");
		$this->logger->setContext("COLLECTIONS", $_SERVER['PHP_SELF']);
		$this->errlog = new Log4Me(OBJECT_ERROR_LOG_STATUS,"error.txt");
		$this->errlog->setContext("COLLECTIONS OBJECT ERROR", $_SERVER['PHP_SELF']);
		
		$this->categoryTableName = $category_baseClass::TABLE_NAME;
		$this->itemTableName = $items_baseClass::TABLE_NAME;
		
		$this->category_primary_key_name = $category_baseClass::PRIMARY_KEY;
		$this->item_primary_key_name = $items_baseClass::PRIMARY_KEY;
		
		$this->category_schema_array = json_decode($category_baseClass::SCHEMA);
		$this->items_schema_array = json_decode($items_baseClass::SCHEMA);
		
		$this->category_baseClass = $category_baseClass;
		$this->items_baseClass = $items_baseClass;
		
		
		$this->dbCategory = new ToBeRecordSetHelper(DB_NAME, $this->categoryTableName);
	}
	
	protected function returnErrorArray($message, $code = 0, $xtndMessage = "") 
	{
		$this->errlog->error($message . " code: " . $code . Log4Me::NWLN_CHAR . $xtndMessage);
		
		//Don't return extended error messages for production version
		$xtndMessage = G12Object::EXTENTED_ERR_MSG ? $xtndMessage : ""; 
		return array(RETVAL::STATUS => RETVAL::DB_ERROR, RETVAL::ERR_MSG => $message, RETVAL::EXTND_ERR_MSG => $xtndMessage);
	}

	/*
	public function categorySchema()
	{
		return $this->category_schema_array;
	}
	
	public function itemSchema()
	{
		return $this->items_schema_array;
	}
	
	public function categoryPrimaryKeyName()
	{
		return $this->category_primary_key_name;
	}
	
	public function itemPrimaryKeyName()
	{
		return $this->item_primary_key_name;
	}
	*/

	public function selectItems()
	{
		$sql = 'SELECT * FROM `' . $this->categoryTableName . '` ;';
		$recordSet = $this->dbCategory->getRowSet($sql);
		if(empty($recordSet))
		{
			return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, self::FIELDS => array()); //return empty array
		}
		else if($recordSet == false)
		{
			$extErrMsg = $category_baseClass::CLASS_NAME . ".getList: " . $this->dbCategory->getErrMsg();
			return $this->returnErrorArray("Failed to retrieve Items", RETVAL::DB_FAILED_QUERY, $extErrMsg);
		}
		else
		{
			$collection = array();
			$pdo = $this->dbCategory->getPDO();
			$psql = 'SELECT * FROM `' . $this->itemTableName . '` WHERE `' . $this->category_primary_key_name .
																	 '` = :' . $this->category_primary_key_name;
			try
			{
				$statement = $pdo->prepare($psql);
				//$this->logger->debug("PSQL:" . $psql);
				$catagories = array();
				foreach($recordSet as $row)
				{
					$id = $row[$this->category_primary_key_name];
					$params = array($this->category_primary_key_name => $id);
					$statement->execute($params);
					$itemsSet = $statement->fetchAll(PDO::FETCH_ASSOC);
					$items = array();
					foreach($itemsSet as $itemRow)
					{
						//$this->logger->debug("ITEM array " . print_r($itemRow, true));
						$items[] = $itemRow;
					}
					$row[self::CATAGORY_ITEMS] = $items;
					$catagories[] = $row;
				}
				//$this->logger->debug("PARAMS:" . print_r($params, true));
				//$statement->execute($params);
				//return $statement->fetchAll(PDO::FETCH_ASSOC);
				
			}catch(PDOException $e){
				//$this->logger->debug("DEBUG getMessage(" . $e->getMessage() . ")");
				//return false;
				//$this->rreturnErrorArray
			}
			//$this->logger->debug("LIST TEST" . print_r($recordSet, true));
			return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, self::CATAGORIES => $catagories);
		}
	}

}