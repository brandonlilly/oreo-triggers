<?php

class DCStorage{
	
	// Properties
	private $dc = array();
	private $dcmax = array();
	private $dcnum = array();
	private $storage = array();
	private $max = array();
	private $truemax = array();
	private $size = 0;
	private $player;
	
	// Constructor
	public function __construct($player, $anydcs){
		
		$this->max[0] = 1;
		$this->dcnum = func_num_args()-1;
		$j = 1;
		if($player instanceof Deathcounter){
			$this->dcnum++;
			$j--;
			$player = NULL;
		}
		$this->player = $player;
		
		for($i=0; $i<$this->dcnum; $i++, $j++){
			$this->dc[$i] = func_get_arg($j);
			if(!($this->dc[$i] instanceof Deathcounter)){
				Error("COMPILER ERROR FOR DCSTORAGE(): ARGUMENT $i NEEDS TO BE A DEATHCOUNTER");
			}
			
			$this->dcmax[$i] = $this->dc[$i]->Max;
			if($this->max[$this->size]*($this->dc[$i]->Max+1) <= 4294967296){
				$this->max[$this->size] *= ($this->dc[$i]->Max+1);
			} else {
				$tempmax = $this->dc[$i]->Max;
				$max=$this->max[$this->size];
				while($max*2 <= 4294967296){
					$max *= 2;
					$tempmax = $tempmax >> 1;
				}
				$this->truemax[$this->size] = ($max-1);
				$this->size++;
				$this->max[$this->size] = ($tempmax+1);
			}
		}
		$this->truemax[$this->size] = ($this->max[$this->size]-1);
		
		for($i=0; $i<=$this->size; $i++){
			$this->storage[$i] = new Deathcounter($player);
		}

	}


