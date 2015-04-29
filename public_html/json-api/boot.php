<?php
/**
 * Import all class files here
 * Initialize global constants
 *
 * @author Thomas Wiegand (wieg0002)
 * @version 1.0
 *
 */
 
/*
Commenting Guidelines - use Javadoc style commenting as above

Order of Tags
Include tags in the following order:

@author (classes and interfaces only, required)
@version (classes and interfaces only, required. See footnote 1)
@param (methods and constructors only)
@return (methods only)
@exception (@throws is a synonym added in Javadoc 1.2)
@see
@since
@serial (or @serialField or @serialData)
@deprecated (see How and When To Deprecate APIs)

*/

require_once "lib/general/base/Log4me.class.php";
$logger = new Log4Me(GLOBAL_LOG_STATUS,"log.txt");
$logger->setContext("BOOT UP THE SERVICE", $_SERVER['PHP_SELF']);

require_once "lib/PublicData.class.php";
require_once "lib/PublicCollections.class.php";
require_once "lib/PublicCourts.class.php";
require_once "lib/PublicMtcMembers.class.php";
require_once "lib/Db.class.php";
require_once "lib/security.class.php";
require_once "lib/PublicUtils.class.php";
require_once "lib/PublicCannedQuery.class.php";
require_once "lib/PublicMtcOpenDates.class.php";
require_once "lib/PublicEventsHelper.class.php";


?>