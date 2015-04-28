<?php
	//NOTE: to give an overview of how functions scale, each was tested with low end, high end, and maxed values for all deathcounters involved.
	//The low end value used was 100, the high end value used was 1000000, and the maxed value was 2147483647 or 4294967295, depending on the function.


/**
 * Class Deathcounter
 * 
 * @property Deathcounter P1, P2
 * @property Deathcounter P3
 * @property Deathcounter P4
 * @property Deathcounter P5
 * @property Deathcounter P6
 * @property Deathcounter P7
 * @property Deathcounter P8
 * @property Deathcounter AllPlayers
 * @property Deathcounter CP
 * @property Deathcounter Foes
 * @property Deathcounter Allies
 * @property Deathcounter All
 * @property Deathcounter F1
 * @property Deathcounter F2
 * @property Deathcounter F3
 * @property Deathcounter F4
 */
class Deathcounter {
	
	// Properties
	public $Player;
	public $Unit;
	public $Min = 0;
	public $Max = 2147483647;
	public $PlayerClass = null;
	
    // Player Casting
	private $P1, $P3, $P4, $P5, $P6, $P7, $P8;
    private $AllPlayers, $CP, $Foes, $Allies, $All;
	private $F1, $F2, $F3, $F4;
	
	// Private
	private $randomindex;
	private $EnumArray = null;
	
	
    // Constructor
	public function __construct($player = null, $max = null){
		$this->randomindex = rand();
		
		global $DeathcounterUnits;
		$found = false;
		// If CP Allies Foes or AllPlayers dc, reserve the entire unit
		if ($player == CP || $player == Allies || $player == Foes || $player == AllPlayers ) {
			$this->Player = $player;
			if ( $max ) { $this->Max = $max; }
			
			foreach($DeathcounterUnits as $unit => $playerArray) {
				$availablecount = 0;
				foreach($playerArray as $playerIndex=>$SlotUsage) {
					if ( $SlotUsage === true ) { $availablecount++; } 
				}
				if ( $availablecount == 8 ) {
					foreach($playerArray as $playerIndex=>$slot) {
						 $DeathcounterUnits[$unit][$playerIndex] = 4;
					}
					$this->Unit = $unit;
					$found = true;
					break;
				}
				
			}
            if ( !$found ) {
                Error('Failed to find any available deathcounter slots (perhaps allocate more slots in config.php)');
            }
			
		} elseif( $player instanceof Player ){
			
			$this->Player = CP;
			$this->PlayerClass = $player;
			if ( $max ) { $this->Max = $max; }
			
			// Find available deathcounter slots and set players/unit appropriately
			foreach($DeathcounterUnits as $unit => $playerArray) {
				$availablecount = 0;
				foreach($playerArray as $playerIndex=>$SlotUsage) {
					if ( in_array($playerIndex,$player->PlayerList) && $SlotUsage === true ) {
						$availablecount++;
					}
				}
				if ( $availablecount == count($player->PlayerList) ) {
					foreach($playerArray as $playerIndex=>$slot) {
						if ( in_array($playerIndex,$player->PlayerList) ) {
							$DeathcounterUnits[$unit][$playerIndex] = 4;
						}
					}
					$this->Unit = $unit;
					$found = true;
					break;
				}
				
			}
			if ( !$found ) {
				Error('Failed to find any available deathcounter slots (perhaps allocate more slots in config.php)');
			}
			
		}else {
			if ( $player ) { $this->Max = $player; }
			$break = false;
			foreach($DeathcounterUnits as $unit => $playerArray) {
				
				foreach($playerArray as $playerIndex=>$SlotUsage) {
					if ( $SlotUsage === true ) {
						$DeathcounterUnits[$unit][$playerIndex] = 4;
						$this->Unit = $unit;
						$this->Player = $playerIndex;
						$break = true;
						break;
					}
				}
				if ( $break ){ break; }
			}
			if ( !$break ) {
				Error('Failed to find any available deathcounter slots (perhaps allocate more slots in config.php)');
			}
			
		}
		
	}
	
	
	public function __get($name){
		
		$short = GetPlayerShorthand($name);
		$long  = get_defined_constants(true);
		$long  = $long["user"][$short];
		
		$clone = clone $this;
		
		switch($long){
			case F1:         $clone->Player = F1; return $clone;         break;
			case F2:         $clone->Player = F2; return $clone;         break;
			case F3:         $clone->Player = F3; return $clone;         break;
			case F4:         $clone->Player = F4; return $clone;         break;
			
			case AllPlayers: $clone->Player = AllPlayers; return $clone; break;
			case Allies:     $clone->Player = Allies; return $clone;     break;
			case Foes:       $clone->Player = Foes; return $clone;       break;
			case CP:         $clone->Player = CP; return $clone;         break;
		}
		
		if($this->PlayerClass instanceof Player){
			if( $name === "All" ){
				return $clone->Player = $this->PlayerClass;
			}
			
			if( !in_array($long, $this->PlayerClass->PlayerList) ){
				Error("$name wasn't one of the players you specified in declaration");
			}
		}
		
		switch($long){
			case P1:         $clone->Player = P1; return $clone;         break;
			case P2:         $clone->Player = P2; return $clone;         break;
			case P3:         $clone->Player = P3; return $clone;         break;
			case P4:         $clone->Player = P4; return $clone;         break;
			case P5:         $clone->Player = P5; return $clone;         break;
			case P6:         $clone->Player = P6; return $clone;         break;
			case P7:         $clone->Player = P7; return $clone;         break;
			case P8:         $clone->Player = P8; return $clone;         break;
		}
		
		Error("Invalid property for this Deathcounter: $name");
		
	}
	
	
	public function __toString(){
		$enum = '';
		if( is_array($this->EnumArray) ){
			$enum = json_encode($this->EnumArray);
		} else {
			if( $this->Max - $this->Min > 500 ){ 
				Error("Range of values of deathcounter is too high (over 500; Oreo generates 1 trigger per possible value) to be automated into a string. Raise minimum or lower maximum."); 
			}
		}
		
		return "_XDCX_$this->Player::$this->Unit::$this->Min::$this->Max::{$enum}_XDCX_";
	}
	
	public function enumerate($array){
		if( !is_array($array) ){ Error("You must pass in an array into enumerate"); }
		$this->EnumArray = $array;
	}
	
	public function binaryPower(){
		return getBinaryPower($this->Max);
	}
	
	/////
	// CONDITIONS
	///
	
	public function atLeast($n){ return AtLeast($this, $n); }
	public function atMost($n) { return AtMost($this, $n); }
	public function exactly($n){ return Exactly($this, $n); }
	public function between($a, $b) {
		return $this->atLeast($a) . $this->atMost($b);
	}
	public function equalTo($dc) { return Equal($this, $dc); }
	public function lessThan($dc) { return LessThan($this, $dc); }
	public function greaterThan($dc) { return GreaterThan($this, $dc); }
	public function lessThanOrEqual($dc) { return LessThanOrEqual($this, $dc); }
	public function greaterThanOrEqual($dc) { return GreaterThanOrEqual($this, $dc); }
	
	
	/////
	// ACTIONS
	///

	//MAX ! (NOTE: COMPILER FUNCTION, NOT TRIGGER FUNCTION)
	/*
		� Sets the max bounds of a deathcounter. Useful for making Deathcounter functions more efficient.
		� Format:
			- $dc->max($var) is analogous to $dc->Max = $var
			- $var must be a constant (integer)
		� No triggers, switches, or deathcounters used.
		� Deathcounters can have a max value of 4294967295, but multiplication, division, and square root functions can only support a max of 2147483647
	*/
	public function max($max) {
		//ERROR
		if ( func_num_args() != 1 ) {
			Error('COMPILER ERROR FOR MAX(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 1: CONSTANT (INTEGER))');
		}
		else if ( is_numeric($max) && $max < 4294967296 ) {
			$this->Max = $max;
		}
		else if ( is_numeric($max) ) {
			Error('COMPILER ERROR FOR MAX(): MAX CANNOT EXCEED 2^32-1 (4294967295)');
		}
		else {
			Error('COMPILER ERROR FOR MAX(): ARGUMENT MUST BE A CONSTANT (INTEGER)');
		}
		return '';
	}
	
	public function range($min, $max) {
		if( !is_int($max) || !is_int($min) ){
			Error("max and min parameters must be integers");
		}
		if( $max >= 4294967296 || $min >= 4294967296 ){
			Error("max and min must be below 4294967296");
		}
		if( $min > $max ){
			Error("The max value must be greater than the minimum value");
		}
		$this->Min = $min;
		$this->Max = $max;
		return '';
	}
	
	public function min($min) {
		//ERROR
		if( !is_int($min) ){
			Error("min must be an integer");
		}
		if( $min < 4294967296 ){
			Error("min must be below 4294967296");
		}
		if( $min > $this->Max ){
			Error("min must be below the max (current max: {$this->Max})");
		}
		$this->Min = $min;
		return '';
	}
	
