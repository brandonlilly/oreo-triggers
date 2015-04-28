<?php
class ElseClass{
	
	// Properties
	public $Suppressed;
	public $IfText;
	public $ElseSwitch;
	public $NestSwitch;
	public $LineNumber;
	
	// for elseif's then
	public $ElseConditions;
	public $SwitchList;
	
	// Constructor
	public function ElseClass($suppress, $iftext, $elseswitch, $nestswitch, $elseconditions = null, SwitchList $switchlist = null, $linenumber = null, $prependstate ){
		$this->Suppressed = $suppress;
		$this->IfText = &$iftext;
		$this->ElseSwitch = $elseswitch;
		$this->NestSwitch = $nestswitch;
		$this->ElseConditions = &$elseconditions;
		$this->SwitchList = $switchlist;
		$this->LineNumber = $linenumber;
		$this->PrependState = $prependstate;
	}
	
	
	public function _else() {
	
		// Accumulate actions
		$actions = AggrigateActions(func_get_args());
		
		
		$text = &$this->IfText;
		$elseswitch = $this->ElseSwitch;
		$nested = isset($this->NestSwitch);
		
		// Conditions
		$conditions = $elseswitch->is_clear();
		
		// Develop nesting text
		$nestBottom = '';
		if ( $nested ) {
			$nestswitch = $this->NestSwitch;
			
			$conditions = $nestswitch->is_set() . $conditions;
			
			$nestBottom = 	HEADING().
							$nestswitch->is_set().
							ACTIONS().
							PreserveTrigger().
							$nestswitch->kill();
		}
		
		
		// Compile trigger's text
		
		$text .= HEADING().
		            $conditions.
				 ACTIONS().
					PreserveTrigger().
					$actions.
				 ENDT().
				 $elseswitch->killTrigger().
				 $nestBottom;
		
		if ( !$nested && !$this->Suppressed ) {
			OutputTriggers($text, $this->LineNumber, $this->PrependState);
		}
		
		return $text;
		
	}
	
	
	public function _elseif() {

		// Accumulate conditions
		$switchlist = new SwitchList();
		$conditions = AggrigateConditions(func_get_args(), $switchlist);
		
		OrReplace($conditions, $switchlist,$this->NestSwitch);
		return new ElseClass($this->Suppressed, $this->IfText, $this->ElseSwitch, $this->NestSwitch, $conditions, $switchlist, $this->LineNumber, $this->PrependState );
		
	}
	
	public function then() {
		
		// Accumulate actions
		$actions = AggrigateActions(func_get_args());
		
		$text = &$this->IfText;
		$elseswitch = $this->ElseSwitch;
		$nested = isset($this->NestSwitch);
		
		// Conditions
		$conditions = $elseswitch->is_clear() . $this->ElseConditions;
		
		// If the last argument is 'e'
		$elseset = false;
		$elseAction = '';
		$elsekill = '';
		if ( func_get_arg(func_num_args()-1) === e ) {
			$elseset = true;
			//$elseswitch = new TempSwitch();
			$elseAction = $elseswitch->set();
			
		} else {
			$elsekill = $elseswitch->killTrigger();
		}
		
		$nestBottom = '';
		// Develop nesting text
		if ( $nested ) {
			
			$nestswitch = $this->NestSwitch;
			
			$conditions = $nestswitch->is_set() . $conditions;
			if ( !$elseset ) {
				$nestBottom = 	HEADING().
								$nestswitch->is_set().
								ACTIONS().
								PreserveTrigger().
								$nestswitch->kill();
			}
		}
		
		// Kill switches
		$switchText = '';
		if ( $this->SwitchList instanceof SwitchList ) {
			if( !empty($this->SwitchList->Switches) ){
				$clearactions = '';
				foreach( $this->SwitchList->Switches as $s){
					/* @var TempSwitch $s */
					$clearactions .=  $s->release();
				}
				$switchText = 	HEADING().
								Always().
								ACTIONS().
								$clearactions.
								PreserveTrigger().
								ENDT();
			}
		}
		
		// Compile trigger's text
		$text .= HEADING().
					$conditions.
				 ACTIONS().
					PreserveTrigger().
					$actions.
					$elseAction.
				 ENDT().
                 $elsekill.
                 $switchText.
                 $nestBottom;
		
		
		if ( $elseset ) {
			return new ElseClass($this->Suppressed, $text,$elseswitch,$nestswitch, null, null, $this->LineNumber, $this->PrependState);
		}
		
		if ( !$nested && !$this->Suppressed ) {
			OutputTriggers($text, $this->LineNumber, $this->PrependState);
		}
		
		return $text;
		
		
	}
	
}



