<?php

include_once "DbHelper.class.php";

class ToBeRecordSetHelper extends RecordSetHelper
{
	function __construct($dbName, $tableName, $pdo = NULL)
	{
		parent::__construct(new MySQLTable($dbName, $tableName, $pdo), false);
	}
}