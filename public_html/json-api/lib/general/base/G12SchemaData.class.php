<?php

//////////////////////////////////////////////////////////////////////////////////
//
// Thursday Apr 09 2015 22:42:37
//	Auto Generated Classes - Please do NOT Modify
//
//////////////////////////////////////////////////////////////////////////////////


class BaseAta_lesson
{
   const SCHEMA = '
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

   const PRIMARY_KEY = "lesson_id";
   const TABLE_NAME = "ata_lesson";
   const CLASS_NAME = "AtaLesson";
   const LESSON_ID = "lesson_id";
   const CATEGORY = "category";
   const NAME = "name";
   const LESSON_PRO = "lesson_pro";
   const LESSON_COST = "lesson_cost";
   const CONTENT = "content";

   private static $params = array('lesson_id'=>NULL, 'category'=>NULL, 'name'=>NULL, 'lesson_pro'=>NULL, 'lesson_cost'=>NULL, 'content'=>NULL);

   public final static function getParams()
   {
      return self::$params;
   }
   
}

class BaseAta_program
{
   const SCHEMA = '
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

   const PRIMARY_KEY = "program_id";
   const TABLE_NAME = "ata_program";
   const CLASS_NAME = "AtaProgram";
   const PROGRAM_ID = "program_id";
   const PROGRAM_NAME = "program_name";
   const PROGRAM_DESCRIPTION = "program_description";
   const START_DATE = "start_date";
   const END_DATE = "end_date";
   const START_TIME = "start_time";
   const END_TIME = "end_time";
   const DAYS = "days";
   const COST = "cost";
   const CONTENT = "content";
   const PRESTO_URL = "presto_url";
   const ATA_PROGRAM_CATEGORY_ID = "ata_program_category_id";

   private static $params = array('program_id'=>NULL, 'program_name'=>NULL, 'program_description'=>NULL, 'start_date'=>NULL, 'end_date'=>NULL, 'start_time'=>NULL, 'end_time'=>NULL, 'days'=>NULL, 'cost'=>NULL, 'content'=>NULL, 'presto_url'=>NULL, 'ata_program_category_id'=>NULL);

   public final static function getParams()
   {
      return self::$params;
   }
   
}

class BaseAta_program_category
{
   const SCHEMA = '
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

   const PRIMARY_KEY = "ata_program_category_id";
   const TABLE_NAME = "ata_program_category";
   const CLASS_NAME = "AtaProgramCategory";
   const ATA_PROGRAM_CATEGORY_ID = "ata_program_category_id";
   const NAME = "name";
   const CONTENT = "content";

   private static $params = array('ata_program_category_id'=>NULL, 'name'=>NULL, 'content'=>NULL);

   public final static function getParams()
   {
      return self::$params;
   }
   
}

class BaseCanned_query
{
   const SCHEMA = '
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

   const PRIMARY_KEY = "canned_query_id";
   const TABLE_NAME = "canned_query";
   const CLASS_NAME = "CannedQuery";
   const CANNED_QUERY_ID = "canned_query_id";
   const KEY = "key";
   const NAME = "name";
   const DESCRIPTION = "description";
   const FORM = "form";
   const QUERY = "query";
   const CLASS_LIST = "class_list";

   private static $params = array('canned_query_id'=>NULL, 'key'=>NULL, 'name'=>NULL, 'description'=>NULL, 'form'=>NULL, 'query'=>NULL, 'class_list'=>NULL);

   public final static function getParams()
   {
      return self::$params;
   }
   
}

class BaseEvent
{
   const SCHEMA = '
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

   const PRIMARY_KEY = "event_id";
   const TABLE_NAME = "event";
   const CLASS_NAME = "Event";
   const EVENT_ID = "event_id";
   const EVENT_TYPE = "event_type";
   const EVENT_NAME = "event_name";
   const EVENT_DESCRIPTION = "event_description";
   const EVENT_DATE_TIME = "event_date_time";
   const CONTENT = "content";
   const PRESTO_URL = "presto_url";

   private static $params = array('event_id'=>NULL, 'event_type'=>NULL, 'event_name'=>NULL, 'event_description'=>NULL, 'event_date_time'=>NULL, 'content'=>NULL, 'presto_url'=>NULL);

   public final static function getParams()
   {
      return self::$params;
   }
   
}

class BaseMtc_court
{
   const SCHEMA = '
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

   const PRIMARY_KEY = "court_id";
   const TABLE_NAME = "mtc_court";
   const CLASS_NAME = "MtcCourt";
   const COURT_ID = "court_id";
   const COURT_NAME = "court_name";
   const COURT_TYPE = "court_type";

   private static $params = array('court_id'=>NULL, 'court_name'=>NULL, 'court_type'=>NULL);

   public final static function getParams()
   {
      return self::$params;
   }
   
}

class BaseMtc_court_reservation
{
   const SCHEMA = '
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

   const PRIMARY_KEY = "court_reservation_id";
   const TABLE_NAME = "mtc_court_reservation";
   const CLASS_NAME = "MtcCourtReservation";
   const COURT_RESERVATION_ID = "court_reservation_id";
   const COURT_ID = "court_id";
   const TIME_STAMP = "time_stamp";
   const STATUS = "status";
   const DATE = "date";
   const START_TIME = "start_time";
   const END_TIME = "end_time";
   const MEMBER1_ID = "member1_id";
   const NOTES = "notes";

   private static $params = array('court_reservation_id'=>NULL, 'court_id'=>NULL, 'time_stamp'=>NULL, 'status'=>NULL, 'date'=>NULL, 'start_time'=>NULL, 'end_time'=>NULL, 'member1_id'=>NULL, 'notes'=>NULL);

   public final static function getParams()
   {
      return self::$params;
   }
   
}

