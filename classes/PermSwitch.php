<?php
class PermSwitch{
	
	// Properties
	public $index;
	
	// Constructor
	public function __construct(){
		global $SwitchArray;
		
		foreach($SwitchArray as $key=>$value){
			if ( $value == 0 ) {
				$this->index = $key;
				$SwitchArray[$key] = 3;
				break;
			}
		}
	}
	
	public function __toString(){
        return $this->is_set();
    }
	
	public function kill() 			{ Error(NL.'Error: You shouldn\'t be killing a Permanent Switch!'.NL); }
	public function killTrigger() 	{ Error(NL.'Error: You shouldn\'t be killing a Permanent Switch!'.NL); }
	
	public function is_set(){
		return SwitchIsSet($this->index);
	}
	public function is_clear(){
		return SwitchIsClear($this->index);
	}
	
	public function set(){
		return SetSwitch($this->index, Set);
	}
	public function clear(){
		return SetSwitch($this->index, Clear);
	}
	public function toggle(){
		return SetSwitch($this->index, Toggle);
	}
	public function randomize(){
		return SetSwitch($this->index, Randomize);
	}
	
}

?>