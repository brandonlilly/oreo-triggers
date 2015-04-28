<?php
class SwitchList{
	
	// Properties
	public $Switches = array();
	
	// Constructor
	public function __construct($switches = null){
		if ( !(is_array($switches) || ($switches instanceof PermSwitch) || $switches === null) ){
			Error('Error1: Must annex only switches, switchlists or arrays to switchlist (constructor) (this is probably Kaias\'s fault)');
		}
		// Add every switch 
		foreach(func_get_args() as $switch){
			if( is_array($switch) ){
				foreach($switch as $s){
					array_push($this->Switches, $s);
				}
			} else {
				array_push($this->Switches, $switch);
			}
		}
	}
	
	public function addSwitch($switches){
		if ( !( is_array($switches) || ($switches instanceof PermSwitch) || ($switches instanceof SwitchList)) ){
			Error('Error2: Must annex only switches, switchlists or arrays to switchlist (this is probably Kaias\'s fault)');
		}
		
		// Add every switch 
		foreach(func_get_args() as $switch){
			if( $switch instanceof PermSwitch ){
				$this->Switches[] = $switch;
			}
			elseif( is_array($switch) ){
				foreach($switch as $s){
					$this->addSwitch($s);
				}
			}
			elseif( $switch instanceof SwitchList ){
				$this->addSwitch($switch->Switches);
			}
			else {
				Error('Error3: Must annex only switches, switchlists or arrays to switchlist (this is probably Kaias\'s fault)');
			}
		}
	}
	
	
	
}



?>
