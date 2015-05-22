<?php
class IfClass{
	
	// Properties
	/* @var SwitchList $SwitchList */
	public $SwitchList;
	public $Conditions;
	public $NestSwitch;
	public $Suppressed;
	public $LineNumber;
	public $PrependState;
	
	// Constructor
	public function IfClass(&$conditions, SwitchList $switchlist, $nestswitch = null, $suppress = null, $linenumber = null, $prependstate = false ){
		$this->SwitchList = $switchlist;
		$this->Conditions = $conditions;
		$this->NestSwitch = $nestswitch;
		$this->Suppressed = $suppress;
		$this->LineNumber = $linenumber;
		$this->PrependState = $prependstate;
		
	}
	
	public function __invoke($actions = null) {
		return $this->then($actions);
	}
	
	public function then($actions = null) {
		
		// Accumulate actions
		$actions = AggregateActions(func_get_args());
		
		$HEADING  = HEADING();
		$ACTIONS  = ACTIONS();
		$ENDT     = ENDT();
		$PRESERVE = PreserveTrigger(); 
		
		// If the last argument is 'e'
		$elseset = false;
		$elseAction = null;
		if ( func_get_arg(func_num_args()-1) == e ) {
			$elseset = true;
			$elseswitch = new TempSwitch();
			$elseAction = 	$elseswitch->set();
		}
		
        $nested = false;
		if ( $this->NestSwitch ) {
			$nested = true;
		}
		
		// Develop nesting text
		$nestTop = ''; $nestBottom = '';
		if ( $nested ) {
			/** @var $nestswitch TempSwitch */
			$nestswitch = $this->NestSwitch;
			
			$nestTop .=		     $nestswitch->set().
								ENDT();
			$this->Conditions =  $nestswitch->is_set().
								 $this->Conditions;
			
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
		
		
		
		$text =  "{$nestTop}{$HEADING}{$this->Conditions}{$ACTIONS}{$PRESERVE}{$actions}{$elseAction}{$ENDT}{$switchText}{$nestBottom}";
		/*
		$text .= $nestTop.
				 HEADING().
					$this->Conditions.
				 ACTIONS().
					PreserveTrigger().
					$actions.
					$elseAction.
			ENDT().
				 $switchText.
				 $nestBottom;
		*/
		
		if ( $elseset ) {
			return new ElseClass($this->Suppressed,$text,$elseswitch,$nestswitch, null, null, $this->LineNumber, $this->PrependState);
		}
		
		if ( !$nested && !$this->Suppressed) {
			OutputTriggers($text, $this->LineNumber, $this->PrependState);
		}
		
		return $text;
		
	}
	
	public function then_justonce($actions) {
		
		// Accumulate actions
		$actions = AggregateActions(func_get_args());
		
		$HEADING  = HEADING();
		$ACTIONS  = ACTIONS();
		$ENDT     = ENDT();
		
		// If the last argument is 'e'
		$elseset = false;
		$elseAction = null;
		if ( func_get_arg(func_num_args()-1) == e ) {
			$elseset = true;
			$elseswitch = new TempSwitch();
			$elseAction = 	$elseswitch->set();
		}
		
        $nested = false;
		if ( $this->NestSwitch ) {
			$nested = true;
		}
		
		// Develop nesting text
		$nestTop = ''; $nestBottom = '';
		if ( $nested ) {
			/** @var $nestswitch TempSwitch */
			$nestswitch = $this->NestSwitch;
			
			$nestTop.= 		     $nestswitch->set().
								ENDT();
			$this->Conditions =  $nestswitch->is_set().
								 $this->Conditions;
			
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
		
		$text = "{$nestTop}{$HEADING}{$this->Conditions}{$ACTIONS}{$actions}{$elseAction}{$ENDT}{$switchText}{$nestBottom}";
		
		/*
		$text = $nestTop.
				 HEADING().
					$this->Conditions.
				 ACTIONS().
					$actions.
					$elseAction.
			ENDT().
				 $switchText.
				 $nestBottom;
		*/
		
		if ( $elseset ) {
			return new ElseClass($this->Suppressed,$text,$elseswitch,$nestswitch, null, null, $this->LineNumber, $this->PrependState);
		}
		
		if ( !$nested && !$this->Suppressed) {
			OutputTriggers($text, $this->LineNumber, $this->PrependState);
		}
		
		return $text;
		
	}
	
	
}



