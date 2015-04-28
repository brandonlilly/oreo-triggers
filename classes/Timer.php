<?php

class Timer {
	
	public $Limit;
	public $DC;
	
	public function __construct($limit, $player = null){
		if ( $player ){
			$this->DC = new Deathcounter($player);
		} else {
			$this->DC = new Deathcounter();
		}
		$this->Limit = $limit;
		
		if($player != NULL ){
			$Player = $player;
		} else {
			$Player = new Player(AllPlayers);
			$Player->prepend->_if( ForemostPlayer(), $this->DC->between(1,pow(2,31)-1) )->then(
				$this->DC->subtract(1),
			'');
		}

		$Player->_if( $this->DC->between(1,pow(2,31)-1) )->then(
			$this->DC->subtract(1),
		'');
		
	}

	
	/////
	//CONDITIONS
	//
	
	public function expires(){
		return $this->DC->exactly(1);
	}
	
	public function expired(){
		return $this->DC->atMost(1);
	}
	
	public function isPaused(){
		return $this->DC->atLeast(pow(2,31));
	}
	
	public function notPaused(){
		return $this->DC->atMost(pow(2,31));
	}
	
	public function isRunning(){
		return $this->DC->between(2, pow(2,31));
	}
	
	public function atLeast($n){
		return orGroup( $this->DC->between($n+1, pow(2,31)-1), $this->DC->atLeast($n+pow(2,31)+1) );
	}
	
	public function atMost($n){
		return orGroup( $this->DC->atMost($n+1), $this->DC->between(pow(2,31), $n+pow(2,31)+1) );
	}
	
	
	/////
	//ACTIONS
	//
	
	public function start(){
		return $this->DC->setTo($this->Limit+1);
	}
	
	public function pause(){
		$text =
			_if( $this->DC->atMost(pow(2,31)-1) )->then(
				$this->DC->add(pow(2,31))
			);

		return $text;
	}
	
	public function resume(){
		$text = 
		_if( $this->DC->atLeast(pow(2,31)) )->then(
			$this->DC->subtract(pow(2,31))
		);
		
		return $text;
	}

	public function set($n){
		if( is_numeric($n) ){
			return $this->DC->setTo($n+1);
		}
		return  $this->DC->setTo($n).
				$this->DC->add(1);
	}

	public function reset(){
		return $this->DC->setTo($this->Limit+1);
	}

	public function add($n){
		return $this->DC->add($n);
	}
	
	public function subtract($n){
		return $this->DC->subtract($n).
				$this->DC->subtract(1).
				$this->DC->add(1);
	}
	
		
}

