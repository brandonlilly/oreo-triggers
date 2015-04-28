<?php 
	
	$oreo_version = $_REQUEST['vers'];
	
	$newvers = file_get_contents("http://www.torchlinetechnology.com/OreoUpdates.php?vers=$oreo_version");
	$newarray = json_decode($newvers, true);
	
	if( !empty($newarray) ){
		
		$output = "CHANGE LOG:"."<br /><br />";
		$log = "";
		foreach($newarray as $version){
			if( !empty($version['changelog']) ){
				$log .=
					"<a href=$version[link]>".
					"Oreo ($version[version])".
					"</a><br />";
				foreach($version['changelog'] as $change){
					$log .= "<li>$change"."<br/>";
				}
				$log .= "</ol>"."<br/>";
			}
			
		}
		
		if( $log ){
			$output = $log . "Current: $oreo_version"."<br />Don't want to be notified? Change your config file.<br />";
			echo "SUCCESS",$output;
		}
		
	}
	
	
