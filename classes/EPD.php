<?php

class EPD{
	
	// Properties
	public $player;
	
	// Constructor
	public function __construct($player){
		
		if( func_num_args() != 1 ) {
			die("Invalid input for EPD class; only argument is address of EPD");
		}
		
		$this->player = $player;
	}


	/////
	//CONDITIONS
	//
	public function atLeast($n){
		return Memory($this->player, AtLeast, $n);
	}
	
	public function atMost($n){
		return Memory($this->player, AtMost, $n);
	}
	
	public function exactly($n){
		return Memory($this->player, Exactly, $n);
	}
	
	public function between($n, $m){
		return Memory($this->player, AtLeast, $n).
				Memory($this->player, AtMost, $m);
	}
	
	
	/////
	//ACTIONS
	//
	
	
	
}

?>