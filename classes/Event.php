<?php

class Event{
	
	// Properties
	public $State;
	public $Player = null;
	private $Counter = false;
	public $Condition;
	
	public $P1;
	public $P2;
	public $P3;
	public $P4;
	public $P5;
	public $P6;
	public $P7;
	public $P8;
	public $CP;
	
	
	// Constructor
	public function __construct($ConditionsOrArray){
		
		// Check if player specified array passed
		if( is_array($ConditionsOrArray) && !$ConditionsOrArray[0] ){
			
			$this->Player = CP;
			$this->State = new Deathcounter(CP);
			
			// CP casting
			$this->CP = clone $this;
            $this->CP->State = $this->State->CP;
			
			foreach($ConditionsOrArray as $player=>$conditions ){
				$Player = new Player($player);
								
				// Create state handling triggers
				$Player->_if( $conditions )->then(
					$this->State->add(1),
					_if( $this->State->exactly(1000001) )->then(
						$this->State->setTo(1),
					''),
				e)->_elseif( $this->State->between(1,999999) )->then(
					$this->State->setTo(1000000),
				e)->_else(
					$this->State->setTo(0),
				'');
				
				// Add player casting
				$ps = GetPlayerShorthand($player);
				$this->{$ps} = clone $this;
				$this->{$ps}->State = $this->State->{$ps};
			}
			
		} elseif( $ConditionsOrArray instanceof Player ){ 
			
			// if player object specified
			
			$Player = $ConditionsOrArray;
			$this->State = new Deathcounter($Player);
			
			// Accumulate conditions
			$switchlist = new SwitchList();
			$conditions = AggregateConditions(func_get_args(), $switchlist);
			
			// Create state handling triggers
			$Player->_if( $conditions )->then(
				$this->State->add(1),
				_if( $this->State->exactly(1000001) )->then(
					$this->State->setTo(1),
				''),
			e)->_elseif( $this->State->between(1,999999) )->then(
				$this->State->setTo(1000000),
			e)->_else(
				$this->State->setTo(0),
			'');

			// Add player casting
			foreach( $Player->PlayerList as $player ){
				$ps = GetPlayerShorthand($player);
				$this->{$ps} = clone $this;
				$this->{$ps}->State = $this->State->{$ps};
			}
			
		} else {
			
			// Accumulate conditions
			$switchlist = new SwitchList();
			$conditions = AggregateConditions(func_get_args(), $switchlist);
			
			global $PrependedEvents;
			foreach($PrependedEvents as $event){
				if( $conditions == $event->Condition ){
					$this->State = $event->State;
					$this->Condition = $event->Condition;
					$this->Counter = $event->Counter;
					return;
				}
			}
			
			// Not player specified array
			$Player = new Player(AllPlayers);
			$this->State = new Deathcounter();
			
			// Create state handling triggers
			$Player->prepend->_if( ForemostPlayer() )->then(
				_if( $conditions )->then(
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
			
			$this->Condition = $conditions;
			$PrependedEvents[] = $this;
		}
		
	}


	/////
	// CONDITIONS
	///

	public function isHappening(){
		return $this->State->between(1,999999);
	}
	
	public function isStopped(){
		return not($this->State->between(1,999999));
	}
	
	public function justHappened(){
		return $this->State->exactly(1);
	}
	
	public function justStopped(){
		return $this->State->exactly(1000000);
	}
	
	
	
	public function happensFor($qmod, $loops){
		if( $qmod === AtLeast )
			return $this->State->between($loops, 999999);
		if( $qmod === Exactly )
			return $this->State->exactly($loops);
		if( $qmod === AtMost )
			return $this->State->atMost($loops);
	}
	
	public function occurs($qmod, $numberoftimes){
		// If Counter hasn't been used before, allocate a dc and create triggers for it
		if( $this->Counter === false ){
			$this->Counter = new Deathcounter($this->Player);
			
			if( $this->Player !== null ){
				$Player = new Player($this->Player);
				$Player->prepend->_if( $this->justHappened() )->then(
					$this->Counter->add(1),
				'');
			} else {
				$Player = new Player(AllPlayers);
				$Player->prepend->_if( $this->justHappened(), ForemostPlayer() )->then(
					$this->Counter->add(1),
				'');
			}
		}
		
		if( $qmod === AtLeast )
			return $this->Counter->atLeast($numberoftimes);
		if( $qmod === AtMost )
			return $this->Counter->atMost($numberoftimes);
		if( $qmod === Exactly )
			return $this->Counter->exactly($numberoftimes);
	}
	
}

?>