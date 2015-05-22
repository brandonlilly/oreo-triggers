<?php
class Player{
	
	// Properties
	public $PlayerString;
	public $PlayerList = array();
	public $ConditionType = Any;
	public $PrependState = false;
	
	// Prepend casting
	public $prepend = false;
	
	// Condition type casting
	public $all;
	public $each;
	public $and;
	
	public $any;
	public $or;
	
	// Constructor
	public function __construct($players = null){
		
		$argnum = func_num_args();
		
		if ( $argnum > 0 && $players ) { 
			$text = '';
			for ( $i=0; $i <= $argnum; $i++ ) {
				$arg = func_get_arg($i);
				if( is_int($arg) ) {
					$arg = 'Player '.$arg;
				}
				if ( $arg ) {
					$this->PlayerList[] = $arg;
					if( Minted() ){
						$text .= "<trig_group>$arg</trig_group>";
					}else{
						$text .= '"'.$arg.'",';
					}
				}
			}
			if( !Minted() ){
				$text = substr($text, 0, -1);
			}
			$this->PlayerString = $text;
		}
		
		
		// Condition type casing
		$copy1 = clone $this;
		$copy2 = clone $this;
		$copy3 = clone $this;
		
		$copy1->ConditionType = Any;
		$copy2->ConditionType = All;
		
		$this->any = $copy1;
		$this->or = $copy1;

		$this->all = $copy2;
		$this->each = $copy2;
		$this->and = $copy2;
		
		// Prepend casing
		$copy3->PrependState = true;
		$this->prepend = $copy3;
	}
	
	// IF
	public function _if() {
		global $TriggerOwner;
		if ( $this->PlayerString ) {
			$TriggerOwner = $this->PlayerString;
		}
		
		// Accumulate conditions
		$switchlist = new SwitchList();
		$conditions = AggregateConditions(func_get_args(), $switchlist);
		
		// Analysis handling
		global $AnalysisRoot;
		$line = null;
		if( $AnalysisRoot ){
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
			foreach($backtrace as $stack){
				if($stack['file'] == $AnalysisRoot){
					$line = $stack['line'];
					break;
				}
			}
		}
		
		OrReplace($conditions, $switchlist);
		return new IfClass($conditions, $switchlist, null, null, $line, $this->PrependState);
	}
	
	// SWITCH
	public function _switch(){
		if (!func_num_args()) { Error('You have to give your cases a value', E_USER_ERROR ); }
		
		// Analysis handling
		global $AnalysisRoot;
		$line = null;
		if( $AnalysisRoot ){
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
			foreach($backtrace as $stack){
				if($stack['file'] == $AnalysisRoot){
					$line = $stack['line'];
					break;
				}
			}
		}
		
		return new SwitchClass(func_get_arg(0), null, null, false, $line, $this->PrependState);
	}
	
	// ALWAYS
	public function always() {
		
		global $TriggerOwner;
		if ( $this->PlayerString ) {
			$TriggerOwner = $this->PlayerString;
		}
		
		// Accumulate actions
		$actions = AggregateActions(func_get_args());
		
		$text = 	HEADING().Always().ACTIONS().PreserveTrigger().$actions.ENDT();
		OutputTriggers($text, null, $this->PrependState);
		
		// Analysis handling
		global $AnalysisRoot;
		$line = null;
		if( $AnalysisRoot ){
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
			foreach($backtrace as $stack){
				if($stack['file'] == $AnalysisRoot){
					$line = $stack['line'];
					break;
				}
			}
			if( $line ){ InsertAnalysis($line,CountTriggers($text)); }
		}
	}
	
	// JUSTONCE
	public function justonce() {
		
		global $TriggerOwner;
		if ( $this->PlayerString ) {
			$TriggerOwner = $this->PlayerString;
		}
		
		// Accumulate actions
		$actions = AggregateActions(func_get_args());
		
		$text = 	HEADING().Always().ACTIONS().$actions.ENDT();
		OutputTriggers($text, null, $this->PrependState);
		
		// Analysis handling
		global $AnalysisRoot;
		$line = null;
		if( $AnalysisRoot ){
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
			foreach($backtrace as $stack){
				if($stack['file'] == $AnalysisRoot){
					$line = $stack['line'];
					break;
				}
			}
			if( $line ){ InsertAnalysis($line,CountTriggers($text)); }
		}
	}
	
