<?php

	
	/* 
	
	KeyStroke: Provides support for local EPD keyboard detection (For Windows SC 1.16.1)
	
	Creating a KeyStroke object automatically creates triggers (owned by the last player group to own a trigger)
	that give you access to the pressed, released, isUp and isDown conditions for the key group passed into the constructor.
	When chat is open it automatically stops detecting the keys being pressed (with the exception of left click detection).
	 
	Ex1:
		$QKey = new KeyStroke(Q);
		$Player->_if( $QKey->pressed() )->then(
			Display('Q key was just pressed!'),
		'');
	 
	If multiple keys are passed into the KeyStroke on construction then the events will correspond to when all the keys
	are down or up.
	
	Ex2:
		$PlusKey = new KeyStroke(Shift,=);
		$Player->_if( $PlusKey->pressed() )->then(
			Display(' "Shift" and "=" were just pressed creating a "+" !'),
		'');'
	
	Warning: 
		Certain combinations of keys won't work together (Shift, Ctrl or Alt with 0-9 for instance),
		simply because these have reserved meanings for Starcraft.
	
	Resource Cost:
		Triggers: At most ( 5 + 11*[NumberOfKeysPassed] )
		Deathcounters: 1
		TempSwitches: 1 + [NumberOfKeysPassed]
		
	*/


class KeyStroke{
	
	// Properties
	public $Keys = array();
	public $PressTriggers;
	public $State;
	
	// Constructor
	public function __construct($keys){
		
		global $KeyStrokes;
		
		// Populate Keys array
		$argnum = func_num_args();
		if ( $argnum > 0 && $keys !== null ) { 
			for ( $i=0; $i < $argnum; $i++ ) {
				$arg = func_get_arg($i);
				if ( $arg !== null ) {
					$foundmatch = false;
					$arg = strtoupper($arg);
					
					// Check if left click
					if ( $arg == 'LEFTCLICK' ){
						$this->Keys[] = array($arg);
					} else {
						foreach($KeyStrokes as $player=>$blockarray){
							for($k=0; $k <= 3; $k++) {
								// If match is found
								if ( $arg == strtoupper($blockarray[$k]) ){
									$foundmatch = true;
									$this->Keys[] = array($arg,$player,$k);
								}
							}
						}
						if ( !$foundmatch ) {
							Error( "$arg is an unrecognized Key" );
						}
					}
				}
			}
		}else {
			Error("You need to specify a Key");
		}
		
		// Generate pressed conditions based on Keys array
		$conditions = array();
		foreach($this->Keys as $key=>$value) {
			$player = $value[1];
			$block = $value[2];
			
			// Check if left click
			if( $value[0] == 'LEFTCLICK' ) {
				$conditions[] = Memory(12626, AtLeast, 1);
			} else {
				switch($block){
					case 0:
						$conditions[] = Memory($player, Exactly, 1).ChatIsClosed(). _OR .ChatIsClosed(). Memory($player, Exactly, 257). _OR .
										Memory($player, Exactly, 65537).ChatIsClosed(). _OR .ChatIsClosed(). Memory($player, Exactly, 65793). _OR .
										Memory($player, Exactly, 16777217).ChatIsClosed(). _OR .ChatIsClosed(). Memory($player, Exactly, 16777473). _OR .
										Memory($player, Exactly, 16842753).ChatIsClosed(). _OR .ChatIsClosed(). Memory($player, Exactly, 16843009);
						break;
					case 1: 
						$conditions[] = Memory($player, AtLeast, 256).Memory($player, AtMost, 257).ChatIsClosed()._OR.
										Memory($player, AtLeast, 65792).Memory($player, AtMost, 65793).ChatIsClosed()._OR.
										Memory($player, AtLeast, 16777472).Memory($player, AtMost, 16777473).ChatIsClosed()._OR.
										Memory($player, AtLeast, 16843008).Memory($player, AtMost, 16843009).ChatIsClosed();
						break;
					case 2: 
						$conditions[] = Memory($player, AtLeast, 65536).Memory($player, AtMost, 65793).ChatIsClosed()._OR.
										Memory($player, AtLeast, 16842752).Memory($player, AtMost, 16843009).ChatIsClosed();
						break;
					case 3: 
						$conditions[] = Memory($player, AtLeast, 16777216).ChatIsClosed();
						break;
				}
			}
			
		}
		
		$this->State = new Deathcounter();
		$Player = new Player(AllPlayers);
		
		// Create state handling triggers
		if ( $argnum == 1 ) {
			$Player->prepend->_if( ForemostPlayer() )->then(
				_if( $conditions[0] )->then(
					$this->State->add(1),
					_if( $this->State->exactly(1000001) )->then(
						$this->State->setTo(1),
					''),
				e)->_elseif( $this->State->between(1,999999) )->then(
					$this->State->setTo(1000000),
				e)->_else(
					$this->State->setTo(0),
				''),
			'');
			
		} else {
            $nconditions = '';
			$nSwitches = array();
			foreach($conditions as $condition) {
				$nswitch = new TempSwitch();
				$nSwitches[] = $nswitch;
				$nconditions .= $nswitch->is_set();
				$Player->_if( $condition )->then(
					$nswitch->set(),
				'');
			}
			
			$Player->prepend->_if( ForemostPlayer() )->then(
				_if( $nconditions, ChatIsClosed() )->then(
					$this->State->add(1),
					_if( $this->State->exactly(1000001) )->then(
						$this->State->setTo(1),
					''),
				e)->_elseif( $this->State->between(1,999999) )->then(
					$this->State->setTo(1000000),
				e)->_else(
					$this->State->setTo(0),
				''),
			'');
			
			foreach ($nSwitches as $switch) {
				$Player->prepend->_if( ForemostPlayer(), $switch->is_set() )->then(
					$switch->kill(),
				'');
			}
		}
		
	}


	/////
	// CONDITIONS
	///

	public function isDown(){
		return $this->State->between(1,999999);
	}
	
	public function isUp(){
		return not($this->State->between(1,999999));
	}
	
	public function pressed(){
		return $this->State->exactly(1);
	}
	
	public function released(){
		return $this->State->exactly(1000000);
	}
	
}

?>