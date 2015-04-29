<?php
include_once "general/GenericData.class.php";
include_once "general/Email.class.php";

class MtcMemberSecure extends MtcMember
{
	private function mdFiveEm(&$postArray)
	{
		if(!empty($postArray['password_hint_answer']))
		{
			$pswd_hint = md5($postArray['password_hint_answer']);
			$postArray['password_hint_answer'] = $pswd_hint;		
		}

		if(!empty($postArray['password']))
		{
			$pswd = md5($postArray['password']);
			$postArray['password'] = $pswd;		
		}
	}
	
	public function __construct()
   	{
      	parent::__construct();
   	}

	public function create($postArray)
   	{
		$this->logger->debug("DEBUG1");
		//Convert password and password_hint_answer to md5
		$this->mdFiveEm($postArray);
		//This array specifies the field names that are required to execute the method
		$params = BaseMtc_member::getParams();
		
		//Extend generic behviour add permissions
		$this->isInsert = true;
		if($this->loadParams($postArray, $params))
		{
			$errTest = $this->validateFields($params);
			if($errTest === false)
			{
				$pdo = $this->dbHelper->getPDO();
				$pdo->beginTransaction();
				$id = $this->dbHelper->insertRow($params, $this->primaryKeyName);
				if($id == false)
				{
					$extErrMsg = $this->table_name . ": " . $this->dbHelper->getErrMsg() . " Row Values: " . print_r($params, true);
					if(!strpos('Duplicate entry', $this->dbHelper->getErrMsg()))
					{
						if(!strpos("for key 'email'", $this->dbHelper->getErrMsg()))
						{
							return $this->returnErrorArray("The email " . $params['email'] . " has already been used.", RETVAL::DB_FAILED_INSERT, $extErrMsg);
						}
						return $this->returnErrorArray("Duplicate Entry", RETVAL::DB_FAILED_INSERT, $extErrMsg);
					}
					return $this->returnErrorArray("Failed to insert Data", RETVAL::DB_FAILED_INSERT, $extErrMsg);
				}
				else
				{
					$perms = new ToBeRecordSetHelper(DB_NAME, "mtc_permissions", $pdo);
					$date = new DateTime();
					$permission_params = array("permission_id" => NULL, "member_id" => $id, 
												"first_name" => $params['first_name'], "last_name" => $params['last_name'],
												"permissions" => 0, "comments" => $date->format('Y-m-d H:i:s'));
											 
					$perm_id = $perms->insertRow($permission_params, "permission_id");
					if($perm_id == false)
					{
						$pdo->rollBack();
						$extErrMsg = $this->table_name . ": " . $this->dbHelper->getErrMsg() . " Row Values: " . print_r($params, true);
						return $this->returnErrorArray("Failed to insert Permission Data", RETVAL::DB_FAILED_INSERT, $extErrMsg);
					}
					else
					{
						$confirm = new ToBeRecordSetHelper(DB_NAME, "mtc_email_confirm", $pdo);
						$confirmationCode = md5($this->random_string(10));
						$confirm_params = array("email_confirm_id" => NULL, "confirmation_code" => $confirmationCode, "email" => $params['email']);
						$confirm_id = $confirm->insertRow($confirm_params, "email_confirm_id");
						if ($confirm_id == false) {
							$pdo->rollBack();
							$extErrMsg = 'mtc_email_confirm' . ": confirm_id:" . $confirm_id . " Row Values: " . print_r($confirm_params, true);
							return $this->returnErrorArray("Failed to insert Permission Data", RETVAL::DB_FAILED_INSERT, $extErrMsg);
						} else
						{
							$pdo->commit();
							$this->sendRegistrationEmail($params, $confirmationCode);
							return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, $this->primaryKeyName => $id);
						}
						return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, $this->primaryKeyName => $id);
					}
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

	private function sendRegistrationEmail($params, $confirmationCode)
	{
		$email = new email();
		$email->sendWelcomeEmail($params['first_name'], $params['email'], $confirmationCode, $params['amount_enclosed']);
	}
	
	private function random_string($length)
	{
		// we are using only this characters/numbers in the string generation  
		$chars ="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
		$string =''; // define variable with empty value
		// we generate a random integer first, then we are getting corresponding character , then append the character to $string variable. we are repeating this cycle until it reaches the given length 
		for($i=0;$i<$length; $i++) 
		{
			$string .= $chars[rand(0,strlen($chars)-1)];
	 
		}
		return $string ; // return the final string 
	}

   	public function update($postArray)
   	{
		//Convert password and password_hint_answer to md5
		$this->mdFiveEm($postArray);
		//This array specifies the field names that are required to execute the method
		$params = BaseMtc_member::getParams();
		return $this->updateRow($postArray, $params);
   	}
	
	public function delete($id)
	{
		if(is_array($id)) //get the primary key value
		{
			$id = $id[$this->primaryKeyName];
		}
		if(is_numeric($id))
		{
			$pdo = $this->dbHelper->getPDO();
			$pdo->beginTransaction();
			try{
				$sql = 'DELETE FROM `mtc_court_reservation` WHERE `member1_id` = ' . $id;
				$pdo->exec($sql);
			}catch(PDOException $e){
				$pdo->rollBack();
				$xtErrMsg =  "Failed Delete for mtc_court_reservation";
				return $this->returnErrorArray("Delete Error", RETVAL::DB_FAILED_DELETE, $xtErrMsg);
			}
			try{
				$sql = 'DELETE FROM `mtc_permissions` WHERE `member_id` = ' . $id;
				$pdo->exec($sql);
			}catch(PDOException $e){
				$pdo->rollBack();
				$xtErrMsg =  "Failed Delete for mtc_permissions";
				return $this->returnErrorArray("Delete Error", RETVAL::DB_FAILED_DELETE, $xtErrMsg);
			}
			try{
				$sql = 'DELETE FROM `mtc_user_session` WHERE `userid` = ' . $id;
				$pdo->exec($sql);
			}catch(PDOException $e){
				$pdo->rollBack();
				$xtErrMsg =  "Failed Delete for mtc_user_session";
				return $this->returnErrorArray("Delete Error", RETVAL::DB_FAILED_DELETE, $xtErrMsg);
			}
			if($this->dbHelper->deleteRow($id, $this->primaryKeyName))
			{
				$pdo->commit();
				return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, $this->primaryKeyName => $id);
			}
			else
			{
				$pdo->rollBack();
				$xtErrMsg =  "Failed Delete for " . $this->className . " id:" . $id . " " . $this->dbHelper->getErrMsg();
				return $this->returnErrorArray("Database Error", RETVAL::DB_FAILED_DELETE, $xtErrMsg);
			}
		}
		else
		{
			$xtErrMsg =  "Failed Delete for " . $this->className . " " . $id . " is NOT a numeric value";
			return $this->returnErrorArray("Delete Error", RETVAL::DB_FAILED_DELETE, $xtErrMsg);
		}
	}

	private function makeWhereClause(&$postArray)
	{
		$i = 0;
		$order_by = "";
		$str = "";
		foreach($postArray as $key => $value)
		{
			if(0 == $i)
			{
				$str .= ' WHERE `' . $key . '` LIKE :' . $key . ' ';
				$order_by = ' ORDER BY `' . $key . '` ';
			}
			else
			{
				$str .= ' AND `' . $key . '` LIKE :' . $key . ' ';
			}
			$val = $postArray[$key];
			//Append the wild card to value
			$postArray[$key] = $val . "%";
			$i++;
		}
		$str .= $order_by;
		return $str;
	}
	
	public function search($postArray)
	{
		//strip off unwanted params
		unset($postArray['method']);

		$where_clause = $this->makeWhereClause($postArray);
		$sql = 'SELECT * FROM `' . $this->table_name . '` ' . $where_clause . ' ;';
		$this->logger->debug("DEBUG search sql: " . $sql);
		
		$pdo = $this->dbHelper->getPDO();
		try
		{
			$statement = $pdo->prepare($sql);
			$retVal = $statement->execute($postArray);
			if($retVal)
			{
				$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
				if($rows)
				{
					//Remove sensitive fields
					unset($rows['password']);
					//unset($rows['password_hint']);
					unset($rows['password_hint_answer']);
					unset($rows['uuid']);
					return array(RETVAL::STATUS => RETVAL::DB_SUCCESS, "fields" => $rows);
				}
				elseif(empty($rows))
				{
					$xtErrMsg =  "No Records for Query: " . $sql . " POST ARRAY: " . print_r($postArray, true);
					return $this->returnErrorArray("No Return. Check input values. ", RETVAL::DB_FAILED_QUERY, $xtErrMsg);
				}
				else
				{
					$extErrMsg = "No Records for Query: " . $sql . " POST ARRAY: " . print_r($postArray, true);
					return $this->returnErrorArray("Failed to retrieve Items. ", RETVAL::DB_FAILED_QUERY, $extErrMsg);
				}
			}
			else
			{
				$extErrMsg = "FAILED Query: " . $sql . " POST ARRAY: " . print_r($postArray, true);
				return $this->returnErrorArray("Failed to Prepare search. ", RETVAL::DB_FAILED_QUERY, $extErrMsg);
			}
		}catch(PDOException $e){
			$extErrMsg = "Exception on " . $sql . " POST ARRAY: " . print_r($postArray, true);
			return $this->returnErrorArray("Failed to Execute search. ", RETVAL::DB_FAILED_QUERY, $extErrMsg);
		}
	}
	
	public function getList($postArray=NULL)
	{
		//This array specifies the field names that are required to execute the method
      	$params = BaseMtc_member::getParams();
		//Remove sensitive fields
		unset($params['password']);
		//unset($params['password_hint']);
		unset($params['password_hint_answer']);
		unset($params['uuid']);
		return $this->selectItemsUsing($postArray, $params);
	}
	
	public function get($postArray)
   	{
		//This array specifies the field names that are required to execute the method
      	$params = BaseMtc_member::getParams();
		//Remove sensitive fields
		unset($params['password']);
		//unset($params['password_hint']);
		unset($params['password_hint_answer']);
		unset($params['uuid']);
    	return $this->getItemByIdUsing($postArray, $params);
   	}
}

