<?php


//////////////////////////////////////////////////////////////////////////////////
//
// Thursday Apr 09 2015 22:42:37
//	Auto Generated Classes - Please do NOT Modify
//
//////////////////////////////////////////////////////////////////////////////////



include_once "general/GenericData.class.php"; 

/**
   {
      "namespace": "tobe.avro",
      "type": "record",
      "name": "ata_lesson",
      "class_name": "AtaLesson",
      "fields": [
         {"name": "lesson_id", "type": "int(11) [PRIMARY_KEY]", "class":"", "optional": "NO"},
         {"name": "category", "type": "varchar(45)", "class":"", "optional": "NO"},
         {"name": "name", "type": "varchar(64)", "class":"", "optional": "NO"},
         {"name": "lesson_pro", "type": "varchar(45)", "class":"", "optional": "NO"},
         {"name": "lesson_cost", "type": "varchar(45)", "class":"", "optional": "YES"},
         {"name": "content", "type": "longtext", "class":"", "optional": "YES"}
      ],
      "Primary_key": "lesson_id"
   }';

*/
class AtaLesson extends Generic
{
   public function __construct()
   {
      //Use the BaseAta_lesson definitions
      parent::__construct('BaseAta_lesson');
   }

   public function getSchema($postArray=NULL)
   {
      return $this->makeSchemaArray();
   }

   public function create($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseAta_lesson::getParams();
      return $this->insertRow($postArray, $params);
   }

   public function getList($postArray=NULL)
   {
      return $this->selectItems($postArray);
   }

   public function get($postArray)
   {
      return $this->getItemById($postArray);
   }

   public function update($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseAta_lesson::getParams();
      return $this->updateRow($postArray, $params);
   }

   public function delete($postArray)
   {
      return $this->deleteItemById($postArray);
   }
}

/**
   {
      "namespace": "tobe.avro",
      "type": "record",
      "name": "ata_program",
      "class_name": "AtaProgram",
      "fields": [
         {"name": "program_id", "type": "int(11) [PRIMARY_KEY]", "class":"", "optional": "NO"},
         {"name": "program_name", "type": "varchar(64)", "class":"", "optional": "NO"},
         {"name": "program_description", "type": "varchar(1024)", "class":"", "optional": "NO"},
         {"name": "start_date", "type": "date", "class":"", "optional": "YES"},
         {"name": "end_date", "type": "date", "class":"", "optional": "YES"},
         {"name": "start_time", "type": "time", "class":"", "optional": "YES"},
         {"name": "end_time", "type": "time", "class":"", "optional": "YES"},
         {"name": "days", "type": "varchar(64)", "class":"", "optional": "YES"},
         {"name": "cost", "type": "varchar(64)", "class":"", "optional": "YES"},
         {"name": "content", "type": "longtext", "class":"", "optional": "YES"},
         {"name": "presto_url", "type": "varchar(400)", "class":"", "optional": "YES"},
         {"name": "ata_program_category_id", "type": "int(10) unsigned", "class":"", "optional": "YES"}
      ],
      "Primary_key": "program_id"
   }';

*/
class AtaProgram extends Generic
{
   public function __construct()
   {
      //Use the BaseAta_program definitions
      parent::__construct('BaseAta_program');
   }

   public function getSchema($postArray=NULL)
   {
      return $this->makeSchemaArray();
   }

   public function create($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseAta_program::getParams();
      return $this->insertRow($postArray, $params);
   }

   public function getList($postArray=NULL)
   {
      return $this->selectItems($postArray);
   }

   public function get($postArray)
   {
      return $this->getItemById($postArray);
   }

   public function update($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseAta_program::getParams();
      return $this->updateRow($postArray, $params);
   }

   public function delete($postArray)
   {
      return $this->deleteItemById($postArray);
   }
}

/**
   {
      "namespace": "tobe.avro",
      "type": "record",
      "name": "ata_program_category",
      "class_name": "AtaProgramCategory",
      "fields": [
         {"name": "ata_program_category_id", "type": "int(10) unsigned [PRIMARY_KEY]", "class":"", "optional": "NO"},
         {"name": "name", "type": "varchar(45)", "class":"", "optional": "NO"},
         {"name": "content", "type": "longtext", "class":"", "optional": "NO"}
      ],
      "Primary_key": "ata_program_category_id"
   }';

*/
class AtaProgramCategory extends Generic
{
   public function __construct()
   {
      //Use the BaseAta_program_category definitions
      parent::__construct('BaseAta_program_category');
   }

