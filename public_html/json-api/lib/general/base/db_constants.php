<?php

class DB_ATA_CLIENT
{
   const TABLE_NAME = "ata_client";
   const CLIENT_ID = "client_id";
   const LAST_NAME = "last_name";
   const FIRST_NAME = "first_name";
   const ADDRESS = "address";
   const CITY = "city";
   const POSTAL_CODE = "postal_code";
   const HOME_PHONE = "home_phone";
   const BUSINESS_PHONE = "business_phone";
   const EMAIL = "email";
   const PASSWORD = "password";
   const PASSWORD_HINT = "password_hint";
   const PASSWORD_HINT_ANSWER = "password_hint_answer";
   const UUID = "uuid";
}

class DB_ATA_CLIENT_HAS_ATA_PROGRAM
{
   const TABLE_NAME = "ata_client_has_ata_program";
   const CLIENT_ID = "client_id";
   const PROGRAM_ID = "program_id";
}

class DB_ATA_EVENT
{
   const TABLE_NAME = "ata_event";
   const EVENT_ID = "event_id";
   const EVENT_NAME = "event_name";
   const EVENT_DESCRIPTION = "event_description";
   const EVENT_DATE_TIME = "event_date_time";
   const PAGE_ID = "page_id";
}

class DB_ATA_EVENT_HAS_ATA_CLIENT
{
   const TABLE_NAME = "ata_event_has_ata_client";
   const EVENT_ID = "event_id";
   const CLIENT_ID = "client_id";
}

class DB_ATA_LESSON
{
   const TABLE_NAME = "ata_lesson";
   const LESSON_ID = "lesson_id";
   const LESSON_NAME = "lesson_name";
   const LESSON_DESCRIPTION = "lesson_description";
   const PAGE_ID = "page_id";
}

class DB_ATA_LESSON_HAS_ATA_CLIENT
{
   const TABLE_NAME = "ata_lesson_has_ata_client";
   const LESSON_ID = "lesson_id";
   const CLIENT_ID = "client_id";
}

class DB_ATA_PROGRAM
{
   const TABLE_NAME = "ata_program";
   const PROGRAM_ID = "program_id";
   const PROGRAM_NAME = "program_name";
   const PROGRAM_DESCRIPTION = "program_description";
   const START_DATE = "start_date";
   const END_DATE = "end_date";
   const TIME = "time";
   const PAGE_ID = "page_id";
}
/*
class DB_HTMLBLOCK
{
   const TABLE_NAME = "htmlBlock";
   const HTMLBLOCKS = "htmlBlocks";
   const HTMLBLOCK_ID = "htmlBlock_id";
   const CONTENT = "content";
   const TITLE = "title";
   const TYPE = "type";
   const PAGE_ID = "page_id";
}
*/
class DB_MTC_COURT
{
   const TABLE_NAME = "mtc_court";
   const COURT_ID = "court_id";
   const COURT_NAME = "court_name";
   const COURT_TYPE = "court_type";
}

class DB_MTC_COURT_RESERVATION
{
   const TABLE_NAME = "mtc_court_reservation";
   const COURT_RESERVATION_ID = "court_reservation_id";
   const DATE = "date";
   const START_TIME = "start_time";
   const END_TIME = "end_time";
}

class DB_MTC_EVENT
{
   const TABLE_NAME = "mtc_event";
   const EVENT_ID = "event_id";
   const EVENT_NAME = "event_name";
   const EVENT_DESCRIPTION = "event_description";
   const EVENT_DATE_TIME = "event_date_time";
   const PAGE_ID = "page_id";
}

class DB_MTC_EVENT_HAS_MTC_MEMBER
{
   const TABLE_NAME = "mtc_event_has_mtc_member";
   const EVENT_ID = "event_id";
   const MEMBER_ID = "member_id";
}

class DB_MTC_GROUP
{
   const TABLE_NAME = "mtc_group";
   const GROUP_ID = "group_id";
   const GROUP_NAME = "group_name";
   const GROUP_TYPE = "group_type";
   const PRIMARY_MEMBER_ID = "primary_member_id";
}

class DB_MTC_GUEST
{
   const TABLE_NAME = "mtc_guest";
   const GUEST_ID = "guest_id";
   const LAST_NAME = "last_name";
   const FIRST_NAME = "first_name";
   const SPONSOR = "sponsor";
}

class DB_MTC_MEMBER
{
   const TABLE_NAME = "mtc_member";
   const MEMBER_ID = "member_id";
   const LAST_NAME = "last_name";
   const FIRST_NAME = "first_name";
   const ADDRESS = "address";
   const CITY = "city";
   const POSTAL_CODE = "postal_code";
   const HOME_PHONE = "home_phone";
   const BUSINESS_PHONE = "business_phone";
   const EMAIL = "email";
   const PASSWORD = "password";
   const PASSWORD_HINT = "password_hint";
   const PASSWORD_HINT_ANSWER = "password_hint_answer";
   const UUID = "uuid";
}

class DB_MTC_MEMBER_HAS_MTC_GROUP
{
   const TABLE_NAME = "mtc_member_has_mtc_group";
   const MEMBER_ID = "member_id";
   const GROUP_ID = "group_id";
}

class DB_MTC_PLAYER_GROUP_HAS_COURT_RESERVATION
{
   const TABLE_NAME = "mtc_player_group_has_court_reservation";
   const PLAYER_GROUP_ID = "player_group_id";
   const COURT_RESERVATION_ID = "court_reservation_id";
   const RESERVER_MEMBER_ID = "reserver_member_id";
   const MEMBER_ID = "member_id";
}

class DB_MTC_PLAYER_GROUP_MEMBER
{
   const TABLE_NAME = "mtc_player_group_member";
   const GUEST_ID = "guest_id";
   const MEMBER_ID = "member_id";
   const PLAYER_GROUP_ID = "player_group_id";
   const PLAYER_GROUP_MEMBER_ID = "player_group_member_id";
}
/*
class DB_PAGE
{
   const TABLE_NAME = "page";
   const PAGES = "pages";
   const PAGE_ID = "page_id";
   const TITLE = "title";
   const PAGE_TYPE_ID = "page_type_id";
}

class DB_PAGE_TYPE
{
   const TABLE_NAME = "page_type";
   const PAGE_TYPE_ID = "page_type_id";
   const NAME = "name";
   const TITLE = "title";
   const DESCRIPTION = "description";
}
*/
///////////////////////////////////////

/*
class DB_PAGE_TYPE
{
	const TABLE_NAME = "page_type";
	const PAGE_TYPE_ID = "page_type_id";
	const NAME = "name";
	const TITLE = "title";
	const DESCRIPTION = "description";
}

class DB_PAGE
{
	const TABLE_NAME = "page";
	const PAGES = "pages";
	const PAGE_ID = "page_id";
	const TITLE = "title";
	const PAGE_TYPE_ID = "page_type_id";	
}

class DB_HTMLBLOCK
{
	const TABLE_NAME = "htmlBlock";
	const HTMLBLOCKS = "htmlBlocks";
	const HTMLBLOCK_ID = "htmlBlock_id";
	const CONTENT = "content";
	const TITLE = "title";
	const TYPE = "type";
	const PAGE_ID = "page_id";
}
*/

?>