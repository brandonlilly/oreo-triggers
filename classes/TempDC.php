<?php

class TempDC extends Deathcounter {
	
	public function __construct($player = null, $max = null){
		global $DeathcounterUnits;
		$found = false;
		if ($player == CP || $player == Allies || $player == Foes || $player == AllPlayers ) {
			$this->Player = $player;
			if ( $max ) { $this->Max = $max; }
			
			foreach($DeathcounterUnits as $unit => $playerArray) {
				$availablecount = 0;
				foreach($playerArray as $playerIndex=>$SlotUsage) {
					if ( $SlotUsage === true || $SlotUsage == 2 ) { $availablecount++; } 
				}
				if ( $availablecount == 8 ) {
					foreach($playerArray as $playerIndex=>$slot) {
						 $DeathcounterUnits[$unit][$playerIndex] = 3;
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
					if ( in_array($playerIndex,$player->PlayerList) && ( $SlotUsage === true || $SlotUsage == 2 ) ) {
						$availablecount++;
					}
				}
				if ( $availablecount == count($player->PlayerList) ) {
					foreach($playerArray as $playerIndex=>$slot) {
						if ( in_array($playerIndex,$player->PlayerList) ) {
							$DeathcounterUnits[$unit][$playerIndex] = 3;
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
			
			foreach($DeathcounterUnits as $unit => $playerArray) {
				
				foreach($playerArray as $playerIndex=>$SlotUsage) {
					if ( $SlotUsage === true || $SlotUsage == 2 ) {
						$DeathcounterUnits[$unit][$playerIndex] = 3;
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
	
	
	public function kill() {
		$cleartext = $this->setTo(0);
		
		// Release from deathcounter array
		global $DeathcounterUnits;
		if ( $this->PlayerClass instanceof Player ){
			foreach($this->PlayerClass->PlayerList as $plyr){
				$DeathcounterUnits[$this->Unit][$plyr] = 2;
			}
		} elseif ( $this->Player == CP || $this->Player == Allies || $this->Player == Foes || $this->Player == AllPlayers ) {
			$DeathcounterUnits[$this->Unit] = array( P1=>2, P2=>2, P3=>2, P4=>2, P5=>2, P6=>2, P7=>2, P8=>2);
		} else {
			$DeathcounterUnits[$this->Unit][$this->Player] = 2;
		}
		
		$this->Player = null;
		$this->Unit = null;
		$this->Max = null;
				
		return $cleartext;
	}
	
	// kill alias
	public function release(){ return $this->kill(); }
		
}

?>