   public function getSchema($postArray=NULL)
   {
      return $this->makeSchemaArray();
   }

   public function create($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseAta_program_category::getParams();
      return $this->insertRow($postArray, $params);
   }

   public function getList($postArray=NULL)
   {
      return $this->selectItems($postArray);
   }

   public function get($postArray)
   {
      return $this->getItemById($postArray);
   }

   public function update($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseAta_program_category::getParams();
      return $this->updateRow($postArray, $params);
   }

   public function delete($postArray)
   {
      return $this->deleteItemById($postArray);
   }
}

/**
   {
      "namespace": "tobe.avro",
      "type": "record",
      "name": "canned_query",
      "class_name": "CannedQuery",
      "fields": [
         {"name": "canned_query_id", "type": "int(11) [PRIMARY_KEY]", "class":"", "optional": "NO"},
         {"name": "key", "type": "varchar(255)", "class":"", "optional": "NO"},
         {"name": "name", "type": "varchar(255)", "class":"", "optional": "NO"},
         {"name": "description", "type": "varchar(255)", "class":"", "optional": "YES"},
         {"name": "form", "type": "varchar(1024)", "class":"", "optional": "NO"},
         {"name": "query", "type": "varchar(1024)", "class":"", "optional": "NO"},
         {"name": "class_list", "type": "varchar(255)", "class":"", "optional": "YES"}
      ],
      "Primary_key": "canned_query_id"
   }';

*/
class CannedQuery extends Generic
{
   public function __construct()
   {
      //Use the BaseCanned_query definitions
      parent::__construct('BaseCanned_query');
   }

   public function getSchema($postArray=NULL)
   {
      return $this->makeSchemaArray();
   }

   public function create($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseCanned_query::getParams();
      return $this->insertRow($postArray, $params);
   }

   public function getList($postArray=NULL)
   {
      return $this->selectItems($postArray);
   }

   public function get($postArray)
   {
      return $this->getItemById($postArray);
   }

   public function update($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseCanned_query::getParams();
      return $this->updateRow($postArray, $params);
   }

   public function delete($postArray)
   {
      return $this->deleteItemById($postArray);
   }
}

/**
   {
      "namespace": "tobe.avro",
      "type": "record",
      "name": "event",
      "class_name": "Event",
      "fields": [
         {"name": "event_id", "type": "int(11) [PRIMARY_KEY]", "class":"", "optional": "NO"},
         {"name": "event_type", "type": "varchar(45)", "class":"", "optional": "NO"},
         {"name": "event_name", "type": "varchar(64)", "class":"", "optional": "NO"},
         {"name": "event_description", "type": "varchar(1024)", "class":"", "optional": "NO"},
         {"name": "event_date_time", "type": "datetime", "class":"", "optional": "NO"},
         {"name": "content", "type": "longtext", "class":"", "optional": "YES"},
         {"name": "presto_url", "type": "varchar(400)", "class":"", "optional": "YES"}
      ],
      "Primary_key": "event_id"
   }';

*/
class Event extends Generic
{
   public function __construct()
   {
      //Use the BaseEvent definitions
      parent::__construct('BaseEvent');
   }

   public function getSchema($postArray=NULL)
   {
      return $this->makeSchemaArray();
   }

   public function create($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseEvent::getParams();
      return $this->insertRow($postArray, $params);
   }

   public function getList($postArray=NULL)
   {
      return $this->selectItems($postArray);
   }

   public function get($postArray)
   {
      return $this->getItemById($postArray);
   }

   public function update($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseEvent::getParams();
      return $this->updateRow($postArray, $params);
   }

   public function delete($postArray)
   {
      return $this->deleteItemById($postArray);
   }
}

/**
   {
      "namespace": "tobe.avro",
      "type": "record",
      "name": "mtc_court",
      "class_name": "MtcCourt",
      "fields": [
         {"name": "court_id", "type": "int(11) [PRIMARY_KEY]", "class":"", "optional": "NO"},
         {"name": "court_name", "type": "varchar(45)", "class":"", "optional": "NO"},
         {"name": "court_type", "type": "varchar(45)", "class":"", "optional": "NO"}
      ],
      "Primary_key": "court_id"
   }';

*/
class MtcCourt extends Generic
{
   public function __construct()
   {
      //Use the BaseMtc_court definitions
      parent::__construct('BaseMtc_court');
   }

   public function getSchema($postArray=NULL)
   {
      return $this->makeSchemaArray();
   }

