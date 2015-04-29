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
	define("FOREIGN_KEY", "FOREIGN_KEY");
	define("PRI", "PRI");
	
	function makeFieldConstants($rs)
	{
		foreach($rs as $rw)
		{
			echo TAB . 'const ' . strtoupper($rw[FIELD]) . ' = "' . $rw[FIELD] . '";' . '<br>';
		}
		echo '<br>';
	}
	
	function getClassName($class_name, $field_name)
	{
		switch($class_name)
		{
			///////////////////  Dont forget the ending comma  ///////////////////
			case "MtcMember":
				if("membership_category_id" == $field_name)
				{
					return '"class":"MtcMembershipCategory",';
				}
				break;
			case "MtcCourtReservation":
				if("member1_id" == $field_name)
				{
					return '"class":"MtcMemberSecure",';
				}
				if("court_id" == $field_name)
				{
					return '"class":"MtcCourt",';
				}
		}
		return '"class":"",';
	}
	
	function makeSchemaConst($table_name, $rs)
	{
		//primary key hack
		//always assume primary key is first
		$done = false;
		$class_name = formatNameForClass($table_name);
		echo TAB . '{<br>';
		echo TAB . TAB . '"namespace": "tobe.avro",' . '<br>';
		echo TAB . TAB . '"type": "record",' . '<br>';
		echo TAB . TAB . '"name": "' . $table_name . '",' . '<br>';
		echo TAB . TAB . '"class_name": "' . $class_name . '",' . '<br>';
		echo TAB . TAB . '"fields": [' . '<br>';
		$n = count($rs);
		$i = 0;
		$primary_key = NULL;
		foreach($rs as $rw)
		{
			$class_field_value = '';
			$type = $rw[TYPE];
			if($rw[KEY] == PRI)
			{
				//TODO we are assuming the first PRI is the only primary key
				//WARNING this could be a bad assumption
				//Probably need to make a list of primary and foreign key types
				//Or investigate querying for indexes
				if(!$done)
				{
					$primary_key = $rw[FIELD];
					$done = true;
					$type .= " [" . PRIMARY_KEY . "]";
				}
				else
				{
					$type .= "[" . FOREIGN_KEY . "]";
				}
			}
			
			//Add class Name for field
			$class_field_value = getClassName($class_name, $rw[FIELD]);

			echo TAB . TAB . TAB . '{"name": "' . $rw[FIELD] . '", "type": "' . $type . '", ' . $class_field_value . ' "optional": "' . $rw[OPTIONAL] . '"}';
			$i++;
			echo ($n != $i) ? ',' : ''; //No trailing comma
			echo '<br>';
		}
		echo TAB . TAB . '],' . '<br>';
		echo TAB . TAB . '"Primary_key": "' . $primary_key . '"' . '<br>';
		echo TAB . "}';" . '<br><br>';
		
		return $primary_key;
	}
	
	function makeCrud($table_name, $rs)
	{
		echo 'class Base' . ucfirst($table_name) . '<br>';
		echo '{' . '<br>';
		
		//make the schema constant
		echo TAB . "const SCHEMA = '" . '<br>';

		$primary_key = makeSchemaConst($table_name, $rs);
		
		echo TAB . 'const PRIMARY_KEY = "' . $primary_key . '";' . '<br>';
		echo TAB . 'const TABLE_NAME = "' . $table_name . '";' . '<br>';
		echo TAB . 'const CLASS_NAME = "' . formatNameForClass($table_name) . '";' . '<br>';
		//make constants for each field name
		makeFieldConstants($rs);
		
		//make the params array
		echo TAB . 'private static $params = array(';
		$i = 0;
		$n = count($rs);
		foreach($rs as $rw)
		{
			echo "'" . $rw[FIELD] . "'=>NULL";
			$i++;
			echo ($n != $i) ? ', ' : ''; //No trailing comma
		}
		echo ');<br><br>';
		
		//make a getter for it
		echo TAB . 'public final static function getParams()<br>';
		echo TAB .'{<br>';
		echo TAB .TAB . 'return self::$params;<br>';
		echo TAB . '}<br>';
		echo TAB . '<br>';

		//close class
		echo '}' . '<br>';
		echo '<br>';
		
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
		echo TAB . TAB . '//Use the ' . $baseClassName . ' definitions<br>';
		echo TAB . TAB . "parent::__construct('" . $baseClassName . "');<br>";
		echo TAB . '}<br>';
		echo '<br>';	
		echo TAB . 'public function getSchema($postArray=NULL)<br>';
		echo TAB . '{<br>';
		echo TAB . TAB . 'return $this->makeSchemaArray();<br>';
		echo TAB . '}<br>';
		echo '<br>';	
		if($baseClassName != "BaseMtc_member") //Use sub class to get Mtc_member info (no password etc.)
		{
			echo TAB . 'public function create($postArray)<br>';
			echo TAB . '{<br>';
			echo TAB . TAB . '//This array specifies the field names that are required to execute the method<br>';
			echo TAB . TAB . '$params = ' . $baseClassName . '::getParams();<br>';
			echo TAB . TAB . 'return $this->insertRow($postArray, $params);<br>'; 
			echo TAB . '}<br>';
			echo '<br>';	
			echo TAB . 'public function getList($postArray=NULL)<br>';
			echo TAB . '{<br>';
			echo TAB . TAB . 'return $this->selectItems($postArray);<br>';
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
		}
		echo '}<br>';
		echo '<br>';
	}
	
	function makeClasses()
	{
		echo '//////////////////////////////////////////////////////////////////////////////////<br>';
		echo '//<br>';
		date_default_timezone_set('EST5EDT');
		$dt = new DateTime();
		echo '//        ' . $dt->format('l M d Y H:i:s') . '<br>';
		echo '//			Auto Generated Classes - Please do NOT Modify<br>';
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
			makeCrud($table_name, $rs);
		}
		
		echo '<br>';
		echo '//////////////////////////////////////////////////////////////////////////////////<br>';
		echo '//<br>';
		echo '//        ' . $dt->format('l M d Y H:i:s') . '<br>';
		echo '//			Auto Generated Classes - Please do NOT Modify<br>';
		echo '//<br>';
		echo '//////////////////////////////////////////////////////////////////////////////////<br>';
		echo '<br><br>';

		echo '<br>';
		echo 'include_once "general/GenericData.class.php"; <br>';
		echo '<br>';

		//Make the sub classes
		foreach($recordset as $row)
		{
			$table_name = $row['Tables_in_' . DB_NAME];
			$sql = 'SHOW FULL COLUMNS FROM `' . $table_name . '`';
			$rs = $db->query($sql);
			makeSubClasses($table_name, $rs);
		}

	}
	
	makeClasses();
	
	////////////////////////////////////   TEST area /////////////////////////////////////////////////
	
	class SomeTestStuff
	{
		const SCHEMA = '
		{"namespace": "tobe.avro",
		 "type": "record",
		 "name": "User",
		 "fields": [
			 {"name": "name", "type": "string"},
			 {"name": "favorite_number",  "type": ["int", "null"]},
			 {"name": "favorite_color", "type": ["string", "null"]}
		 ]
		}';
		
		private static $params = array('key1' => 1, 'key2' => 2);
		public final static function getParams()
		{
			return self::$params;
		}
	
	}

	
	
	
	/*
	
		//Deprecated
	function makeConstants()
	{
		$db = new ToBeDb();
		$sql = 'SHOW FULL TABLES FROM ' . DB_NAME;
		$recordset = $db->query($sql);
		foreach($recordset as $row)
		{
			$table_name = $row['Tables_in_' . DB_NAME];
			echo 'class DB_' . strtoupper($table_name) . '<br>';
			echo '{' . '<br>';
			echo TAB . 'const TABLE_NAME = "' . $table_name . '";' . '<br>';
			
			//Add extras here
			if($table_name == HTMLBLOCK)
				echo TAB . 'const HTMLBLOCKS = "htmlBlocks";' . '<br>';
			if($table_name == PAGE)
				echo TAB . 'const PAGES = "pages";' . '<br>';
			
			$sql = 'SHOW FULL COLUMNS FROM `' . $table_name . '`';
			$rs = $db->query($sql);
			foreach($rs as $rw)
			{
				echo TAB . 'const ' . strtoupper($rw[FIELD]) . ' = "' . $rw[FIELD] . '";' . '<br>';
			}
			echo '}' . '<br>';
			echo ' ' . '<br>';  	
		}
	}

	
	class GenericExample
	{
		const SCHEMA = '
		{"namespace": "tobe.avro",
		 "type": "record",
		 "name": "User",
		 "fields": [
			 {"name": "name", "type": "string"},
			 {"name": "favorite_number",  "type": ["int", "null"]},
			 {"name": "favorite_color", "type": ["string", "null"]}
		 ]
		}';
	}
	*/
	
	/*
	define ('TABLE_NAME', 'table_name');
	define ('COLUMN_NAME', 'column_name');
	define ('IS_NULLABLE', 'is_nullable');
	define ('COLUMN_TYPE', 'column_type');
	define ('COLUMN_COMMENT', 'column_comment');
	
	define ('FIELD', 'Field');
	define ('TYPE', 'TYPE');
	define ('NULL', 'NULL');	
	define ('KEY', 'KEY');
	define ('DEFAULT', 'DEFAULT');
	define ('EXTRA', 'EXTRA');
	define ('COMMENT', 'COMMENT');

	//EXTRAS 
	define ('HTMLBLOCK', 'htmlBlock');
	define ('PAGE', 'page');
	*/
	
	/*
	class DB_PAGE_TYPE
	{
		const TABLE_NAME = "page_type";
		const PAGE_TYPE_ID = "page_type_id";
		const NAME = "name";
		const TITLE = "title";
		const DESCRIPTION = "description";
	}
	*/
	
?>
</code>
</body>
</html>
