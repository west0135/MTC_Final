<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Generate Basic Data Classes</title>
</head>
<body>
<code>
<?php
	
	ini_set('display_errors','On'); error_reporting(E_ALL);

	require_once "ToBeDb.class.php";
	
	define("TAB","&nbsp;&nbsp;&nbsp");
	define("SPC", "&nbsp;");
	define("FIELD", "Field");
	define("TYPE", "Type");
	define("OPTIONAL", "Null");
	//Could add more here
	define("KEY", "Key");
	define("DEFAULT", "Default");
	define("COMMENT", "Comment");
	define("PRIMARY_KEY", "PRIMARY_KEY");
	define("PRI", "PRI");
	
	function makeFieldConstants($rs)
	{
		foreach($rs as $rw)
		{
			echo TAB . $rw[FIELD] . '<br>';
		}
		echo '<br>';
	}
	
	function makeTableTerms($table_name, $rs)
	{
		echo TAB . 'TABLE_NAME ' . $table_name . '<br>';
		echo TAB . 'CLASS_NAME ' . formatNameForClass($table_name) . '<br>';
		//make constants for each field name
		makeFieldConstants($rs);
		echo ');<br><br>';
	}
	
	function formatNameForClass($name)
	{
		$arr = explode("_", $name);
		for($i = 0; $i < count($arr); $i++)
		{
			$arr[$i] = ucfirst($arr[$i]);
			//echo $arr[$i] . " ";
		}
		//echo '<br>';
		return implode("", $arr);
	}

	
	//$className, $table_name, $primaryKeyName
	function makeSubClasses($table_name, $rs)
	{
		$className = formatNameForClass($table_name);
		$baseClassName = 'Base' . ucfirst($table_name);
		echo '/**<br>';
		makeSchemaConst($table_name, $rs);
		echo '*/<br>';
		echo 'class ' . $className . '  extends Generic<br>';
		//class ATALesson extends Generic
		echo '{<br>';
		echo TAB . 'public function __construct()<br>';
		echo TAB . '{<br>';
		echo TAB . TAB . '//Use the ' . $table_name . ' table<br>';
		echo TAB . TAB . 'parent::__construct("' . $className . '", ' . $baseClassName . '::TABLE_NAME, <br>';
		echo TAB . TAB . $baseClassName . '::PRIMARY_KEY, ' . $baseClassName . '::SCHEMA);<br>';
		echo TAB . '}<br>';
		echo '<br>';	
		echo TAB . 'public function getSchema($postArray=NULL)<br>';
		echo TAB . '{<br>';
		echo TAB . TAB . 'return $this->makeSchemaArray();<br>';
		echo TAB . '}<br>';
		echo '<br>';	
		echo TAB . 'public function create($postArray)<br>';
		echo TAB . '{<br>';
		echo TAB . TAB . '//This array specifies the field names that are required to execute the method<br>';
		echo TAB . TAB . '$params = ' . $baseClassName . '::getParams();<br>';
		echo TAB . TAB . 'return $this->insertRow($postArray, $params);<br>'; 
		echo TAB . '}<br>';
		echo '<br>';	
		echo TAB . 'public function getList($postArray=NULL)<br>';
		echo TAB . '{<br>';
		echo TAB . TAB . 'return $this->selectItems();<br>';
		echo TAB . '}<br>';
		echo '<br>';	
		echo TAB . 'public function get($postArray)<br>';
	   	echo TAB . '{<br>';
		echo TAB . TAB . 'return $this->getItemById($postArray);<br>';
	   	echo TAB . '}<br>';
		echo '<br>';	
		echo TAB . 'public function update($postArray)<br>';
		echo TAB . '{<br>';
		echo TAB . TAB . '//This array specifies the field names that are required to execute the method<br>';
		echo TAB . TAB . '$params = ' . $baseClassName . '::getParams();<br>';
		echo TAB . TAB . 'return $this->updateRow($postArray, $params);<br>';
		echo TAB . '}<br>';
		echo '<br>';
	   	echo TAB . 'public function delete($postArray)<br>';
	   	echo TAB . '{<br>';
		echo TAB . TAB . 'return $this->deleteItemById($postArray);<br>';
	   	echo TAB . '}<br>';

		echo '}<br>';
		echo '<br>';
	}
	
	function makeTERMS()
	{
		echo '//////////////////////////////////////////////////////////////////////////////////<br>';
		echo '//<br>';
		echo '//			Auto Generated terminology';
		echo '//<br>';
		echo '//////////////////////////////////////////////////////////////////////////////////<br>';
		echo '<br><br>';

		$db = new ToBeDb();
		$sql = 'SHOW FULL TABLES FROM ' . DB_NAME;
		$rs;
		$recordset = $db->query($sql);
		foreach($recordset as $row)
		{
			$table_name = $row['Tables_in_' . DB_NAME];
			$sql = 'SHOW FULL COLUMNS FROM `' . $table_name . '`';
			$rs = $db->query($sql);
			makeTableTerms($table_name, $rs);
		}
		

	}
	
	makeTerms();
	
	
	
	
?>
</code>
</body>
</html>