   public function create($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseMtc_court::getParams();
      return $this->insertRow($postArray, $params);
   }

   public function getList($postArray=NULL)
   {
      return $this->selectItems($postArray);
   }

   public function get($postArray)
   {
      return $this->getItemById($postArray);
   }

   public function update($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseMtc_court::getParams();
      return $this->updateRow($postArray, $params);
   }

   public function delete($postArray)
   {
      return $this->deleteItemById($postArray);
   }
}

/**
   {
      "namespace": "tobe.avro",
      "type": "record",
      "name": "mtc_court_reservation",
      "class_name": "MtcCourtReservation",
      "fields": [
         {"name": "court_reservation_id", "type": "int(11) [PRIMARY_KEY]", "class":"", "optional": "NO"},
         {"name": "court_id", "type": "int(11)", "class":"MtcCourt", "optional": "NO"},
         {"name": "time_stamp", "type": "timestamp", "class":"", "optional": "NO"},
         {"name": "status", "type": "varchar(16)", "class":"", "optional": "YES"},
         {"name": "date", "type": "date", "class":"", "optional": "NO"},
         {"name": "start_time", "type": "time", "class":"", "optional": "NO"},
         {"name": "end_time", "type": "time", "class":"", "optional": "NO"},
         {"name": "member1_id", "type": "int(11)[FOREIGN_KEY]", "class":"MtcMemberSecure", "optional": "NO"},
         {"name": "notes", "type": "varchar(255)", "class":"", "optional": "YES"}
      ],
      "Primary_key": "court_reservation_id"
   }';

*/
class MtcCourtReservation extends Generic
{
   public function __construct()
   {
      //Use the BaseMtc_court_reservation definitions
      parent::__construct('BaseMtc_court_reservation');
   }

   public function getSchema($postArray=NULL)
   {
      return $this->makeSchemaArray();
   }

   public function create($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseMtc_court_reservation::getParams();
      return $this->insertRow($postArray, $params);
   }

   public function getList($postArray=NULL)
   {
      return $this->selectItems($postArray);
   }

   public function get($postArray)
   {
      return $this->getItemById($postArray);
   }

   public function update($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseMtc_court_reservation::getParams();
      return $this->updateRow($postArray, $params);
   }

   public function delete($postArray)
   {
      return $this->deleteItemById($postArray);
   }
}

/**
   {
      "namespace": "tobe.avro",
      "type": "record",
      "name": "mtc_email_confirm",
      "class_name": "MtcEmailConfirm",
      "fields": [
         {"name": "email_confirm_id", "type": "int(10) [PRIMARY_KEY]", "class":"", "optional": "NO"},
         {"name": "confirmation_code", "type": "varchar(32)", "class":"", "optional": "NO"},
         {"name": "email", "type": "varchar(200)", "class":"", "optional": "NO"},
         {"name": "time_stamp", "type": "timestamp", "class":"", "optional": "NO"}
      ],
      "Primary_key": "email_confirm_id"
   }';

*/
class MtcEmailConfirm extends Generic
{
   public function __construct()
   {
      //Use the BaseMtc_email_confirm definitions
      parent::__construct('BaseMtc_email_confirm');
   }

   public function getSchema($postArray=NULL)
   {
      return $this->makeSchemaArray();
   }

   public function create($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseMtc_email_confirm::getParams();
      return $this->insertRow($postArray, $params);
   }

   public function getList($postArray=NULL)
   {
      return $this->selectItems($postArray);
   }

   public function get($postArray)
   {
      return $this->getItemById($postArray);
   }

   public function update($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseMtc_email_confirm::getParams();
      return $this->updateRow($postArray, $params);
   }

   public function delete($postArray)
   {
      return $this->deleteItemById($postArray);
   }
}

