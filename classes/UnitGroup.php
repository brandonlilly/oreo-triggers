<?php
class UnitGroup{

	// Properties
	public $Unit;
	public $Location;
	public $Player;

	// Player Casting
	public $P1;
  public $P2;
  public $P3;
  public $P4;
  public $P5;
  public $P6;
  public $P7;
  public $P8;
  public $P9;
  public $P10;
  public $P11;
  public $P12;
  public $AllPlayers;
  public $CP;
  public $Foes;
  public $Allies;
	public $F1;
	public $F2;
	public $F3;
	public $F4;

	// Private
	private $EventArray = array();

	// Constructor
	public function UnitGroup($unit, $player, $location){
		ValidUnitCheck($unit);
		$this->Unit = $unit;
		$this->Player = $player;
		$this->Location = $location;

		// Fill Player Casting slots
		$clone = clone $this;
		$this->P1 = clone $clone;
		$this->P1->Player = P1;
        $this->P2 = clone $clone;
        $this->P2->Player = P2;
        $this->P3 = clone $clone;
        $this->P3->Player = P3;
        $this->P4 = clone $clone;
        $this->P4->Player = P4;
        $this->P5 = clone $clone;
        $this->P5->Player = P5;
        $this->P6 = clone $clone;
        $this->P6->Player = P6;
        $this->P7 = clone $clone;
        $this->P7->Player = P7;
        $this->P8 = clone $clone;
        $this->P8->Player = P8;
        $this->P9 = clone $clone;
        $this->P9->Player = P9;
        $this->P10 = clone $clone;
        $this->P10->Player = P10;
        $this->P11 = clone $clone;
        $this->P11->Player = P11;
        $this->P12 = clone $clone;
        $this->P12->Player = P12;
        $this->CP = clone $clone;
        $this->CP->Player = CP;
        $this->AllPlayers = clone $clone;
        $this->AllPlayers->Player = AllPlayers;
        $this->Allies = clone $clone;
        $this->Allies->Player = Allies;
        $this->Foes = clone $clone;
        $this->Foes->Player = Foes;
		$clone = clone $this;
		$this->F1 = clone $clone;
		$this->F1->Player = F1;
        $this->F2 = clone $clone;
        $this->F2->Player = F2;
        $this->F3 = clone $clone;
        $this->F3->Player = F3;
        $this->F4 = clone $clone;
        $this->F4->Player = F4;
	}


	/////
	//CONDITIONS
	//

	public function at($location){
		return Bring($this->Player,$this->Unit, AtLeast, 1, $location);
	}

	public function notAt($location){
		return Bring($this->Player,$this->Unit, Exactly, 0, $location);
	}

	public function isHome(){
		return $this->at($this->Location);
	}

	// at and notAt aliases
	public function inside($location){ return $this->at($location); }
	public function outside($location){ return $this->notAt($location); }

	public function bring($qmod, $n, $location = null){
		if( $location === null ){ $location = $this->Location; }
		return Bring($this->Player, $this->Unit, $qmod, $n, $location);
	}

	public function command($qmod, $n){
		return Command($this->Player, $this->Unit, $qmod, $n);
	}


	// not finished
	public function enters($location){
		//Error("function ->enters() is not yet implemented, sorry.");

		$event = new Event(Bring($this->Player,$this->Unit, AtLeast, 1, $location));
		return $event->justHappened();
	}

	public function exits($location){
		//Error("function ->exits() is not yet implemented, sorry.");

		$event = new Event(Bring($this->Player,$this->Unit, AtLeast, 1, $location));
		return $event->justStopped();
	}

	/////
	//ACTIONS
	//

	public function create($n, $property = null){
		$argnum = func_num_args();
		if ( $property ) {
			$properties = array();
			if ( is_array($property) ){
				$properties = $property;
			}else{
				for ( $i=1; $i <= $argnum; $i++ ) {
					$arg = func_get_arg($i);
					$properties[] = $arg;
				}
			}
			return CreateUnitWithProperties($this->Player, $this->Unit, $n, $this->Location, $properties);
		}

		return CreateUnit($this->Player, $this->Unit, $n, $this->Location);
	}

	public function createAt($location, $n, $property = null){
		$argnum = func_num_args();
		if ( $property ) {
			$properties = array();
			if ( is_array($property) ){
				$properties = $property;
			}else{
				for ( $i=2; $i <= $argnum; $i++ ) {
					$arg = func_get_arg($i);
					$properties[] = $arg;
				}
			}
			return CreateUnitWithProperties($this->Player, $this->Unit, $n, $location, $properties);
		}

		return CreateUnit($this->Player, $this->Unit, $n, $location);
	}

	public function teleportTo($location, $n = All, $fromloc = null) {
		if( $fromloc === null ){ $fromloc = $this->Location; }
		return MoveUnit($this->Player, $this->Unit, $n, $fromloc, $location);
	}

	public function acquireLocation($locations) {
		if ( !$locations ){
			Error('You need to pass in a location!');
		}
		$text = '';
		foreach( func_get_args() as $arg ){
			$text .= MoveLocation($arg, $this->Player, $this->Unit, $this->Location);
		}
		return $text;
	}


	public function moveTo($location, $fromloc = null) {
		if( $fromloc === null ){ $fromloc = $this->Location; }
		return Order($this->Player, $this->Unit, $fromloc, Move, $location);
	}
	public function attackTo($location) {
		return Order($this->Player, $this->Unit, $this->Location, Attack, $location);
	}
	public function patrolTo($location) {
		return Order($this->Player, $this->Unit, $this->Location, Patrol, $location);
	}


	public function giveTo($player, $n = All, $atlocation = null) {
		if( $atlocation === null ){ $atlocation = $this->Location; }
		return Give($this->Player, $this->Unit, $n, $player, $atlocation);
	}
	public function kill($n = All) {
		return KillUnitAtLocation($this->Player, $this->Unit, $n, $this->Location);
	}
	public function remove($n = All) {
		return RemoveUnitAtLocation($this->Player, $this->Unit, $n, $this->Location);
	}


	public function enableInvincibility() {
		return SetInvincibility($this->Player, $this->Unit, $this->Location, Enabled);
	}
	public function disableInvincibility() {
		return SetInvincibility($this->Player, $this->Unit, $this->Location, Disabled);
	}
	public function toggleInvincibility() {
		return SetInvincibility($this->Player, $this->Unit, $this->Location, Toggle);
	}

	public function enableDoodadState() {
		return SetDoodadState($this->Player, $this->Unit, $this->Location, Enabled);
	}
	public function disableDoodadState() {
		return SetDoodadState($this->Player, $this->Unit, $this->Location, Disabled);
	}

	public function setHealth($percent) {
		return ModifyHealth($this->Player, $this->Unit, All, $this->Location, $percent);
	}
	public function setEnergy($percent) {
		return ModifyEnergy($this->Player, $this->Unit, All, $this->Location, $percent);
	}
	public function setShield($percent) {
		return ModifyShield($this->Player, $this->Unit, All, $this->Location, $percent);
	}


}
