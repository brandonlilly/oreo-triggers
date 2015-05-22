<?php

error_reporting( E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR|E_PARSE|E_USER_ERROR );

// Enabling error display will greatly increase compile time
// ini_set('display_errors', '1');

// Version precedence
//      1 < 1.0
//    1.0 < 1.01
//   1.01 = 1.1
//    1.1 < 1.10
//   1.10 > 1.10b
//  1.10b < 1.10.0
$oreo_version = "1.0.1";

$SwitchArray = array();
$SwitchRange = null;
function switch_range($min, $max){
	if( $min < 1 ){         die( "You can't set the switch minimum below 1! (fix in config.php)" ); }
	if( $max > 255 ){       die( "You can't set the switch maximum above 255! (fix in config.php)" ); }
	if( $max - $min < 0 ){  die( "Your switch range max can't be less than your switch range minimum! (fix in config.php)" ); }
	global $SwitchArray;
	global $SwitchRange;
	$SwitchRange = $max - $min + 1;
	for($i=0; $i <= $max; $i++) {
		if( $i < $min ){ $SwitchArray[] = 5; }
		else { $SwitchArray[] = 0; }
	}
}

$CheckForUpdates = false;
function check_for_updates($bool = true){
	if( !is_bool($bool) ) { die("You can only pass a boolean to check_for_updates() (i.e. true or false)"); }
	global $CheckForUpdates;
	$CheckForUpdates = $bool;
}

$classDirectory = null;
function SetClassFolder($directory){
	if( !is_dir($directory) ){ die("\"$directory\" is not a valid directory to search for class definitions in."); }
	global $classDirectory;
	$classDirectory = rtrim($directory, '/')."/";;
}

require_once("internal/constants.php");
require_once("config.php");
require_once("functions/Actions.php");
require_once("functions/Conditions.php");
require_once("functions/Mint.php");
require_once("internal/data/charswap.php");
require_once("internal/data/unitdata.php");
require_once("internal/data/charcodes.php");
require_once("internal/data/aiscripts.php");
require_once("internal/data/validunits.php");
require_once("internal/data/keyaddresses.php");
require_once("internal/IfClass.php");
require_once("internal/ElseClass.php");
require_once("internal/SwitchClass.php");
require_once("internal/SwitchList.php");
require_once("internal/Functions.php");

function __autoload($className) {
	global $classDirectory;
	if ( include_once("classes/$className.php") ) return;
	if ( include_once("/classes/$className.php") ) return;
	if ( include_once("$classDirectory$className.php") && $classDirectory !== null) return;
	Error("Can't find the class in any known directory: $classDirectory$className.php");
}

register_shutdown_function('handleShutdown');

require_once("header.php");

// Start timing script
$ScriptTimer = microtime(true);

// Remember Oreo directory
$OreoRoot = __DIR__;

// Initial state
$TriggerOwner = '"Player 1"';
$TriggerCount = 0;
$PrependTrigs = '';
$PrependXML = '';
$PrependMintXML = '';
$ForemostSwitch = false;
$PrependedEvents = array();
$PlayerSwitchArray = array();
$HyperPlayer = null;
$PropertyArray = array();
$AnalysisRoot;
$AnalysisArray = array();

// Root File
$RootFile = str_replace("/", "\\", rtrim($_SERVER['DOCUMENT_ROOT'], '/').$_SERVER['PHP_SELF']);

// Mint variables
$Minted = false;
$MapPath = false;
$OutputSuppressed = false;
$OutputMapPath = false;
$XMLDocPath = null;
$RetainTmpXML = false;