/**
   {
      "namespace": "tobe.avro",
      "type": "record",
      "name": "mtc_member",
      "class_name": "MtcMember",
      "fields": [
         {"name": "member_id", "type": "int(11) [PRIMARY_KEY]", "class":"", "optional": "NO"},
         {"name": "first_name", "type": "varchar(64)", "class":"", "optional": "NO"},
         {"name": "last_name", "type": "varchar(64)", "class":"", "optional": "NO"},
         {"name": "address", "type": "varchar(64)", "class":"", "optional": "NO"},
         {"name": "city", "type": "varchar(45)", "class":"", "optional": "NO"},
         {"name": "postal_code", "type": "varchar(7)", "class":"", "optional": "NO"},
         {"name": "home_phone", "type": "varchar(16)", "class":"", "optional": "YES"},
         {"name": "business_phone", "type": "varchar(16)", "class":"", "optional": "YES"},
         {"name": "family_members", "type": "varchar(1024)", "class":"", "optional": "YES"},
         {"name": "email", "type": "varchar(200)", "class":"", "optional": "NO"},
         {"name": "password", "type": "varchar(70)", "class":"", "optional": "NO"},
         {"name": "password_hint", "type": "varchar(64)", "class":"", "optional": "YES"},
         {"name": "password_hint_answer", "type": "varchar(64)", "class":"", "optional": "YES"},
         {"name": "avatar_url", "type": "varchar(400)", "class":"", "optional": "YES"},
         {"name": "donate", "type": "tinyint(1)", "class":"", "optional": "YES"},
         {"name": "membership_category_id", "type": "int(11)", "class":"MtcMembershipCategory", "optional": "NO"},
         {"name": "amount_enclosed", "type": "varchar(45)", "class":"", "optional": "YES"}
      ],
      "Primary_key": "member_id"
   }';

*/
class MtcMember extends Generic
{
   public function __construct()
   {
      //Use the BaseMtc_member definitions
      parent::__construct('BaseMtc_member');
   }

   public function getSchema($postArray=NULL)
   {
      return $this->makeSchemaArray();
   }

}

/**
   {
      "namespace": "tobe.avro",
      "type": "record",
      "name": "mtc_membership_category",
      "class_name": "MtcMembershipCategory",
      "fields": [
         {"name": "membership_category_id", "type": "int(11) [PRIMARY_KEY]", "class":"", "optional": "NO"},
         {"name": "name", "type": "varchar(64)", "class":"", "optional": "NO"},
         {"name": "category", "type": "varchar(255)", "class":"", "optional": "NO"},
         {"name": "fee", "type": "varchar(45)", "class":"", "optional": "NO"}
      ],
      "Primary_key": "membership_category_id"
   }';

*/
class MtcMembershipCategory extends Generic
{
   public function __construct()
   {
      //Use the BaseMtc_membership_category definitions
      parent::__construct('BaseMtc_membership_category');
   }

   public function getSchema($postArray=NULL)
   {
      return $this->makeSchemaArray();
   }

   public function create($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseMtc_membership_category::getParams();
      return $this->insertRow($postArray, $params);
   }

   public function getList($postArray=NULL)
   {
      return $this->selectItems($postArray);
   }

   public function get($postArray)
   {
      return $this->getItemById($postArray);
   }

   public function update($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseMtc_membership_category::getParams();
      return $this->updateRow($postArray, $params);
   }

   public function delete($postArray)
   {
      return $this->deleteItemById($postArray);
   }
}

/**
   {
      "namespace": "tobe.avro",
      "type": "record",
      "name": "mtc_notice",
      "class_name": "MtcNotice",
      "fields": [
         {"name": "notice_id", "type": "int(11) [PRIMARY_KEY]", "class":"", "optional": "NO"},
         {"name": "name", "type": "varchar(64)", "class":"", "optional": "NO"},
         {"name": "title", "type": "varchar(64)", "class":"", "optional": "NO"},
         {"name": "category", "type": "varchar(64)", "class":"", "optional": "YES"},
         {"name": "content", "type": "longtext", "class":"", "optional": "YES"}
      ],
      "Primary_key": "notice_id"
   }';

*/
class MtcNotice extends Generic
{
   public function __construct()
   {
      //Use the BaseMtc_notice definitions
      parent::__construct('BaseMtc_notice');
   }

   public function getSchema($postArray=NULL)
   {
      return $this->makeSchemaArray();
   }

   public function create($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseMtc_notice::getParams();
      return $this->insertRow($postArray, $params);
   }

   public function getList($postArray=NULL)
   {
      return $this->selectItems($postArray);
   }

   public function get($postArray)
   {
      return $this->getItemById($postArray);
   }

   public function update($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseMtc_notice::getParams();
      return $this->updateRow($postArray, $params);
   }

   public function delete($postArray)
   {
      return $this->deleteItemById($postArray);
   }
}

