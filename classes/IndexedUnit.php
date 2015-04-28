<?php
class IndexedUnit extends UnitGroup{
	
	// Properties
	protected $Index;
	
	// Constructor
	public function __construct($index, $unit=NULL, $player=NULL, $location=NULL){
		$this->Index = $index;
		if( func_num_args() > 1 && $unit !== NULL ){
			ValidUnitCheck($unit);
		}
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
    //CONVERSION FUNCTIONS
    //
	
    //Unit index pointer: u = 0: 5885096, u > 0: 6455960 - 336*(u-1)
    public function convertToPointer($index=NULL){
        if(func_num_args() == 0){
            $index = $this->Index;
        }
        if($index == 0){
            return 5885096;
        }
        return 6455960 - 336*($index-1);
    }
	
    //Function to output EPD address given the EPD function offset
    //for EPD function offset, see: http://www.staredit.net/topic/10471/
    public function convertToEPD($firstunitEPDindex){
        if($this->Index == 0){
            return $firstunitEPDindex;
        }
        return $firstunitEPDindex + 142716 - 84*($this->Index-1);
    }
	
	
	
	/////
    // CONDITIONS
    //
	
	//HEALTH POINTS: AtLeast 256*a, AtMost 256*a + 255
	public function health($qmod, $n){
		$player = IndexedUnit::convertToEPD(19027);
		return Memory($player, $qmod, $n*256);
	}
	public function exactHealth($qmod, $n){
		$player = IndexedUnit::convertToEPD(19027);
		return Memory($player, $qmod, $n);
	}
	
	//CURRENT COORDINATE: Exactly x + 65536*y
	public function currentCoordinate($qmod, $n){
		$player = IndexedUnit::convertToEPD(19035);
		return Memory($player, $qmod, $n);
	}
	
	public function currentXCoordinate($qmod, $n){
		$player = IndexedUnit::convertToEPD(19036);
		return Memory($player, $qmod, $n*256);
	}
	
	public function currentYCoordinate($qmod, $n){
		$player = IndexedUnit::convertToEPD(19037);
		return Memory($player, $qmod, $n*256);
	}
	
	//DIRECTION: AtLeast 16777216*o, AtMost 16777216*o + 16777215
	public function direction($qmod, $n){
		$player = IndexedUnit::convertToEPD(19043);
		return Memory($player, $qmod, $n*16777216);
	}
	
	//ATTACK COOLDOWN: AtLeast 65536*c
	public function attackCooldown($qmod, $n){
		if($qmod == AtLeast){
			$player = IndexedUnit::convertToEPD(19046);
			return Memory($player, $qmod, $n*65536);
		}
		else if($qmod == AtMost){
			$player = IndexedUnit::convertToEPD(19046);
			return Memory($player, $qmod, $n*65536+65535);
		}
		else if($qmod == Exactly){
			$player = IndexedUnit::convertToEPD(19046);
			return Memory($player, AtLeast, $n*65536).
				Memory($player, AtMost, $n*65536+65535);
		}
		
	}
	
	//ORDER COORDINATE: Exactly x + 65536*y
	public function orderCoordinate($qmod, $n){
		$player = IndexedUnit::convertToEPD(19047);
		return Memory($player, $qmod, $n);
	}
	public function orderYCoordinate($qmod, $n){
		$player = IndexedUnit::convertToEPD(19047);
		return Memory($player, $qmod, $n*65536);
	}
	
	//TARGET ID: (Unit index pointer)
	public function targetID($qmod, $n){
		$player = IndexedUnit::convertToEPD(19048);
		return Memory($player, $qmod, $n);
	}
	
	//SHIELD POINTS: AtLeast 256*a, AtMost 256*a + 255
	public function shield($qmod, $n){
		$player = IndexedUnit::convertToEPD(19049);
		return Memory($player, $qmod, $n*256);
	}
	public function exactShield($qmod, $n){
		$player = IndexedUnit::convertToEPD(19049);
		return Memory($player, $qmod, $n);
	}
	
	//KILL COUNT: AtLeast 16777216*a, AtMost 16777216*a + 16777215
	public function killCount($qmod, $n){
		$player = IndexedUnit::convertToEPD(19060);
		return Memory($player, $qmod, $n*16777216);
	}
	
	//ENERGY POINTS: AtLeast 16777216*a, AtMost 16777216*a + 16777215
	public function energyPoints($qmod, $n){
		$player = IndexedUnit::convertToEPD(19065);
		return Memory($player, $qmod, $n*16777216);
	}
	public function exactEnergyPoints($qmod, $n){
		$player = IndexedUnit::convertToEPD(19065);
		return Memory($player, $qmod, $n*65536);
	}
	
	//RALLY COORDINATE: Exactly x + 65536*y
	public function rallyCoordinate($qmod, $n){
		$player = IndexedUnit::convertToEPD(19087);
		return Memory($player, $qmod, $n);
	}
	public function rallyYCoordinate($qmod, $n){
		$player = IndexedUnit::convertToEPD(19087);
		return Memory($player, $qmod, $n*65536);
	}
	
	//MATRIX DAMAGE ABSORPTION Exactly a*16777216
	public function matrixDamageAbsorption($qmod, $n){
		$player = IndexedUnit::convertToEPD(19093);
		return Memory($player, $qmod, $n*16777216);
	}
	
	// MATRIX/STIM/ENSNARE/LOCKDOWN TIMER:
	// MATRIX TIMER: WON'T WORK IF STIMMED, ENSNARED, OR LOCKED DOWN
	public function matrixTimer($qmod, $n){
		$player = IndexedUnit::convertToEPD(19094);
		return Memory($player, $qmod, $n);
	}
	// STIM TIMER: PROBLEMS IF ENSNARED OR LOCKED DOWN
	public function stimTimer($qmod, $n){
		$player = IndexedUnit::convertToEPD(19094);
		return Memory($player, $qmod, $n*256);
	}
	// ENSNARE TIMER: PROBLEMS IF LOCKED DOWN
	public function ensnareTimer($qmod, $n){
		$player = IndexedUnit::convertToEPD(19094);
		return Memory($player, $qmod, $n*65536);
	}
	// LOCKDOWN TIMER
	public function lockdownTimer($qmod, $n){
		$player = IndexedUnit::convertToEPD(19094);
		return Memory($player, $qmod, $n*16777216);
	}
	// MAELSTROM TIMER
	public function maelstromTimer($qmod, $n){
		$player = IndexedUnit::convertToEPD(19098);
		return Memory($player, $qmod, $n);
	}
	
	// Boolean Unit States:
	public function isMaelstromed(){
		$player = IndexedUnit::convertToEPD(19098);
		return Memory($player, AtLeast, 1).
			   Memory($player, AtMost, 255);
	}
	public function isMatrixed(){
		$player = IndexedUnit::convertToEPD(19093);
		return Memory($player, AtLeast, 65536);
	}
	public function isBlind(){
		$player = IndexedUnit::convertToEPD(19097);
		return Memory($player, AtLeast, 16777216);
	}
	public function isLockedDown(){
		$player = IndexedUnit::convertToEPD(19094);
		return Memory($player, AtLeast, 16777216);
	}
	public function isEnsnared(){
		$player = IndexedUnit::convertToEPD(19094);
		return Memory($player, AtLeast, 65536).
			   Memory($player, AtMost, 16777215);
	}
	public function isStimmed(){
		$player = IndexedUnit::convertToEPD(19094);
		return Memory($player, AtLeast, 256).
			   Memory($player, AtMost, 65535);
	}
	
	
    //IS SELECTED BY
    //0x6284E8 161889+12*p+e SELECTED UNIT (SHARED): (Unit index pointer)
    public function isSelectedByP1(){
        $EPD = new EPD(161889);
        $n = IndexedUnit::convertToPointer();
        return $EPD->Exactly($n);
    }
    public function isSelectedByP2(){
        $EPD = new EPD(161901);
        $n = IndexedUnit::convertToPointer();
        return $EPD->Exactly($n);
    }
    public function isSelectedByP3(){
        $EPD = new EPD(161913);
        $n = IndexedUnit::convertToPointer();
        return $EPD->Exactly($n);
    }
    public function isSelectedByP4(){
        $EPD = new EPD(161925);
        $n = IndexedUnit::convertToPointer();
        return $EPD->Exactly($n);
    }
    public function isSelectedByP5(){
        $EPD = new EPD(161937);
        $n = IndexedUnit::convertToPointer();
        return $EPD->Exactly($n);
    }
    public function isSelectedByP6(){
        $EPD = new EPD(161949);
        $n = IndexedUnit::convertToPointer();
        return $EPD->Exactly($n);
    }
    public function isSelectedByP7(){
        $EPD = new EPD(161961);
        $n = IndexedUnit::convertToPointer();
        return $EPD->Exactly($n);
    }
    public function isSelectedByP8(){
        $EPD = new EPD(161973);
        $n = IndexedUnit::convertToPointer();
        return $EPD->Exactly($n);
    }
	
    //IS TARGETING
    //0x6284E8 161889+12*p+e SELECTED UNIT (SHARED): (Unit index pointer)
    public function isTargeting($index){
        if($index instanceof IndexedUnit){
            $index = $index->Index;
        }
        $n = IndexedUnit::convertToPointer($index);
        return IndexedUnit::targetID(Exactly,$n);
    }

	//ORDER_
	public function order_stop(){
		$player = IndexedUnit::convertToEPD(19044);
		return Memory($player, AtLeast, 768).
				Memory($player, AtMost, 775);
	}
	public function order_move(){
		$player = IndexedUnit::convertToEPD(19044);
		return Memory($player, AtLeast, 67072).
			Memory($player, AtMost, 67079);
	}
	public function order_attack(){
		$player = IndexedUnit::convertToEPD(19044);
		return Memory($player, AtLeast, 69120).
			Memory($player, AtMost, 69127);
	}
	public function order_patrol(){
		$player = IndexedUnit::convertToEPD(19044);
		return Memory($player, AtLeast, 104448).
			Memory($player, AtMost, 104455);
	}
	public function order_hold(){
		$player = IndexedUnit::convertToEPD(19044);
		return Memory($player, AtLeast, 92928).
			Memory($player, AtMost, 92935);
	}
	public function order_holdPosition(){
		$player = IndexedUnit::convertToEPD(19044);
		return Memory($player, AtLeast, 92928).
			Memory($player, AtMost, 92935);
	}
	
	// Index is the next available index ID
	public function isNAIID(){
		return Memory(161845, Exactly, IndexedUnit::convertToPointer());
	}
	
	
	// UnitType is the specified unit/unitid
	public function isUnitType($unit){
		if(is_string($unit)){
			ValidUnitCheck($unit);
			$unit = GetUnitID($unit);
		}
		if(!is_int($unit)){
			Error("Expecting UnitID integer or a Unit's string name");
		}
		$player = IndexedUnit::convertToEPD(19050);
		return Memory($player, Exactly, $unit);
	}
	
	
	/////
    // ACTIONS
    //
	
    //GET HEALTH!
	public function getHealth(Deathcounter $dc, $maxvalue){
		
        $text = '';
        $ignore = new TempSwitch();

        for($i=$maxvalue; $i>0; $i--){
            $text .= _if( $ignore->is_clear(), $this->health(AtLeast, $i) )->then(
                $dc->setTo($i),
                $ignore->set(),
            '');
        }
        $text .= _if($ignore->is_clear())->then(
            $dc->setTo(0),
            '');
        $text .= _if($ignore->is_set())->then(
            $ignore->kill(),
        '');

        return $text;
    }


    //GET CURRENT X COORDINATE!
	public function getCurrentXCoordinate(Deathcounter $dc, $maxtile){

        $text = '';
        $ignore = new TempSwitch();

        for($i=$maxtile*32-1; $i>0; $i--){
            $text .= _if( $ignore->is_clear(), $this->currentXCoordinate(AtLeast, $i) )->then(
                $dc->setTo($i),
                $ignore->set(),
            '');
        }
        $text .= _if($ignore->is_clear())->then(
            $dc->setTo(0),
            '');
        $text .= _if($ignore->is_set())->then(
            $ignore->kill(),
            '');

        return $text;
    }
    //GET CURRENT Y COORDINATE!
    public function getCurrentYCoordinate(Deathcounter $dc, $maxtile){

        $text = '';
        $ignore = new TempSwitch();

        for($i=$maxtile*32-1; $i>0; $i--){
            $text .= _if( $ignore->is_clear(), $this->currentYCoordinate(AtLeast, $i) )->then(
                $dc->setTo($i),
                $ignore->set(),
            '');
        }
        $text .= _if($ignore->is_clear())->then(
            $dc->setTo(0),
            '');
        $text .= _if($ignore->is_set())->then(
            $ignore->kill(),
            '');
	    
        return $text;
    }
	
	
    //GET DIRECTION! (NOTE: MOVEMENT DIRECTION, NOT ANIMATION DIRECTION)
    public function getDirection($dc){

        $text = '';
        $ignore = new TempSwitch();

        for($i=255; $i>0; $i--){
            $text .= _if( $ignore->is_clear(), $this->direction(AtLeast, $i) )->then(
                $dc->setTo($i),
                $ignore->set(),
            '');
        }
        $text .= _if($ignore->is_clear())->then(
            $dc->setTo(0),
        '');
        $text .= $ignore->kill();

        return $text;
    }
	
    public function getRealAngle($min, $max, Deathcounter $dc){
	    
        $text = '';
        $ignore = new TempSwitch();
	    
	    $scmin = round($min*(64 / 90)); 
	    $scmax = round($max*(64 / 90));
	    
	    if( $max >= $min ){
	        for($i=$scmax; $i>$scmin; $i-- ) {
	            $text .= _if( $ignore->is_clear(), $this->direction(AtLeast, $i) )->then(
	                $dc->setTo( round(($i+0.5)*(90 / 64)*4) ),
	                $ignore->set(),
	            '');
	        }
		} else{
		    for($i=255;$i>$scmin;$i--){
			    $text .= _if( $ignore->is_clear(), $this->direction(AtLeast, $i) )->then(
	                $dc->setTo($i),
	                $ignore->set(),
	            '');
		    }
		    
		    for($i=$scmax;$i>0;$i--){
			    $text .= _if( $ignore->is_clear(), $this->direction(AtLeast, $i) )->then(
	                $dc->setTo($i),
	                $ignore->set(),
	            '');
		    }
		    
	    }
        $text .= _if($ignore->is_clear())->then(
            $dc->setTo(0),
        '');
        $text .= $ignore->kill();

        return $text;
    }


    //GET ATTACK COOLDOWN!
    public function getAttackCooldown(Deathcounter $dc, $maxvalue){

        $text = '';
        $ignore = new TempSwitch();

        for($i=$maxvalue; $i>0; $i--){
            $text .= _if( $ignore->is_clear(), $this->attackCooldown(AtLeast, $i) )->then(
                $dc->setTo($i),
                $ignore->set(),
            '');
        }
        $text .= _if($ignore->is_clear())->then(
            $dc->setTo(0),
        '');
        $text .= _if($ignore->is_set())->then(
            $ignore->kill(),
        '');

        return $text;
    }


    //GET Y ORDER COORDINATE!
    public function getYOrderCoordinate(Deathcounter $dc, $maxvalue, $by = 1){
	    
        $text = '';
        $ignore = new TempSwitch();
	    
        for($i=$maxvalue*32-1; $i>0; $i -= $by){
            $text .= _if( $ignore->is_clear(), $this->orderYCoordinate(AtLeast, $i) )->then(
                $dc->setTo($i),
                $ignore->set(),
            '');
        }
        $text .= _if($ignore->is_clear())->then(
            $dc->setTo(0),
        '');
        $text .= _if($ignore->is_set())->then(
            $ignore->kill(),
        '');
	    
        return $text;
    }
	
    public function getYOrderRange(Deathcounter $dc, $mintile, $maxtile, $by = 1){
	    
        $text = '';
        $ignore = new TempSwitch();
	    
        for($i=$maxtile*32-1; $i>$mintile*32; $i -= $by){
            $text .= _if( $ignore->is_clear(), $this->orderYCoordinate(AtLeast, $i) )->then(
                $dc->setTo($i),
                $ignore->set(),
            '');
        }
        $text .= _if($ignore->is_clear())->then(
            $dc->setTo(0),
        '');
        $text .= _if($ignore->is_set())->then(
            $ignore->kill(),
        '');
	    
        return $text;
    }
	
	
    //GET TARGET INDEX!
    //RETURNS 1700 IF NOT ATTACKING ANYTHING, 1701 IF THE UNIT IS OUT OF THE $maxvalue BOUNDS
    public function getTargetID(Deathcounter $dc, $lowerbound, $upperbound = null){
	    
	    $maxvalue = 1699;
	    $minvalue = 0;
	    if( func_num_args() == 2 ){
		    $maxvalue = $lowerbound;
	    }
	    if( func_num_args() == 3 ){
		    $minvalue = $lowerbound;
		    $maxvalue = $upperbound;
	    }
	    
        $text = '';
        $ignore = new TempSwitch();
	    
        for($i=$maxvalue; $i>=$minvalue; $i--){
            if($i != $this->Index){
                $text .= _if( $this->isTargeting($i) )->then(
                    $dc->setTo($i),
                    $ignore->set(),
                '');
            }
        }
        $text .= _if($this->targetID(AtMost,5885095))->then(
            $dc->setTo(1700),
            $ignore->set(),
        '');
        $text .= _if($ignore->is_clear())->then(
            $dc->setTo(1701),
        '');
        $text .= _if($ignore->is_set())->then(
            $ignore->kill(),
        '');
	    
        return $text;
    }
	
	public function getSpecificTargetIDs(Deathcounter $dc, array $nums){
	    $text = '';
        $ignore = new TempSwitch();
		
		foreach($nums as $num){
			if($num instanceof IndexedUnit){
				$num = $num->Index;
			}
			if($num !== $this->Index){
				$text .= _if( $this->isTargeting($num) )->then( $dc->setTo($num), $ignore->set() );
			}
		}
		$text .= _if($ignore->is_clear())->then(
            $dc->setTo(1700),
        '');
        $text .= _if($ignore->is_set())->then(
            $ignore->kill(),
        '');
	    
        return $text;
    }
	
	//CHECK TARGET INDEX!
    //SETS SWITCH IF THE DC MATCHES THE TARGET INDEX
    public function checkTargetID(Deathcounter $dc, PermSwitch $switch, $lowerbound, $upperbound = null){
	    
	    $maxvalue = 1699;
	    $minvalue = 0;
	    if( func_num_args() == 3 ){
		    $maxvalue = $lowerbound;
	    }
	    if( func_num_args() == 4 ){
		    $minvalue = $lowerbound;
		    $maxvalue = $upperbound;
	    }
	    
        $text = '';
	    
        for($i=$maxvalue; $i>=$minvalue; $i--){
            if($i != $this->Index){
                $text .= _if( $dc->exactly($i), $this->isTargeting($i) )->then(
                    $switch->set(),
                '');
            }
        }
	    
        return $text;
    }
	
	public function checkSpecificTargetIDs(Deathcounter $dc, PermSwitch $switch, Array $nums){
	    $text = '';
		
		foreach($nums as $num){
			if($num instanceof IndexedUnit){
				$num = $num->Index;
			}
			if($num != $this->Index){
				$text .= _if( $dc->exactly($num), $this->isTargeting($num) )->then( $switch->set() );
			}
		}
	    
        return $text;
    }
	
	
    //GET SHIELD!
    public function getShield(Deathcounter $dc, $maxvalue){
	    
        $text = '';
        $ignore = new TempSwitch();
	    
        for($i=$maxvalue; $i>0; $i--){
            $text .= _if( $ignore->is_clear(), $this->shield(AtLeast, $i) )->then(
                $dc->setTo($i),
                $ignore->set(),
                '');
        }
        $text .= _if($ignore->is_clear())->then(
            $dc->setTo(0),
        '');
        $text .= _if($ignore->is_set())->then(
            $ignore->kill(),
        '');

        return $text;
    }
	
	
    //GET KILL COUNT!
    public function getKillCount(Deathcounter $dc, $maxvalue=255){

        $text = '';
        $ignore = new TempSwitch();

        for($i=min($maxvalue,255); $i>0; $i--){
            $text .= _if( $ignore->is_clear(), $this->killCount(AtLeast, $i) )->then(
                $dc->setTo($i),
                $ignore->set(),
                '');
        }
        $text .= _if($ignore->is_clear())->then(
            $dc->setTo(0),
        '');
        $text .= _if($ignore->is_set())->then(
            $ignore->kill(),
        '');

        return $text;
    }


	
	
}

