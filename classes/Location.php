<?php
class Location{
	
	// Properties
	public $Name;
	
	// Constructor
	public function __construct($name){
		$this->Name = $name;
		
	}
	
	public function __toString(){
		return $this->Name;
	}
	
	/////
	// ACTIONS
	///
	
	public function centerOn($player, $unit = null, $location = null) {
		$argnum = func_num_args();
		if ( $argnum == 3 ) {
			return MoveLocation($this->Name, $player, $unit, $location);
		}
		if ( $player instanceof UnitGroup ) {
			if( $unit == null ) { $unit = $player->Location; }
			return MoveLocation($this->Name, $player->Player, $player->Unit, $unit);
		}
		if ( $argnum == 1 && $player ) {
			return MoveLocation($this->Name, Neutral, "Map Revealer", $player);
		}
		Error('Error: Bad centerOn() argument');
	}
	
	public function acquire($location) {
		if ( !$location ){
			Error('You need to pass in a location!');
		}
		$text = '';
		foreach(func_get_args() as $loc){
			$text .= MoveLocation($loc, Neutral, "Map Revealer", $this->Name);
		}
		return $text;
	}
	
	public function centerView() {
		return CenterView($this);
	}
	
	public function ping() {
		return MinimapPing($this);
	}
	
	
	// Aesthetics
	
	public function explode($player = P8){
		return
		CreateUnitWithProperties($player,'Terran Wraith',1, $this, Cloaked).
		KillUnit($player, 'Terran Wraith');
	}
	
}

