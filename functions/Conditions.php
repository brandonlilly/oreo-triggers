<?php
	
	// STANDARD
	function Accumulate($player, $qmod, $n, $resourcetype) 	{ 
		if( is_string($player) ){
			if( Minted() ){
				return XMLCondition(array(
					'c' => 'Accumulate',
					'g' => $player,
					'm' => $qmod,
					'n' => $n,
					'r' => $resourcetype,
				));
			}
			return TAB."Accumulate(\"$player\", $qmod, $n, $resourcetype);".NL;
		}
		$text = '';
		if( count($player->PlayerList) == 1 ){
			return Accumulate($player->PlayerList[0], $qmod, $n, $resourcetype);
		} elseif ( $player->ConditionType === All ){
			foreach($player->PlayerList as $p){ $text .= Accumulate($p, $qmod, $n, $resourcetype); }
			return $text;
		} elseif ( $player->ConditionType === Any ){
			foreach($player->PlayerList as $p){ $text .= Accumulate($p, $qmod, $n, $resourcetype) . _OR; }
			$text = substr($text,0,-1*strlen(_OR));
			return orGroup($text);
		}
	}
	function Always() 										{ 
		if( Minted() ){
			return '';
		}
		return TAB.'Always();'.NL;
	}
	function Bring($player, $unit, $qmod, $n, $location)	{ 
		if( is_string($player) ){ 
			GetLocName($location);
			GetUnitType($unit);
			if( Minted() ){
				return XMLCondition(array(
					'c' => 'Bring',
					'g' => $player,
					'u' => $unit,
					'm' => $qmod,
					'n' => $n,
					'l' => $location,
				));
			}
			return TAB."Bring(\"$player\", \"$unit\", \"$location\", $qmod, $n);".NL;
		}
		$text = '';
		if( count($player->PlayerList) == 1 ){
			return Bring($player->PlayerList[0], $unit, $qmod, $n, $location);
		} elseif( $player->ConditionType === All ){
			foreach($player->PlayerList as $p){ $text .= Bring($p, $unit, $qmod, $n, $location); }
			return $text;
		} elseif ( $player->ConditionType === Any ){
			foreach($player->PlayerList as $p){ $text .= Bring($p, $unit, $qmod, $n, $location) . _OR; }
			$text = substr($text,0,-1*strlen(_OR));
			return orGroup($text);
		}
	}
	function Command($player, $unit, $qmod, $n)				{
		if( is_string($player) ){
			GetUnitType($unit);
			if( Minted() ){
				return XMLCondition(array(
					'c' => 'Command',
					'g' => $player,
					'u' => $unit,
					'm' => $qmod,
					'n' => $n,
				));
			}
			return TAB."Command(\"$player\", \"$unit\", $qmod, $n);".NL;
		}
		$text = '';
		if( count($player->PlayerList) == 1 ){
			return Command($player->PlayerList[0], $unit, $qmod, $n);
		} elseif( $player->ConditionType === All ){
			foreach($player->PlayerList as $p){ $text .= Command($p, $unit, $qmod, $n); }
			return $text;
		} elseif ( $player->ConditionType === Any ){
			foreach($player->PlayerList as $p){ $text .= Command($p, $unit, $qmod, $n) . _OR; }
			$text = substr($text,0,-1*strlen(_OR));
			return orGroup($text);
		}
	}
	function CommandTheLeast($unit)			            	{
		GetUnitType($unit);
		if( Minted() ){
			return XMLCondition(array(
				'c' => 'Command the Least',
				'u' => $unit,
			));
		}
		return TAB."Command the Least(\"$unit\");".NL;
	}
	function CommandTheLeastAt($unit, $location)           	{
		GetLocName($location); GetUnitType($unit);
		if( Minted() ){
			return XMLCondition(array(
				'c' => 'Command the Least At',
				'u' => $unit,
				'l' => $location,
			));
		}
		return TAB."Command the Least At(\"$unit\", \"$location\");".NL;
	}
	function CommandTheMost($unit)			            	{
		GetUnitType($unit);
		if( Minted() ){
			return XMLCondition(array(
				'c' => 'Command the Most',
				'u' => $unit,
			));
		}
		return TAB."Command the Most(\"$unit\");".NL;
	}
	function CommandTheMostAt($unit, $location)           	{
		GetLocName($location); GetUnitType($unit);
		if( Minted() ){
			return XMLCondition(array(
				'c' => 'Command the Most At',
				'u' => $unit,
				'l' => $location,
			));
		}
		return TAB."Commands the Most At(\"$unit\", \"$location\");".NL;
	}
	function Deaths($player, $qmod, $n, $unit) 				{ 
		if( is_string($player) ){
			GetUnitType($unit);
			if( Minted() ){
				return XMLCondition(array(
					'c' => 'Deaths',
					'g' => $player,
					'u' => $unit,
					'm' => $qmod,
					'n' => $n,
				));
			}
			$nl = NL;
			return TAB."Deaths(\"$player\", \"$unit\", $qmod, $n);$nl";
		}
		$text = '';
		if( count($player->PlayerList) == 1 ){
			return Deaths($player->PlayerList[0], $qmod, $n, $unit);
		} elseif( $player->ConditionType === All ){
			foreach($player->PlayerList as $p){ $text .= Deaths($p, $qmod, $n, $unit); }
			return $text;
		} elseif ( $player->ConditionType === Any ){
			foreach($player->PlayerList as $p){ $text .= Deaths($p, $qmod, $n, $unit) . _OR; }
			$text = substr($text,0,-1*strlen(_OR));
			return orGroup($text);
		}
	}
	function Elapsed($qmod, $seconds)						{
		if( Minted() ){
			return XMLCondition(array(
				'c' => 'Elapsed Time',
				'm' => $qmod,
				'n' => $seconds,
			));
		}
		return TAB."Elapsed Time($qmod, $seconds);".NL;
	}
	function HighestScore($scoretype)	                	{
		if( Minted() ){
			return XMLCondition(array(
				'c' => 'Highest Score',
				'r' => $scoretype,
			));
		}
		return TAB."Highest Score($scoretype);".NL;
	}
	function KillsOf($player, $unit, $qmod, $n)				{
		if( is_string($player) ){
			GetUnitType($unit);
			if( Minted() ){
				return XMLCondition(array(
					'c' => 'Kill',
					'g' => $player,
					'u' => $unit,
					'm' => $qmod,
					'n' => $n,
				));
			}
			return TAB."Kill(\"$player\", \"$unit\", $qmod, $n);".NL;
		}
		$text = '';
		if( count($player->PlayerList) == 1 ){
			return KillsOf($player->PlayerList[0], $unit, $qmod, $n);
		} elseif( $player->ConditionType === All ){
			foreach($player->PlayerList as $p){ $text .= Kill($p, $unit, $qmod, $n); }
			return $text;
		} elseif ( $player->ConditionType === Any ){
			foreach($player->PlayerList as $p){ $text .= Kill($p, $unit, $qmod, $n) . _OR; }
			$text = substr($text,0,-1*strlen(_OR));
			return orGroup($text);
		}
	}
	function LeastKills($unit)			            	    {
		if( Minted() ){
			GetUnitType($unit);
			return XMLCondition(array(
				'c' => 'Least Kills',
				'u' => $unit,
			));
		}
		return TAB."Least Kills(\"$unit\");".NL;
	}
	function LeastResources($resourcetype)	            	{
		if( Minted() ){
			return XMLCondition(array(
				'c' => 'Least Resources',
				'r' => $resourcetype,
			));
		}
		return TAB."Least Resources($resourcetype);".NL;
	}
	function LowestScore($scoretype)	                	{
		if( Minted() ){
			return XMLCondition(array(
				'c' => 'Lowest Score',
				'r' => $scoretype,
			));
		}
		return TAB."Lowest Score($scoretype);".NL;
	}
	function Memory($playernumber, $qmod, $n)				{
		if( Minted() ){
			return XMLCondition(array(
				'c' => 'Memory',
				'g' => $playernumber,
				//'u' => 0,
				'm' => $qmod,
				'n' => $n,
			));
		}
		return TAB."Memory($playernumber, $qmod, $n);".NL;
	}
	function MostKills($unit)			             	    {
		GetUnitType($unit);
		if( Minted() ){
			return XMLCondition(array(
				'c' => 'Most Kills',
				'u' => $unit,
			));
		}
		return TAB."Most Kills(\"$unit\");".NL;
	}
	function MostResources($resourcetype)	            	{
		if( Minted() ){
			return XMLCondition(array(
				'c' => 'Most Resources',
				'r' => $resourcetype,
			));
		}
		return TAB."Most Resources($resourcetype);".NL;
	}
	function Never() 										{
		if( Minted() ){
			return XMLCondition(array(
				'c' => 'Never',
			));
		}
		return TAB.'Never();'.NL;
	}
	function Opponents($player, $qmod, $n)				{
		if( is_string($player) ){
			if( Minted() ){
				return XMLCondition(array(
					'c' => 'Opponents',
					'g' => $player,
					'm' => $qmod,
					'n' => $n,
				));
			}
			return TAB."Opponents(\"$player\", $qmod, $n);".NL;
		}
		$text = '';
		if( count($player->PlayerList) == 1 ){
			return Opponents($player->PlayerList[0], $qmod, $n);
		} elseif( $player->ConditionType === All ){
			foreach($player->PlayerList as $p){ $text .= Opponents($p, $qmod, $n); }
			return $text;
		} elseif ( $player->ConditionType === Any ){
			foreach($player->PlayerList as $p){ $text .= Opponents($p, $qmod, $n) . _OR; }
			$text = substr($text,0,-1*strlen(_OR));
			return orGroup($text);
		}
	}
	function SwitchIsSet($nameOrID) 							{
		if( Minted() ){
			if ( is_numeric($nameOrID) ){ $nameOrID--; }
			return XMLCondition(array(
				'c' => 'Switch',
				'r' => $nameOrID,
				'm' => 'is set',
			));
		}
		if( is_numeric($nameOrID) ){
			$nameOrID = 'Switch'.$nameOrID;
		}
		if( $nameOrID instanceof PermSwitch ){
			$nameOrID = $nameOrID->index;
		}
		$nl = NL;
		return TAB."Switch(\"$nameOrID\", set);$nl";
	}
	function SwitchIsClear($nameOrID) 							{
		if( Minted() ){
			if ( is_numeric($nameOrID) ){ $nameOrID--; }
			return XMLCondition(array(
				'c' => 'Switch',
				'r' => $nameOrID,
				'm' => 'not set',
			));
		}
		if( is_numeric($nameOrID) ){
			$nameOrID = 'Switch'.$nameOrID;
		}
		if( $nameOrID instanceof PermSwitch ){
			$nameOrID = $nameOrID->index;
		}
		$nl = NL;
		return TAB."Switch(\"$nameOrID\", not set);$nl";
	}
	function Score($player, $scoretype, $qmod, $n)			{ 
		if( is_string($player) ){
			if( Minted() ){
				return XMLCondition(array(
					'c' => 'Score',
					'g' => $player,
					'r' => $scoretype,
					'm' => $qmod,
					'n' => $n,
				));
			}
			return TAB."Score(\"$player\", $scoretype, $qmod, $n);".NL;
		}
		$text = '';
		if( count($player->PlayerList) == 1 ){
			return Score($player->PlayerList[0], $scoretype, $qmod, $n);
		} elseif( $player->ConditionType === All ){
			foreach($player->PlayerList as $p){ $text .= Score($p, $scoretype, $qmod, $n); }
			return $text;
		} elseif ( $player->ConditionType === Any ){
			foreach($player->PlayerList as $p){ $text .= Score($p, $scoretype, $qmod, $n) . _OR; }
			$text = substr($text,0,-1*strlen(_OR));
			return orGroup($text);
		}
	}
	
	
	
	// CUSTOM
	function Ore($player, $qmod, $n){ return Accumulate($player, $qmod, $n, Ore); }
	function Gas($player, $qmod, $n){ return Accumulate($player, $qmod, $n, Gas); }
	
	function ElapsedTime($qmod, $seconds)					{ return Elapsed($qmod, $seconds); }
	
	function AtLeast($dc, $n) 	{ return Deaths($dc->Player, AtLeast, $n, $dc->Unit); }
	function AtMost($dc, $n) 	{ return Deaths($dc->Player, AtMost, $n, $dc->Unit); }
	function Exactly($dc, $n) 	{ return Deaths($dc->Player, Exactly, $n, $dc->Unit); }
	
	function LessThan(Deathcounter $dc1, Deathcounter $dc2) {
		$switch1 = new TempSwitch();
		$switch2 = new TempSwitch();
		$tempdc = new TempDC();
        $maxpower = getBinaryPower( min($dc1->Max,$dc2->Max) );
		$text =	ACTIONS().$switch1->set().$tempdc->setTo(0).PreserveTrigger().ENDT();
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= mute_if( $switch1->is_set(), $dc1->atLeast($k), $dc2->atLeast($k) )->then(
				$dc1->subtract($k),
				$dc2->subtract($k),
				$tempdc->add($k),
			'');
		}
		$text .= mute_if( $switch1->is_set(), $dc2->atLeast(1) )->then(
			$switch2->set(),
		'');
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= mute_if( $switch1->is_set(), $tempdc->atLeast($k) )->then(
				$dc1->add($k),
				$dc2->add($k),
				$tempdc->subtract($k),
			'');
		}
		$tempdc->kill();
		$text .= 	HEADING().
					$switch2->is_set();

		$returnarray = array($text, new SwitchList($switch1, $switch2));
		return $returnarray;
	}
	function GreaterThan(Deathcounter $dc1, Deathcounter $dc2) {
		$switch1 = new TempSwitch();
		$switch2 = new TempSwitch();
		$tempdc = new TempDC();
        $maxpower = getBinaryPower( min($dc1->Max,$dc2->Max) );
		$text =	ACTIONS().$switch1->set().$tempdc->setTo(0).PreserveTrigger().ENDT();
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= mute_if( $switch1->is_set(), $dc1->atLeast($k), $dc2->atLeast($k) )->then(
				$dc1->subtract($k),
				$dc2->subtract($k),
				$tempdc->add($k),
			'');
		}
		$text .= mute_if( $switch1->is_set(), $dc1->atLeast(1) )->then(
			$switch2->set(),
		'');
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= mute_if( $switch1->is_set(), $tempdc->atLeast($k) )->then(
				$dc1->add($k),
				$dc2->add($k),
				$tempdc->subtract($k),
			'');
		}
		$tempdc->kill();
		$text .= 	HEADING().
					$switch2->is_set();

		$returnarray = array($text, new SwitchList($switch1, $switch2));
		return $returnarray;
	}
	function LessThanOrEqual(Deathcounter $dc1, Deathcounter $dc2) {
		$switch1 = new TempSwitch();
		$switch2 = new TempSwitch();
		$tempdc = new TempDC();
        $maxpower = getBinaryPower( min($dc1->Max,$dc2->Max) );
		$text =	ACTIONS().$switch1->set().$tempdc->setTo(0).PreserveTrigger().ENDT();
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= mute_if( $switch1->is_set(), $dc1->atLeast($k), $dc2->atLeast($k) )->then(
				$dc1->subtract($k),
				$dc2->subtract($k),
				$tempdc->add($k),
			'');
		}
		$text .= mute_if( $switch1->is_set(), $dc1->exactly(0) )->then(
			$switch2->set(),
		'');
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= mute_if( $switch1->is_set(), $tempdc->atLeast($k) )->then(
				$dc1->add($k),
				$dc2->add($k),
				$tempdc->subtract($k),
			'');
		}
		$tempdc->kill();
		$text .= 	HEADING().
					$switch2->is_set();

		$returnarray = array($text, new SwitchList($switch1, $switch2));
		return $returnarray;
	}
	function GreaterThanOrEqual(Deathcounter $dc1, Deathcounter $dc2) {
		$switch1 = new TempSwitch();
		$switch2 = new TempSwitch();
		$tempdc = new TempDC();
        $maxpower = getBinaryPower( min($dc1->Max,$dc2->Max) );
		$text =	ACTIONS().$switch1->set().$tempdc->setTo(0).PreserveTrigger().ENDT();
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= mute_if( $switch1->is_set(), $dc1->atLeast($k), $dc2->atLeast($k) )->then(
				$dc1->subtract($k),
				$dc2->subtract($k),
				$tempdc->add($k),
			'');
		}
		$text .= mute_if( $switch1->is_set(), $dc2->exactly(0) )->then(
			$switch2->set(),
		'');
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= mute_if( $switch1->is_set(), $tempdc->atLeast($k) )->then(
				$dc1->add($k),
				$dc2->add($k),
				$tempdc->subtract($k),
			'');
		}
		$tempdc->kill();
		$text .= 	HEADING().
					$switch2->is_set();

		$returnarray = array($text, new SwitchList($switch1, $switch2));
		return $returnarray;
	}
	function Within(Deathcounter $dc1, Deathcounter $dc2, $range) {
		if ( !is_object($dc1) || !is_object($dc2) ) {
			Error('Within/Equal error: at least one of the variables passed is not an object');
		}
		$switch1 = new TempSwitch();
		$switch2 = new TempSwitch();
		$tempdc = new TempDC();
        $maxpower = getBinaryPower( min($dc1->Max,$dc2->Max) );
		$text =		ACTIONS().$switch1->set().$tempdc->setTo(0).PreserveTrigger().ENDT();
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= mute_if( $switch1->is_set(), $dc1->atLeast($k), $dc2->atLeast($k) )->then(
				$dc1->subtract($k),
				$dc2->subtract($k),
				$tempdc->add($k),
			'');
		}
		$text .= mute_if( $switch1->is_set(), $dc1->atMost($range), $dc2->atMost($range) )->then(
			$switch2->set(),
		'');
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= mute_if( $switch1->is_set(), $tempdc->atLeast($k) )->then(
				$dc1->add($k),
				$dc2->add($k),
				$tempdc->subtract($k),
			'');
		}
		$tempdc->kill();
		$text .= 	HEADING().
					$switch2->is_set();

		$returnarray = array($text, new SwitchList($switch1, $switch2));
		return $returnarray;
	}

	function Equal($dc1,$dc2){
		return Within($dc1,$dc2,0);
	}
	
	
	function ForemostPlayer(){
		global $ForemostSwitch;
		if( !$ForemostSwitch ){
			$ForemostSwitch = new PermSwitch();
		}
		return $ForemostSwitch->is_clear();
	}
	
	// EPDS
	function ChatIsOpen() 		{ return Memory(264056, AtLeast, 1); } // Local
	function ChatIsClosed() 	{ return Memory(264056, Exactly, 0); } // Local

	
