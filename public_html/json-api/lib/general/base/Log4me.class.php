<?php
//NOTE for edumedia turn all off
//Set individual Loggers On or Off by Class

/*
define('GLOBAL_LOG_STATUS', Log4Me::DEBUG);
define('OBJECT_LOG_STATUS', Log4Me::DEBUG);
define('OBJECT_ERROR_LOG_STATUS', Log4Me::DEBUG);
define('PDO_STANDARD_TABLE_LOG_STATUS', Log4Me::DEBUG);
define('PDO_STANDARD_TABLE_ERROR_LOG_STATUS', Log4Me::ERROR); //Log4Me::ERROR
define('RECORD_SET_HELPER_LOG_STATUS', Log4Me::DEBUG);
define('TO_BE_DB_ERROR_LOG_STATUS', Log4Me::ERROR);
*/


define('GLOBAL_LOG_STATUS', Log4Me::OFF);
define('OBJECT_LOG_STATUS', Log4Me::OFF);
define('OBJECT_ERROR_LOG_STATUS', Log4Me::OFF);
define('PDO_STANDARD_TABLE_LOG_STATUS', Log4Me::OFF);
define('PDO_STANDARD_TABLE_ERROR_LOG_STATUS', Log4Me::OFF); //Log4Me::ERROR
define('RECORD_SET_HELPER_LOG_STATUS', Log4Me::OFF);
define('TO_BE_DB_ERROR_LOG_STATUS', Log4Me::OFF);


class Log4Me
{
	const NWLN_CHAR = "\r\n";
	const DEBUG = 5;
	const INFO = 4;
	const WARN = 3;
	const ERROR = 2;
	const FATAL = 1;
	const OFF = 0;
	
	const TEST_MSG = false;
	
	const MAX_SIZE = 100000; //200K
	
	private $mode;
	private $filename;
	private $fullpath;
	private $path;
	private $context = "";
	private $caller = "";
	private $size;
	private $verbose = true;
	
	public function __construct($mode, $filename)
	{
		date_default_timezone_set('America/New_York');
		
		$this->mode = $mode;
		$this->filename = $filename;
		$this->path = "logs/";
		$this->fullpath = $this->path . $this->filename;
		/*
		if(!$isLocal)
		{
			if($this->check_dir($this->path))
			{
				$this->fullpath = $this->path . $this->filename; 
			}
			else
			{
				$this->fullpath = $this->filename; 
			}
		}
		*/
		$this->size = 0;
		if(file_exists($this->fullpath))
		{
			//clearstatcache (true, $this->fullpath);
			$this->size = filesize($this->fullpath);
			$this->testMsg("CONSTRUCTOR", "FILE_SIZE[" . $this->size . "]");
			if($this->size > self::MAX_SIZE)
			{
				$make_new = true;
				//copy existing file to a unique file name
				$newfile = $this->path . time() . "_" . $this->filename;
				copy ($this->fullpath , $newfile);
				//delete the old
				unlink($this->fullpath);
				$this->size = 0;
				$this->testMsg("CONSTRUCTOR", "NEW_FILE " . $newfile );
			}
		}
		else
		{
			$this->testMsg("CONSTRUCTOR", "NO_FILE");
		}
	}
	
	public function setContext($str,$caller)
	{
		$this->context = $str;
		$this->caller = $caller;
		$this->testMsg("SET_CONTEXT ", $str . " " .$caller);
	}
	
	public function setSingleLineMode()
	{
		$this->verbose = false;
	}
	
	public function debug($msg)
	{
		if($this->mode > self::INFO)
		{
			$this->logToFile($msg);
		}
	}
	
	public function info($msg)
	{
		if($this->mode > self::WARN)
		{
			$this->logToFile($msg);
		}
	}

	public function warn($msg)
	{
		if($this->mode > self::ERROR)
		{
			$this->logToFile($msg);
		}
	}

	public function error($msg)
	{
		if($this->mode > self::FATAL)
		{
			$this->logToFile($msg);
		}
	}

	public function fatal($msg)
	{
		if($this->mode > self::OFF)
		{
			$this->logToFile($msg);
		}
	}
	
	private function getDateTime()
	{
		$date = new DateTime();
		return $date->format(DateTime::W3C);
	}

	private function testMsg($context, $msg)
	{
		if(self::TEST_MSG)
		{
			// open test file
			$testFile = $this->path . "test.txt";
			 
			$fd = fopen($testFile, "a");
			$str = self::NWLN_CHAR;
			$str .= $context . " [" . $this->getDateTime() . "]" . self::NWLN_CHAR;
			$str .=  $msg; 
			// write string
			fwrite($fd, $str .self::NWLN_CHAR);
			// close file
			fclose($fd);
		}
	}
	
	private function logToFile($msg)
	{ 
		// open file
		$fd = fopen($this->fullpath, "a");
		$str = "";
		if($this->verbose)
		{
			$str .= self::NWLN_CHAR;
			if(strlen($this->context))
			{
				$str .= $this->context . self::NWLN_CHAR;
			}
			$str .=  $this->levelToString() . " at " . $this->getDateTime() . " in " . $this->caller . self::NWLN_CHAR . $msg;
		}
		else
		{
			$str .= $msg;
		}
		// write string
		fwrite($fd, $str . self::NWLN_CHAR);
		// close file
		fclose($fd);
	}


	//Checks if directory exists - if exists return true
	//if !exist create new dir return true on success, false on failure
	private function check_dir($dir)
	{
		if(file_exists($dir))
		{
			return true;
		}
		else
		{
			if(@mkdir($dir))
			{
				//echo "<h1>SUCCESS New Folder</h1>";
				return true;
			}
			else
			{
				//echo "<h1>FAIL New Folder</h1>";
				return false;
			}
		}
	}
	
	private function levelToString()
	{
		switch($this->mode)
		{
			case self::OFF:
				return "OFF";
			case self::DEBUG:
				return "DEBUG";
			case self::INFO:
				return "INFO";
			case self::WARN:
				return "WARN";
			case self::ERROR:
				return "ERROR";
			case self::FATAL:
				return "FATAL";
			default:
				return "mode:" . $this->mode;
		}
	}
}

?>
