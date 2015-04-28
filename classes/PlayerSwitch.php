<?php
class PlayerSwitch extends PermSwitch{
	
	// Constructor
	public function __construct($players = AllPlayers){
		parent::__construct();
		
		global $PlayerSwitchArray;
		if( !($players instanceof Player) ){
			$players = new Player($players);
		}
		$PlayerSwitchArray[] = array($this,$players);
	}
	
	public function kill() 			{ Error(NL.'Error: You shouldn\'t be killing a Player Switch!'.NL); }
	public function killTrigger() 	{ Error(NL.'Error: You shouldn\'t be killing a Player Switch!'.NL); }
	
}

?>