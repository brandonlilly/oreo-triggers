<?php
class TempSwitch extends PermSwitch{
	
	public function __construct(){
		global $SwitchArray;
		
		foreach($SwitchArray as $key=>$value){
			if ( $value <= 1 ) {
				$this->index = $key;
				$SwitchArray[$key] = 2;
				break;
			}
		}
		if( !$this->index ){
			global $SwitchRange;
			if( $SwitchRange === 255 ){
				Error("You've used too many switches!");
			} else {
				Error("You've used too many switches! You need to set a larger switch range for Oreo Triggers (do so in config.php)");
			}
		}
	}
	
	public function kill() {
		global $SwitchArray;
		$SwitchArray[$this->index] = 1;
		
		$text = SetSwitch($this->index, Clear);
		
		$this->index = null;
		
		return $text;
	}
	
	// kill alias
	public function release(){ return $this->kill(); }
	
	public function killTrigger() {
		global $SwitchArray;
		$SwitchArray[$this->index] = 1;
		
		$text = HEADING().
			       SwitchIsSet($this->index).
			    ACTIONS().
			       SetSwitch($this->index, Clear).
				   PreserveTrigger().
			    ENDT();
		
		$this->index = null;
		
		return $text;
	}
	
}

?>