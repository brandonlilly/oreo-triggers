<?php
	
	function WriteToXML($text){
		global $XMLDocPath;
		$xmlhandle = fopen($XMLDocPath, "a");
		fwrite($xmlhandle, $text);
		fclose($xmlhandle);
	}
	
	function PrependToXML($text){
		global $PrependMintXML;
		$PrependMintXML .= $text;
	}
	
	function MintUnit($unit, $player, $x, $y, $properties = null){
		if ( !Minted() ){ return null; }
		
		if( func_num_args() > 5 ){
			$props = array_slice(func_get_args(),4);
		} else{
			$props = $properties;
		}
		
		if( is_string($player) ){
			$proptext = GeneratePropertiesSet($props);
			$proptext = str_replace(array('<uprp>','</uprp>'),'',$proptext);
			$text = '<unit>'.NL.
						TAB."<player>$player</player>".NL.
						TAB."<unit_id>$unit</unit_id>".NL.
						TAB."<x>$x</x>".NL.
						TAB."<y>$y</y>".
						$proptext.
					'</unit>'.NL;
			
			PrependToXML($text);
		}
		if( $player instanceof Player ){
			foreach($player->PlayerList as $plyr){
				MintUnit($unit, $plyr, $x, $y, $props);
			}
		}
	}
	
	
	function MintLocation($name, $x1, $y1, $x2, $y2){
		if ( !Minted() ){ return null; }
		
		$text = '<mrgn>'.NL.
					TAB."<mrgn_name>$name</mrgn_name>".NL.
					TAB."<x>$x1</x>".NL.
					TAB."<y>$y1</y>".NL.
					TAB."<xe>$x2</xe>".NL.
					TAB."<ye>$y2</ye>".NL.
				'</mrgn>'.NL;
		
		PrependToXML($text);
		
		return new Location($name);
	}
	
	function MintWav($filepath, $name){
		if ( !Minted() ){ return null; }
		
		if ( !file_exists($filepath) ){
			Error('Invalid filepath '.$filepath.' passed to MintWav(), no such file exists');
		}
		
		$text = 	TAB."<wav_path>$filepath</wav_path>".NL.
					TAB."<wav_name>$name</wav_name>".NL;
		
		PrependToXML($text);
	}
	
	function MintMapRevealers($width,$height,$players){
		if ( !Minted() ){ return null; }
		$pArray = array_splice(func_get_args(),2);
		if($players instanceof Player){
			$pArray = $players->PlayerList;
		}
		foreach($pArray as $player ){
			for($x=8;$x<=$width;$x+=16){
				for($y=8;$y<=$height;$y+=16){
					MintUnit('Map Revealer',$player,$x*32,$y*32);
				}
			}
		}
	}
	
	
	/// new mint
	
	function MintTile($tileid, $x, $y){
		if ( !Minted() ){ return null; }
		
		$text =     TAB."<x>$x</x>".NL.
					TAB."<y>$y</y>".NL.
					TAB."<mtxm>$tileid</mtxm>".NL.NL;
		
		PrependToXML($text);
	}
	
	function MintPlayer($player, $owner, $force, $color = null, $race = null){
		if ( !Minted() ){ return null; }
		
		if( is_string($player) ){
			
			if( $owner === Neutral ){ $owner = "Neutral"; }
			
			$text =     TAB."<player>$player</player>".NL.
			            TAB."<ownr>$owner</ownr>".NL.
			            TAB."<forc_player>[Forc] $force</forc_player>".NL;
			
			if( $color !== null ){
				$text .= TAB."<colr>$color</colr>".NL;
			}
			if( $race !== null ){
				$text .= TAB."<side>$race</side>".NL;
			}
			
			PrependToXML($text);
		}
		if( $player instanceof Player ){
			foreach($player->PlayerList as $plyr){
				MintPlayer($plyr, $owner, $force, $color, $race);
			}
		}
	}
	
	function MintMap($tileset, $width, $height){
		if ( !Minted() ){ return null; }
		
		$text =     TAB."<era>$tileset</era>".NL.
					TAB."<dim_x>$width</dim_x>".NL.
					TAB."<dim_y>$height</dim_y>".NL.NL;
		
		PrependToXML($text);
	}
	
	function MintMapTitle($title){
		if ( !Minted() ){ return null; }
		
		$text = TAB."<sprp_name>$title</sprp_name>".NL;
		PrependToXML($text);
	}
	
	function MintMapDesc($description){
		if ( !Minted() ){ return null; }
		
		$text = TAB."<sprp_desc>$description</sprp_desc>".NL;
		PrependToXML($text);
	}
	
	function MintFog($player, $x, $y, $FogOrNone = 1){
		if ( !Minted() ){ return null; }
		
		if( is_string($player) ){
			$bool = 0;
			if( $FogOrNone ){ $bool = 1; }
			
			$text =     TAB."<player>$player</player>".NL.
						TAB."<x>$x</x>".NL.
						TAB."<y>$y</y>".NL.
						TAB."<mask>$bool</mask>".NL;
			
			PrependToXML($text);
		}
		if( $player instanceof Player ){
			foreach($player->PlayerList as $plyr){
				MintFog($plyr, $x, $y, $FogOrNone);
			}
		}
	}
	
	function MintSprite($spriteid, $player, $x, $y, $state = Enabled){
		if ( !Minted() ){ return null; }

		if( is_string($player) ){
			$disableflag = '';
			if( $state === Disabled ){ $disableflag = "<thg2_disabled/>"; }
		
			$text = '<thg2>'.NL.
						TAB."<player>$player</player>".NL.
						TAB."<thg2_id>$spriteid</thg2_id>".NL.
						TAB."<x>$x</x>".NL.
						TAB."<y>$y</y>".NL.
						TAB.$disableflag.NL.
					'</thg2>'.NL;
			
			PrependToXML($text);
		}
		if( $player instanceof Player ){
			foreach($player->PlayerList as $plyr){
				MintSprite($spriteid, $plyr, $x, $y, $state);
			}
		}
	}
	
	function MintForceSettings($force, $name, $allied = true, $sharedvision = true, $randomize = false, $alliedvictory = true){
		if ( !Minted() ){ return null; }
		// Default values
		if( $allied === null ){ $allied = true; }
		if( $sharedvision === null ){ $sharedvision = true; }
		if( $randomize === null ){ $randomize = false; }
		if( $alliedvictory === null ){ $alliedvictory = true; }
		
		$alliedflag = $visionflag = $randomizeflag = $alliedvictoryflag = '';
		if( $allied === true )      { $alliedflag =         TAB."<forc_allies/>".NL; }
		if( $sharedvision === true ){ $visionflag =         TAB."<forc_sharedvision/>".NL; }
		if( $randomize === true )   { $randomizeflag =      TAB."<forc_randomstartlocation/>".NL; }
		if( $alliedvictory === true){ $alliedvictoryflag =  TAB."<forc_alliedvictory/>".NL; }
		
		$text = 	TAB."<force>[Forc] $force</force>".NL.
					TAB."<forc_name>$name</forc_name>".NL.
					$alliedflag.
					$visionflag.
					$randomizeflag.
					$alliedvictoryflag.NL;
		
		PrependToXML($text);
	}
	
	function MintUnitSettings($unit, $health, $shields ){
		Error("MintUnitSettings function hasn't been made yet, sorry :(");
	}
	
	
?>
