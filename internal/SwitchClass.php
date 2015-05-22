<?php
class SwitchClass{
	
	// Properties
	public $Deathcounter;
	public $Value;
	public $Actions;
	public $Nested;
	public $LineNumber;
	public $PrependState;
	
	// Constructor
	public function __construct($deathcounter, $value, $actions, $nested = false, $linenumber = null, $prependstate = false){
		$this->Deathcounter = $deathcounter;
		$this->Value = $value;
		$this->Actions = $actions;
		$this->Nested = $nested;
		$this->LineNumber = $linenumber;
		$this->PrependState = $prependstate;
	}

	public function _for($cases) {
		if (!$cases) { Error('You have to give your switch some cases!', E_USER_ERROR ); }
		
		$trig = null;
		
		$cases = array_filter(func_get_args());
		$argnum = count($cases);
		
		// Accumulate cases
		for($i=0; $i<($argnum); $i++){
			$case = $cases[$i];
			
			$conditions = '';
			
			// Generate conditions
			if ( $case->Value == 'Default' ){
				if( $i != ($argnum-1) ){ Error('Default has to come last in the switch!'); }
				$conditions = '';
			}else{
				foreach($case->Value as $value){
					$conditions .= Deaths($this->Deathcounter->Player, Exactly, $value, $this->Deathcounter->Unit)._OR;
				}
			}
			$conditions = substr($conditions,0,-1*strlen(_OR));
			$conditions = orGroup($conditions);
			
			// If there are more cases to come, e else them
			$E = '';
			if( $i < ($argnum-1) ){
				$E = e;
			}
			
			// If this is the first case 
			if( $i == 0 ){
				if($this->Nested){
					$trig = _if( $conditions )->then(
						$case->Actions,
						$E
					);
				} else{
					$trig = mute_if( $conditions )->then(
						$case->Actions,
						$E
					);
				}
			}
			// If this is not the first case
			else {
				$trig = $trig->_elseif( $conditions )->then(
					$case->Actions,
					$E
				);
			}
		}
		
		if ( !$this->Nested ) {
			OutputTriggers($trig, $this->LineNumber, $this->PrependState);
		}
		
		return $trig;
	}

	public function then($actions){
		if (!$actions) { Error('You have to give your cases some actions', E_USER_ERROR ); }
		
		// Accumulate actions
		$actions = AggregateActions(func_get_args());
		
		return new SwitchClass(null,$this->Value,$actions);
	}
	
}
	
	
	