/**
   {
      "namespace": "tobe.avro",
      "type": "record",
      "name": "mtc_open_dates",
      "class_name": "MtcOpenDates",
      "fields": [
         {"name": "open_dates_id", "type": "int(11) [PRIMARY_KEY]", "class":"", "optional": "NO"},
         {"name": "name", "type": "varchar(64)", "class":"", "optional": "NO"},
         {"name": "day", "type": "varchar(10)", "class":"", "optional": "NO"},
         {"name": "start_date", "type": "date", "class":"", "optional": "NO"},
         {"name": "end_date", "type": "date", "class":"", "optional": "NO"},
         {"name": "start_time", "type": "time", "class":"", "optional": "NO"},
         {"name": "end_time", "type": "time", "class":"", "optional": "NO"},
         {"name": "type", "type": "smallint(6)", "class":"", "optional": "NO"},
         {"name": "comments", "type": "varchar(255)", "class":"", "optional": "YES"}
      ],
      "Primary_key": "open_dates_id"
   }';

*/
class MtcOpenDates extends Generic
{
   public function __construct()
   {
      //Use the BaseMtc_open_dates definitions
      parent::__construct('BaseMtc_open_dates');
   }

   public function getSchema($postArray=NULL)
   {
      return $this->makeSchemaArray();
   }

   public function create($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseMtc_open_dates::getParams();
      return $this->insertRow($postArray, $params);
   }

   public function getList($postArray=NULL)
   {
      return $this->selectItems($postArray);
   }

   public function get($postArray)
   {
      return $this->getItemById($postArray);
   }

   public function update($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseMtc_open_dates::getParams();
      return $this->updateRow($postArray, $params);
   }

   public function delete($postArray)
   {
      return $this->deleteItemById($postArray);
   }
}

/**
   {
      "namespace": "tobe.avro",
      "type": "record",
      "name": "mtc_permissions",
      "class_name": "MtcPermissions",
      "fields": [
         {"name": "permission_id", "type": "int(11) [PRIMARY_KEY]", "class":"", "optional": "NO"},
         {"name": "member_id", "type": "int(11)", "class":"", "optional": "NO"},
         {"name": "first_name", "type": "varchar(45)", "class":"", "optional": "NO"},
         {"name": "last_name", "type": "varchar(45)", "class":"", "optional": "NO"},
         {"name": "permissions", "type": "int(11)", "class":"", "optional": "NO"},
         {"name": "comments", "type": "varchar(45)", "class":"", "optional": "NO"}
      ],
      "Primary_key": "permission_id"
   }';

*/
class MtcPermissions extends Generic
{
   public function __construct()
   {
      //Use the BaseMtc_permissions definitions
      parent::__construct('BaseMtc_permissions');
   }

   public function getSchema($postArray=NULL)
   {
      return $this->makeSchemaArray();
   }

   public function create($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseMtc_permissions::getParams();
      return $this->insertRow($postArray, $params);
   }

   public function getList($postArray=NULL)
   {
      return $this->selectItems($postArray);
   }

   public function get($postArray)
   {
      return $this->getItemById($postArray);
   }

   public function update($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseMtc_permissions::getParams();
      return $this->updateRow($postArray, $params);
   }

   public function delete($postArray)
   {
      return $this->deleteItemById($postArray);
   }
}

/**
   {
      "namespace": "tobe.avro",
      "type": "record",
      "name": "mtc_user_session",
      "class_name": "MtcUserSession",
      "fields": [
         {"name": "userid", "type": "int(11)", "class":"", "optional": "NO"},
         {"name": "ukey", "type": "varchar(13)", "class":"", "optional": "NO"},
         {"name": "ip_address", "type": "varchar(30)", "class":"", "optional": "NO"},
         {"name": "device", "type": "varchar(200)", "class":"", "optional": "NO"},
         {"name": "modified", "type": "timestamp", "class":"", "optional": "NO"}
      ],
      "Primary_key": ""
   }';

*/
class MtcUserSession extends Generic
{
   public function __construct()
   {
      //Use the BaseMtc_user_session definitions
      parent::__construct('BaseMtc_user_session');
   }

   public function getSchema($postArray=NULL)
   {
      return $this->makeSchemaArray();
   }

   public function create($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseMtc_user_session::getParams();
      return $this->insertRow($postArray, $params);
   }

   public function getList($postArray=NULL)
   {
      return $this->selectItems($postArray);
   }

   public function get($postArray)
   {
      return $this->getItemById($postArray);
   }

   public function update($postArray)
   {
      //This array specifies the field names that are required to execute the method
      $params = BaseMtc_user_session::getParams();
      return $this->updateRow($postArray, $params);
   }

   public function delete($postArray)
   {
      return $this->deleteItemById($postArray);
   }
}
