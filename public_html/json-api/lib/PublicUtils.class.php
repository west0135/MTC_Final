<?php
include_once "general/GenericData.class.php";

class Utils
{
	//const mtc_res_images = "http://marchtennisclub.com/res/images/";
	
	private $res;
	private $images_path;
	public function __construct()
   	{
		//$this->res = $_SERVER['DOCUMENT_ROOT'] . "/json-api/lib/res/";
		//$this->images_path = $_SERVER['DOCUMENT_ROOT'] . "/res/images";
		$this->res = $_SERVER['DOCUMENT_ROOT'] . GLOBAL_PATHS::MTC_RES;
		$this->images_path = $_SERVER['DOCUMENT_ROOT'] . GLOBAL_PATHS::MTC_IMAGES_PATH;

   	}
	
	public function getLabels($postArray)
	{
		//getting then decoding json is a little bit of extra work but keeps the method witin the framework
		if(isset($postArray['language']))
		{
			$path = $this->res . $postArray['language'] . '_strings.json';
			$json = file_get_contents ($path);
		}
		else
		{
			$path = $this->res . 'en_strings.json';
			$json = file_get_contents ($path);
		}
		return json_decode($json);
	}
	
	public function getImageUrlList($postArray=NULL)
	{
		$dir = $this->images_path;
		//$dir = $_SERVER['DOCUMENT_ROOT'] . "/json-api/lib/res";
		//$lst = @scandir($dir);
		$lst = scandir($dir);
		if(!$lst)
		{
			return array("status" => "ERROR", "errMsg" => "No Images in[" . $dir . "]");
		}
		$fields = array();
		foreach($lst as $item)
		{
			if($item == '.' || $item == '..' || $item === null) { continue; }
			//$url = self::mtc_res_images . $item;
			$url = GLOBAL_PATHS::MTC_RES_IMAGES . $item;
			$fields[] = array("url" => $url);
		}
		return array("status" => "SUCCESS", "fields" => $fields, "path" => GLOBAL_PATHS::MTC_RES_IMAGES);
	}

}