	// NEVER
	public function never() {
		
		global $TriggerOwner;
		if ( $this->PlayerString ) {
			$TriggerOwner = $this->PlayerString;
		}
		
		// Accumulate actions
		$actions = AggregateActions(func_get_args());
		
		$text = 	HEADING().Never().ACTIONS().$actions.ENDT();
		OutputTriggers($text, null, $this->PrependState);
		
		// Analysis handling
		global $AnalysisRoot;
		$line = null;
		if( $AnalysisRoot ){
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
			foreach($backtrace as $stack){
				if($stack['file'] == $AnalysisRoot){
					$line = $stack['line'];
					break;
				}
			}
			if( $line ){ InsertAnalysis($line,CountTriggers($text)); }
		}
	}
	
	// Current Owner
	public function currentOwner() {
		global $TriggerOwner;
		$TriggerOwner = $this->PlayerString;
	}
	
	
	// SET RESOURCE
	public function setResource($amount, $resource) {
		$text = '';
		if( is_numeric($amount) ){
			foreach($this->PlayerList as $Player){
				$text .= SetResources($Player, "Set To", $amount, $resource);
			}
			return $text;
		}
		
		$tempdc = new TempDC();
		$maxpower = getBinaryPower( $amount->Max );
		$text .= 	$this->setResource(0, $resource).
					$tempdc->setTo(0);
		
        for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
				$text .= _if( $amount->atLeast($k) )->then(
				$this->addResource($k, $resource),
				$amount->subtract($k),
				$tempdc->add($k),
			'');
		}
        for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $tempdc->atLeast($k) )->then(
				$amount->add($k),
				$tempdc->subtract($k),
			'');
		}
		
		$text .= $tempdc->kill();
		
		return $text;
	}
	
	// ADD RESOURCE
	public function addResource($amount, $resource) {
		$text = '';
		if( is_numeric($amount) ){
			foreach($this->PlayerList as $Player){
				$text .= SetResources($Player, "Add", $amount, $resource);
			}
			return $text;
		}
		
		$tempdc = new TempDC();
		$maxpower = getBinaryPower( $amount->Max );
		$text .= $tempdc->setTo(0);
		
        for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $amount->atLeast($k) )->then(
				$this->addResource($k, $resource),
				$amount->subtract($k),
				$tempdc->add($k),
			'');
		}
        for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $tempdc->atLeast($k) )->then(
				$amount->add($k),
				$tempdc->subtract($k),
			'');
		}
		
		$text .= $tempdc->kill();
		
		return $text;
	}
	
	// SUBTRACT RESOURCE
	public function subtractResource($amount, $resource) {
		$text = '';
		if( is_numeric($amount) ){
			foreach($this->PlayerList as $Player){
				$text .= SetResources($Player, "Subtract", $amount, $resource);
			}
			return $text;
		}
		
		$tempdc = new TempDC();
		$maxpower = getBinaryPower( $amount->Max );
		$text .= $tempdc->setTo(0);
		
        for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $amount->atLeast($k) )->then(
				$this->subtractResource($k, $resource),
				$amount->subtract($k),
				$tempdc->add($k),
			'');
		}
        for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $tempdc->atLeast($k) )->then(
				$amount->add($k),
				$tempdc->subtract($k),
			'');
		}
		
		$text .= $tempdc->kill();
		
		return $text;
	}
	
	public function subtractOre($amount) 		{ return $this->subtractResource($amount, Ore); }
	public function setOre($amount) 			{ return $this->setResource($amount, Ore); }
	public function addOre($amount) 			{ return $this->addResource($amount, Ore); }
	public function subtractGas($amount) 		{ return $this->subtractResource($amount, Gas); }
	public function setGas($amount) 			{ return $this->setResource($amount, Gas); }
	public function addGas($amount) 			{ return $this->addResource($amount, Gas); }
	public function subtractOreAndGas($amount) 	{ return $this->subtractResource($amount, OreAndGas); }
	public function setOreAndGas($amount) 		{ return $this->setResource($amount, OreAndGas); }
	public function addOreAndGas($amount) 		{ return $this->addResource($amount, OreAndGas); }
	
	// HYPERTRIGGERS
	public function hypertriggers() {
		global $HyperPlayer;
		$HyperPlayer = $this;
	}
	
}

?>