	/////
	//STORE
	//
	public function store($playerSpecifier=NULL){
		//ERROR
		if(func_num_args() > 1){
			Error('COMPILER ERROR FOR STORE(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 0 OR 1: PLAYER');
		}
		if(func_num_args() == 1 && !IsStandardPlayer($playerSpecifier)){
			Error('COMPILER ERROR FOR STORE(): ARGUMENT MUST SPECIFY A PLAYER (P1, P2, P3, P4, P5, P6, P7, P8, Allies, Foes, AllPlayers, CP)');
		}
		if(func_num_args() == 1 && $this->player == NULL){
			Error('COMPILER ERROR FOR STORE(): ARGUMENT SPECIFIES A PLAYER, BUT NO PLAYER WAS SPECIFIED IN DCSTORAGE DECLARATION');
		}
		
		$size = $this->size;
		$max = $this->max[$size];
		$tempdc = new TempDC();
		
		$text = '';
		if(func_num_args()==0){
			$storage = $this->storage[$size];
			foreach($this->storage as $storage){
				$text .= $storage->setTo(0);
			}
		}else{
			$storage = clone $this->storage[$size];
            $storage->Player = $playerSpecifier;
			foreach($this->storage as $stor){
				$asdf = clone $stor;
                $asdf->Player = $playerSpecifier;
				$text .= $asdf->setTo(0);
			}
		}
		
		for($i=$this->dcnum-1; $i>=0; $i--){
			if($this->dcmax[$i] < $max){
				$max /= ($this->dcmax[$i]+1);
				for($j=getBinaryPower($this->dcmax[$i]); $j>=0; $j--){
					$k = pow(2,$j);
					$text .= _if( $this->dc[$i]->atLeast($k) )->then(
					    $this->dc[$i]->subtract($k),
						$storage->add($max*$k),
						$tempdc->add($k),
					'');
				}
				for($j=getBinaryPower($this->dcmax[$i]); $j>=0; $j--){
					$k = pow(2,$j);
					$text .= _if( $tempdc->atLeast($k) )->then(
					    $this->dc[$i]->add($k),
						$tempdc->subtract($k),
					'');
				}
			} else{
				$dcpow = getBinaryPower($this->dcmax[$i]);
				for($j=getBinaryPower($max-1); $j>=0; $j--, $dcpow--){
					$k1 = pow(2,$j);
					$k2 = pow(2,$dcpow);
					$text .= _if( $this->dc[$i]->atLeast($k2) )->then(
					    $this->dc[$i]->subtract($k2),
						$storage->add($k1),
						$tempdc->add($k2),
					'');
				}
				$size--;
				if(func_num_args()==0){
					$storage = $this->storage[$size];
				}else{
					$storage = clone $this->storage[$size];
	                $storage->Player = $playerSpecifier;
				}
				
				$max = $this->max[$size];
				for($j=$dcpow; $j>=0; $j--){
					$k = pow(2,$j);
					$text .= _if( $this->dc[$i]->atLeast($k) )->then(
					    $this->dc[$i]->subtract($k),
						$storage->add($max*$k),
						$tempdc->add($k),
					'');
				}
				for($j=getBinaryPower($this->dcmax[$i]); $j>=0; $j--){
					$k = pow(2,$j);
					$text .= _if( $tempdc->atLeast($k) )->then(
					    $this->dc[$i]->add($k),
						$tempdc->subtract($k),
					'');
				}
			}
		}
		$text .= $tempdc->kill();
		
		return $text;
		
	}

	
	/////
	//RETRIEVE
	//
	public function retrieve($playerSpecifier=NULL){
		//ERROR
		if(func_num_args() > 1){
			Error('COMPILER ERROR FOR RETRIEVE(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 0 OR 1: PLAYER');
		}
		if(func_num_args() == 1 && !IsStandardPlayer($playerSpecifier)){
			Error('COMPILER ERROR FOR RETRIEVE(): ARGUMENT MUST SPECIFY A PLAYER (P1, P2, P3, P4, P5, P6, P7, P8, Allies, Foes, AllPlayers, CP)');
		}
		if(func_num_args() == 1 && $this->player == NULL){
			Error('COMPILER ERROR FOR RETRIEVE(): ARGUMENT SPECIFIES A PLAYER, BUT NO PLAYER WAS SPECIFIED IN DCSTORAGE DECLARATION');
		}
		
		$size = $this->size;
		$max = $this->max[$size];
		$tempdc = new TempDC();
		
		$text = '';
		foreach($this->dc as $dc){
			$text .= $dc->setTo(0);
		}
		if(func_num_args()==0){
			$storage = $this->storage[$size];
		}else{
			$storage = clone $this->storage[$size];
            $storage->Player = $playerSpecifier;
		}
		for($i=$this->dcnum-1; $i>=0; $i--){
			if($this->dcmax[$i] < $max){
				$max /= ($this->dcmax[$i]+1);
				for($j=getBinaryPower($this->dcmax[$i]); $j>=0; $j--){
					$k = pow(2,$j);
					$text .= _if( $storage->atLeast($max*$k) )->then(
					    $this->dc[$i]->add($k),
						$storage->subtract($max*$k),
						$tempdc->add($max*$k),
					'');
				}
			} else{
				$dcpow = getBinaryPower($this->dcmax[$i]);
				for($j=getBinaryPower($max-1); $j>=0; $j--, $dcpow--){
					$k1 = pow(2,$j);
					$k2 = pow(2,$dcpow);
					$text .= _if( $storage->atLeast($k1) )->then(
					    $this->dc[$i]->add($k2),
						$storage->subtract($k1),
						$tempdc->add($k1),
					'');
				}
				for($j=getBinaryPower($this->truemax[$size]); $j>=0; $j--){
					$k = pow(2,$j);
					$text .= _if( $tempdc->atLeast($k) )->then(
					    $storage->add($k),
						$tempdc->subtract($k),
					'');
				}
				$size--;
				if(func_num_args()==0){
					$storage = $this->storage[$size];
				}else{
					$storage = clone $this->storage[$size];
	                $storage->Player = $playerSpecifier;
				}
				$max = $this->max[$size];
				for($j=$dcpow; $j>=0; $j--){
					$k = pow(2,$j);
					$text .= _if( $storage->atLeast($max*$k) )->then(
					    $this->dc[$i]->add($k),
						$storage->subtract($max*$k),
						$tempdc->add($max*$k),
					'');
				}
			}
		}
		if(func_num_args()==0){
			$storage = $this->storage[0];
		}else{
			$storage = clone $this->storage[0];
            $storage->Player = $playerSpecifier;
		}
		for($i=getBinaryPower($this->truemax[0]); $i>=0; $i--){
			$k = pow(2,$i);
			$text .= _if( $tempdc->atLeast($k) )->then(
			    $storage->add($k),
				$tempdc->subtract($k),
			'');
		}
		$text .= $tempdc->kill();
		
		return $text;
		
	}

}