class BaseMtc_email_confirm
{
   const SCHEMA = '
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

   const PRIMARY_KEY = "email_confirm_id";
   const TABLE_NAME = "mtc_email_confirm";
   const CLASS_NAME = "MtcEmailConfirm";
   const EMAIL_CONFIRM_ID = "email_confirm_id";
   const CONFIRMATION_CODE = "confirmation_code";
   const EMAIL = "email";
   const TIME_STAMP = "time_stamp";

   private static $params = array('email_confirm_id'=>NULL, 'confirmation_code'=>NULL, 'email'=>NULL, 'time_stamp'=>NULL);

   public final static function getParams()
   {
      return self::$params;
   }
   
}

class BaseMtc_member
{
   const SCHEMA = '
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

   const PRIMARY_KEY = "member_id";
   const TABLE_NAME = "mtc_member";
   const CLASS_NAME = "MtcMember";
   const MEMBER_ID = "member_id";
   const FIRST_NAME = "first_name";
   const LAST_NAME = "last_name";
   const ADDRESS = "address";
   const CITY = "city";
   const POSTAL_CODE = "postal_code";
   const HOME_PHONE = "home_phone";
   const BUSINESS_PHONE = "business_phone";
   const FAMILY_MEMBERS = "family_members";
   const EMAIL = "email";
   const PASSWORD = "password";
   const PASSWORD_HINT = "password_hint";
   const PASSWORD_HINT_ANSWER = "password_hint_answer";
   const AVATAR_URL = "avatar_url";
   const DONATE = "donate";
   const MEMBERSHIP_CATEGORY_ID = "membership_category_id";
   const AMOUNT_ENCLOSED = "amount_enclosed";

   private static $params = array('member_id'=>NULL, 'first_name'=>NULL, 'last_name'=>NULL, 'address'=>NULL, 'city'=>NULL, 'postal_code'=>NULL, 'home_phone'=>NULL, 'business_phone'=>NULL, 'family_members'=>NULL, 'email'=>NULL, 'password'=>NULL, 'password_hint'=>NULL, 'password_hint_answer'=>NULL, 'avatar_url'=>NULL, 'donate'=>NULL, 'membership_category_id'=>NULL, 'amount_enclosed'=>NULL);

   public final static function getParams()
   {
      return self::$params;
   }
   
}

class BaseMtc_membership_category
{
   const SCHEMA = '
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

   const PRIMARY_KEY = "membership_category_id";
   const TABLE_NAME = "mtc_membership_category";
   const CLASS_NAME = "MtcMembershipCategory";
   const MEMBERSHIP_CATEGORY_ID = "membership_category_id";
   const NAME = "name";
   const CATEGORY = "category";
   const FEE = "fee";

   private static $params = array('membership_category_id'=>NULL, 'name'=>NULL, 'category'=>NULL, 'fee'=>NULL);

   public final static function getParams()
   {
      return self::$params;
   }
   
}

class BaseMtc_notice
{
   const SCHEMA = '
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

   const PRIMARY_KEY = "notice_id";
   const TABLE_NAME = "mtc_notice";
   const CLASS_NAME = "MtcNotice";
   const NOTICE_ID = "notice_id";
   const NAME = "name";
   const TITLE = "title";
   const CATEGORY = "category";
   const CONTENT = "content";

   private static $params = array('notice_id'=>NULL, 'name'=>NULL, 'title'=>NULL, 'category'=>NULL, 'content'=>NULL);

   public final static function getParams()
   {
      return self::$params;
   }
   
}

class BaseMtc_open_dates
{
   const SCHEMA = '
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

   const PRIMARY_KEY = "open_dates_id";
   const TABLE_NAME = "mtc_open_dates";
   const CLASS_NAME = "MtcOpenDates";
   const OPEN_DATES_ID = "open_dates_id";
   const NAME = "name";
   const DAY = "day";
   const START_DATE = "start_date";
   const END_DATE = "end_date";
   const START_TIME = "start_time";
   const END_TIME = "end_time";
   const TYPE = "type";
   const COMMENTS = "comments";

   private static $params = array('open_dates_id'=>NULL, 'name'=>NULL, 'day'=>NULL, 'start_date'=>NULL, 'end_date'=>NULL, 'start_time'=>NULL, 'end_time'=>NULL, 'type'=>NULL, 'comments'=>NULL);

   public final static function getParams()
   {
      return self::$params;
   }
   
}

class BaseMtc_permissions
{
   const SCHEMA = '
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

   const PRIMARY_KEY = "permission_id";
   const TABLE_NAME = "mtc_permissions";
   const CLASS_NAME = "MtcPermissions";
   const PERMISSION_ID = "permission_id";
   const MEMBER_ID = "member_id";
   const FIRST_NAME = "first_name";
   const LAST_NAME = "last_name";
   const PERMISSIONS = "permissions";
   const COMMENTS = "comments";

   private static $params = array('permission_id'=>NULL, 'member_id'=>NULL, 'first_name'=>NULL, 'last_name'=>NULL, 'permissions'=>NULL, 'comments'=>NULL);

   public final static function getParams()
   {
      return self::$params;
   }
   
}

class BaseMtc_user_session
{
   const SCHEMA = '
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

   const PRIMARY_KEY = "";
   const TABLE_NAME = "mtc_user_session";
   const CLASS_NAME = "MtcUserSession";
   const USERID = "userid";
   const UKEY = "ukey";
   const IP_ADDRESS = "ip_address";
   const DEVICE = "device";
   const MODIFIED = "modified";

   private static $params = array('userid'=>NULL, 'ukey'=>NULL, 'ip_address'=>NULL, 'device'=>NULL, 'modified'=>NULL);

   public final static function getParams()
   {
      return self::$params;
   }
   
}