	//ADD !
	/*
		� Adds argument deathcounter or constant (integer) to the calling deathcounter.
		� Format:
			- $dc->add($var) is analogous to $dc += $var
			- $var must be a deathcounter or a constant (integer)
		� Max values:
			- argument: 4294967295
		� Specifics:
			- low end trigger number: 16
			- high end trigger number: 42
			- max trigger number: 66
			- 1 temporary switch
			- 1 temporary deathcounter
	*/
	public function add($var){
		//ERROR
		if ( func_num_args() != 1 ) {
			Error('COMPILER ERROR FOR ADD(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 1: DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if ( is_numeric($var) ) {
			return Add($this, $var);
		}
		if (!($var instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR ADD(): ARGUMENT NEEDS TO EITHER BE A CONSTANT (INTEGER) OR A DEATHCOUNTER');
		}
		
		$tempdc = new TempDC();
		
		$maxpower = getBinaryPower( $var->Max );
		
		$text = $tempdc->setTo(0);
		
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $var->atLeast($k) )->then(
				$this->add($k),
				$var->subtract($k),
				$tempdc->add($k),
			'');
		}
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $tempdc->atLeast($k) )->then(
				$var->add($k),
				$tempdc->subtract($k),
			'');
		}
		
		$text .= 	$tempdc->kill();
		
		return $text;
	}
	
	//ADDDEL !
	/*
		� Adds argument deathcounter or constant (integer) to the calling deathcounter and clears the argument deathcounter.
		� Format:
			- $dc->add($var) is analogous to $dc += $var; $var = 0;
			- $var must be a deathcounter or a constant (integer)
		� Max values:
			- argument: 4294967295
		� Specifics:
			- low end trigger number: 8
			- high end trigger number: 21
			- max trigger number: 33
			- 1 temporary switch
	*/
	public function addDel($var){
		//ERROR
		if ( func_num_args() != 1 ) {
			Error('COMPILER ERROR FOR ADDDEL(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 1: DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if ( is_numeric($var) ) {
			return Add($this, $var);
		}
		if (!($var instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR ADDDEL(): ARGUMENT NEEDS TO EITHER BE A CONSTANT (INTEGER) OR A DEATHCOUNTER');
		}
		
		$maxpower = getBinaryPower( $var->Max );
		
		$text = '';
		
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $var->atLeast($k) )->then(
				$this->add($k),
				$var->subtract($k),
			'');
		}
		
		return $text;
	}
	
	
	//SUBTRACT !
	/*
		� Subtracts calling deathcounter by argument deathcounter or constant (integer).
		� Format:
			- $dc->subtract($var) is analogous to $dc -= $var
			- $var must be a deathcounter or a constant (integer)
		� Max values:
			- argument: 4294967295
		� Specifics:
			- low end trigger number: 16
			- high end trigger number: 42
			- max trigger number: 66
			- 1 temporary switch
			- 1 temporary deathcounter
	*/
	public function subtract($var2)	{
		//ERROR
		if ( func_num_args() != 1 ) {
			Error('COMPILER ERROR FOR SUBTRACT(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 1: DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if ( is_numeric($var2) ) {
			return Subtract($this, $var2);
		}
		if (!($var2 instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR SUBTRACT(): ARGUMENT NEEDS TO EITHER BE A CONSTANT (INTEGER) OR A DEATHCOUNTER');
		}
		
		$tempdc = new TempDC();
		
		$maxpower = getBinaryPower( min( $this->Max, $var2->Max) );

		$text = $tempdc->setTo(0);
		
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $var2->atLeast($k) )->then(
				$this->subtract($k),
				$var2->subtract($k),
				$tempdc->add($k),
			'');
		}
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $tempdc->atLeast($k) )->then(
				$var2->add($k),
				$tempdc->subtract($k),
			'');
		}
		
		$text .= 	$tempdc->kill();		
		
		return $text;
	}
	// SUB !
	public function sub($n)	{ return $this->subtract($n); }
	
	
	//SUBTRACT DEL!
	/*
		� Subtracts calling deathcounter by argument deathcounter or constant (integer) and clears the argument deathcounter.
		� Format:
			- $dc->subtract($var) is analogous to $dc -= $var and $var = 0
			- $var must be a deathcounter or a constant (integer)
		� Max values:
			- argument: 4294967295
		� Specifics:
			- low end trigger number: 16
			- high end trigger number: 42
			- max trigger number: 66
			- 1 temporary switch
			- 1 temporary deathcounter
	*/
	public function subtractDel($var)	{
		//ERROR
		if ( func_num_args() != 1 ) {
			Error('COMPILER ERROR FOR SUBTRACTDEL(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 1: DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if ( is_numeric($var) ) {
			return Subtract($this, $var);
		}
		if (!($var instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR SUBTRACTDEL(): ARGUMENT NEEDS TO EITHER BE A CONSTANT (INTEGER) OR A DEATHCOUNTER');
		}
		
		$maxpower = getBinaryPower( min( $this->Max, $var->Max) );
		
		$text = '';
		
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $var->atLeast($k) )->then(
				$this->subtract($k),
				$var->subtract($k),
			'');
		}
		
		$text .= $var->setTo(0);
		
		return $text;
	}


	/**
	 * TODO: Write this comment block
	 * 
	 * @param Deathcounter $var2
	 * @return string
	 */
	public function subDivBecome(Deathcounter $var2, $by = 100)	{
		$maxpower = getBinaryPower( $var2->Max / $by );
		$text = '';
		
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $var2->atLeast($k*$by) )->then(
				$var2->subtract($k*$by),
				$this->subtract($k),
			'');
		}
		$text .= _if( $var2->atLeast($by/2) )->then(
			$this->subtract(1),
		'');
				
		return $text;
	}
	
	
	//ABSOLUTE DIFFERENCE !
	/*
		� Returns the absolute difference of two numbers.
		� Format:
			- $dc->absDifference($var1, $var2) is analogous to $dc = abs( $var2 - $var1 ), $signSwitch is set if negative
			- $var1 must be a deathcounter
			- $var2 must be a deathcounter
			- $signSwitch, if added, must be a switch
		� Max values:
			- argument 1: 2147483647
			- argument 2: 2147483647
		� Specifics:
			- low end trigger number: 27
			- high end trigger number: 66
			- max trigger number: 99
			- 1 temporary switch
			- 1 temporary deathcounter
	*/
	public function absDifference($var1, $var2, $signSwitch=NULL){
		//ERROR
		if ( func_num_args() != 2 && func_num_args() != 3 ) {
			Error('COMPILER ERROR FOR ABSDIFFERENCE(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 2: DEATHCOUNTER, DEATHCOUNTER)');
		}
		if (!($var1 instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR ABSDIFFERENCE(): ARGUMENT 1 NEEDS TO BE A DEATHCOUNTER');
		}
		if (!($var2 instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR ABSDIFFERENCE(): ARGUMENT 2 NEEDS TO BE A DEATHCOUNTER');
		}
		if (func_num_args() == 3 && !($signSwitch instanceof PermSwitch)) {
			Error('COMPILER ERROR FOR ABSDIFFERENCE(): ARGUMENT 3 NEEDS TO BE A SWITCH');
		}

		$maxpower1 = getBinaryPower( min( $var1->Max, $var2->Max) );
		$maxpower2 = getBinaryPower( max( $var1->Max, $var2->Max) );
		
		$tempdc = new TempDC( max( $var1->Max, $var2->Max) );
		$switch = new TempSwitch();
		
		$text = $this->setTo(0);
		
		if($signSwitch!=NULL){
			$text .= $signSwitch->clear();
		}

		for($i=$maxpower1; $i>=0; $i--) {
			$k=pow(2, $i);
			$text .= _if( $var1->atLeast($k), $var2->atLeast($k) )->then(
				$var1->subtract($k),
				$var2->subtract($k),
				$tempdc->add($k),
			'');
		}
		if($signSwitch!=NULL){
			$text .= _if( $var2->atLeast(1) )->then(
				$var1->add(2147483648),
			'');
			$text .= _if( $var2->atMost(0) )->then(
				$var2->add(2147483648),
				$signSwitch->set(),
				$switch->set(),
			'');
		} else {
			$text .= _if( $var2->atLeast(1) )->then(
				$var1->add(2147483648),
			'');
			$text .= _if( $var2->atMost(0) )->then(
				$var2->add(2147483648),
				$switch->set(),
			'');
		}
		for($i=$maxpower2; $i>=0; $i--) {
			$k=pow(2, $i);
			$text .= _if( $var1->atLeast($k), $var2->atLeast($k) )->then(
				$var1->subtract($k),
				$var2->subtract($k),
				$this->add($k),
				$tempdc->add($k),
			'');
		}
		for($i=$maxpower2; $i>=0; $i--) {
			$k=pow(2, $i);
			$text .= _if( $tempdc->atLeast($k) )->then(
				$var1->add($k),
				$var2->add($k),
				$tempdc->subtract($k),
			'');
		}
		$text .= _if( $switch->is_clear() )->then(
			$var1->subtract(2147483648),
		'');
		$text .= _if( $switch->is_set() )->then(
			$var2->subtract(2147483648),
			$switch->kill(),
		'');
		
		$text .= $tempdc->kill();
		
		return $text;
	}
	
	//SIGNED SUBTRACTION !
	/*
		� Returns the absolute difference of two numbers.
		� Format:
			- $dc->absSubtraction($dc, $var2) is analogous to $dc = abs( $var2 - $dc ), $signSwitch is set if negative
			- $var2 must be a deathcounter
			- $signSwitch must be a switch
		� Max values:
			- calling deathcounter: 2147483647
			- argument 1: 2147483647
		� Specifics:
			- low end trigger number: 25
			- high end trigger number: 62
			- max trigger number: 97
			- 0 temporary switches
			- 1 temporary deathcounter
	*/
	public function signedSubtraction($var2, $signSwitch){
		//ERROR
		if ( func_num_args() != 2) {
			Error('COMPILER ERROR FOR ABSDIFFERENCE(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 2: DEATHCOUNTER, DEATHCOUNTER)');
		}
		if (!($var2 instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR ABSDIFFERENCE(): ARGUMENT 1 NEEDS TO BE A DEATHCOUNTER');
		}
		if (!($signSwitch instanceof PermSwitch)) {
			Error('COMPILER ERROR FOR ABSDIFFERENCE(): ARGUMENT 2 NEEDS TO BE A SWITCH');
		}

		$maxpower1 = getBinaryPower( min( $this->Max, $var2->Max) );
		$maxpower2 = getBinaryPower( max( $this->Max, $var2->Max) );
		
		$tempdc = new TempDC( max( $this->Max, $var2->Max) );
		
		$text = '';
		if($signSwitch!=NULL){
			$text .= $signSwitch->clear();
		}
		
		for($i=$maxpower1; $i>=0; $i--) {
			$k=pow(2, $i);
			$text .= _if( $this->atLeast($k), $var2->atLeast($k) )->then(
				$this->subtract($k),
				$var2->subtract($k),
				$tempdc->add($k),
			'');
		}
		$text .= _if( $var2->atLeast(1) )->then(
			$signSwitch->set(),
		'');
		for($i=$maxpower2; $i>=0; $i--) {
			$k=pow(2, $i);
			$text .= _if( $var2->atLeast($k) )->then(
				$var2->subtract($k),
				$this->add($k),
				$tempdc->add($k),
			'');
		}
		$text .= $var2->becomeDel($tempdc);
		
		return $text;
	}
	
	
	//SET TO !
	/*
		� Sets calling deathcounter to argument deathcounter or constant (integer).
		� Format:
			- $dc->setTo($var) is analogous to $dc = $var
			- $var must be a deathcounter or a constant (integer)
		� Max values:
			- argument: 4294967295
		� Specifics:
			- low end trigger number: 16
			- high end trigger number: 42
			- max trigger number: 66
			- 1 temporary switch
			- 1 temporary deathcounter
	*/
	public function setTo($var2) 	{
		//ERROR
		if ( func_num_args() != 1 ) {
			Error('COMPILER ERROR FOR SETTO(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 1: DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if ( is_numeric($var2) ) {
			return Set($this, $var2);
		}
		if (!($var2 instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR SETTO(): ARGUMENT NEEDS TO EITHER BE A CONSTANT (INTEGER) OR A DEATHCOUNTER');
		}
		
		$tempdc = new TempDC();
		
		$maxpower = getBinaryPower( $var2->Max );
		
		$text = 	$this->setTo(0).
					$tempdc->setTo(0);
		
        for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $var2->atLeast($k) )->then(
				$this->add($k),
				$var2->subtract($k),
				$tempdc->add($k),
			'');
		}
        for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $tempdc->atLeast($k) )->then(
				$var2->add($k),
				$tempdc->subtract($k),
			'');
		}
		
		$text .= 	$tempdc->kill();		
		
		return $text;
		
	}
	
	
	//BECOME !
	/*
		� Sets calling deathcounter to argument deathcounter without restoring it.
		� Format:
			- $dc->become($var) is analogous to $dc = $var and $var = 0
			- $var must be a deathcounter
		� Max values:
			- argument: 4294967295
		� Specifics:
			- low end trigger number: 9
			- high end trigger number: 22
			- max trigger number: 34
			- 1 temporary switch
			- 0 temporary deathcounters
	*/
	public function become($var2) {
		//ERROR
		if ( func_num_args() != 1 ) {
			Error('COMPILER ERROR FOR BECOME(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 1: DEATHCOUNTER)');
		}
		if (!($var2 instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR BECOME(): ARGUMENT NEEDS TO BE A DEATHCOUNTER');
		}
		
		$maxpower = getBinaryPower( $var2->Max );
		
		$text = 	$this->setTo(0);
		
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $var2->atLeast($k) )->then(
				$this->add($k),
				$var2->subtract($k),
			'');
		}	
		
		return $text;
	}
	//BECOME MULTIPLY!
	/*
		� Sets calling deathcounter to argument 1 (DC) times argument 2 (constant (integer)) without restoring argument 1.
		� Format:
			- $dc->becomeMultiply($var1, $var2) is analogous to $dc = $var1 * $var2 and var1 = 0
			- $var1 must be a deathcounter
			- $var2 must be a constant (integer)
		� Max values:
			- argument 1: 4294967295
			- argument 2: 4294967295
			* NOTE: if the product is greater than 4294967295, it will loop back to 0; results would be unpredictable
		� Specifics:
			- low end trigger number: 9
			- high end trigger number: 22
			- max trigger number: 34
			- 1 temporary switch
			- 0 temporary deathcounters
	*/
	public function becomeMultiply($var2, $var3) {
		//ERROR
		if ( func_num_args() != 2 ) {
			Error('COMPILER ERROR FOR BECOMEMULTIPLY(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 2: DEATHCOUNTER, CONSTANT (INTEGER))');
		}
		if (!($var2 instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR BECOMEMULTIPLY(): ARGUMENT 1 NEEDS TO BE A DEATHCOUNTER');
		}
		if ( !is_numeric($var3) ) {
			Error('COMPILER ERROR FOR BECOMEMULTIPLY(): ARGUMENT 2 NEEDS TO BE A CONSTANT (INTEGER)');
		}
		
		$maxpower = getBinaryPower( $var2->Max );
		
		$text = 	$this->setTo(0);
		
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $var2->atLeast($k) )->then(
				$this->add($k * $var3),
				$var2->subtract($k),
			'');
		}	
		
		return $text;
	}
	//BECOME DIVIDE!
	/*
		� Sets calling deathcounter to argument 1 (DC) divided by argument 2 (constant (integer)) without restoring argument 1.
		� Format:
			- $dc->becomeDivide($var1, $var2) is analogous to $dc = floor( $var1 / $var2 ) and var1 = 0
			- $var1 must be a deathcounter
			- $var2 must be a constant (integer)
		� Max values:
			- argument 1: 4294967295
			- argument 2: 4294967295
		� Specifics:
			- low end trigger number: 9
			- high end trigger number: 22
			- max trigger number: 34
			- 1 temporary switch
			- 0 temporary deathcounters
	*/
	public function becomeDivide($var2, $var3) {
		//ERROR
		if ( func_num_args() != 2 ) {
			Error('COMPILER ERROR FOR BECOMEDIVIDE(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 2: DEATHCOUNTER, CONSTANT (INTEGER))');
		}
		if (!($var2 instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR BECOMEDIVIDE(): ARGUMENT 1 NEEDS TO BE A DEATHCOUNTER');
		}
		if ( !is_numeric($var3) ) {
			Error('COMPILER ERROR FOR BECOMEDIVIDE(): ARGUMENT 2 NEEDS TO BE A CONSTANT (INTEGER)');
		}
		
		$maxpower = getBinaryPower( floor($var2->Max / $var3) );
		
		$text = 	$this->setTo(0);
		
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $var2->atLeast($k) * $var3 )->then(
				$this->add($k),
				$var2->subtract($k * $var3),
			'');
		}
		$text .= $var2->setTo(0);
		
		return $text;
	}
	//BECOME ROUNDED DIVIDE!
	/*
		� Sets calling deathcounter to argument 1 (DC) divided by argument 2 (constant (integer)) rounded without restoring argument 1.
		� Format:
			- $dc->becomeRoundedDivide($var1, $var2) is analogous to $dc = round( $var1 / $var2 ) and var1 = 0
			- $var1 must be a deathcounter
			- $var2 must be a constant (integer)
		� Max values:
			- argument 1: 4294967295
			- argument 2: 4294967295
		� Specifics:
			- low end trigger number: 9
			- high end trigger number: 22
			- max trigger number: 34
			- 1 temporary switch
			- 0 temporary deathcounters
	*/
	public function becomeRoundedDivide($var2, $var3) {
		//ERROR
		if ( func_num_args() != 2 ) {
			Error('COMPILER ERROR FOR BECOMEROUNDEDDIVIDE(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 2: DEATHCOUNTER, CONSTANT (INTEGER))');
		}
		if (!($var2 instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR BECOMEROUNDEDDIVIDE(): ARGUMENT 1 NEEDS TO BE A DEATHCOUNTER');
		}
		if ( !is_numeric($var3) ) {
			Error('COMPILER ERROR FOR BECOMEROUNDEDDIVIDE(): ARGUMENT 2 NEEDS TO BE A CONSTANT (INTEGER)');
		}
		
		$maxpower = getBinaryPower( floor($var2->Max / $var3) );
		
		$text = 	$this->setTo(0);
		
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $var2->atLeast($k) * $var3 )->then(
				$this->add($k),
				$var2->subtract($k * $var3),
			'');
		}
        $text .= _if( $var2->atLeast( ceil( $var3 / 2 ) ) )->then(
			$this->add(1),
		'');
		$text .= $var2->setTo(0);
		
		return $text;
	}
	//NOTE: THE DELETE EQUIVALENTS ARE THE SAME AS THEIR ABOVE COUNTERPARTS. ONLY DIFFERENCE IS THEY DESTROY THE DEATHCOUNTER ARGUMENT.

	// BECOME DELETE !
	public function becomeDel($var2) {
		//ERROR
		if ( func_num_args() != 1 ) {
			Error('COMPILER ERROR FOR BECOMEDEL(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 1: DEATHCOUNTER)');
		}
		if (!($var2 instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR BECOMEDEL(): ARGUMENT NEEDS TO BE A DEATHCOUNTER');
		}
		
		$maxpower = getBinaryPower( $var2->Max );
		
		$text = 	$this->setTo(0);
		
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $var2->atLeast($k) )->then(
				$this->add($k),
				$var2->subtract($k),
			'');
		}
		
		$text .= 	$var2->kill();
		
		return $text;
	}
	//BECOME MULTIPLY DELETE!
	public function becomeMultiplyDel($var2, $var3) {
		//ERROR
		if ( func_num_args() != 2 ) {
			Error('COMPILER ERROR FOR BECOMEMULTIPLYDEL(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 2: DEATHCOUNTER, CONSTANT (INTEGER))');
		}
		if (!($var2 instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR BECOMEMULTIPLYDEL(): ARGUMENT 1 NEEDS TO BE A DEATHCOUNTER');
		}
		if ( !is_numeric($var3) ) {
			Error('COMPILER ERROR FOR BECOMEMULTIPLYDEL(): ARGUMENT 2 NEEDS TO BE A CONSTANT (INTEGER)');
		}
		
		$maxpower = getBinaryPower( $var2->Max );
		
		$text = 	$this->setTo(0);
		
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $var2->atLeast($k) )->then(
				$this->add($k * $var3),
				$var2->subtract($k),
			'');
		}	
		
		$text .= 	$var2->kill();
		
		return $text;
	}
	//BECOME DIVIDE DELETE!
	public function becomeDivideDel($var2, $var3) {
		//ERROR
		if ( func_num_args() != 2 ) {
			Error('COMPILER ERROR FOR BECOMEDIVIDEDEL(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 2: DEATHCOUNTER, CONSTANT (INTEGER))');
		}
		if (!($var2 instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR BECOMEDIVIDEDEL(): ARGUMENT 1 NEEDS TO BE A DEATHCOUNTER');
		}
		if ( !is_numeric($var3) ) {
			Error('COMPILER ERROR FOR BECOMEDIVIDEDEL(): ARGUMENT 2 NEEDS TO BE A CONSTANT (INTEGER)');
		}
		
		$maxpower = getBinaryPower( floor($var2->Max / $var3) );
		
		$text = 	$this->setTo(0);
		
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $var2->atLeast($k) * $var3 )->then(
				$this->add($k),
				$var2->subtract($k * $var3),
			'');
		}
		$text .= $var2->setTo(0);
		
		$text .= 	$var2->kill();
		
		return $text;
	}
	//BECOME ROUNDED DIVIDE DELETE!
	public function becomeRoundedDivideDel($var2, $var3) {
		//ERROR
		if ( func_num_args() != 2 ) {
			Error('COMPILER ERROR FOR BECOMEROUNDEDDIVIDEDEL(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 2: DEATHCOUNTER, CONSTANT (INTEGER))');
		}
		if (!($var2 instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR BECOMEROUNDEDDIVIDEDEL(): ARGUMENT 1 NEEDS TO BE A DEATHCOUNTER');
		}
		if ( !is_numeric($var3) ) {
			Error('COMPILER ERROR FOR BECOMEROUNDEDDIVIDEDEL(): ARGUMENT 2 NEEDS TO BE A CONSTANT (INTEGER)');
		}
		
		$maxpower = getBinaryPower( floor($var2->Max / $var3) );
		
		$text = 	$this->setTo(0);
		
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $var2->atLeast($k) * $var3 )->then(
				$this->add($k),
				$var2->subtract($k * $var3),
			'');
		}
        $text .= _if( $var2->atLeast( ceil( $var3 / 2 ) ) )->then(
			$this->add(1),
		'');
		$text .= $var2->setTo(0);
		
		$text .= 	$var2->kill();
		
		return $text;
	}
	
	
	// MULTIPLY BY!
	/*
		� Multiplies calling deathcounter by argument constant (integer) or deathcounter.
		� Format:
			- $dc->multiplyBy($var) is analogous to $dc *= $var
			- $var must be a deathcounter or a constant (integer)
		� Max values:
			- calling deathcounter: 2147483647
			- argument: 2147483647
			* NOTE: if the product is greater than 4294967295, it will loop back to 0; results would be unpredictable
		� Low-end specifics:
			- trigger number: 94
			- temp switch number: 9
			- temp deathcounters: 1
		� High-end specifics:
			- trigger number: 523
			- temp switch number: 22
			- temp deathcounters: 1
		� Maxed specifics:
			- trigger number: 1150
			- temp switch number: 33
			- temp deathcounters: 1
	*/
	public function multiplyBy($var2)	{
		//ERROR
		if ( func_num_args() != 1 ) {
			Error('COMPILER ERROR FOR MULTIPLYBY(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 1: DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if (!($var2 instanceof Deathcounter || is_numeric($var2))) {
			Error('COMPILER ERROR FOR MULTIPLYBY(): ARGUMENT NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}

        $text = '';

		//INTEGER MULTIPLICATION
		if ( is_numeric($var2) ) {
		
			if ( $var2 == 0 ) {
				return $this->setTo(0);
			}
			
			if ( $var2 == 1 ) {
				return $text;
			}
			
			$tempdc = new TempDC();
			$maxpower = getBinaryPower( $this->Max );
			
			for($i=$maxpower; $i >= 0; $i--) {
				$k = pow(2, $i);
				$text .= _if( $this->atLeast($k) )->then(
					$this->subtract($k),
					$tempdc->add($k),
				'');
			}
			for($i=$maxpower; $i >= 0; $i--) {
				$k = pow(2, $i);
                if($k * $var2 < 2147483648){
				    $text .= _if( $tempdc->atLeast($k) )->then(
				    	$this->add($k * $var2),
				    	$tempdc->subtract($k),
				    '');
                }
                else{
                    $text .= _if( $tempdc->atLeast($k) )->then(
                        $this->setTo(2147483648),
                        $tempdc->subtract($k),
                    '');
                }
			}
			
			$text .= 	$tempdc->kill();		
		
			return $text;

		}
		
		
		//DEATHCOUNTER MULTIPLICATION
		$result = new TempDC();
		$result->Max = $this->Max * $var2->Max;
		
		$maxpower1 = getBinaryPower( $this->Max );
		$maxpower2 = getBinaryPower( $var2->Max );

        $enableInner = new TempSwitch();
		
		//get dynamic switches
        $kSwitches = array();
		for($i=$maxpower2; $i >= 0; $i--) {
			$kSwitches[$i] = new TempSwitch();
		}
		
		
		// Outer
		for($i=$maxpower2; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $var2->atLeast($k) )->then(
				$var2->subtract($k),
				$kSwitches[$i]->set(),
			'');
		}
		for($i1=$maxpower1; $i1 >= 0; $i1--) {
			$k1 = pow(2, $i1);

            $innertrigs = "";
            for($i2=$maxpower2; $i2 >= 0; $i2--) {
                $k2 = pow(2, $i2);
                if($k1 * $k2 < 2147483648){
                    $innertrigs .= _if( $enableInner->is_set(), $kSwitches[$i2]->is_set() )->then(
                        $result->add($k1 * $k2),
                    "");
                }
                else{
                    $innertrigs .= _if( $enableInner->is_set(), $kSwitches[$i2]->is_set() )->then(
                        $result->setTo(2147483648),
                    "");
                }
            }
            $text .= _if( $this->atLeast($k1) )->then(
                $enableInner->set(),
            '');
	        $text .= $innertrigs;
            $text .= _if( $enableInner->is_set() )->then(
                $enableInner->clear(),
				$this->subtract($k1),
			'');
		}
		for($i=$maxpower2; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $kSwitches[$i]->is_set() )->then(
				$var2->add($k),
			'');
		}
		$text .= $this->become($result);
		
		$kSwitchClear = '';
		foreach($kSwitches as $keySwitch){
				$kSwitchClear .= $keySwitch->kill();
		}
		
		$text .= 	$kSwitchClear.
					$enableInner->kill().
					$result->kill();		
		
		return $text;
		
	}
	
	// PRODUCT OF!
	/*
		� Multiplies argument 1 by argument 2 and returns result to calling deathcounter.
		� Format:
			- $dc->productOf($var1, $var2) is analogous to $dc = $var1 * $var2
			- $var1 must be a deathcounter or a constant (integer)
			- $var2 must be a deathcounter or a constant (integer)
		� Max values:
			- argument 1: 2147483647
			- argument 2: 2147483647
			* NOTE: if the product is greater than 4294967295, it will loop back to 0; results would be unpredictable
		� Low-end specifics:
			- trigger number: 87
			- temp switch number: 9
			- temp deathcounters: 1
		� High-end specifics:
			- trigger number: 503
			- temp switch number: 22
			- temp deathcounters: 1
		� Maxed specifics:
			- trigger number: 1119
			- temp switch number: 33
			- temp deathcounters: 1
	*/
	public function productOf($var1, $var2)	{
		//ERROR
		if ( func_num_args() != 2 ) {
			Error('COMPILER ERROR FOR PRODUCTOF(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 2: DEATHCOUNTER OR CONSTANT (INTEGER), DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if (!($var1 instanceof Deathcounter || is_numeric($var1))) {
			Error('COMPILER ERROR FOR PRODUCTOF(): ARGUMENT 1 NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}
		if (!($var2 instanceof Deathcounter || is_numeric($var2))) {
			Error('COMPILER ERROR FOR PRODUCTOF(): ARGUMENT 2 NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}
		
		//INTEGER-INTEGER MULTIPLICATION
		if ( is_numeric($var1) && is_numeric($var2) ) {
			return $this->setTo($var1 * $var2);
		}
		
		
		//DEATHCOUNTER-INTEGER MULTIPLICATION
		if ( is_numeric($var2) ) {
		
			if ( $var2 == 0 ) {
				return $this->setTo(0);
			}
			
			if ( $var2 == 1 ) {
				return $this->setTo($var1);
			}
			
			$tempdc = new TempDC();
			$maxpower = getBinaryPower( $var1->Max );
			
			$text = 	$this->setTo(0);
		
			for($i=$maxpower; $i >= 0; $i--) {
				$k = pow(2, $i);
				$text .= _if( $var1->atLeast($k) )->then(
					$var1->subtract($k),
					$tempdc->add($k),
				'');
			}
			for($i=$maxpower; $i >= 0; $i--) {
				$k = pow(2, $i);
                if($k * $var2 < 2147483648){
                    $text .= _if( $tempdc->atLeast($k) )->then(
                        $var1->add($k),
                        $this->add($k * $var2),
                        $tempdc->subtract($k),
                    '');
                }
                else{
                    $text .= _if( $tempdc->atLeast($k) )->then(
                        $var1->add($k),
                        $this->setTo(2147483648),
                        $tempdc->subtract($k),
                    '');
                }
			}
			
			$text .= 	$tempdc->kill();		
		
			return $text;

		}
		
		
		//INTEGER-DEATHCOUNTER MULTIPLICATION
		if ( is_numeric($var1) ) {
			return $this->productOf($var2, $var1);
		}

		
		//DEATHCOUNTER-DEATHCOUNTER MULTIPLICATION
		$tempdc1 = new TempDC( $var1->Max );
		
		$maxpower1 = getBinaryPower( $var1->Max );
		$maxpower2 = getBinaryPower( $var2->Max );

        $enableInner = new TempSwitch();
		
		//get dynamic switches
        $kSwitches = array();
		for($i=$maxpower2; $i >= 0; $i--) {
			$kSwitches[$i] = new TempSwitch();
		}
		
		$text = 	$this->setTo(0);
		// Outer
		for($i=$maxpower2; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $var2->atLeast($k) )->then(
				$var2->subtract($k),
				$kSwitches[$i]->set(),
			'');
		}
		for($i1=$maxpower1; $i1 >= 0; $i1--) {
			$k1 = pow(2, $i1);

            $innertrigs = '';
            for($i2=$maxpower2; $i2 >= 0; $i2--) {
                $k2 = pow(2, $i2);
                if($k1 * $k2 < 2147483648){
                    $innertrigs .= _if( $enableInner->is_set(), $kSwitches[$i2]->is_set() )->then(
                        $this->add($k1 * $k2),
                    "");
                }
                else{
                    $innertrigs .= _if( $enableInner->is_set(), $kSwitches[$i2]->is_set() )->then(
                        $this->setTo(2147483648),
                    "");
                }
            }
			$text .= _if( $var1->atLeast($k1) )->then(
                $enableInner->set(),
            '');
		    $text .= $innertrigs;
            $text .= _if( $enableInner->is_set() )->then(
                $enableInner->clear(),
				$var1->subtract($k1),
				$tempdc1->add($k1),
			'');
		}
		for($i=$maxpower2; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $kSwitches[$i]->is_set() )->then(
				$var2->add($k),
			'');
		}
		$text .= $var1->becomeDel($tempdc1);
		
		$kSwitchClear = '';
		foreach($kSwitches as $keySwitch){
				$kSwitchClear .= $keySwitch->kill();
		}
		
		$text .= $kSwitchClear;
		$text .= $enableInner->kill();
		
		return $text;
		
	}


	// SQUARED !
	/*
		� Returns the value squared. If no argument is provided, it will square the calling deathcounter.
		� Format:
			- $dc->sqaured($var2) is analogous to $dc = $var2 * $var2
			- $var2 must be a deathcounter or a constant (integer)
			- $dc->sqaured() is analogous to $dc = $dc * $dc
		� Max values:
			- argument: 2147483647
			* NOTE: if the product is greater than 4294967295, it will loop back to 0; results would be unpredictable
		� Low-end specifics:
			- trigger number: 44
			- temp switch number: 7
			- temp deathcounters: 0
		� High-end specifics:
			- trigger number: 252
			- temp switch number: 20
			- temp deathcounters: 0
		� Maxed specifics:
			- trigger number: 560
			- temp switch number: 31
			- temp deathcounters: 0
	*/
	public function squared($var2=NULL)	{
		//ERRORS
		if ( func_num_args() > 1 ) {
			Error('COMPILER ERROR FOR SQUARED(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 0 OR 1: DEATHCOUNTER OR CONSTANT (INTEGER)');
		}
		if (func_num_args() == 1 && !($var2 instanceof Deathcounter || is_numeric($var2))) {
			Error('COMPILER ERROR FOR SQUARED(): ARGUMENT NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}

		//CONSTANT SQUARED
		if ( is_numeric($var2) ) {
			return $this->setTo($var2*$var2);
		}


		//DEATHCOUNTER SQUARED
		if( $var2 == NULL ){
			$var2 = $this;
		}
		$maxpower = getBinaryPower( $var2->Max );

		//get dynamic switches
		$kSwitches = array();
		for($i=$maxpower; $i >= 0; $i--) {
			$kSwitches[$i] = new TempSwitch();
		}
		
		if( $this != $var2 ){
			$text = $this->setTo(0);
		}
		
		for($i=$maxpower; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $var2->atLeast($k) )->then(
				$var2->subtract($k),
				$kSwitches[$i]->set(),
			'');
		}
		for($i1=$maxpower; $i1 >= 0; $i1--) {
			$k1 = pow(2,$i1);
			for($i2=$i1; $i2 >= 0; $i2--) {
				$k2 = pow(2,$i2);
				if($i1==$i2){
					if($k2*$k2 < 2147483648){
						$text .= _if( $kSwitches[$i2]->is_set() )->then(
							$this->add($k2*$k2),
						'');
					}
					else{
						$text .= _if( $kSwitches[$i2]->is_set() )->then(
							$this->setTo(2147483648),
						'');
					}
				}
				else{
					if($k1*$k2 < 2147483648){
						$text .= _if( $kSwitches[$i1]->is_set(), $kSwitches[$i2]->is_set() )->then(
							$this->add($k1*$k2*2),
						'');
					}
					else{
						$text .= _if( $kSwitches[$i1]->is_set(), $kSwitches[$i2]->is_set() )->then(
							$this->setTo(2147483648),
						'');
					}
				}
			}
		}
		
		if($this != $var2){
			for($i=$maxpower; $i >= 0; $i--) {
				$k = pow(2, $i);
				$text .= _if( $kSwitches[$i]->is_set() )->then(
					$var2->add($k),
				'');
			}
		}
		

		$kSwitchClear = '';
		foreach($kSwitches as $keySwitch){
			$kSwitchClear .= $keySwitch->kill();
		}

		$text .= 	$kSwitchClear;

		return $text;

	}
	
	
	// DIVIDE BY!
	/*
		� Divides calling deathcounter by the argument. Result is truncated.
		� NOTE: if the divisor is 0, the function will return a 0
		� Format:
			- $dc->divideBy($var) is analogous to $dc = floor( $dc / $var )
			- $var must be a deathcounter or a constant (integer)
		� Max values:
			- calling deathcounter: 2147483647
			- argument: 2147483647
		� Low-end specifics:
			- trigger number: 120
			- temp switch number: 10
			- temp deathcounters: 1
		� High-end specifics:
			- trigger number: 601
			- temp switch number: 23
			- temp deathcounters: 1
		� Maxed specifics:
			- trigger number: 1272
			- temp switch number: 34
			- temp deathcounters: 1
	*/
	public function divideBy($divisor)	{
		//ERROR
		if ( func_num_args() != 1 ) {
			Error('COMPILER ERROR FOR DIVIDEBY(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 1: DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if (!($divisor instanceof Deathcounter || is_numeric($divisor))) {
			Error('COMPILER ERROR FOR DIVIDEBY(): ARGUMENT NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}
		
		//INTEGER DIVISION
		if ( is_numeric($divisor) ) {
			
			if( $divisor == 0 ) {
				Error('COMPILER ERROR FOR DIVIDEBY(): DIVIDE BY 0');
			}
			
			if( $divisor == 1 ) {
				return '';
			}
	
			$tempdc = new TempDC();
			$maxpower = getBinaryPower( floor($this->Max / $divisor) );
			$text = '';
			
			for($i=$maxpower; $i >= 0; $i--) {
				$k = pow(2, $i);
				$text .= _if( $this->atLeast($k * $divisor) )->then(
					$this->subtract($k * $divisor),
					$tempdc->add($k),
				'');
			}
			$text .= $this->setTo(0);
			for($i=$maxpower; $i >= 0; $i--) {
				$k = pow(2, $i);
				$text .= _if( $tempdc->atLeast($k) )->then(
					$this->add($k),
					$tempdc->subtract($k),
				'');
			}
			
			$text .= 	$tempdc->kill();
			
		
			return $text;
		}
	
		//DEATHCOUNTER DIVISION
		if ( $this->Max > 2147483647) {
			Error('COMPILER ERROR FOR DIVIDEBY(): CALLING DEATHCOUNTER\'S MAX SIZE IS TOO LARGE (EXCEEDS 2147483647)');
		}
		if ( $divisor->Max > 2147483647) {
			Error('COMPILER ERROR FOR DIVIDEBY(): ARGUMENT\'S MAX SIZE IS TOO LARGE (EXCEEDS 2147483647)');
		}
		
		$result = new TempDC();
		$nestswitch = new TempSwitch();
		$ignore = new TempSwitch();
		
		$maxpower1 = getBinaryPower( $this->Max );
		$maxpower2 = getBinaryPower( $divisor->Max );
		$kSwitches = array();
		
		//align 0 to 2^31 (for temporary negative numbers)
		$text =	$this->add(2147483648);
		
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
			$text .= _if( $divisor->atLeast($k2) )->then(
				$divisor->subtract($k2),
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
				if( $k1 * $k2 <= $this->Max ){
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
						$this->subtract($k1 * $k2),
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
			$text .= _if( $ignore->is_clear() , $this->atLeast(2147483648) )->then(
				$result->add($k1),
				$conditionGroupClear,
			'');
			$text .= _if( $ignore->is_clear() , $this->atMost(2147483647) )->then(
				$nestswitch->set(),
			'');
			$text .= _if( $ignore->is_set() )->then(
				$ignore->clear(),
				$nestswitch->set(),
			'');
			for($i2=$maxpower2; $i2 >= 0; $i2--) {
				$k2 = pow(2, $i2);
				if( $k1 * $k2 <= $this->Max ){
					$text .= _if( $nestswitch->is_set() , $kSwitches[$i2]->is_set() )->then(
						$this->add($k1 * $k2),
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
				$divisor->add($k),
				$kSwitches[$i]->clear(),
			'');
		}
		$text .= $this->setTo(0);
		for($i=$maxpower1; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $result->atLeast($k) )->then(
				$result->subtract($k),
				$this->add($k),
			'');
		}
		
		//if user divides by 0, return 0
		$text .= _if( $divisor->exactly(0) )->then(
			$this->setTo(0),
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
		
		$text .= 	$result->kill().
					$kSwitchClear.
					$nestswitch->kill().
					$ignore->kill();
	
		return $text;
		
	}
	
	// ROUNDED DIVIDE BY!
	/*
		� Divides calling deathcounter by the argument. Result is rounded.
		� NOTE: if the divisor is 0, the function will return a 0
		� Format:
			- $dc->roundedDivideBy($var) is analogous to $dc = round( $dc / $var )
			- $var must be a deathcounter or a constant (integer)
		� Max values:
			- calling deathcounter: 2147483647
			- argument: 2147483647
		� Low-end specifics:
			- trigger number: 129
			- temp switch number: 10
			- temp deathcounters: 1
		� High-end specifics:
			- trigger number: 623
			- temp switch number: 23
			- temp deathcounters: 1
		� Maxed specifics:
			- trigger number: 1305
			- temp switch number: 34
			- temp deathcounters: 1
	*/
	public function roundedDivideBy($divisor)	{
		//ERROR
		if ( func_num_args() != 1 ) {
			Error('COMPILER ERROR FOR ROUNDEDDIVIDEBY(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 1: DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if (!($divisor instanceof Deathcounter || is_numeric($divisor))) {
			Error('COMPILER ERROR FOR ROUNDEDDIVIDEBY(): ARGUMENT NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}
		
		//INTEGER DIVISION
		if ( is_numeric($divisor) ) {
			
			if( $divisor == 0 ) {
				Error('COMPILER ERROR FOR ROUNDEDDIVIDEBY(): DIVIDE BY 0');
			}
			
			if( $divisor == 1 ) {
				return '';
			}
	
			$tempdc = new TempDC();
			$maxpower = getBinaryPower( floor($this->Max / $divisor) );
			$text = '';
			
			for($i=$maxpower; $i >= 0; $i--) {
				$k = pow(2, $i);
				$text .= _if( $this->atLeast($k * $divisor) )->then(
					$this->subtract($k * $divisor),
					$tempdc->add($k),
				'');
			}
			if ( $divisor > 2 ) {
				$text .= _if( $this->atMost( ceil( $divisor / 2) - 1) )->then(
						$this->setTo(0),
					'');
			}
			$text .= _if( $this->atLeast(1) )->then(
					$this->setTo(1),
				'');
			for($i=$maxpower; $i >= 0; $i--) {
				$k = pow(2, $i);
				$text .= _if( $tempdc->atLeast($k) )->then(
					$this->add($k),
					$tempdc->subtract($k),
				'');
			}
			
			$text .= 	$tempdc->kill();
			
		
			return $text;
		}
	
		//DEATHCOUNTER DIVISION
		if ( $this->Max > 2147483647) {
			Error('COMPILER ERROR FOR ROUNDEDDIVIDEBY(): CALLING DEATHCOUNTER\'S MAX SIZE IS TOO LARGE (EXCEEDS 2147483647)');
		}
		if ( $divisor->Max > 2147483647) {
			Error('COMPILER ERROR FOR ROUNDEDDIVIDEBY(): ARGUMENT\'S MAX SIZE IS TOO LARGE (EXCEEDS 2147483647)');
		}
		
		$result = new TempDC();
		$nestswitch = new TempSwitch();
		$ignore = new TempSwitch();
		
		$maxpower1 = getBinaryPower( $this->Max );
		$maxpower2 = getBinaryPower( $divisor->Max );
		$kSwitches = array();
		
		//align 0 to 2^31 (for temporary negative numbers)
		$text =	$this->add(2147483648);
		
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
			$text .= _if( $divisor->atLeast($k2) )->then(
				$divisor->subtract($k2),
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
				if( $k1 * $k2 <= $this->Max ){
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
						$this->subtract($k1 * $k2),
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
			$text .= _if( $ignore->is_clear() , $this->atLeast(2147483648) )->then(
				$result->add($k1),
				$conditionGroupClear,
			'');
			$text .= _if( $ignore->is_clear() , $this->atMost(2147483647) )->then(
				$nestswitch->set(),
			'');
			$text .= _if( $ignore->is_set() )->then(
				$ignore->clear(),
				$nestswitch->set(),
			'');
			for($i2=$maxpower2; $i2 >= 0; $i2--) {
				$k2 = pow(2, $i2);
				if( $k1 * $k2 <= $this->Max ){
					$text .= _if( $nestswitch->is_set() , $kSwitches[$i2]->is_set() )->then(
						$this->add($k1 * $k2),
					'');
				}
			}
			$text .= _if( $nestswitch->is_set() )->then(
				$nestswitch->clear(),
				$conditionGroupClear,
			'');
		}
		// [ROUNDED PORTION]
		$doDeMorgans = 0;
		$kconditions = '';
		$triggertext = '';
		for($i=$maxpower2; $i >= 0; $i--) {
			$k = pow(2, $i);
			if( ceil($k / 2) <= $this->Max ){
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
				
				$text .= _if( $kSwitches[$i]->is_set() )->then(
					$this->subtract( ceil($k / 2) ),
				'');
			}
			else {
				$triggertext .= _if( $kSwitches[$i]->is_set() )->then(
					$ignore->set(),
				'');
				$kconditions .= $kSwitches[$i]->is_clear();
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
		$text .= _if( $ignore->is_clear() , $this->atLeast(2147483648) )->then(
			$result->add(1),
		'');
		$text .= _if( $ignore->is_set() )->then(
			$ignore->clear(),
		'');
		// [/ROUNDED PORTION]
		for($i=$maxpower2; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $kSwitches[$i]->is_set() )->then(
				$divisor->add($k),
				$kSwitches[$i]->clear(),
			'');
		}
		$text .= $this->setTo(0);
		for($i=$maxpower1; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $result->atLeast($k) )->then(
				$result->subtract($k),
				$this->add($k),
			'');
		}
		
		//if user divides by 0, return 0
		$text .= _if( $divisor->exactly(0) )->then(
			$this->setTo(0),
		'');

        $kSwitchClear = '';
		foreach($kSwitches as $keySwitch){
			$kSwitchClear .= $keySwitch->kill();
		}
		
		
		$text .= 	$conditionGroupClear.
					$result->kill().
					$kSwitchClear.
					$nestswitch->kill().
					$ignore->kill();
		
		if($maxpower2 > 15){
			$conditionGroupSwitch->kill();
		}
		if($maxpower2 > 29){
			$conditionGroupSwitch2->kill();
		}
	
		return $text;
		
	}
	
	// QUOTIENT OF!
	/*
		� Divides argument 1 by argument 2 and returns answer to calling deathcounter. Result is truncated.
		� NOTE: if the divisor is 0, the function will return a 0
		� Format:
			- $dc->quotientOf($var1, $var2) is analogous to $dc = floor( $var1 / $var2 )
			- $var1 must be a deathcounter or a constant (integer)
			- $var2 must be a deathcounter or a constant (integer)
		� Max values:
			- argument 1: 2147483647
			- argument 2: 2147483647
		� Low-end specifics:
			- trigger number: 127
			- temp switch number: 10
			- temp deathcounters: 1
		� High-end specifics:
			- trigger number: 621
			- temp switch number: 23
			- temp deathcounters: 1
		� Maxed specifics:
			- trigger number: 1303
			- temp switch number: 34
			- temp deathcounters: 1
	*/
	public function quotientOf($numerator, $denominator)	{
		//ERROR
		if ( func_num_args() != 2 ) {
			Error('COMPILER ERROR FOR QUOTIENTOF(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 2: DEATHCOUNTER OR CONSTANT (INTEGER), DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if (!($numerator instanceof Deathcounter || is_numeric($numerator))) {
			Error('COMPILER ERROR FOR QUOTIENTOF(): ARGUMENT 1 NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}
		if (!($denominator instanceof Deathcounter || is_numeric($denominator))) {
			Error('COMPILER ERROR FOR QUOTIENTOF(): ARGUMENT 2 NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}
		
		//INTEGER-INTEGER DIVISION
		if ( is_numeric($numerator) && is_numeric($denominator) ) {
			return $this->setTo( floor( $numerator / $denominator) );
		}
		
		//DEATHCOUNTER-INTEGER DIVISION
		if ( is_numeric($denominator) ) {
			
			if( $denominator == 0 ) {
				Error('COMPILER ERROR FOR QUOTIENTOF(): DIVIDE BY 0');
			}
			
			if( $denominator == 1 ) {
				return $this->setTo($numerator);
			}
	
			$tempdc = new TempDC();
			$maxpower = getBinaryPower( floor($numerator->Max / $denominator) );
			
			$text = $this->setTo(0);
			for($i=$maxpower; $i >= 0; $i--) {
				$k = pow(2, $i);
				$text .= _if( $numerator->atLeast($k * $denominator) )->then(
					$numerator->subtract($k * $denominator),
					$tempdc->add($k),
					$this->add($k),
				'');
			}
			for($i=$maxpower; $i >= 0; $i--) {
				$k = pow(2, $i);
				$text .= _if( $tempdc->atLeast($k) )->then(
					$numerator->add($k * $denominator),
					$tempdc->subtract($k),
				'');
			}
			
			$text .= 	$tempdc->kill();
			
			return $text;
		}
		
		//INTEGER-DEATHCOUNTER DIVISION
		if ( is_numeric($numerator) ) {
			return $this->setTo($numerator).
						$this->divideBy($denominator);
		}
		
	
		//DEATHCOUNTER-DEATHCOUNTER DIVISION
		if ( $numerator->Max > 2147483647) {
			Error('COMPILER ERROR FOR QUOTIENTOF(): ARGUMENT 1\'S MAX SIZE IS TOO LARGE (EXCEEDS 2147483647)');
		}
		if ( $denominator->Max > 2147483647) {
			Error('COMPILER ERROR FOR QUOTIENTOF(): ARGUMENT 2\'S MAX SIZE IS TOO LARGE (EXCEEDS 2147483647)');
		}
		
		$tempdc = new TempDC( $numerator->Max );
		$nestswitch = new TempSwitch();
		$ignore = new TempSwitch();
		
		$maxpower1 = getBinaryPower( $numerator->Max );
		$maxpower2 = getBinaryPower( $denominator->Max );
		$kSwitches = array();
		
		//align 0 to 2^31 (for temporary negative numbers)
		$text =	    $tempdc->setTo($numerator).
					$this->setTo(0).
					$tempdc->add(2147483648);
		
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
				if( $k1 * $k2 <= $tempdc->Max ){
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
						$tempdc->subtract($k1 * $k2),
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
			$text .= _if( $ignore->is_clear() , $tempdc->atLeast(2147483648) )->then(
				$this->add($k1),
				$conditionGroupClear,
			'');
			$text .= _if( $ignore->is_clear() , $tempdc->atMost(2147483647) )->then(
				$nestswitch->set(),
			'');
			$text .= _if( $ignore->is_set() )->then(
				$ignore->clear(),
				$nestswitch->set(),
			'');
			for($i2=$maxpower2; $i2 >= 0; $i2--) {
				$k2 = pow(2, $i2);
				if( $k1 * $k2 <= $tempdc->Max ){
					$text .= _if( $nestswitch->is_set() , $kSwitches[$i2]->is_set() )->then(
						$tempdc->add($k1 * $k2),
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
		
		//if user divides by 0, return 0
		$text .= _if( $denominator->exactly(0) )->then(
			$this->setTo(0),
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
		
		$text .= 	$tempdc->kill().
					$kSwitchClear.
					$nestswitch->kill().
					$ignore->kill();
	
		return $text;
		
	}
	
	// ROUNDED QUOTIENT OF!
	/*
		� Divides argument 1 by argument 2 and returns answer to calling deathcounter. Result is rounded.
		� NOTE: if the divisor is 0, the function will return a 0
		� Format:
			- $dc->roundedQuotientOf($var1, $var2) is analogous to $dc = round( $var1 / $var2 )
			- $var1 must be a deathcounter or a constant (integer)
			- $var2 must be a deathcounter or a constant (integer)
		� Max values:
			- argument 1: 2147483647
			- argument 2: 2147483647
		� Low-end specifics:
			- trigger number: 136
			- temp switch number: 10
			- temp deathcounters: 1
		� High-end specifics:
			- trigger number: 643
			- temp switch number: 23
			- temp deathcounters: 1
		� Maxed specifics:
			- trigger number: 1336
			- temp switch number: 34
			- temp deathcounters: 1
	*/
	public function roundedQuotientOf($numerator, $denominator)	{
		//ERROR
		if ( func_num_args() != 2 ) {
			Error('COMPILER ERROR FOR ROUNDEDQUOTIENTOF(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 2: DEATHCOUNTER OR CONSTANT (INTEGER), DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if (!($numerator instanceof Deathcounter || is_numeric($numerator))) {
			Error('COMPILER ERROR FOR ROUNDEDQUOTIENTOF(): ARGUMENT 1 NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}
		if (!($denominator instanceof Deathcounter || is_numeric($denominator))) {
			Error('COMPILER ERROR FOR ROUNDEDQUOTIENTOF(): ARGUMENT 2 NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}
		
		//INTEGER-INTEGER DIVISION
		if ( is_numeric($numerator) && is_numeric($denominator) ) {
			return $this->setTo( floor( $numerator / $denominator) );
		}
		
		//DEATHCOUNTER-INTEGER DIVISION
		if ( is_numeric($denominator) ) {
			
			if( $denominator == 0 ) {
				Error('COMPILER ERROR FOR ROUNDEDQUOTIENTOF(): DIVIDE BY 0');
			}

			if( $denominator == 1 ) {
				return $this->setTo($numerator);
			}
	
			$tempdc = new TempDC();
			$maxpower = getBinaryPower( floor($numerator->Max / $denominator) );
			
			$text = $this->setTo(0);

			for($i=$maxpower; $i >= 0; $i--) {
				$k = pow(2, $i);
				$text .= _if( $numerator->atLeast($k * $denominator) )->then(
					$numerator->subtract($k * $denominator),
					$tempdc->add($k),
					$this->add($k),
				'');
			}
			$text .= _if( $numerator->atLeast( ceil( $denominator / 2)) )->then(
					$this->add(1),
				'');
			for($i=$maxpower; $i >= 0; $i--) {
				$k = pow(2, $i);
				$text .= _if( $tempdc->atLeast($k) )->then(
					$numerator->add($k * $denominator),
					$tempdc->subtract($k),
				'');
			}
			
			$text .= 	$tempdc->kill();
			
			return $text;
		}
		
		//INTEGER-DEATHCOUNTER MODULUS
		if ( is_numeric($numerator) ) {
			return $this->setTo($numerator).
						$this->roundedDivideBy($denominator);
		}
		
	
		//DEATHCOUNTER-DEATHCOUNTER DIVISION
		if ( $numerator->Max > 2147483647) {
			Error('COMPILER ERROR FOR ROUNDEDQUOTIENTOF(): ARGUMENT 1\'S MAX SIZE IS TOO LARGE (EXCEEDS 2147483647)');
		}
		if ( $denominator->Max > 2147483647) {
			Error('COMPILER ERROR FOR ROUNDEDQUOTIENTOF(): ARGUMENT 2\'S MAX SIZE IS TOO LARGE (EXCEEDS 2147483647)');
		}
		
		$tempdc = new TempDC( $numerator->Max );
		$nestswitch = new TempSwitch();
		$ignore = new TempSwitch();
		
		$maxpower1 = getBinaryPower( $numerator->Max );
		$maxpower2 = getBinaryPower( $denominator->Max );
		$kSwitches = array();
		
		//align 0 to 2^31 (for temporary negative numbers)
		$text =	$tempdc->setTo($numerator).
				$this->setTo(0).
				$tempdc->add(2147483648);
		
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
				if( $k1 * $k2 <= $tempdc->Max ){
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
						$tempdc->subtract($k1 * $k2),
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
			$text .= _if( $ignore->is_clear() , $tempdc->atLeast(2147483648) )->then(
				$this->add($k1),
				$conditionGroupClear,
			'');
			$text .= _if( $ignore->is_clear() , $tempdc->atMost(2147483647) )->then(
				$nestswitch->set(),
			'');
			$text .= _if( $ignore->is_set() )->then(
				$ignore->clear(),
				$nestswitch->set(),
			'');
			for($i2=$maxpower2; $i2 >= 0; $i2--) {
				$k2 = pow(2, $i2);
				if( $k1 * $k2 <= $tempdc->Max ){
					$text .= _if( $nestswitch->is_set() , $kSwitches[$i2]->is_set() )->then(
						$tempdc->add($k1 * $k2),
					'');
				}
			}
			$text .= _if( $nestswitch->is_set() )->then(
				$nestswitch->clear(),
				$conditionGroupClear,
			'');
		}
		// [ROUNDED PORTION]
		$doDeMorgans = 0;
		$kconditions = '';
		$triggertext = '';
		for($i=$maxpower2; $i >= 0; $i--) {
			$k = pow(2, $i);
			if( ceil($k / 2) <= $tempdc->Max ){
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
				
				$text .= _if( $kSwitches[$i]->is_set() )->then(
					$tempdc->subtract( ceil($k / 2) ),
				'');
			}
			else {
				$triggertext .= _if( $kSwitches[$i]->is_set() )->then(
					$ignore->set(),
				'');
				$kconditions .= $kSwitches[$i]->is_clear();
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
		$text .= _if( $ignore->is_clear() , $tempdc->atLeast(2147483648) )->then(
			$this->add(1),
		'');
		$text .= _if( $ignore->is_set() )->then(
			$ignore->clear(),
		'');
		// [/ROUNDED PORTION]
		for($i=$maxpower2; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $kSwitches[$i]->is_set() )->then(
				$denominator->add($k),
				$kSwitches[$i]->clear(),
			'');
		}
		
		//if user divides by 0, return 0
		$text .= _if( $denominator->exactly(0) )->then(
			$this->setTo(0),
		'');

        $kSwitchClear = '';
		foreach($kSwitches as $keySwitch){
			$kSwitchClear .= $keySwitch->kill();
		}
		
		
		$text .= 	$conditionGroupClear.
					$tempdc->kill().
					$kSwitchClear.
					$nestswitch->kill().
					$ignore->kill();
		
		if($maxpower2 > 15){
			$conditionGroupSwitch->kill();
		}
		if($maxpower2 > 29){
			$conditionGroupSwitch2->kill();
		}
	
		return $text;
		
	}
	
	
	// MODULUS!
	/*
		� Calling deathcounter is divided by the argument and the remainder is returned to the calling deathcounter.
		� NOTE: if the divisor is 0, the function will return a 0
		� Format:
			- $dc->modulus($var) is analogous to $dc %= $var
			- $var must be a deathcounter or a constant (integer)
		� Max values:
			- calling deathcounter: 2147483647
			- argument: 2147483647
		� Low-end specifics:
			- trigger number: 120
			- temp switch number: 10
			- temp deathcounters: 0
		� High-end specifics:
			- trigger number: 601
			- temp switch number: 23
			- temp deathcounters: 0
		� Maxed specifics:
			- trigger number: 1272
			- temp switch number: 34
			- temp deathcounters: 0
	*/
	public function modulus($divisor)	{
		//ERROR
		if ( func_num_args() != 1 ) {
			Error('COMPILER ERROR FOR MODULUS(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 1: DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if (!($divisor instanceof Deathcounter || is_numeric($divisor))) {
			Error('COMPILER ERROR FOR MODULUS(): ARGUMENT NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}
		
		//INTEGER MODULUS
		if ( is_numeric($divisor) ) {
			
			if( $divisor == 0 ) {
				Error('COMPILER ERROR FOR MODULUS(): DIVIDE BY 0');
			}
			
			$maxpower = getBinaryPower( $this->Max );
			$text = '';

			for($i=$maxpower; $i >= 0; $i--) {
				$k = pow(2, $i);
				$text .= _if( $this->atLeast($k * $divisor) )->then(
					$this->subtract($k * $divisor),
				'');
			}	
			
			return $text;
		}
		
		
		//DEATHCOUNTER MODULUS
		if ( $this->Max > 2147483647) {
			Error('COMPILER ERROR FOR MODULUS(): CALLING DEATHCOUNTER\'S MAX SIZE IS TOO LARGE (EXCEEDS 2147483647)');
		}
		if ( $divisor->Max > 2147483647) {
			Error('COMPILER ERROR FOR MODULUS(): ARGUMENT\'S MAX SIZE IS TOO LARGE (EXCEEDS 2147483647)');
		}
		
		$nestswitch2 = new TempSwitch();
		$ignore = new TempSwitch();
		
		$maxpower1 = getBinaryPower( $this->Max );
		$maxpower2 = getBinaryPower( $divisor->Max );
		$kSwitches = array();
		
		//align 0 to 2^31 (for temporary negative numbers)
		$text =	$this->setTo($divisor).
				$this->add(2147483648);

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
			$text .= _if( $divisor->atLeast($k2) )->then(
				$divisor->subtract($k2),
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
				if( $k1 * $k2 <= $this->Max ){
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
						$this->subtract($k1 * $k2),
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
			$text .= _if( $ignore->is_clear() , $this->atMost(2147483647) )->then(
				$nestswitch2->set(),
			'');
			$text .= _if( $ignore->is_set() )->then(
				$ignore->clear(),
				$nestswitch2->set(),
			'');
			for($i2=$maxpower2; $i2 >= 0; $i2--) {
				$k2 = pow(2, $i2);
				if( $k1 * $k2 <= $this->Max ){
					$text .= _if( $nestswitch2->is_set() , $kSwitches[$i2]->is_set() )->then(
						$this->add($k1 * $k2),
					'');
				}
			}
			$text .= _if( $nestswitch2->is_set() )->then(
				$nestswitch2->clear(),
			'');
			$text .= $conditionGroupClear;
		}
		for($i=$maxpower2; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $kSwitches[$i]->is_set() )->then(
				$divisor->add($k),
				$kSwitches[$i]->clear(),
			'');
		}
		
		//if user divides by 0,  return 0
		$text .= _if ( $divisor->exactly(0) )->then(
			$this->setTo(0),
		'');

        $kSwitchClear = '';
		foreach($kSwitches as $keySwitch){
			$kSwitchClear .= $keySwitch->kill();
		}
		
		$text .= 	$this->subtract(2147483648).
					$conditionGroupClear.
					$kSwitchClear.
					$nestswitch2->kill().
					$ignore->kill();
		
		if($maxpower2 > 15){
			$conditionGroupSwitch->kill();
		}
		if($maxpower2 > 29){
			$conditionGroupSwitch2->kill();
		}
	
		return $text;
		
	}
	
	// MODULUS OF!
	/*
		� Argument 1 is divided by argument 2 and the remainder is returned to the calling deathcounter.
		� NOTE: if the divisor is 0, the function will return a 0
		� Format:
			- $dc->modulusOf($var1, $var2) is analogous to $dc = $var1 % $var2
			- $var1 must be a deathcounter or a constant (integer)
			- $var2 must be a deathcounter or a constant (integer)
		� Max values:
			- argument 1: 2147483647
			- argument 2: 2147483647
		� Low-end specifics:
			- trigger number: 120
			- temp switch number: 10
			- temp deathcounters: 0
		� High-end specifics:
			- trigger number: 601
			- temp switch number: 23
			- temp deathcounters: 0
		� Maxed specifics:
			- trigger number: 1272
			- temp switch number: 34
			- temp deathcounters: 0
	*/
	public function modulusOf($numerator, $denominator)	{
		//ERROR
		if ( func_num_args() != 2 ) {
			Error('COMPILER ERROR FOR MODULUSOF(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 2: DEATHCOUNTER OR CONSTANT (INTEGER), DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if (!($numerator instanceof Deathcounter || is_numeric($numerator))) {
			Error('COMPILER ERROR FOR MODULUSOF(): ARGUMENT 1 NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}
		if (!($denominator instanceof Deathcounter || is_numeric($denominator))) {
			Error('COMPILER ERROR FOR MODULUSOF(): ARGUMENT 2 NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}
		
		//INTEGER-INTEGER MODULUS
		if ( is_numeric($numerator) && is_numeric($denominator) ) {
			return $this->setTo($numerator % $denominator);
		}
		
		//DEATHCOUNTER-INTEGER MODULUS
		if ( is_numeric($denominator) ) {
			
			if( $denominator == 0 ) {
				Error('COMPILER ERROR FOR MODULUSOF(): DIVIDE BY 0');
			}
	
			$tempdc = new TempDC();
			$maxpower1 = getBinaryPower( floor($numerator->Max / $denominator) );
			$maxpower2 = getBinaryPower( $denominator - 1 );
			$maxpower3 = getBinaryPower( $numerator->Max );
			
			$text = $this->setTo(0);

			for($i=$maxpower1; $i >= 0; $i--) {
				$k = pow(2, $i);
				$text .= _if( $numerator->atLeast($k * $denominator) )->then(
					$numerator->subtract($k * $denominator),
					$tempdc->add($k * $denominator),
				'');
			}
			for($i=$maxpower2; $i >= 0; $i--) {
				$k = pow(2, $i);
				$text .= _if( $numerator->atLeast($k) )->then(
					$numerator->subtract($k),
					$this->add($k),
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
		
		
		//DEATHCOUNTER-DEATHCOUNTER MODULUS
		if ( $numerator->Max > 2147483647) {
			Error('COMPILER ERROR FOR MODULUSOF(): ARGUMENT 1\'S MAX SIZE IS TOO LARGE (EXCEEDS 2147483647)');
		}
		if ( $denominator->Max > 2147483647) {
			Error('COMPILER ERROR FOR MODULUSOF(): ARGUMENT 2\'S MAX SIZE IS TOO LARGE (EXCEEDS 2147483647)');
		}
		
		$nestswitch = new TempSwitch();
		$ignore = new TempSwitch();
		
		$maxpower1 = getBinaryPower( $this->Max );
		$maxpower2 = getBinaryPower( $denominator->Max );
		$kSwitches = array();

		
		//align 0 to 2^31 (for temporary negative numbers)
		$text =	$this->setTo($numerator).
				$this->add(2147483648);
		
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
				if( $k1 * $k2 <= $this->Max ){
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
						$this->subtract($k1 * $k2),
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
			$text .= _if( $ignore->is_clear() , $this->atMost(2147483647) )->then(
				$nestswitch->set(),
			'');
			$text .= _if( $ignore->is_set() )->then(
				$ignore->clear(),
				$nestswitch->set(),
			'');
			for($i2=$maxpower2; $i2 >= 0; $i2--) {
				$k2 = pow(2, $i2);
				if( $k1 * $k2 <= $this->Max ){
					$text .= _if( $nestswitch->is_set() , $kSwitches[$i2]->is_set() )->then(
						$this->add($k1 * $k2),
					'');
				}
			}
			$text .= _if( $nestswitch->is_set() )->then(
				$nestswitch->clear(),
			'');
			$text .= $conditionGroupClear;
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
			$this->setTo(0),
		'');

        $kSwitchClear = '';
		foreach($kSwitches as $keySwitch){
			$kSwitchClear .= $keySwitch->kill();
		}
		
		$text .= 	$this->subtract(2147483648).
					$conditionGroupClear.
					$kSwitchClear.
					$nestswitch->kill().
					$ignore->kill();
		
		if($maxpower2 > 15){
			$conditionGroupSwitch->kill();
		}
		if($maxpower2 > 29){
			$conditionGroupSwitch2->kill();
		}
	
		return $text;
		
	}
	
	
	//SQUARE ROOT!
	/*
		� The square root of the argument is returned to the deathcounter. Result is truncated.
		� If no argument is passed, the function will take and return the square root of the calling deathcounter.
		� Format:
			- $dc->squareRoot() is analogous to $dc = floor( sqrt($dc) )
			- $dc->squareRoot($var) is analogous to $dc = floor( sqrt($var) )
			- $var must be a deathcounter or a constant (integer)
		� Max value:
			- argument: 2147483647
		� Low-end specifics:
			- trigger number: 13
			- temp switch number: 2
			- temp deathcounters: 0
		� High-end specifics:
			- trigger number: 284
			- temp switch number: 21
			- temp deathcounters: 0
		� Maxed specifics:
			- trigger number: 631
			- temp switch number: 33
			- temp deathcounters: 0
	*/
	public function squareRoot($var2) {
		//ERROR
		if ( func_num_args() > 1) {
			Error('COMPILER ERROR FOR SQUAREROOT(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 0 OR 1: DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if ( func_num_args() == 0 ){
			$var2 = $this;
		}
		if (!($var2 instanceof Deathcounter || is_numeric($var2))) {
			Error('COMPILER ERROR FOR SQUAREROOT(): ARGUMENT NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}
	
		//INTEGER SQUARE ROOT
		if ( is_numeric($var2) ) {
			return $this->setTo( floor( sqrt($var2) ) );
		}

        $text = '';
		
		//DEATHCOUNTER SQUARE ROOT, LESS THAN 40401 (EFFICIENCY)
		if ( $var2->Max < 40401 ) {
			$ignore = new TempSwitch();
			for( $i=floor( sqrt($var2->Max) ); $i>=1; $i--) {
				$text .= _if( $ignore->is_clear(), $var2->atLeast($i*$i) )->then(
					$this->setTo($i),
					$ignore->set(),
				'');
			}
			$text .= _if( $ignore->is_clear() )->then(
				$this->setTo(0),
			'');
			$text .= $ignore->kill();
			
			return $text;
		}
		
		
		//DEATHCOUNTER SQUARE ROOT, LESS THAN 32 BITS
		if ( func_num_args() == 0 && $this->Max > 2147483647) {
			Error('COMPILER ERROR FOR SQUAREROOT(): CALLING DEATHCOUNTER\'S MAX SIZE IS TOO LARGE (EXCEEDS 2147483647)');
		}
		else if ( $var2->Max > 2147483647) {
			Error('COMPILER ERROR FOR SQUAREROOT(): ARGUMENT\'S MAX SIZE IS TOO LARGE (EXCEEDS 2147483647)');
		}
		
		$restore = new TempSwitch();
		$maxpower = getBinaryPower( $this->Max );
		$kSwitches = array();
		
		
		if ( $var2 !== $this) {
			$maxpower = getBinaryPower( $var2->Max );
			$text .= $this->setTo($var2);
		}
	
		
		//align 0 to 2^31 (for temporary negative numbers)
		$text .=	$this->add(2147483648);
		
		//get dynamic switches
		for($i=$maxpower-$maxpower%2; $i >= 0; $i--) {
			$kSwitches[$i] = new TempSwitch();
		}

		
		//actual algorithm
		for($i1=$maxpower-$maxpower%2, $j=$maxpower-$maxpower%2+1; $i1 >= 0; $i1-=2, $j--) {
			$k1 = pow(2, $i1);
			for($i2=$j; $i2 > $i1+1; $i2--) {
				$k2 = pow(2, $i2);
				$text .= _if( $kSwitches[$i2]->is_set() )->then(
							$this->subtract($k2),
				'');
			}
			$text .= $this->subtract($k1);
			$text .= _if( $this->atMost(2147483647) )->then(
				$restore->set(),
			'');
			for($i2=$j; $i2 > $i1+1; $i2--) {
				$k2 = pow(2, $i2);
				$text .= _if( $restore->is_set(), $kSwitches[$i2]->is_set() )->then(
							$this->add($k2),
				'');
			}
			$text .= _if( $restore->is_set() )->then(
							$this->add($k1),
				'');
			for($i2=$i1+1; $i2 < $j; $i2++) {
				$text .= _if( $kSwitches[$i2+1]->is_clear() )->then(
							$kSwitches[$i2]->clear(),
				'');
				$text .= _if( $kSwitches[$i2+1]->is_set() )->then(
							$kSwitches[$i2]->set(),
				'');
			}
			if( $j < $maxpower-$maxpower%2+1 ) {
				$text .= _if( $restore->is_clear() )->then(
					$kSwitches[$i1]->set(),
					$kSwitches[$j]->clear(),
				'');
				$text .= _if( $restore->is_set() )->then(
					$restore->clear(),
					$kSwitches[$j]->clear(),
				'');
			}
			else {
				$text .= _if( $restore->is_clear() )->then(
					$kSwitches[$i1]->set(),
				'');
				$text .= _if( $restore->is_set() )->then(
					$restore->clear(),
				'');
			}
		}
		$text .= $this->setTo(0);
		for($i=ceil($maxpower/2); $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $kSwitches[$i]->is_set() )->then(
				$this->add($k),
			'');
		}
		
		$kSwitchClear = '';
		foreach($kSwitches as $keySwitch){
			$kSwitchClear .= $keySwitch->kill();
		}
		
		$text .= 	$kSwitchClear.
					$restore->kill();
		
		return $text;
		
	}
	
	//ROUNDED SQUARE ROOT!
	/**
     * � The square root of the argument is returned to the deathcounter. Result is rounded.
     * � If no argument is passed, the function will take and return the square root of the calling deathcounter.
     * � Format:
     * 	    - $dc->roundedSquareRoot() is analogous to $dc = round( sqrt($dc) )
     *  	- $dc->roundedSquareRoot($var) is analogous to $dc = round( sqrt($var) )
     * 		- $var must be a deathcounter or a constant (integer)
     * 	� Max value:
     * 		- argument: 2147483647
     * 	� Low-end specifics:
     * 		- trigger number: 13
     * 		- temp switch number: 2
     * 		- temp deathcounters: 0
     * 	� High-end specifics:
     * 		- trigger number: 294
     * 		- temp switch number: 21
     * 		- temp deathcounters: 0
     * 	� Maxed specifics:
     * 		- trigger number: 658
     * 		- temp switch number: 33
     * 		- temp deathcounters: 0
     *
     * @param $var2 Deathcounter
     * @return OreoAction
     */
    public function roundedSquareRoot($var2 = null) {
		//ERROR
		if ( func_num_args() > 1) {
			Error('COMPILER ERROR FOR ROUNDEDSQUAREROOT(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 0 OR 1: DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if ( func_num_args() == 0 ){
			$var2 = $this;
		}
		if (!($var2 instanceof Deathcounter || is_numeric($var2))) {
			Error('COMPILER ERROR FOR ROUNDEDSQUAREROOT(): ARGUMENT NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}
	
		//INTEGER SQUARE ROOT
		if ( is_numeric($var2) ) {
			return $this->setTo( round( sqrt($var2) ) );
		}

        $text = '';
		
		//DEATHCOUNTER SQUARE ROOT, LESS THAN 40401 (EFFICIENCY)
		if ( $var2->Max < 40201 ) {
			$ignore = new TempSwitch();
			for( $i=floor( sqrt($var2->Max) ); $i>=1; $i--) {
				$text .= _if( $ignore->is_clear(), $var2->atLeast(ceil(($i-.5)*($i-.5))) )->then(
					$this->setTo($i),
					$ignore->set(),
				'');
			}
			$text .= _if( $ignore->is_clear() )->then(
				$this->setTo(0),
			'');
			$text .= $ignore->kill();
			
			return $text;
		}
		
			
		//DEATHCOUNTER SQUARE ROOT, LESS THAN 32 BITS
		if ( func_num_args() == 0 && $this->Max > 2147483647) {
			Error('COMPILER ERROR FOR ROUNDEDSQUAREROOT(): CALLING DEATHCOUNTER\'S MAX SIZE IS TOO LARGE (EXCEEDS 2147483647)');
		}
		else if ( $var2->Max > 2147483647) {
			Error('COMPILER ERROR FOR ROUNDEDSQUAREROOT(): ARGUMENT\'S MAX SIZE IS TOO LARGE (EXCEEDS 2147483647)');
		}
		
		$restore = new TempSwitch();
		$maxpower = getBinaryPower( $this->Max );
		$kSwitches = array();
		
		
		if ( $var2 !== $this) {
			$maxpower = getBinaryPower( $var2->Max );
			$text .= $this->setTo($var2);
		}
	
		
		//align 0 to 2^31 (for temporary negative numbers)
		$text .=	$this->add(2147483648);
		
		//get dynamic switches
		for($i=$maxpower-$maxpower%2; $i >= 0; $i--) {
			$kSwitches[$i] = new TempSwitch();
		}

		
		//actual algorithm
		for($i1=$maxpower-$maxpower%2, $j=$maxpower-$maxpower%2+1; $i1 >= 0; $i1-=2, $j--) {
			$k1 = pow(2, $i1);
			for($i2=$j; $i2 > $i1+1; $i2--) {
				$k2 = pow(2, $i2);
				$text .= _if( $kSwitches[$i2]->is_set() )->then(
							$this->subtract($k2),
				'');
			}
			$text .= $this->subtract($k1);
			$text .= _if( $this->atMost(2147483647) )->then(
				$restore->set(),
			'');
			for($i2=$j; $i2 > $i1+1; $i2--) {
				$k2 = pow(2, $i2);
				$text .= _if( $restore->is_set(), $kSwitches[$i2]->is_set() )->then(
							$this->add($k2),
				'');
			}
			$text .= _if( $restore->is_set() )->then(
							$this->add($k1),
				'');
			for($i2=$i1+1; $i2 < $j; $i2++) {
				$text .= _if( $kSwitches[$i2+1]->is_clear() )->then(
							$kSwitches[$i2]->clear(),
				'');
				$text .= _if( $kSwitches[$i2+1]->is_set() )->then(
							$kSwitches[$i2]->set(),
				'');
			}
			if( $j < $maxpower-$maxpower%2+1 ) {
				$text .= _if( $restore->is_clear() )->then(
					$kSwitches[$i1]->set(),
					$kSwitches[$j]->clear(),
				'');
				$text .= _if( $restore->is_set() )->then(
					$restore->clear(),
					$kSwitches[$j]->clear(),
				'');
			}
			else {
				$text .= _if( $restore->is_clear() )->then(
					$kSwitches[$i1]->set(),
				'');
				$text .= _if( $restore->is_set() )->then(
					$restore->clear(),
				'');
			}
		}
		// [ROUNDED PORTION]
		for($i=floor($maxpower/2); $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $kSwitches[$i]->is_set() )->then(
						$this->subtract($k),
			'');
		}
		$text .= _if( $this->atMost(2147483648) )->then(
				$this->setTo(0),
			'');
		$text .= _if( $this->atLeast(2147483649) )->then(
				$this->setTo(1),
			'');
		// [/ROUNDED PORTION]
		for($i=floor($maxpower/2); $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $kSwitches[$i]->is_set() )->then(
				$this->add($k),
			'');
		}
		
		$kSwitchClear = '';
		foreach($kSwitches as $keySwitch){
			$kSwitchClear .= $keySwitch->kill();
		}
		
		$text .= 	$kSwitchClear.
					$restore->kill();
		
		return $text;
		
	}
	
	
	// RANDOM NUMBER!
	/*
		� Generates a random number between 0 and the argument and sends the result to the calling deathcounter.
		� Format:
			- $dc->randomNumber($var) is analogous to $dc = rand() % ($var+1)
			- $var must be a deathcounter or a constant (integer)
		� Max value:
			- argument: 2147483646
		� Low-end specifics:
			- trigger number: 152
			- temp switch number: 32
			- temp deathcounters: 0
		� High-end specifics:
			- trigger number: 633
			- temp switch number: 32
			- temp deathcounters: 0
		� Maxed specifics:
			- trigger number: 1304
			- temp switch number: 34
			- temp deathcounters: 0
	*/
	public function randomNumber($max)	{
		//ERROR
		if ( func_num_args() != 1) {
			Error('COMPILER ERROR FOR RANDOMNUMBER(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 1: DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if (!($max instanceof Deathcounter || is_numeric($max))) {
			Error('COMPILER ERROR FOR RANDOMNUMBER(): ARGUMENT NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}

		if( is_numeric($max) ) {
			//silly user; what are you doing?
			if ($max == 0) {
				return $this->setTo(0);
			}

            $text = '';
            $kSwitchClear = '';

			//if $max is a power, make it more efficient
			for($i1=0; $i1<30; $i1++) {
				$k1 = pow(2, $i1+1) - 1;
				if( $max == $k1 ) {
					$text .= $this->setTo(0);
					for($i2=$i1; $i2 >= 0; $i2--) {
						$kSwitches[$i2] = new TempSwitch();
						$text .= $kSwitches[$i2]->randomize();
					}
					for($i2=$i1; $i2 >= 0; $i2--) {
						$k2 = pow(2, $i2);
						$text .= _if( $kSwitches[$i2]->is_set() )->then(
							$this->add($k2),
						'');
					}
					foreach($kSwitches as $keySwitch){
						$kSwitchClear .= $keySwitch->kill();
					}
					$text .= 	$kSwitchClear;
					
					return $text;
				}
			}
			
			$temp = $this->Max;
			$this->Max = ceil(2147483647/($max+1));
			$text .= $this->setTo(0);
			
			for($i=30; $i >= 0; $i--) {
				$kSwitches[$i] = new TempSwitch();
				$text .= $kSwitches[$i]->randomize();
			}
			for($i=30; $i >= 0; $i--) {
				$k = pow(2, $i);
				$text .= _if( $kSwitches[$i]->is_set() )->then(
					$this->add($k),
				'');
			}
			
			$text .= $this->modulus($max+1);
			
			foreach($kSwitches as $keySwitch){
					$kSwitchClear .= $keySwitch->kill();
			}
			$text .= 	$kSwitchClear;
			
			$this->Max = $temp;
			
			return $text;
		}
	
		$text = $this->setTo(0).
                $max->add(1);

		for($i=30; $i >= 0; $i--) {
			$kSwitches[$i] = new TempSwitch();
			$text .= $kSwitches[$i]->randomize();
		}
		for($i=30; $i >= 0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $kSwitches[$i]->is_set() )->then(
				$this->add($k),
			'');
		}

        $kSwitchClear = '';
		foreach($kSwitches as $keySwitch){
				$kSwitchClear .= $keySwitch->kill();
		}
		$text .= 	$kSwitchClear;
		
		$text .= $this->modulus($max);
		
		$text .= $max->subtract(1);
		
		return $text;
	}
	
	// RANDOMIZE!
	/*
		� Generates a random number between $num1 (lower bound) and $num2 (upper bound) and sends the result to the calling deathcounter.
		� If only one argument is given, it will generate a number between 0 and the argument.
		� If $num1 is greater than or equal to $num2, the function will return $num1.
		� Format:
			- $dc->randomize($num1, $num2) is analogous to $dc = $num1 + ( rand() % ($num2-$num1+1) )
			- $num1 must be a deathcounter or a constant (integer)
			- $num2 must be a deathcounter or a constant (integer)
		� Max value:
			- argument 1: 2147483646
			- argument 2: 2147483646
		� Low-end specifics:
			- trigger number: 169
			- temp switch number: 33
			- temp deathcounters: 1
		� High-end specifics:
			- trigger number: 676
			- temp switch number: 33
			- temp deathcounters: 1
		� Maxed specifics:
			- trigger number: 1369
			- temp switch number: 35
			- temp deathcounters: 1
	*/
	public function randomize($num1, $num2)	{
		//ERROR
		if ( func_num_args() < 1 || func_num_args() > 2 ) {
			Error('COMPILER ERROR FOR RANDOMIZE(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 1 OR 2: DEATHCOUNTER OR CONSTANT (INTEGER), DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if (!($num1 instanceof Deathcounter || is_numeric($num1))) {
			Error('COMPILER ERROR FOR RANDOMIZE(): ARGUMENT 1 NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}
		if (func_num_args() == 2 && !($num2 instanceof Deathcounter || is_numeric($num2))) {
			Error('COMPILER ERROR FOR RANDOMIZE(): ARGUMENT 2 NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}
		

		if( func_num_args() == 1 ) {
			return $this->randomNumber($num1);
		}

        $text = '';

		if( is_numeric($num1) && is_numeric($num2) ) {
			if( $num2 > $num1 ) {
				$text .= $this->randomNumber($num2 - $num1);
				$text .= $this->add($num1);
				
				return $text;
			}
		}
		
		if( is_numeric($num1) ) {
			$text .= _if( $num2->atMost($num1) )->then(
					$this->setTo($num1),
				'');
			$text .= _if( $num2->atLeast($num1+1) )->then(
					$num2->subtract($num1),
					$this->randomNumber($num2),
					$num2->add($num1),
					$this->add($num1),
				'');
				
			return $text;
		}
		
		if( is_numeric($num2) ) {
			if( $num2 <= 0 ) {
				return $this->setTo($num1);
			}
			
			$tempdc = new TempDC($num2);
			
			$text .= _if( $num1->atLeast($num2) )->then(
					$this->setTo($num1),
				'');
			$text .= _if( $num1->atMost($num2-1) )->then(
					$tempdc->setTo($num2),
					$tempdc->subtract($num1),
					$this->randomNumber($tempdc),
					$this->add($num1),
					$tempdc->kill(),
				'');
				
			return $text;
		}
		
		
		$tempdc = new TempDC();
		$maxpower = getBinaryPower( min( $num1->Max, $num2->Max) );
		
		for($i=$maxpower; $i>=0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $num1->atLeast($k), $num2->atLeast($k) )->then(
					$num1->subtract($k),
					$num2->subtract($k),
					$tempdc->add($k),
				'');
		}
		
		$text .= _if( $num2->atMost(0) )->then(
				$this->setTo(0),
			'');
		$text .= _if( $num2->atLeast(1) )->then(
				$this->randomNumber($num2),
			'');
		
		for($i=$maxpower; $i>=0; $i--) {
			$k = pow(2, $i);
			$text .= _if( $tempdc->atLeast($k) )->then(
					$tempdc->subtract($k),
					$num1->add($k),
					$num2->add($k),
					$this->add($k),
				'');
		}

		$text .= $tempdc->kill();
				
		return $text;
		
	}
	
	
	
	// TRIGONOMETRIC FUNCTIONS:
	
	// COSINE!
	/*
		� Returns 10000x the cosine of the argument to the calling deathcounter.
		� NOTE: angle is 4x normal (ie. 0 = 0 degrees, 360 = 90 degrees)
		� Format:
			- $dc->cosine($angledc) is analogous to $dc = abs( round( cos($angledc/4)*10000 ) )
			- $angledc must be a deathcounter
		� Max value:
			- argument: 1440
			- returned: 10000
		� Specifics:
			- trigger number: 266
			- temp switch number: 3
			- temp deathcounters: 1
	*/
	public function cosine($angledc) {
		//ERROR
		if ( func_num_args() != 1) {
			Error('COMPILER ERROR FOR COSINE(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 1: DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if (!($angledc instanceof Deathcounter || is_numeric($angledc))) {
			Error('COMPILER ERROR FOR COSINE(): ARGUMENT NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}

        $text = '';

		if (is_numeric($angledc)){
			$text .= $this->setTo( abs(round( cos(deg2rad($angledc/4))*10000 )) );
		}
		
		$saveMax = $this->Max;
		$this->Max = 1440;
		$tempdc = new TempDC(1440);
		$switch = new TempSwitch();
		
		if ( $this !== $angledc ) {
			$text .= $this->setTo($angledc);
		}
		
		$tempdc->Max = 360;
		
		// Manipulate angle
		$text .= 
		_if( $this->atLeast(1440) )->then(
			$this->subtract(1440),
		'').
		_if( $this->atLeast(720) )->then(
			$this->subtract(720),
		'').
		_if( $this->atLeast(360) )->then(
			$this->subtract(360),
			$switch->set(),
		'').
		_if( $this->atLeast(181) )->then(
			$tempdc->setTo(360),
			$tempdc->subtract($this),
			$this->setTo($tempdc),
			$switch->toggle(),
		'');
		
		$tempdc->Max = 1440;
		
		$break = new TempSwitch();
		
		// Cosine function table
		for($i=0; $i <= 180; $i++){
			$text .= _if( $break->is_clear(), $this->exactly($i) )->then(
				$this->setTo( abs(round( cos(deg2rad($i/4))*10000 )) ),
				$tempdc->setTo( abs(round( sin(deg2rad($i/4))*10000 )) ),
				$break->set(),
			'');
		}
		
		$this->Max = $saveMax;
		$tempdc->Max = 10000;
		
		$text .= $break->kill();
		
		$text .= _if( $switch->is_set() )->then(
			$this->becomeDel($tempdc),
			$switch->kill(),
		'');
		
		return $text;
	}
	
	// SINE!
	/*
		� Returns 10000x the sine of the argument to the calling deathcounter.
		� NOTE: angle is 4x normal (ie. 0 = 0 degrees, 360 = 90 degrees)
		� Format:
			- $dc->sine($angledc) is analogous to $dc = abs( round( sin($angledc/4)*10000 ) )
			- $angledc must be a deathcounter
		� Max value:
			- argument: 1440
			- returned: 10000
		� Specifics:
			- trigger number: 266
			- temp switch number: 3
			- temp deathcounters: 1
	*/
	public function sine($angledc) {
		//ERROR
		if ( func_num_args() != 1) {
			Error('COMPILER ERROR FOR SINE(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 1: DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if (!($angledc instanceof Deathcounter || is_numeric($angledc))) {
			Error('COMPILER ERROR FOR SINE(): ARGUMENT NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}

        $text = '';

		if (is_numeric($angledc)){
			$text .= $this->setTo( abs(round( sin(deg2rad($angledc/4))*10000 )) );
		}
		
		$saveMax = $this->Max;
		$this->Max = 1440;
		$tempdc = new TempDC(1440);
		$switch = new TempSwitch();
		
		if ( $this !== $angledc ) {
			$text .= $this->setTo($angledc);
		}
		
		$tempdc->Max = 360;
		
		// Manipulate angle
		$text .= 
		_if( $this->atLeast(1440) )->then(
			$this->subtract(1440),
		'').
		_if( $this->atLeast(720) )->then(
			$this->subtract(720),
		'').
		_if( $this->atLeast(360) )->then(
			$this->subtract(360),
			$switch->set(),
		'').
		_if( $this->atLeast(181) )->then(
			$tempdc->setTo(360),
			$tempdc->subtract($this),
			$this->setTo($tempdc),
			$switch->toggle(),
		'');
		
		$tempdc->Max = 1440;
		
		$break = new TempSwitch();
		
		// Sine function table
		for($i=0; $i <= 180; $i++){
			$text .= _if( $break->is_clear(), $this->exactly($i) )->then(
				$this->setTo( abs(round( sin(deg2rad($i/4))*10000 )) ),
				$tempdc->setTo( abs(round( cos(deg2rad($i/4))*10000 )) ),
				$break->set(),
			'');
		}
		
		$this->Max = $saveMax;
		$tempdc->Max = 10000;
		
		$text .= $break->kill();
		
		$text .= _if( $switch->is_set() )->then(
			$this->becomeDel($tempdc),
			$switch->kill(),
		'');
		
		return $text;
	}
	
	// TANGENT!
	/*
		� Returns 10000x the tangent of the argument to the calling deathcounter.
		� NOTE: angle is 4x normal (ie. 0 = 0 degrees, 360 = 90 degrees)
		� NOTE: cosine and sine supported 0-360 degrees, but tangent only supports 0-45 degrees; this is because tan(90) = 1/0
		� Format:
			- $dc->tangent($angledc) is analogous to $dc = abs( round( tan($angledc/4)*10000 ) )
			- $angledc must be a deathcounter
		� Max value:
			- argument: 180 (45 degrees)
			- returned: 10000
		� Specifics:
			- trigger number: 183
			- temp switch number: 2
			- temp deathcounters: 0
	*/
	public function tangent($angledc) {
		//ERROR
		if ( func_num_args() != 1) {
			Error('COMPILER ERROR FOR TANGENT(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 1: DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if (!($angledc instanceof Deathcounter || is_numeric($angledc))) {
			Error('COMPILER ERROR FOR TANGENT(): ARGUMENT NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}

        $text = '';

		if (is_numeric($angledc)){
			$text .= $this->setTo( abs(round( tan(deg2rad($angledc/4))*10000 )) );
		}
		
		$break = new TempSwitch();
		
		// Tangent function table
		for($i=0; $i <= 180; $i++){
			$text .= _if( $break->is_clear(), $angledc->exactly($i) )->then(
				$this->setTo( abs(round( tan(deg2rad($i/4))*10000 )) ),
				$break->set(),
			'');
		}
		$text .= $break->kill();
		
		return $text;
	}
	
	// SCTANGENT!
	/*
		� Returns 1000x the tangent of the argument to the calling deathcounter.
		� NOTE: angle is according to SC degrees (90 degrees is 64 SC degrees)
		� Format:
			- $dc->tangent($angledc) is analogous to $dc = abs(round( tan(($i-0.5)*90/64)*1000 ))
			- $angledc must be a deathcounter
		� Max value:
			- argument: 128 (360 degrees)
			- returned: 1000
		� Specifics:
			- trigger number: 129
			- temp switch number: 2
			- temp deathcounters: 0
	*/
	function SCtangent(Deathcounter $angledc) {
        $text = '';
		
		$break = new TempSwitch();
		
		// SCtangent function table
		$text .= _if( $angledc->exactly(0) )->then(
			$this->setTo( abs(round( tan(deg2rad(0))*1000 )) ),
			$break->set(),
		'');
		for($i=1; $i <= 128; $i++){
			$text .= _if( $break->is_clear(), $angledc->exactly($i) )->then(
				$this->setTo( abs(round( tan(deg2rad(($i-0.5)*90/64))*1000 )) ),
				$break->set(),
			'');
		}
		$text .= $break->kill();
		
		return $text;
	}
	
	// ARCCOS!
	/*
		� Returns 4x the arccosine of the argument to the calling deathcounter.
		� NOTE: angle is 4x normal (ie. 0 = 0 degrees, 360 = 90 degrees)
		� NOTE: argument passed in should be 10000x the unit value (ie. 1 is actually 10000, 0.05 is actually 500); as such, the max value of the argument should be 10000
		� Format:
			- $dc->arccosine($x) is analogous to $dc = round( arccos($x/10000)*4 )
			- $x must be a deathcounter
		� Max value:
			- argument: 10000
			- returned: 360
		� Specifics:
			- trigger number: 363
			- temp switch number: 2
			- temp deathcounters: 0
	*/
	public function arccosine($x) {
		//ERROR
		if ( func_num_args() != 1) {
			Error('COMPILER ERROR FOR ARCCOSINE(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 1: DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if (!($x instanceof Deathcounter || is_numeric($x))) {
			Error('COMPILER ERROR FOR ARCCOSINE(): ARGUMENT NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}

        $text = '';

		if (is_numeric($x)){
            $text .= $this->setTo( abs(round(4*rad2deg(acos($x/10000)))) );
        }
		
		$break = new TempSwitch();
		
		// Arctangent function table
		for($i=0; $i < 360; $i++){
			$text .= _if( $break->is_clear(), $x->atLeast( round( cos(deg2rad(($i+.5)/4))*10000 ) ) )->then(
				$this->setTo($i),
				$break->set(),
			'');
		}
		$text .= _if( $break->is_clear() )->then(
			$this->setTo(360),
		'');
		
		$text .= $break->kill();
		
		return $text;
	}
	
	// ARCSIN!
	/*
		� Returns 4x the arcsine of the argument to the calling deathcounter.
		� NOTE: angle is 4x normal (ie. 0 = 0 degrees, 360 = 90 degrees)
		� NOTE: argument passed in should be 10000x the unit value (ie. 1 is actually 10000, 0.05 is actually 500); as such, the max value of the argument should be 10000
		� Format:
			- $dc->arcsine($x) is analogous to $dc = round( arcsin($y/10000)*4 )
			- $y must be a deathcounter
		� Max value:
			- argument: 10000
			- returned: 360
		� Specifics:
			- trigger number: 363
			- temp switch number: 2
			- temp deathcounters: 0
	*/
	public function arcsine($y) {
		//ERROR
		if ( func_num_args() != 1) {
			Error('COMPILER ERROR FOR ARCSINE(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 1: DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if (!($y instanceof Deathcounter || is_numeric($y))) {
			Error('COMPILER ERROR FOR ARCSINE(): ARGUMENT NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}

        $text = '';

		if (is_numeric($y)){
            $text .= $this->setTo( abs(round(4*rad2deg(asin($y/10000)))) );
        }
		
		$break = new TempSwitch();
		
		// Arctangent function table
		for($i=360; $i > 0; $i--){
			$text .= _if( $break->is_clear(), $y->atLeast( round( sin(deg2rad(($i-.5)/4))*10000 ) ) )->then(
				$this->setTo($i),
				$break->set(),
			'');
		}
		$text .= _if( $break->is_clear() )->then(
			$this->setTo(0),
		'');
		
		$text .= $break->kill();
		
		return $text;
	}
	
	// ARCTAN!
	/*
		� Returns 4x the arctangent of the argument to the calling deathcounter.
		� NOTE: angle is 4x normal (ie. 0 = 0 degrees, 360 = 90 degrees)
		� NOTE: argument passed in should be 10000x the slope value (ie. 1 is actually 10000, 0.05 is actually 500)
		� NOTE: because the tangent of 90 degrees is 1/0, this function only works between 0 and 45 degrees; consequently, the max value of the argument should be 10000
		� Format:
			- $dc->arctangent($slope) is analogous to $dc = round( arctangent($slope/10000)*4 )
			- $slope must be a deathcounter
		� Max value:
			- argument: 10000
			- returned: 180
		� Specifics:
			- trigger number: 183
			- temp switch number: 2
			- temp deathcounters: 0
	*/
	public function arctangent($slope) {
		//ERROR
		if ( func_num_args() != 1) {
			Error('COMPILER ERROR FOR ARCTANGENT(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 1: DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if (!($slope instanceof Deathcounter || is_numeric($slope))) {
			Error('COMPILER ERROR FOR ARCTANGENT(): ARGUMENT NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}
		
		if (is_numeric($slope) && $slope > 10000){
			Error('COMPILER ERROR FOR ARCTANGENT(): ARGUMENT CAN BE AT MOST 10000');
		}

        $text = '';

		if (is_numeric($slope)){
            $text .= $this->setTo( abs(round(4*rad2deg(atan($slope/10000)))) );
        }
		
		$break = new TempSwitch();
		
		for($i=180; $i > 0; $i--){
			$text .= _if( $break->is_clear(), $slope->atLeast( ceil( tan(deg2rad(($i-.5)/4))*10000 ) ) )->then(
				$this->setTo($i),
				$break->set(),
			'');
		}
		$text .= _if( $break->is_clear() )->then(
			$this->setTo(0),
		'');
		
		$text .= $break->kill();
		
		return $text;
	}
	
	// GET COMPONENTS!
	/*
		� Returns 10000x the cosine of the argument to the first argument and 10000x the sine of the argument to the second argument. Calling deathcounter is the angle used.
		� NOTE: angle is 4x normal (ie. 0 = 0 degrees, 360 = 90 degrees)
		� Format:
			- $dc->getComponents($x, $y) is analogous to $x = abs( round( cos($angledc/4)*10000 ) ) and $y = abs( round( sin($angledc/4)*10000 ) )
			- $x must be a deathcounter
			- $y must be a deathcounter
		� Max value:
			- calling deathcounter: 1440
			- returned to argument 1: 10000
			- returned to argument 2: 10000
		� Specifics:
			- trigger number: 293
			- temp switch number: 3
			- temp deathcounters: 1
	*/
	public function getComponents(Deathcounter $x, Deathcounter $y) {
		//ERROR
		if ( func_num_args() != 2 ) {
			Error('COMPILER ERROR FOR GETCOMPONENTS(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 2: DEATHCOUNTER, DEATHCOUNTER)');
		}
		if (!($x instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR GETCOMPONENTS(): ARGUMENT 1 NEEDS TO BE A DEATHCOUNTER');
		}
		if (!($y instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR GETCOMPONENTS(): ARGUMENT 2 NEEDS TO BE A DEATHCOUNTER');
		}
		
		$tempdc = new TempDC(1440);
		$switch = new TempSwitch();
		
		$text = $x->setTo($this);
		$saveMax = $x->Max;
		$x->Max = 1440;
		
		$tempdc->Max = 360;
		
		// Manipulate angle
		$text .= 
		_if( $x->atLeast(1440) )->then(
			$x->subtract(1440),
		'').
		_if( $x->atLeast(720) )->then(
			$x->subtract(720),
		'').
		_if( $x->atLeast(360) )->then(
			$x->subtract(360),
			$switch->set(),
		'').
		_if( $x->atLeast(181) )->then(
			$tempdc->setTo(360),
			$tempdc->subtract($x),
			$x->setTo($tempdc),
			$switch->toggle(),
		'');
		
		$tempdc->Max = 1440;
		
		$break = new TempSwitch();
		
		// Cosine and Sine function table
		for($i=0; $i <= 180; $i++){
			$text .= _if( $x->exactly($i), $break->is_clear() )->then(
				$x->setTo( abs(round( cos(deg2rad($i/4))*10000 )) ),
				$tempdc->setTo( abs(round( sin(deg2rad($i/4))*10000 )) ),
				$break->set(),
			'');
		}
		
		$x->Max = 10000;
		$tempdc->Max = 10000;
		
		$text .= $break->kill();

		$text .= _if( $switch->is_clear() )->then(
			$y->become($tempdc),
			$switch->clear(),
		'');
		$text .= _if( $switch->is_set() )->then(
			$y->become($x),
			$x->becomeDel($tempdc),
			$switch->kill(),
		'');
		
		$x->Max = $saveMax;
		
		return $text;
	}
	// Get Components alias
	public function componentsInto($x, $y) {
		return $this->getComponents($x, $y);
	}
	
	// GET ANGLE!
	/*
		� Returns the angle formed from the point ($originx, $originy) to the point ($destx, $desty).
		� If only two arguments are provided, returns the angle formed by the the horizontal distance (argument 1) and the vertical distance (argument 2).
		� NOTE: angle is 4x normal (ie. 0 = 0 degrees, 360 = 90 degrees)
		� NOTE: if a 0/0 distance is found, the function returns an angle of 0
		� Format:
			- $dc->getComponents($originx, $originy, $destx, $desty) is analogous to $dc = abs( round( arctan( ($originy-$desty)/($originx-$destx) )*4 ) )
			- $dc->getComponents($xdist, $ydist) is analogous to $dc = abs( round( arctan( $ydist/$xdist )*4 ) )
			- $originx must be a deathcounter
			- $originy must be a deathcounter
			- $destx must be a deathcounter
			- $desty must be a deathcounter
		� Max values:
			- argument 1: 2147483647
			- argument 2: 2147483647
			- argument 3: 2147483647
			- argument 4: 2147483647
			- returned: 1440
		� Specifics:
			- trigger number: 684
			- temp switch number: 13
			- temp deathcounters: 6
	*/
	public function getAngle($originx, $originy, $destx, $desty) {
		//ERROR
		if ( func_num_args() != 2 && func_num_args() != 4 ) {
			Error('COMPILER ERROR FOR GETANGLE(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 2 OR 4: DEATHCOUNTER, DEATHCOUNTER; DEATHCOUNTER, DEATHCOUNTER, DEATHCOUNTER, DEATHCOUNTER)');
		}
		if (!($originx instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR GETANGLE(): ARGUMENT 1 NEEDS TO BE A DEATHCOUNTER');
		}
		if (!($originy instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR GETANGLE(): ARGUMENT 2 NEEDS TO BE A DEATHCOUNTER');
		}
		if ( func_num_args() == 4 && !($destx instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR GETANGLE(): ARGUMENT 3 NEEDS TO BE A DEATHCOUNTER');
		}
		if ( func_num_args() == 4 && !($desty instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR GETANGLE(): ARGUMENT 4 NEEDS TO BE A DEATHCOUNTER');
		}
		
		//DISTANCE DIVISION
		if (func_num_args() == 2){
			$xdist = $originx;
			$ydist = $originy;
		
			//FIND DOMINANT DISTANCE
			$maxpower1 = getBinaryPower( min( $xdist->Max, $ydist->Max) );
			$maxpower2 = getBinaryPower( max( $xdist->Max, $ydist->Max) );
			
			$angleswap = new TempSwitch();
			$domdist = new TempDC( max( $xdist->Max, $ydist->Max) );
			$recdist = new TempDC( min( $xdist->Max, $ydist->Max) * 10000 );
			$tempdc = new TempDC();

            $text = '';

			for($i=$maxpower1; $i>=0; $i--) {
				$k=pow(2, $i);
				$text .= _if( $xdist->atLeast($k), $ydist->atLeast($k) )->then(
						$xdist->subtract($k),
						$ydist->subtract($k),
						$recdist->add($k * 10000),
						$domdist->add($k),
						$tempdc->add($k),
					'');
			}		
			$text .= _if( $ydist->atLeast(1) )->then(
					$xdist->add(2147483648),
				'');
			$text .= _if( $ydist->atMost(0) )->then(
					$ydist->add(2147483648),
					$angleswap->set(),
				'');
			for($i=$maxpower2; $i>=0; $i--) {
				$k=pow(2, $i);
				$text .= _if( $xdist->atLeast($k), $ydist->atLeast($k) )->then(
						$xdist->subtract($k),
						$ydist->subtract($k),
						$domdist->add($k),
						$tempdc->add($k),
					'');
			}
			for($i=$maxpower2; $i>=0; $i--) {
				$k=pow(2, $i);
				$text .= _if( $tempdc->atLeast($k) )->then(
						$xdist->add($k),
						$ydist->add($k),
						$tempdc->subtract($k),
					'');
			}
			$text .= _if( $angleswap->is_clear() )->then(
					$xdist->subtract(2147483648),
				'');
			$text .= _if( $angleswap->is_set() )->then(
					$ydist->subtract(2147483648),
				'');
			
			
			//FIX 0/0
			$text .= _if( $recdist->Exactly(0), $domdist->Exactly(0) )->then(
					$domdist->setTo(1),
				'');

			//CALCULATE SLOPE
			$text .= $this->roundedQuotientOf($recdist, $domdist);
			
			//ARCTANGENT
			$text .= $this->arctangent($this);
			
			//90 DEGREE ANGLE
			$text .= _if( $angleswap->is_set() )->then(
				$tempdc->setTo(360),
				$tempdc->subtract($this),
				$tempdc->max(360),
				$this->become($tempdc),
			'');
			
			
			$text .=	$angleswap->kill().
					$recdist->kill().
					$domdist->kill().
					$tempdc->kill();
					
		
			return $text;
		
		}
		
	
		//HORIZONTAL DISTANCE AND DIRECTION
		$maxpower1 = getBinaryPower( min( $originx->Max, $destx->Max) );
		$maxpower2 = getBinaryPower( max( $originx->Max, $destx->Max) );

		$tempdc = new TempDC();
		$xdist = new TempDC( max($originx->Max, $destx->Max) );
		$angleswap = new TempSwitch();
		$left = new TempSwitch();
		$up = new TempSwitch();

        $text = '';

		for($i=$maxpower1; $i>=0; $i--) {
			$k=pow(2, $i);
			$text .= _if( $originx->atLeast($k), $destx->atLeast($k) )->then(
					$originx->subtract($k),
					$destx->subtract($k),
					$tempdc->add($k),
				'');
		}
		$text .= _if( $originx->atLeast(1) )->then(
				$destx->add(2147483648),
				$left->set(),
				$angleswap->toggle(),
			'');
		$text .= _if( $originx->atMost(0) )->then(
				$originx->add(2147483648),
			'');
		for($i=$maxpower2; $i>=0; $i--) {
			$k=pow(2, $i);
			$text .= _if( $originx->atLeast($k), $destx->atLeast($k) )->then(
					$originx->subtract($k),
					$destx->subtract($k),
					$tempdc->add($k),
					$xdist->add($k),
				'');
		}
		for($i=$maxpower2; $i>=0; $i--) {
			$k=pow(2, $i);
			$text .= _if( $tempdc->atLeast($k) )->then(
					$originx->add($k),
					$destx->add($k),
					$tempdc->subtract($k),
				'');
		}
		$text .= _if( $left->is_clear() )->then(
				$originx->subtract(2147483648),
			'');
		$text .= _if( $left->is_set() )->then(
				$destx->subtract(2147483648),
			'');
		
		
		//VERTICAL DISTANCE AND DIRECTION
		$maxpower1 = getBinaryPower( min( $originy->Max, $desty->Max) );
		$maxpower2 = getBinaryPower( max( $originy->Max, $desty->Max) );

		$ydist = new TempDC( max( $originy->Max, $desty->Max) );
		
		for($i=$maxpower1; $i>=0; $i--) {
			$k=pow(2, $i);
			$text .= _if( $originy->atLeast($k), $desty->atLeast($k) )->then(
					$originy->subtract($k),
					$desty->subtract($k),
					$tempdc->add($k),
				'');
		}
		$text .= _if( $desty->atLeast(1) )->then(
				$originy->add(2147483648),
			'');
		$text .= _if( $desty->atMost(0) )->then(
				$desty->add(2147483648),
				$up->set(),
				$angleswap->toggle(),
			'');
		for($i=$maxpower2; $i>=0; $i--) {
			$k=pow(2, $i);
			$text .= _if( $originy->atLeast($k), $desty->atLeast($k) )->then(
					$originy->subtract($k),
					$desty->subtract($k),
					$tempdc->add($k),
					$ydist->add($k),
				'');
		}
		for($i=$maxpower2; $i>=0; $i--) {
			$k=pow(2, $i);
			$text .= _if( $tempdc->atLeast($k) )->then(
					$originy->add($k),
					$desty->add($k),
					$tempdc->subtract($k),
				'');
		}
		$text .= _if( $up->is_clear() )->then(
				$originy->subtract(2147483648),
			'');
		$text .= _if( $up->is_set() )->then(
				$desty->subtract(2147483648),
			'');
			
		
		//FIND DOMINANT DIRECTION
		$maxpower1 = getBinaryPower( min( $xdist->Max, $ydist->Max) );
		$maxpower2 = getBinaryPower( max( $xdist->Max, $ydist->Max) );
		
		$domdist = new TempDC( max( $xdist->Max, $ydist->Max) );
		$recdist = new TempDC( min( $xdist->Max, $ydist->Max) * 10000 );
	
		for($i=$maxpower1; $i>=0; $i--) {
			$k=pow(2, $i);
			$text .= _if( $xdist->atLeast($k), $ydist->atLeast($k) )->then(
					$xdist->subtract($k),
					$ydist->subtract($k),
					$recdist->add($k * 10000),
					$domdist->add($k),
				'');
		}		
		$text .= _if( $ydist->atLeast(1) )->then(
				$xdist->add(2147483648),
			'');
		$text .= _if( $ydist->atMost(0) )->then(
				$ydist->add(2147483648),
				$angleswap->toggle(),
			'');
		for($i=$maxpower2; $i>=0; $i--) {
			$k=pow(2, $i);
			$text .= _if( $xdist->atLeast($k), $ydist->atLeast($k) )->then(
					$xdist->subtract($k),
					$ydist->subtract($k),
					$domdist->add($k),
				'');
		}
		
		
		//FIX 0/0
		$text .= _if( $recdist->Exactly(0), $domdist->Exactly(0) )->then(
				$domdist->setTo(1),
				$up->set(),
			'');
		
		//CALCULATE SLOPE
		$text .= $this->roundedQuotientOf($recdist, $domdist);
		
		//ARCTANGENT
		$text .= $this->arctangent($this);
		
		
		//360 DEGREE ANGLE
		$text .= _if( $angleswap->is_set() )->then(
				$tempdc->setTo(360),
				$tempdc->subtract($this),
				$tempdc->max(360),
				$this->becomeDel($tempdc),
			'');
		$text .= _if( $left->is_set(), $up->is_set() )->then(
				$this->add(360),
			'');
		$text .= _if( $left->is_set(), $up->is_clear() )->then(
				$this->add(720),
			'');
		$text .= _if( $left->is_clear(), $up->is_clear() )->then(
				$this->add(1080),
			'');

		
		$text .=  $xdist->kill().
					$ydist->kill().
					$recdist->kill().
					$domdist->kill().
					$left->kill().
					$up->kill().
					$angleswap->kill();
		
		return $text;
	}
	
	
	public function getAngleAndComponents($x, $y) {
		return $this->getComponents($x, $y);
	}
	
	
	// DISTANCE !
	/*
		� Returns the distance from the point ($x1, $y1) to the point ($x2, $y2) with a resolution of $resolutionMultiplier.
		� If only two arguments are provided, returns the distance of the the horizontal (argument 1) and the vertical (argument 2).
		� NOTE: because this system involves squaring numbers and taking the square root, the numbers become large very quickly. This function will only work with smaller inputs.
		� Format:
			- $dc->distance($x1, $y1, $x2, $y2, $resolutionMultiplier) is analogous to $dc = sqrt( ($x1-$x2)*($x1-$x2) + ($y1-$y2)*($y1-$y2) )*$resolutionMultiplier
			- $dc->distance($xdist, $ydist, $resolutionMultiplier) is analogous to $dc = sqrt( $xdist*$xdist + $ydist*$ydist )*$resolutionMultiplier
			- $x1 must be a deathcounter
			- $y1 must be a deathcounter
			NOTE: if you wish to use distances instead of points, the third argument may be used as the $resolutionMultiplier (see next two lines)
			- $x2 must be a deathcounter if 4-5 arguments are used
			- $x2 must be a constant (integer) if 3 arguments are used
			- $y2 must be a deathcounter
			- $resolutionMultiplier must be a constant (integer)
		� Max values:
			- argument 1: 2147483647
			- argument 2: 2147483647
			- argument 3: 2147483647
			- argument 4: 2147483647
			- argument 5: 2147483647
		� Low-end specifics:
			- trigger number: 190
			- temp switch number: 1
			- temp deathcounters: 1
		� High-end specifics:
			- N/A
		� Maxed specifics:
			- N/A
	*/
	public function distance($x1, $y1, $x2=NULL, $y2=NULL, $resolutionMultiplier=1){
		//ERROR
		if ( func_num_args() < 2 || func_num_args() > 5 ) {
			Error('COMPILER ERROR FOR DISTANCE(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 2-5: DEATHCOUNTERS FOR THE COORDINATES/DISTANCES, CONSTANTS (INTEGERS) FOR THE RESOLUTION MULTIPLIER (if used))');
		}
		if (!($x1 instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR DISTANCE(): ARGUMENT 1 NEEDS TO BE A DEATHCOUNTER');
		}
		if (!($y1 instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR DISTANCE(): ARGUMENT 2 NEEDS TO BE A DEATHCOUNTER');
		}
		if(func_num_args() == 3){
			if (!(is_numeric($x2))) {
				Error('COMPILER ERROR FOR DISTANCE(): ARGUMENT 3 NEEDS TO BE A CONSTANT (INTEGER)');
			}
		}
		if (func_num_args() > 3 && !($x2 instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR DISTANCE(): ARGUMENT 3 NEEDS TO BE A DEATHCOUNTER');
		}
		if (func_num_args() > 3 && !($y2 instanceof Deathcounter)) {
			Error('COMPILER ERROR FOR DISTANCE(): ARGUMENT 4 NEEDS TO BE A DEATHCOUNTER');
		}
		if (func_num_args() == 5 && !(is_numeric($resolutionMultiplier))) {
			Error('COMPILER ERROR FOR DISTANCE(): ARGUMENT 5 NEEDS TO BE A CONSTANT (INTEGER)');
		}
		
		
		//2 ARGUMENTS
		if( func_num_args() == 2 || func_num_args() == 3 ){
			$thisMax = $this->Max;
			$ydist = new TempDC( $y1->Max * $y1->Max );
			$text = '';
			if( func_num_args() == 3 ){
				$resolutionMultiplier = $x2;
			}
			
			$text .= $this->squared($x1);
			$this->Max = $x1->Max * $x1->Max;
			$text .= $ydist->squared($y1);
			$ydist->Max = $y1->Max * $y1->Max;
			$text .= $this->add($ydist);
			if( $resolutionMultiplier > 1 ){
				$text .= $this->multiplyBy($resolutionMultiplier * $resolutionMultiplier);
				$this->Max = $this->Max * $resolutionMultiplier * $resolutionMultiplier;
			}

			$text .= $this->roundedSquareRoot();

			$this->Max = $thisMax;

			$text .= $ydist->kill();
			
			return $text;
		}
		
		
		//4 ARGUMENTS
		$thisMax = $this->Max;
		$this->Max = max( $x1->Max, $x2->Max );
		$ydist = new TempDC( max( $y1->Max, $y2->Max) );

		$text = $this->setTo(0);

		$text .= $this->absDifference($x1, $x2);
		$text .= $ydist->absDifference($y1, $y2);
		
		$text .= $this->squared();
		$this->Max = $this->Max * $this->Max;
		$text .= $ydist->squared();
		$ydist->Max = $ydist->Max * $ydist->Max;
		$text .= $this->add($ydist);
		if( $resolutionMultiplier > 1 ){
			$text .= $this->multiplyBy($resolutionMultiplier * $resolutionMultiplier);
			$this->Max = $this->Max * $resolutionMultiplier * $resolutionMultiplier;
		}
		
		
		$text .= $this->roundedSquareRoot();

		$this->Max = $thisMax;
		
		$text .= $ydist->kill();

		return $text;
	}
	
	public function leaderboard($label = null){
		$index = GetUnitID($this->Unit) + 228;
		
		if( $label === null ){
			foreach ($GLOBALS as $key => $val) {
			   if ($val instanceof Deathcounter) {
				   if( $val->randomindex == $this->randomindex ){
					   $players = '';
					   if( $this->PlayerClass ) {
						   foreach( $this->PlayerClass->PlayerList as $p){
							   $players .= ' '. GetPlayerShorthand($p);
						   }
						   $players = "($players )";
					   }
					   elseif ($this->Player == CP || $this->Player == Allies || $this->Player == Foes || $this->Player == AllPlayers ) { 
						   $players = ''; 
					   }
					   else { 
						   $players = '(in '.GetPlayerShorthand($this->Player).')'; 
					   }
					   $label = "\$$key$players" ;
				   }
			   }
			}
		}
		
		return LeaderBoardKills($label, $index);
	}
	
	
	public function loadAddressValue($address, $min, $max){
		if( func_num_args() < 3 )
			Error('You need to pass in an address, min, and max');
		if( !is_numeric($address) )
			Error('Expecting a numerical value for the address (e.g. 0x6284B8 or 6456504)');
		//if( !is_numeric($offset) )
		//	Error('Expecting a numerical value for the offset (e.g. 0x00 or 256)');
		if( !is_numeric($min) )
			Error('Expecting a numerical value for the min (e.g. 0 or 100)');
		if( !is_numeric($max) )
			Error('Expecting a numerical value for the max (e.g. 255)');
		
		list($player, $offset) = explode('.', (0.25*$address-1452249) );
		$offset *= 0.4;
		
		if($offset > 3){
			Error("Invalid offset: $offset; How did that happen?");
		}
		
		$text = '';
		for($i = $min; $i<=$max; $i++){
			if( $offset == 0 ){
				$text .= 
				_if( Memory($player, Exactly, $i) )->then(
					$this->setTo($i)
				);
			} else {
				$text .=
				_if( Memory($player, AtLeast, pow(256,$offset)*$i), Memory($player, AtMost, (pow(256,$offset)*($i+1)-1)) )->then(
					$this->setTo($i)
				);
			}
		}
		
		return $text;
	}
	
	public function loadPlayerMemory($player, $min, $max){
		if( func_num_args() < 3 )
			Error('You need to pass in an address, min, and max');
		if( !is_numeric($player) )
			Error('Expecting a numerical value for the player (e.g. 15202)');
		//if( !is_numeric($offset) )
		//	Error('Expecting a numerical value for the offset (e.g. 0x00 or 256)');
		if( !is_numeric($min) )
			Error('Expecting a numerical value for the min (e.g. 0 or 100)');
		if( !is_numeric($max) )
			Error('Expecting a numerical value for the max (e.g. 255)');
		
		$text = '';
		for($i = $min; $i<=$max; $i++){
			$text .= _if( Memory($player, Exactly, $i) )->then(
				$this->setTo($i)
			);
		}
		
		return $text;
	}
	
	
	public function getFourthByte($line, $block){
		// Error
		if ( $line < 1 || $line > 11 ) {
			Error('Error: $line must be between 1 and 11');
		}
		if ( $block < 1 || $block > 54 ) {
			Error('Error: $block must be between 1 and 54');
		}
		
		$base = 0;
		$text = '';
		
		if( $line === 1  ){ $base = 186879; } if( $line === 2  ){ $base = 186933; }
		if( $line === 3  ){ $base = 186988; } if( $line === 4  ){ $base = 187042; }
		if( $line === 5  ){ $base = 187097; } if( $line === 6  ){ $base = 187151; }
		if( $line === 7  ){ $base = 187206; } if( $line === 8  ){ $base = 187260; }
		if( $line === 9  ){ $base = 187315; } if( $line === 10 ){ $base = 187369; }
		if( $line === 11 ){ $base = 187424; }
		
		for($i=0;$i<=255;$i++){
			$epd = new EPD($base + $block - 1);
			$text .=
			_if( $epd->between( pow(2,24)*$i, pow(2,24)*($i+1)-1 ) )->then(
				$this->setTo($i),
			'');
		}
		
		return $text;
	}
	

	
}



