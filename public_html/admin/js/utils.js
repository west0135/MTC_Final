//////////////////////////////////////////////  Common Functions ////////////////////////////////// 

//var DOMAIN = "http://geopad.ca";
//var DOMAIN = "http://clients.edumedia.ca";
//var JSON_API_URL = DOMAIN + "/to_be/json-api/";
//var LOGIN_SERVER = DOMAIN + "/to_be/kirk/assets/server.php";

DOMAIN = "http://marchtennisclub.com";
var JSON_API_URL = DOMAIN + "/json-api/";
var LOGIN_SERVER = DOMAIN + "/security/server.php";


//Permissions TODO get from server if possible
var NONE = 0;
var MEMBERSHIP = 1; 		
var RESERVATION = 2; 		
var COURT_CAPTAIN = 3;	
var EDITOR = 4; 	
var ADMIN = 5;		

var RESERVED = "RESERVED"; //the reservation has been created but the individual has not been placed on a court yet
var CONFIRMED = "CONFIRMED"; //A reservation has been created and the individual has been placed on a court
var COMPLETE = "COMPLETE"; //Individual who was on a court with a reservation has finished and been removed from the 
