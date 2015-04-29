<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Generate Basic Language JSON</title>
</head>
<body>
<code>
<?php
	
	ini_set('display_errors','On'); error_reporting(E_ALL);

	require_once "ToBeDb.class.php";
	
	define("TAB","&nbsp;&nbsp;&nbsp");
	//define("SPC", "&nbsp;");
	define("FIELD", "Field");
	define("TYPE", "Type");
	//define("OPTIONAL", "Null");
	//Could add more here
	define("KEY", "Key");
	//define("DEFAULT", "Default");
	//define("COMMENT", "Comment");
	//define("PRIMARY_KEY", "PRIMARY_KEY");
	define("PRI", "PRI");
	
	function makeJsonFields($table_name, $rs, $isLastTable)
	{
		//primary key hack
		//always assume primary key is first
		$done = false;
		$className = formatNameForClass($table_name);
		$n = count($rs);
		$i = 0;
		foreach($rs as $rw)
		{
			if($rw[KEY] == PRI)
			{
				if(!$done)
				{
					//For Primary Key use Table Name
					echo TAB . '{"name":"' . $className . '.' . $rw[FIELD] . '","value":"' . formatLabel($table_name) . '"}'; 
					$done = true;
				}
				else //Probably a foreign Key
				{
					echo TAB . '{"name":"' . $className . '.' . $rw[FIELD] . '","value":"' . formatLabel($rw[FIELD]) . '"}';  	
				}
			}
			else
			{
				echo TAB . '{"name":"' . $className . '.' . $rw[FIELD] . '","value":"' . formatLabel($rw[FIELD]) . '"}';  	
			}
			$i++;
			if($isLastTable) //proccessing last table fields
			{
				echo ($n != $i) ? ',' : ''; //No trailing comma for last item
			}
			else
			{
				echo ',';
			}
			echo '<br>';
		}
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
	
	function formatLabel($field)
	{
		$arr = explode("_", $field);
		for($i = 0; $i < count($arr); $i++)
		{
			$arr[$i] = ucfirst($arr[$i]);
		}
		return implode(" ", $arr);
	}

	function makeJson()
	{
		$db = new ToBeDb();
		$sql = 'SHOW FULL TABLES FROM ' . DB_NAME;
		$rs;
		$recordset = $db->query($sql);
		$n = count($recordset);
		$i = 0;
		echo '{"status":"SUCCESS","fields":[<br>';
		foreach($recordset as $row)
		{
			$i++;
			$isLastTable = $n == $i;
			$table_name = $row['Tables_in_' . DB_NAME];
			$sql = 'SHOW FULL COLUMNS FROM `' . $table_name . '`';
			$rs = $db->query($sql);
			makeJsonFields($table_name, $rs, $isLastTable);
		}
		echo ']}<br>';
	}
	makeJson();
	
?>
</code>
</body>
</html>
