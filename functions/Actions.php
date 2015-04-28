<?php
	
	// STANDARD
	function CenterView($location)					{
		GetLocName($location);
		if( Minted() ){
			return XMLAction(array(
				'c' => 'Center View',
				'l' => $location,
			));
		}
		return TAB.'Center View("'.$location.'");'.NL;
	}
	function CreateUnit($player, $unit, $n, $location) {
		if( is_string($player) ) {
			if( $n instanceof Deathcounter ){
				/* @var Deathcounter $n */
				$tempdc = new TempDC();
				$maxpower = getBinaryPower( min($n->Max, 1700) );
				
				$text = $tempdc->setTo(0);
				
				for($i=$maxpower; $i >= 0; $i--) {
					$k = pow(2, $i);
					$text .= _if( $n->atLeast($k) )->then(
						CreateUnit($player, $unit, $k, $location),
						$n->subtract($k),
						$tempdc->add($k),
					'');
				}
				for($i=$maxpower; $i >= 0; $i--) {
					$k = pow(2, $i);
					$text .= _if( $tempdc->atLeast($k) )->then(
						$n->add($k),
						$tempdc->subtract($k),
					'');
				}
				
				$text .= $tempdc->kill();
				
				return $text;
			}
			GetLocName($location); GetUnitType($unit);
			if( Minted() ){
				return XMLAction(array(
					'c' => 'Create Unit',
					'gf' => $player,
					'u' => $unit,
					'n' => $n,
					'l' => $location,
				));
			}
			return TAB.'Create Unit("'.$player.'", "'.$unit.'", '.$n.', "'.$location.'");'.NL;
		}
		$text = '';
		foreach( $player->PlayerList as $p ){ $text .= CreateUnit($p,$unit,$n,$location); }
		return $text;
	}
	function CreateUnitWithProperties($player, $unit, $n, $location, $properties) { 
		if( is_string($player) ) {  
			if( $n instanceof Deathcounter ){
				/* @var Deathcounter $n */
				$tempdc = new TempDC();
				$maxpower = getBinaryPower( min($n->Max, 1700) );
				
				$text = $tempdc->setTo(0);
				
				for($i=$maxpower; $i >= 0; $i--) {
					$k = pow(2, $i);
					$text .= _if( $n->atLeast($k) )->then(
						CreateUnitWithProperties($player, $unit, $k, $location, $properties),
						$n->subtract($k),
						$tempdc->add($k),
					'');
				}
				for($i=$maxpower; $i >= 0; $i--) {
					$k = pow(2, $i);
					$text .= _if( $tempdc->atLeast($k) )->then(
						$n->add($k),
						$tempdc->subtract($k),
					'');
				}
				
				$text .= $tempdc->kill();
				
				return $text;
			}
			GetLocName($location); $PropertyNumber = GetPropertyNumber($properties); GetUnitType($unit);
			if( Minted() ){
				return XMLAction(array(
					'c' => 'Create Unit with Properties',
					'gf' => $player,
					'u' => $unit,
					'n' => $n,
					'l' => $location,
					'gs' => $PropertyNumber,
				));
			}
			return TAB.'Create Unit with Properties("'.$player.'", "'.$unit.'", '.$n.', "'.$location.'", '.$PropertyNumber.');'.NL;
		}
		$text = '';
		foreach( $player->PlayerList as $p ){ $text .= CreateUnitWithProperties($p,$unit,$n,$location,$properties); }
		return $text;
	}
	function Comment($text = '') 					{
		if( Minted() ){
			HandleStringOutput($text);
			return XMLAction(array(
				'c' => 'Comment',
				's' => $text,
			));
		}
		return TAB.'Comment("'.$text.'");'.NL;
	}
	function Defeat()								{
		if( Minted() ){
			return XMLAction(array(
				'c' => 'Defeat',
			));
		}
		return TAB.'Defeat();'.NL;
	}
	function DisplayText($text, $always = null)		{
		// Automate deathcounters in string
		if( substr_count($text, '_XDCX_') > 1 ){
			list($pretext, $dctext, $posttext) = explode("_XDCX_", $text, 3);
			list($player, $unit, $min, $max, $enum) = explode("::", $dctext, 5);
			
			// If an enumerated DC, use enumerated array
			if ( $enum ){
				$enumarray = json_decode($enum, true);
				
				$text = '';
				foreach($enumarray as $i=>$string){
					if( !is_int($i) ){ Error("\"$i\" is not a valid array key for enumeration. It must be an integer (e.g. 1 or 15)"); }
					$text .=
					_if( Deaths($player, Exactly, $i, $unit) )->then(
						Display($pretext.$string.$posttext)
					);
				}
				
				return $text;
			} 
			// Otherwise use maximum value
			else {
				
				$text = '';
				for( $i=$min; $i<=$max; $i++ ){
					$text .=
					_if( Deaths($player, Exactly, $i, $unit) )->then(
						Display($pretext.$i.$posttext)
					);
				}
				
				return $text;
			}
		}
		if( func_get_arg(2) == 1 ){
			SCCharSwap1($text);
		}
		if( func_get_arg(2) == 2 ){
			SCCharSwap2($text);
		}
		if( Minted() ){
			HandleStringOutput($text);
			$type = 'Display Text Message Always';
			if ( $always === false ) { $type = 'Display Text Message'; }
			return XMLAction(array(
				'c' => $type,
				's' => $text,
			));
		}
		$alwaystext = '';
		if ( $always === false ) { $alwaystext = "Don't "; }
		return TAB."Display Text Message({$alwaystext}Always Display, \"$text\");".NL;
	}
	function Draw()									{
		if( Minted() ){
			return XMLAction(array(
				'c' => 'Draw',
			));
		}
		return TAB.'Draw();'.NL;
	}
	function Give($player, $unit, $n, $newowner, $location) {
		if( is_string($player) && is_string($newowner) ) {
			if( $n instanceof Deathcounter ){
				/* @var Deathcounter $n */
				$tempdc = new TempDC();
				$maxpower = getBinaryPower( min($n->Max, 1700) );
				
				$text = $tempdc->setTo(0);
				
				for($i=$maxpower; $i >= 0; $i--) {
					$k = pow(2, $i);
					$text .= _if( $n->atLeast($k) )->then(
						Give($player, $unit, $k, $newowner, $location),
						$n->subtract($k),
						$tempdc->add($k),
					'');
				}
				for($i=$maxpower; $i >= 0; $i--) {
					$k = pow(2, $i);
					$text .= _if( $tempdc->atLeast($k) )->then(
						$n->add($k),
						$tempdc->subtract($k),
					'');
				}
				
				$text .= $tempdc->kill();
				
				return $text;
			}
			GetLocName($location); GetUnitType($unit);
			if( Minted() ){
				return XMLAction(array(
					'c' => 'Give Units to Player',
					'gf' => $player,
					'u' => $unit,
					'n' => $n,
					'gs' => $newowner,
					'l' => $location,
				));
			}
			return TAB.'Give Units to Player("'.$player.'", "'.$newowner.'", "'.$unit.'", '.$n.', "'.$location.'");'.NL; 
		} elseif( is_string($player)){
			if( count($newowner->PlayerList) > 1 ){
				Error("You're trying to give units to a player object that has more than one player in it. That make's no sense, you can't give a unit to two different players!");
			}
			return Give($player,$unit,$n,$newowner->PlayerList[0],$location);
		}
		$text = '';
		foreach( $player->PlayerList as $p ){ 
			$text .= Give($p,$unit,$n,$newowner,$location);
		}
		return $text;
	}
	function KillUnit($player, $unit)               { 
		if( is_string($player) ) {
			GetUnitType($unit);
			if( Minted() ){
				return XMLAction(array(
					'c' => 'Kill Unit',
					'gf' => $player,
					'u' => $unit,
				));
			}
			return TAB.'Kill Unit("'.$player.'", "'.$unit.'");'.NL;
		}
		$text = '';
		foreach( $player->PlayerList as $p ){ $text .= KillUnit($p,$unit); }
		return $text; 
	}
	function KillUnitAtLocation($player, $unit, $n, $location) {
		if( is_string($player) ) {
			if( $n instanceof Deathcounter ){
				/* @var Deathcounter $n */
				$tempdc = new TempDC();
				$maxpower = getBinaryPower( min($n->Max, 1700) );
				
				$text = $tempdc->setTo(0);
				
				for($i=$maxpower; $i >= 0; $i--) {
					$k = pow(2, $i);
					$text .= _if( $n->atLeast($k) )->then(
						KillUnitAtLocation($player, $unit, $k, $location),
						$n->subtract($k),
						$tempdc->add($k),
					'');
				}
				for($i=$maxpower; $i >= 0; $i--) {
					$k = pow(2, $i);
					$text .= _if( $tempdc->atLeast($k) )->then(
						$n->add($k),
						$tempdc->subtract($k),
					'');
				}
				
				$text .= $tempdc->kill();
				
				return $text;
			}
			GetLocName($location); GetUnitType($unit);
			if( Minted() ){
				return XMLAction(array(
					'c' => 'Kill Unit At Location',
					'gf' => $player,
					'u' => $unit,
					'n' => $n,
					'l' => $location,
				));
			}
			return TAB.'Kill Unit At Location("'.$player.'", "'.$unit.'", '.$n.', "'.$location.'");'.NL;
		}
		$text = '';
		foreach( $player->PlayerList as $p ){ $text .= KillUnitAtLocation($p,$unit,$n,$location); }
		return $text;
	}
	function LeaderBoardPoints($label, $scoretype)		{
		if( Minted() ){
			return XMLAction(array(
				'c' => 'Leaderboard (Points)',
				's' => $label,
				'u' => $scoretype,
			));
		}
		return TAB.'Leader Board Points("'.$label.'", '.$scoretype.');'.NL;
	}
	function LeaderBoardComputers($state)		{
			if( Minted() ){
			GetMintStateConversion($state);
			return XMLAction(array(
				'c' => 'Leaderboard Computer Players',
				'n' => $state,
			));
		}
		return TAB.'Leaderboard Computer Players('.$state.');'.NL;
	}
	function LeaderBoardControl($label, $unit)		{
		if( Minted() ){
			HandleStringOutput($label); GetUnitType($unit);
			return XMLAction(array(
				'c' => 'Leaderboard (Control)',
				's' => $label,
				'u' => $unit,
			));
		}
		return TAB.'Leader Board Control("'.$label.'", "'.$unit.'");'.NL;
	}
	function LeaderBoardControlAtLocation($label, $unit, $location) {
		if( Minted() ){
			HandleStringOutput($label); GetUnitType($unit); GetLocName($location);
			return XMLAction(array(
				'c' => 'Leaderboard (Control At Location)',
				's' => $label,
				'u' => $unit,
				'l' => $location,
			));
		}
		return TAB.'Leader Board Control At Location("'.$label.'", "'.$unit.'", "'.$location.'");'.NL;
	}
	function LeaderBoardGreed($amount)		{
		if( Minted() ){
			return XMLAction(array(
				'c' => 'Leaderboard (Greed)',
				'gs' => $amount,
			));
		}
		return TAB.'Leaderboard Greed('.$amount.');'.NL;
	}
	function LeaderBoardKills($label, $unit)		{
		if( Minted() ){
			HandleStringOutput($label); GetUnitType($unit);
			return XMLAction(array(
				'c' => 'Leaderboard (Kills)',
				's' => $label,
				'u' => $unit,
			));
		}
		return TAB.'Leader Board Kills("'.$label.'", "'.$unit.'");'.NL;
	}
	function LeaderBoardResources($label, $resourcetype)		{
		if( Minted() ){
			HandleStringOutput($label);
			return XMLAction(array(
				'c' => 'Leaderboard (Resources)',
				's' => $label,
				'u' => $resourcetype,
			));
		}
		return TAB.'Leader Board Resources("'.$label.'", '.$resourcetype.');'.NL;
	}
	function LeaderBoardGoalPoints($label, $scoretype, $amount)		{
		if( Minted() ){
			HandleStringOutput($label);
			return XMLAction(array(
				'c' => 'Leaderboard Goal (Points)',
				's' => $label,
				'u' => $scoretype,
				'gs' => $amount,
			));
		}
		return TAB.'Leaderboard Goal Points("'.$label.'", '.$scoretype.', '.$amount.');'.NL;
	}
	function LeaderBoardGoalControl($label, $unit, $amount)		{
		if( Minted() ){
			HandleStringOutput($label); GetUnitType($unit);
			return XMLAction(array(
				'c' => 'Leaderboard Goal (Control)',
				's' => $label,
				'u' => $unit,
				'gs' => $amount,
			));
		}
		return TAB.'Leaderboard Goal Control("'.$label.'", "'.$unit.'", '.$amount.');'.NL;
	}
	function LeaderBoardGoalControlAtLocation($label, $unit, $location, $amount) {
		if( Minted() ){
			HandleStringOutput($label); GetUnitType($unit); GetLocName($location);
			return XMLAction(array(
				'c' => 'Leaderboard Goal (Control At Location)',
				's' => $label,
				'u' => $unit,
				'l' => $location,
				'gs' => $amount,
			));
		}
		return TAB.'Leaderboard Goal Control At Location("'.$label.'", "'.$unit.'", '.$amount.', "'.$location.'");'.NL;
	}
	function LeaderBoardGoalKills($label, $unit, $amount)		{
		if( Minted() ){
			HandleStringOutput($label); GetUnitType($unit);
			return XMLAction(array(
				'c' => 'Leaderboard Goal (Kills)',
				's' => $label,
				'u' => $unit,
				'gs' => $amount,
			));
		}
		return TAB.'Leaderboard Goal Kills("'.$label.'", "'.$unit.'", '.$amount.');'.NL;
	}
	function LeaderBoardGoalResources($label, $resourcetype, $amount)		{
		if( Minted() ){
			HandleStringOutput($label);
			return XMLAction(array(
				'c' => 'Leaderboard Goal (Resources)',
				's' => $label,
				'u' => $resourcetype,
				'gs' => $amount,
			));
		}
		return TAB.'Leaderboard Goal Resources("'.$label.'", '.$amount.', '.$resourcetype.');'.NL;
	}
	function MinimapPing($location)					{
		GetLocName($location);
		if( Minted() ){
			return XMLAction(array(
				'c' => 'Minimap Ping',
				'l' => $location,
			));
		}
		return TAB.'Minimap Ping("'.$location.'");'.NL;
	}
	function ModifyHealth($player, $unit, $n, $location, $percent) {
		if( is_string($player) ) {
			if( $n instanceof Deathcounter ){
				
				$text = '';
				$min = $n->Min;
				$max = $n->Max;
				
				for($i=$min; $i<=$max; $i++){
					$text .= _if( $n->exactly($i) )->then(
						ModifyHealth($player, $unit, $i, $location, $percent),
					'');
				}
				
			}
			if( $percent instanceof Deathcounter ){
				/* @var Deathcounter $percent */
				
				$text = '';
				
				$min = $percent->Min;
				$max = min($percent->Max, 100);
				
				for($i=$min; $i<=$max; $i++){
					$text .= _if( $percent->exactly($i) )->then(
						ModifyHealth($player, $unit, $n, $location, $i),
					'');
				}
				
				return $text;
			}
			GetLocName($location); GetUnitType($unit);
			if( Minted() ){
				return XMLAction(array(
					'c' => 'Modify Unit Hit Points',
					'gf' => $player,
					'u' => $unit,
					'n' => $n,
					'l' => $location,
					'gs' => $percent,
				));
			}
			return TAB.'Modify Unit Hit Points("'.$player.'", "'.$unit.'", '.$percent.', '.$n.', "'.$location.'");'.NL;
		}
		$text = '';
		foreach( $player->PlayerList as $p ){ $text .= ModifyHealth($p,$unit,$n,$location,$percent); }
		return $text;
	}
	function ModifyHangar($player, $unit, $n, $location, $amounttoaddtohangar) {
		if( is_string($player) ) {
			GetLocName($location); GetUnitType($unit);
			if( Minted() ){
				return XMLAction(array(
					'c' => 'Modify Unit Hangar Count',
					'gf' => $player,
					'u' => $unit,
					'n' => $n,
					'l' => $location,
					'gs' => $amounttoaddtohangar,
				));
			}
			return TAB.'Modify Unit Hanger Count("'.$player.'", "'.$unit.'", '.$amounttoaddtohangar.', '.$n.', "'.$location.'");'.NL;
		}
		$text = '';
		foreach( $player->PlayerList as $p ){ $text .= ModifyHangar($p, $unit, $n, $location, $amounttoaddtohangar); }
		return $text;
	}
	function ModifyEnergy($player, $unit, $n, $location, $percent) { 
		if( is_string($player) ) {
			if( $n instanceof Deathcounter ){
				
				$text = '';
				$min = $n->Min;
				$max = $n->Max;
				
				for($i=$min; $i<=$max; $i++){
					$text .= _if( $n->exactly($i) )->then(
						ModifyEnergy($player, $unit, $i, $location, $percent),
					'');
				}
				
			}
			if( $percent instanceof Deathcounter ){
				/* @var Deathcounter $percent */
				
				$text = '';
				
				$min = $percent->Min;
				$max = min($percent->Max, 100);
				
				for($i=$min; $i<=$max; $i++){
					$text .= _if( $percent->exactly($i) )->then(
						 ModifyEnergy($player, $unit, $n, $location, $i),
					'');
				}
				
				return $text;
			}
			GetLocName($location); GetUnitType($unit);
			if( Minted() ){
				return XMLAction(array(
					'c' => 'Modify Unit Energy',
					'gf' => $player,
					'u' => $unit,
					'n' => $n,
					'l' => $location,
					'gs' => $percent,
				));
			}
			return TAB.'Modify Unit Energy("'.$player.'", "'.$unit.'", '.$percent.', '.$n.', "'.$location.'");'.NL;
		}
		$text = '';
		foreach( $player->PlayerList as $p ){ $text .= ModifyEnergy($p,$unit,$n,$location,$percent); }
		return $text;
	}
	function ModifyResource($player, $numberOfSources, $resourceAmount, $location) {
		if( is_string($player) ) {
			if( $numberOfSources instanceof Deathcounter ){
				
				$text = '';
				$min = $numberOfSources->Min;
				$max = $numberOfSources->Max;
				
				for($i=$min; $i<=$max; $i++){
					$text .= _if( $numberOfSources->exactly($i) )->then(
						ModifyResource($player, $i, $resourceAmount, $location),
					'');
				}
				
			}
			if( $resourceAmount instanceof Deathcounter ){
				/* @var Deathcounter $resourceAmount */
				
				$text = '';
				
				$min = $resourceAmount->Min;
				$max = min($resourceAmount->Max, 50000);
				
				if( ($max - $min) > 1000 ){ Error("The range of values your ResourceAmount DC could be exceeds 1000 values. Raise the min or lower the max."); }
				
				for($i=$min; $i<=$max; $i++){
					$text .= _if( $resourceAmount->exactly($i) )->then(
						 ModifyResource($player, $numberOfSources, $i, $location),
					'');
				}
				
				return $text;
			}
			GetLocName($location); GetUnitType($unit);
			if( Minted() ){
				return XMLAction(array(
					'c' => 'Modify Unit Resource Amount',
					'gf' => $player,
					'n' => $numberOfSources,
					'l' => $location,
					'gs' => $resourceAmount,
				));
			}
			return TAB.'Modify Unit Resource Amount("'.$player.'", '.$resourceAmount.', '.$numberOfSources.', "'.$location.'");'.NL;
		}
		$text = '';
		foreach( $player->PlayerList as $p ){ $text .= ModifyResource($p,$numberOfSources,$resourceAmount,$location); }
		return $text;
	}
	function ModifyShield($player, $unit, $n, $location, $percent) { 
		if( is_string($player) ) {
			if( $n instanceof Deathcounter ){
				
				$text = '';
				$min = $n->Min;
				$max = $n->Max;
				
				for($i=$min; $i<=$max; $i++){
					$text .= _if( $n->exactly($i) )->then(
						ModifyShield($player, $unit, $i, $location, $percent),
					'');
				}
				
			}
			if( $percent instanceof Deathcounter ){
				/* @var Deathcounter $percent */
				
				$text = '';
				
				$min = $percent->Min;
				$max = min($percent->Max, 100);
				
				for($i=$min; $i<=$max; $i++){
					$text .= _if( $percent->exactly($i) )->then(
						 ModifyShield($player, $unit, $n, $location, $i),
					'');
				}
				
				return $text;
			}
			GetLocName($location); GetUnitType($unit);
			if( Minted() ){
				return XMLAction(array(
					'c' => 'Modify Unit Shield Points',
					'gf' => $player,
					'u' => $unit,
					'n' => $n,
					'l' => $location,
					'gs' => $percent,
				));
			}
			return TAB.'Modify Unit Shield Points("'.$player.'", "'.$unit.'", '.$percent.', '.$n.', "'.$location.'");'.NL;
		}
		$text = '';
		foreach( $player->PlayerList as $p ){ $text .= ModifyShield($p,$unit,$n,$location,$percent); }
		return $text;
	}
	function MoveLocation($moveloc, $player, $unit, $destloc) { 
		if( is_string($player) ) {
			GetLocName($moveloc); GetLocName($destloc); GetUnitType($unit);
			if( Minted() ){
				return XMLAction(array(
					'c' => 'Move Location',
					'l' => $destloc,
					'gf' => $player,
					'u' => $unit,
					'gs' => $moveloc,
				));
			}
			return TAB.'Move Location("'.$player.'", "'.$unit.'", "'.$destloc.'", "'.$moveloc.'");'.NL;
		}
		if( $player instanceof Player ){
			if ( count($player->PlayerList) > 1 ){
				Error("You're trying to move a location onto multiple players. You can't center a location on two different players' units!");
			}
			MoveLocation($moveloc, $player->PlayerList[0], $unit, $destloc);
		}
	}
	function MoveUnit($player, $unit, $n, $atLoc, $toLoc) { 
		if( is_string($player) ) {
			if( $n instanceof Deathcounter ){
				/* @var Deathcounter $n */
				$tempdc = new TempDC();
				$maxpower = getBinaryPower( min($n->Max, 1700) );
				
				$text = $tempdc->setTo(0);
				
				for($i=$maxpower; $i >= 0; $i--) {
					$k = pow(2, $i);
					$text .= _if( $n->atLeast($k) )->then(
						MoveUnit($player, $unit, $k, $atLoc, $toLoc),
						$n->subtract($k),
						$tempdc->add($k),
					'');
				}
				for($i=$maxpower; $i >= 0; $i--) {
					$k = pow(2, $i);
					$text .= _if( $tempdc->atLeast($k) )->then(
						$n->add($k),
						$tempdc->subtract($k),
					'');
				}
				
				$text .= $tempdc->kill();
				
				return $text;
			}
			GetLocName($atLoc); GetLocName($toLoc); GetUnitType($unit);
			if( Minted() ){
				return XMLAction(array(
					'c' => 'Move Unit',
					'gf' => $player,
					'u' => $unit,
					'n' => $n,
					'l' => $atLoc,
					'gs' => $toLoc,
				));
			}
			return TAB.'Move Unit("'.$player.'", "'.$unit.'", '.$n.', "'.$atLoc.'", "'.$toLoc.'");'.NL;
		}
		$text = '';
		foreach( $player->PlayerList as $p ){ $text .= MoveUnit($p, $unit, $n, $atLoc, $toLoc); }
		return $text;
	}
	function MuteUnitSpeech()						{
		if( Minted() ){
			return XMLAction(array(
				'c' => 'Mute Unit Speech',
			));
		}
		return TAB.'Mute Unit Speech();'.NL;
	}
	function Order($player, $unit, $atLoc, $order, $toLoc) { 
		if( is_string($player) ) {
			GetLocName($atLoc); GetLocName($toLoc); GetUnitType($unit);
			if( Minted() ){
				return XMLAction(array(
					'c' => 'Order',
					'gf' => $player,
					'u' => $unit,
					'l' => $atLoc,
					'n' => $order,
					'gs' => $toLoc,
				));
			}
			return TAB.'Order("'.$player.'", "'.$unit.'", "'.$atLoc.'", "'.$toLoc.'", '.$order.');'.NL;
		}
		$text = '';
		foreach( $player->PlayerList as $p ){ $text .= Order($p, $unit, $atLoc, $order, $toLoc); }
		return $text;
	}
	function PauseGame()							{
		if( Minted() ){
			return XMLAction(array(
				'c' => 'Pause Game',
			));
		}
		return TAB.'Pause Game();'.NL;
	}
	function PauseTimer()							{
		if( Minted() ){
			return XMLAction(array(
				'c' => 'Pause Timer',
			));
		}
		return TAB.'Pause Timer();'.NL;
	}
	function PlayWav($wavpath)						{
		if( Minted() ){
			return XMLAction(array(
				'c' => 'Play WAV',
				'w' => $wavpath,
				't' => 0,
			));
		}
		$wavpath = addslashes($wavpath);
		return TAB.'Play WAV("'.$wavpath.'", 0);'.NL;
	}
	function PreserveTrigger() 						{
		if( Minted() ){
			return XMLAction(array(
				'c' => 'Preserve Trigger',
			));
		}
		return TAB.'Preserve Trigger();'.NL;
	}
	function RemoveUnit($player, $unit)				{ 
		if( is_string($player) ) {
			GetUnitType($unit);
			if( Minted() ){
				return XMLAction(array(
					'c' => 'Remove Unit',
					'gf' => $player,
					'u' => $unit,
				));
			}
			return TAB.'Remove Unit("'.$player.'", "'.$unit.'");'.NL;
		}
		$text = '';
		foreach( $player->PlayerList as $p ){ $text .= RemoveUnit($p, $unit); }
		return $text;
	}
	function RemoveUnitAtLocation($player, $unit, $n, $location) { 
		if( is_string($player) ) {
			if( $n instanceof Deathcounter ){
				/* @var Deathcounter $n */
				$tempdc = new TempDC();
				$maxpower = getBinaryPower( min($n->Max, 1700) );
				
				$text = $tempdc->setTo(0);
				
				for($i=$maxpower; $i >= 0; $i--) {
					$k = pow(2, $i);
					$text .= _if( $n->atLeast($k) )->then(
						RemoveUnitAtLocation($player, $unit, $k, $location),
						$n->subtract($k),
						$tempdc->add($k),
					'');
				}
				for($i=$maxpower; $i >= 0; $i--) {
					$k = pow(2, $i);
					$text .= _if( $tempdc->atLeast($k) )->then(
						$n->add($k),
						$tempdc->subtract($k),
					'');
				}
				
				$text .= $tempdc->kill();
				
				return $text;
			}
			GetLocName($location); GetUnitType($unit);
			if( Minted() ){
				return XMLAction(array(
					'c' => 'Remove Unit At Location',
					'gf' => $player,
					'u' => $unit,
					'n' => $n,
					'l' => $location,
				));
			}
			return TAB.'Remove Unit At Location("'.$player.'", "'.$unit.'", '.$n.', "'.$location.'");'.NL;
		}
		$text = '';
		foreach( $player->PlayerList as $p ){ $text .= RemoveUnitAtLocation($p, $unit, $n, $location); }
		return $text;
	}
	function RunAIScript($script)					{
		GetScript($script);
		if( Minted() ){
			return XMLAction(array(
				'c' => 'Run AI Script',
				'gs' => $script,
			));
		}
		return TAB.'Run AI Script("'.$script.'");'.NL;
	}
	function RunAIScriptAtLocation($script, $location) {
		GetScript($script); GetLocName($location);
		if( Minted() ){
			return XMLAction(array(
				'c' => 'Run AI Script At Location',
				'gs' => $script,
				'l' => $location,
			));
		}
		return TAB.'Run AI Script At Location("'.$script.'", "'.$location.'");'.NL;
	}
	function SetAlliance($player, $status)			{ 
		if( is_string($player) ) {
			if( Minted() ){
				return XMLAction(array(
					'c' => 'Set Alliance Status',
					'gf' => $player,
					'u' => $status,
				));
			}
			return TAB.'Set Alliance Status("'.$player.'", '.$status.');'.NL;
		}
		$text = '';
		foreach( $player->PlayerList as $p ){ $text .= SetAlliance($p, $status); }
		return $text;
	}
	function SetDoodadState($player, $unit, $location, $state) { 
		if( is_string($player) ) {
			GetLocName($location); GetUnitType($unit);
			GetMintStateConversion($state);
			if( Minted() ){
				return XMLAction(array(
					'c' => 'Set Doodad State',
					'gf' => $player,
					'u' => $unit,
					'l' => $location,
					'n' => $state,
				));
			}
			return TAB.'Set Doodad State("'.$player.'", "'.$unit.'", "'.$location.'", '.$state.');'.NL;
		}
		$text = '';
		foreach( $player->PlayerList as $p ){ $text .= SetDoodadState($p, $unit, $location, $state); }
		return $text;
	}
	function SetCountdownTimer($vmod, $seconds)	 	{
		if( Minted() ){
			return XMLAction(array(
				'c' => 'Set Countdown Timer',
				'n' => $vmod,
				't' => $seconds,
			));
		}
		return TAB.'Set Countdown Timer('.$vmod.', '.$seconds.');'.NL;
	}
	function SetDeaths($player, $vmod, $n, $unit) 	{ 
		if( is_string($player) ) {
			GetUnitType($unit);
			if( Minted() ){
				return XMLAction(array(
					'c' => 'Set Deaths',
					'gf' => $player,
					'n' => $vmod,
					'gs' => $n,
					'u' => $unit,
				));
			}
			$nl = NL;
			return TAB."Set Deaths(\"$player\", \"$unit\", $vmod, $n);$nl";
		}
		$text = '';
		foreach( $player->PlayerList as $p ){ $text .= SetDeaths($p, $vmod, $n, $unit); }
		return $text;
	}
	function SetInvincibility($player, $unit, $location, $state) { 
		if( is_string($player) ) {
			GetLocName($location); GetUnitType($unit);
			GetMintStateConversion($state);
			if( Minted() ){
				return XMLAction(array(
					'c' => 'Set Invincibility',
					'gf' => $player,
					'u' => $unit,
					'l' => $location,
					'n' => $state,
				));
			}
			return TAB.'Set Invincibility("'.$player.'", "'.$unit.'", "'.$location.'", '.$state.');'.NL;
		}
		$text = '';
		foreach( $player->PlayerList as $p ){ $text .= SetInvincibility($p, $unit, $location, $state); }
		return $text;
	}
	function SetMissionObjectives($text)			{
		if( Minted() ){
			HandleStringOutput($text);
			return XMLAction(array(
				'c' => 'Set Mission Objectives',
				's' => $text,
			));
		}
		return TAB.'Set Mission Objectives("'.$text.'");'.NL;
	}
	function SetScore($player, $vmod, $n, $scoretype) { 
		if( is_string($player) ) {
			if( Minted() ){
				return XMLAction(array(
					'c' => 'Set Score',
					'gf' => $player,
					'n' => $vmod,
					'gs' => $n,
					'u' => $scoretype,
				));
			}
			return TAB.'Set Score("'.$player.'", '.$vmod.', '.$n.', '.$scoretype.');'.NL;
		}
		$text = '';
		foreach( $player->PlayerList as $p ){ $text .= SetScore($p, $vmod, $n, $scoretype); }
		return $text;
	}
	function SetSwitch($id, $state) 				{
		if( Minted() ){
			if ( $id instanceof PermSwitch ) {
				$id = $id->index;
			}
			if ( is_numeric($id) ){ $id--; }
			return XMLAction(array(
				'c' => 'Set Switch',
				'gs' => $id,
				'n' => $state,
			));
		}
		if ( $id instanceof PermSwitch ) {
			$id = $id->index;
		}
		if ( is_numeric($id) ) {
			$id = 'Switch'.$id.'';
		}
		$nl = NL;
		return TAB."Set Switch(\"$id\", $state);$nl";
	}
	function SetResources($player, $vmod, $n, $resourcetype) { 
		if( is_string($player) ) {
			if( Minted() ){
				return XMLAction(array(
					'c' => 'Set Resources',
					'gf' => $player,
					'n' => $vmod,
					'gs' => $n,
					'u' => $resourcetype,
				));
			}
			return TAB.'Set Resources("'.$player.'", '.$vmod.', '.$n.', '.$resourcetype.');'.NL;
		}
		$text = '';
		foreach( $player->PlayerList as $p ){ $text .= SetResources($p, $vmod, $n, $resourcetype); }
		return $text;
	}
	function TalkingPortrait($unit, $n)				{
		if( Minted() ){
			return XMLAction(array(
				'c' => 'Talking Portrait',
				'u' => $unit,
				't' => $n,
			));
		}
		return TAB.'Talking Portrait("'.$unit.'", '.$n.');'.NL;
	}
	function Transmission($text, $unit, $location, $wavpath) {
		GetLocName($location); GetUnitType($unit);
		$wavpath = addslashes($wavpath);
		if( Minted() ){
			HandleStringOutput($text);
			return XMLAction(array(
				'c' => 'Transmission',
				's' => $text,
				'u' => $unit,
				'l' => $location,
				'w' => $wavpath,
				't' => 0,
				'n' => Subtract,
			));
		}
		return TAB.'Transmission(Always Display, "'.$text.'", "'.$unit.'", "'.$location.'", Subtract, 0, "'.$wavpath.'", 73);'.NL;
	}
	function UnmuteUnitSpeech()						{
		if( Minted() ){
			return XMLAction(array(
				'c' => 'Unmute Unit Speech',
			));
		}
		return TAB.'Unmute Unit Speech();'.NL;
	}
	function UnpauseGame()							{
		if( Minted() ){
			return XMLAction(array(
				'c' => 'Unpause Game',
			));
		}
		return TAB.'Unpause Game();'.NL;
	}
	function UnpauseTimer()							{
		if( Minted() ){
			return XMLAction(array(
				'c' => 'Unpause Timer',
			));
		}
		return TAB.'Unpause Timer();'.NL;
	}
	function Victory()								{
		if( Minted() ){
			return XMLAction(array(
				'c' => 'Victory',
			));
		}
		return TAB.'Victory();'.NL;
	}
	function Wait($n)								{
		if( Minted() ){
			return XMLAction(array(
				'c' => 'Wait',
				't' => $n,
			));
		}
		return TAB.'Wait('.$n.');'.NL;
	}
	
	
    // CUSTOM
	
	function Add($dc, $n) 				{ return SetDeaths($dc->Player,'Add',$n,$dc->Unit); }
	function ClearText()				{ return DisplayText('\\r\\n\\r\\n\\r\\n\\r\\n\\r\\n\\r\\n\\r\\n\\r\\n\\r\\n\\r\\n\\r\\n'); }
	function Display($text, $always = null)	{ return DisplayText($text, $always); }
	function DisplayAlt($text, $always = null)	{
		return DisplayText($text, $always, 1);
	}
	function DisplayAlt2($text, $always = null)	{
		return DisplayText($text, $always, 2);
	}
	function GetVisionOf($player)       {
        if( is_string($player) ) 		{ return RunAIScript("Turn ON Shared Vision for ".$player); }
        $text = '';
        foreach( $player->PlayerList as $p ){
            $text .= RunAIScript("Turn ON Shared Vision for ".$p);
        }
        return $text;
    }
    function LoseVisionOf($player)      {
        if( is_string($player) ) 		{ return RunAIScript("Turn OFF Shared Vision for ".$player); }
        $text = '';
        foreach( $player->PlayerList as $p ){
            $text .= RunAIScript("Turn OFF Shared Vision for ".$p);
        }
        return $text;
    }
	function MissionObjectives($text)	{ return SetMissionObjectives($text); }
	function Mute()						{ return MuteUnitSpeech(); }
	function MuteSpeech()				{ return MuteUnitSpeech(); }
	function Pause()					{ return PauseGame(); }
	function PlaySound($wavpath)		{ return PlayWAV($wavpath); }
	function Portrait($unit, $n)		{ return TalkingPortrait($unit, $n); }
	function Objectives($text)			{ return SetMissionObjectives($text); }
	function Subtract($dc, $n) 			{ return SetDeaths($dc->Player,'Subtract',$n,$dc->Unit); }
	function Set($dc, $n) 				{ return SetDeaths($dc->Player,'Set To',$n,$dc->Unit); }
	function SetAlly($player)			{
		if( is_string($player) ) 		{ return SetAlliance($player, AlliedVictory); }
        $text = '';
		foreach( $player->PlayerList as $p ){
			$text .= SetAlliance($p, AlliedVictory);
		}
		return $text;
	}
	function SetEnemy($player)			{
		if( is_string($player) ) 		{ return SetAlliance($player, Enemy); }
        $text = '';
		foreach( $player->PlayerList as $p ){
			$text .= SetAlliance($p, Enemy);
		}
		return $text;
	}
	function SetObjectives($text)		{ return SetMissionObjectives($text); }
	function Unmute()					{ return UnmuteUnitSpeech(); }
	function UnmuteSpeech()				{ return UnmuteUnitSpeech(); }
	function Unpause()					{ return UnpauseGame(); }


	//QUOTIENT AND MOD !
	/*
		� Divides argument 1 by argument 2 and returns the quotient to argument 3 and the remainder to argument 4
		� NOTE: if the divisor is 0, the function will return a 0
		� Format:
			- QuotientAndMod($numerator, $denominator, $quotient, $modulus) is analogous to $quotient = floor($numerator / $denominator) and $modulus = $numerator % $denominator)
			- $numerator must be a deathcounter or a constant (integer)
			- $denominator must be a deathcounter or a constant (integer)
			- $quotient must be a deathcounter
			- $modulus must be a deathcounter
		� Max values:
			- argument 1: 2147483647
			- argument 2: 2147483647
		� Low-end specifics:
			- trigger number: 127
			- temp switch number: 10
			- temp deathcounters: 0 (1 for integer division)
		� High-end specifics:
			- trigger number: 621
			- temp switch number: 23
			- temp deathcounters: 0 (1 for integer division)
		� Maxed specifics:
			- trigger number: 1303
			- temp switch number: 34
			- temp deathcounters: 0 (1 for integer division)
	*/
	function QuotientAndMod($numerator, $denominator, $quotient, $modulus)	{
		//ERROR
		if ( func_num_args() != 4 ) {
			Error('COMPILER ERROR FOR QUOTIENTANDMOD(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 4: DEATHCOUNTER OR NUMBER, DEATHCOUNTER OR NUMBER, DEATHCOUNTER, DEATHCOUNTER)');
		}
		if (!($numerator instanceof Deathcounter || is_numeric($numerator))) {
			Error('COMPILER ERROR FOR QUOTIENTANDMOD(): ARGUMENT 1 NEEDS TO BE A DEATHCOUNTER OR A NUMBER');
		}
		if (!($denominator instanceof Deathcounter || is_numeric($denominator))) {
			Error('COMPILER ERROR FOR QUOTIENTANDMOD(): ARGUMENT 2 NEEDS TO BE A DEATHCOUNTER OR A NUMBER');
		}
		if (!($quotient instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR QUOTIENTANDMOD(): ARGUMENT 3 NEEDS TO BE A DEATHCOUNTER');
		}
		if (!($modulus instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR QUOTIENTANDMOD(): ARGUMENT 4 NEEDS TO BE A DEATHCOUNTER');
		}

		/* @var Deathcounter $quotient */
		/* @var Deathcounter $modulus */

		$text = '';
		
		//INTEGER-INTEGER DIVISION
		if ( is_numeric($numerator) && is_numeric($denominator) ) {
			
			$text .= $quotient->setTo( floor($numerator / $denominator) );
			$text .= $modulus->setTo( $numerator % $denominator );

			return $text;
		}

		//DEATHCOUNTER-INTEGER DIVISION
		if ( is_numeric($denominator) ) {

			if( $denominator == 0 ) {
				Error('COMPILATION ERROR! MOD BY 0');
			}

			$tempdc = new TempDC();
			$maxpower1 = getBinaryPower( ceil($numerator->Max / $denominator) );
			$maxpower2 = getBinaryPower( $denominator - 1 );
			$maxpower3 = getBinaryPower( $numerator->Max );

			$text = $quotient->setTo(0).
			        $modulus->setTo(0);
			for($i=$maxpower1; $i >= 0; $i--) {
				$k = pow(2, $i);
				$text .= _if( $numerator->atLeast($k * $denominator) )->then(
					$numerator->subtract($k * $denominator),
					$tempdc->add($k * $denominator),
					$quotient->add($k),
				'');
			}
			for($i=$maxpower2; $i >= 0; $i--) {
				$k = pow(2, $i);
				$text .= _if( $numerator->atLeast($k) )->then(
					$numerator->subtract($k),
					$modulus->add($k),
					$tempdc->add($k),
				'');
			}
			for($i=$maxpower3; $i >= 0; $i--) {
				$k = pow(2, $i);
				$text .= _if( $tempdc->atLeast($k) )->then(
					$numerator->add($k),
					$tempdc->subtract($k),
				'');
			}

			$text .= 	$tempdc->kill();

			return $text;
		}


		//DEATHCOUNTER-DEATHCOUNTER DIVISION
		if ( $numerator->Max > 2147483647) {
			Error('COMPILER ERROR FOR QUOTIENTOF(): ARGUMENT 1\'S MAX SIZE IS TOO LARGE (EXCEEDS 2147483647)');
		}
		if ( $denominator->Max > 2147483647) {
			Error('COMPILER ERROR FOR QUOTIENTOF(): ARGUMENT 2\'S MAX SIZE IS TOO LARGE (EXCEEDS 2147483647)');
		}

		$nestswitch = new TempSwitch();
		$ignore = new TempSwitch();

		$maxpower1 = getBinaryPower( $modulus->Max );
		$maxpower2 = getBinaryPower( $denominator->Max );
		$kSwitches = array();


		//align 0 to 2^31 (for temporary negative numbers)
		$text =	$modulus->setTo($numerator).
				$modulus->add(2147483648);

		//get dynamic switches
		for($i=$maxpower2; $i >= 0; $i--) {
			$kSwitches[$i] = new TempSwitch();
		}
		if($maxpower2 > 15){
			$conditionGroupSwitch = new Tempswitch();
			$conditionGroupClear = $conditionGroupSwitch->clear();
		}
		else{
			$conditionGroupClear = '';
		}
		if($maxpower2 > 29){
			$conditionGroupSwitch2 = new Tempswitch();
			$conditionGroupClear .= $conditionGroupSwitch2->clear();
		}


		//actual algorithm
		for($i2=$maxpower2; $i2 >= 0; $i2--) {
			$k2 = pow(2, $i2);
			$text .= _if( $denominator->atLeast($k2) )->then(
				$denominator->subtract($k2),
				$kSwitches[$i2]->set(),
			'');
		}

		for($i1=$maxpower1; $i1 >= 0; $i1--) {
			$k1 = pow(2, $i1);
			$doDeMorgans = 0;
			$kconditions = '';
			$triggertext = '';
			for($i2=$maxpower2; $i2 >= 0; $i2--) {
				$k2 = pow(2, $i2);
				if( $k1 * $k2 <= $modulus->Max ){
					if( $doDeMorgans < 3 && $doDeMorgans > 0 ) {
						$text .= $triggertext;
						$doDeMorgans = 0;
					}
					else if( $doDeMorgans > 0 ) {
						$text .= $ignore->set();
						$text .= _if( $kconditions )->then(
							$ignore->clear(),
						'');
						$doDeMorgans = 0;
					}

					$text .= _if( $kSwitches[$i2]->is_set() )->then(
						$modulus->subtract($k1 * $k2),
					'');
				}
				else {
					$triggertext .= _if( $kSwitches[$i2]->is_set() )->then(
						$ignore->set(),
					'');
					$kconditions .= $kSwitches[$i2]->is_clear();
					$doDeMorgans += 1;
					if($maxpower2 > 15 && $doDeMorgans==15){
						$text .= _if( $kconditions )->then(
							$conditionGroupSwitch->set(),
						'');
						$kconditions = $conditionGroupSwitch->is_set();
					}
					if($maxpower2 > 29 && $doDeMorgans==29){
						$text .= _if( $kconditions )->then(
							$conditionGroupSwitch2->set(),
						'');
						$kconditions = $conditionGroupSwitch->is_set();
						$kconditions .= $conditionGroupSwitch2->is_set();
					}
				}
			}
			$text .= _if( $ignore->is_clear() , $modulus->atLeast(2147483648) )->then(
				$quotient->add($k1),
				$conditionGroupClear,
			'');
			$text .= _if( $ignore->is_clear() , $modulus->atMost(2147483647) )->then(
				$nestswitch->set(),
			'');
			$text .= _if( $ignore->is_set() )->then(
				$ignore->clear(),
				$nestswitch->set(),
			'');
			for($i2=$maxpower2; $i2 >= 0; $i2--) {
				$k2 = pow(2, $i2);
				if( $k1 * $k2 <= $modulus->Max ){
					$text .= _if( $nestswitch->is_set() , $kSwitches[$i2]->is_set() )->then(
						$modulus->add($k1 * $k2),
					'');
				}
			}
			$text .= _if( $nestswitch->is_set() )->then(
				$nestswitch->clear(),
				$conditionGroupClear,
			'');
		}
		for($i=$maxpower2; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $kSwitches[$i]->is_set() )->then(
				$denominator->add($k),
				$kSwitches[$i]->clear(),
			'');
		}

		//if user divides by 0,  return 0
		$text .= _if ( $denominator->exactly(0) )->then(
			$quotient->setTo(0),
			$modulus->setTo(0),
		'');

        $kSwitchClear = '';
		foreach($kSwitches as $keySwitch){
			$kSwitchClear .= $keySwitch->kill();
		}
		
		if($maxpower2 > 15){
			$conditionGroupSwitch->kill();
		}
		if($maxpower2 > 29){
			$conditionGroupSwitch2->kill();
		}

		$text .= 	$modulus->subtract(2147483648).
					$kSwitchClear.
					$nestswitch->kill().
					$ignore->kill();

        return $text;

	}


	//GET MOUSE !
	/*
		� Returns the mouse's x and y coordinates (relative to the screen) to $x and $y, respectively
		� NOTE: LOCAL EUD, CAN'T USE IN MULTIPLAYER MAPS
		� NOTE: origin is top left of the screen
		� Format:
			- GetMouse($x, $y) is analogous to $x = mouse_x and $y = mouse_y
			- $x must be a deathcounter
			- $y must be a deathcounter
		� Max values:
			- returned to $x: 639
			- returned to $y: 479
		�Specifics:
			- trigger number: 1122
			- temp switch number: 1
			- temp deathcounters: 0
	*/

    /**
     * Gets the absolute mouse position and puts it into $x and $y
     * @param $x Deathcounter
     * @param $y
     * @return oreoaction
     */
    function GetMouse($x, $y) {
		//ERROR
		if ( func_num_args() != 2 ) {
			Error('COMPILER ERROR FOR GETMOUSE(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 2: DEATHCOUNTER, DEATHCOUNTER)');
		}
		if (!($x instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR GETMOUSE(): ARGUMENT 1 NEEDS TO BE A DEATHCOUNTER');
		}
		if (!($y instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR GETMOUSE(): ARGUMENT 2 NEEDS TO BE A DEATHCOUNTER');
		}

		$mousex = new EPD(331416);
		$mousey = new EPD(331417);

        $text = '';

		for($i=0; $i<640; $i++) {
			$text .= _if( $mousex->exactly($i) )->then(
				$x->setTo($i),
			'');
		}
		for($i=0; $i<480; $i++) {
			$text .= _if( $mousey->exactly($i) )->then(
				$y->setTo($i),
			'');
		}

		return $text;

	}


	//GET SCREEN !
	/*
		� Returns the screen's x and y coordinates to $x and $y, respectively
		� $xdim and $ydim are constants (integers) representing the dimensions of the map in tiles (eg. $xdim = 256, $ydim = 128 would be 256x128)
		� NOTE: LOCAL EUD, CAN'T USE IN MULTIPLAYER MAPS
		� NOTE: origin is top left of the map
		� Format:
			- GetScreen($x, $y, $xdim, $ydim) is analogous to $x = screen_x and $y = screen_y
			- $x must be a deathcounter
			- $y must be a deathcounter
			- $xdim must be a constant (integer)
			- $ydim must be a constant (integer)
		� Max values:
			- returned to $x: 1408 for $xdim=64, 7552 for $xdim=256
			- returned to $y: 1672 for $ydim=64, 7816 for $ydim=256
		�Specifics:
			- trigger number: 389 for 64x64, 1925 for 256x256
			- temp switch number: 1
			- temp deathcounters: 0
	*/
	function GetScreen($x, $y, $xdim, $ydim) {
		//ERROR
		if(func_num_args() != 4){
			Error('COMPILER ERROR FOR GETSCREEN(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 4: DEATHCOUNTER, DEATHCOUNTER, NUMBER, NUMBER)');
		}
		if(!($x instanceof Deathcounter)){
			Error('COMPILER ERROR FOR GETSCREEN(): ARGUMENT 1 NEEDS TO BE A DEATHCOUNTER');
		}
		if(!($y instanceof Deathcounter)){
			Error('COMPILER ERROR FOR GETSCREEN(): ARGUMENT 2 NEEDS TO BE A DEATHCOUNTER');
		}
		if(!(is_numeric($xdim))){
			Error('COMPILER ERROR FOR GETSCREEN(): ARGUMENT 3 NEEDS TO BE A NUMBER');
		}
		if(!(is_numeric($ydim))){
			Error('COMPILER ERROR FOR GETSCREEN(): ARGUMENT 4 NEEDS TO BE A NUMBER');
		}

		$screenx = new EPD(161849);
		$screeny = new EPD(161859);

        $text = '';

		for($i=0; $i<=$xdim*4-80; $i++) {
			$text .= _if( $screenx->exactly($i*8) )->then(
				$x->setTo($i*8),
			'');
		}
		for($i=0; $i<=$ydim*4-47; $i++) {
			$text .= _if( $screeny->exactly($i*8) )->then(
				$y->setTo($i*8),
			'');
		}

		return $text;

	}


	//GET CURSOR !
	/*
		� Returns the cursor's x and y coordinates (relative to the map) to $x and $y, respectively
		� $xdim and $ydim are constants (integers) representing the dimensions of the map in tiles (eg. $xdim = 256, $ydim = 128 would be 256x128)
		� NOTE: LOCAL EUD, CAN'T USE IN MULTIPLAYER MAPS
		� NOTE: origin is top left of the map, origin for the mouse is top left of the screen
		� Format:
			- GetScreen($x, $y, $xdim, $ydim) is analogous to $x = mouse_x + screen_x and $y = mouse_y + screen_y
			- $x must be a deathcounter
			- $y must be a deathcounter
			- $xdim must be a constant (integer)
			- $ydim must be a constant (integer)
		� Max values:
			- returned to $x: $xdim*32 - 1
			- returned to $y: $ydim*32 - 1
		�Specifics:
			- trigger number: 1507 for 64x64, 3043 for 256x256
			- temp switch number: 1
			- temp deathcounters: 0
	*/
	function GetCursor($x, $y, $xdim, $ydim) {
		//ERROR
		if(func_num_args() != 4){
			Error("COMPILER ERROR FOR GETCURSOR(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 4: DEATHCOUNTER, DEATHCOUNTER, NUMBER, NUMBER)");
		}
		if(!($x instanceof Deathcounter)){
			Error("COMPILER ERROR FOR GETCURSOR(): ARGUMENT 1 NEEDS TO BE A DEATHCOUNTER");
		}
		if(!($y instanceof Deathcounter)){
			Error("COMPILER ERROR FOR GETCURSOR(): ARGUMENT 2 NEEDS TO BE A DEATHCOUNTER");
		}
		if(!(is_numeric($xdim))){
			Error("COMPILER ERROR FOR GETCURSOR(): ARGUMENT 3 NEEDS TO BE A NUMBER");
		}
		if(!(is_numeric($ydim))){
			Error("COMPILER ERROR FOR GETCURSOR(): ARGUMENT 4 NEEDS TO BE A NUMBER");
		}

		$mousex = new EPD(331416);
		$mousey = new EPD(331417);
		$screenx = new EPD(161849);
		$screeny = new EPD(161859);

        $text = '';

		for($i=0; $i<640; $i++) {
			$text .= _if( $mousex->exactly($i) )->then(
				$x->setTo($i),
			'');
		}
		for($i=0; $i<480; $i++) {
			$text .= _if( $mousey->exactly($i) )->then(
				$y->setTo($i),
			'');
		}

		for($i=1; $i<=$xdim*4-80; $i++) {
			$text .= _if( $screenx->exactly($i*8) )->then(
				$x->add($i*8),
			'');
		}
		for($i=1; $i<=$ydim*4-47; $i++) {
			$text .= _if( $screeny->exactly($i*8) )->then(
				$y->add($i*8),
			'');
		}

		return $text;

	}
	
	
?>