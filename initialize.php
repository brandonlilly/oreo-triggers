<?php

	error_reporting( E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR|E_PARSE|E_USER_ERROR );
	//ini_set('display_errors', '1');
	
	$oreo_version = "1.0.1";
	
	//      1 < 1.0
	//    1.0 < 1.01
	//   1.01 = 1.1
	//    1.1 < 1.10
	//   1.10 > 1.10b
	//  1.10b < 1.10.0
	
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
	
	$class_directory = null;
	function SetClassFolder($directory){
		if( !is_dir($directory) ){ die("\"$directory\" is not a valid directory to search for class definitions in."); }
		global $class_directory;
		$class_directory = rtrim($directory, '/')."/";;
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
	
	function __autoload($class_name) {
		global $class_directory;
		if ( include_once("classes/$class_name.php") ) return;
		if ( include_once("/classes/$class_name.php") ) return;
		if ( include_once("$class_directory$class_name.php") && $class_directory !== null) return;
		Error("Can't find the class in any known directory: $class_directory$class_name.php");
	}
	
	register_shutdown_function('handleShutdown');
	
	?> <table cellpadding="0" cellspacing="0"><tr><td>Triggers:<br /><textarea cols="90" rows="40" style="resize: none;font-family:Consolas,Arial;font-size:12;" id="triggerfield" readonly><?php
	
	// Start timing script
	$ScriptTimer = microtime(true);
	
	// Remember Oreo directory
	$OreoRoot = __DIR__;
	
	// Trigger Globals
	$TriggerOwner = '"Player 1"';
	$TriggerCount = 0;
	
	// Prepend Global
	$PrependTrigs = '';
	$PrependXML = '';
	$PrependMintXML = '';
	
	// Foremost Global
	$ForemostSwitch = false;
	
	// Events Global
	$PrependedEvents = array();
	
	// Player Switch Global
	$PlayerSwitchArray = array();
	
	// Hypertriggers
	$HyperPlayer = null;
	
	// Unit Properties Global
	$PropertyArray = array();
	
	// Analysis Globals
	$AnalysisRoot;
	$AnalysisArray = array();
	
	// Root File
	$RootFile = str_replace("/", "\\", rtrim($_SERVER['DOCUMENT_ROOT'], '/').$_SERVER['PHP_SELF']);
	
	// Mint Variables
	$Minted = false;
	$MapPath = false;
	$OutputSuppressed = false;
	$OutputMapPath = false;
	$XMLDocPath = null;
	$RetainTmpXML = false;
	
	
