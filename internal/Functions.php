<?php
	
	// Nested If Trigger
	function _if() {
		
		// Accumulate conditions
		$switchlist = new SwitchList();
		$conditions = AggrigateConditions(func_get_args(), $switchlist);
		
		$nestswitch = new TempSwitch();
		OrReplace($conditions, $switchlist, $nestswitch);
		return new IfClass($conditions, $switchlist, $nestswitch);
	}
	
	// Muted If Trigger
	function mute_if() {
		
		// Accumulate conditions
		$switchlist = new SwitchList();
		$conditions = AggrigateConditions(func_get_args(), $switchlist);
		
		OrReplace($conditions, $switchlist);
		return new IfClass($conditions, $switchlist, null, true);
	}
	
	
	function CreateCondition(TempSwitch $reserveswitch, TempSwitch $conditional, $actionstext){
		
		$text = ACTIONS().PreserveTrigger().$reserveswitch->set().$actionstext.ENDT().HEADING().$conditional->is_set().$reserveswitch->is_set();
		
		return array("$text", new SwitchList($reserveswitch, $conditional));
	}
	
	
	// Switch Trigger
	function _switch($deathcounter){
		if (!$deathcounter) { Error('You have to give your cases a value', E_USER_ERROR ); }
		return new SwitchClass($deathcounter, null, null, true);
	}
	
	// Switch Case Trigger
	function _case(){
		if (!func_num_args()) { Error('You have to give your cases a value', E_USER_ERROR ); }
		return new SwitchClass(null, func_get_args(), null);
	}
	
	// Switch Default Trigger
	function _default($actions){
		if (!$actions) { Error('You have to give your cases some actions', E_USER_ERROR ); }
		$actions = '';
		
		// Accumulate actions
		foreach(func_get_args() as $action){
			$actions .= $action;
		}
		
		return new SwitchClass(null,'Default',$actions);
	}
	
	// Formatting
	function HEADING() {
		$tOwner = PLAYERX;
		
		$argnum = func_num_args();
		if ( $argnum > 0 ) {
			$text = '';
			for ( $i=0; $i <= $argnum; $i++ ) {
				$arg = func_get_arg($i);
				if( is_int($arg) ) {
					$arg = 'Player '.$arg;
				}
				if ( $arg ) {
					$text .= $arg.',';
				}
			}
			$tOwner = substr($text, 0, -1);
		}
		if( Minted() ){
			return $tOwner.'<trig/>'.NL;
		}
		return  NL.'Trigger('.$tOwner.') {'.NL.
				   'Conditions:'.NL;
	}
	function ACTIONS() 		{
		//if( Minted() ){
		//	return NL;
		//}
		return 'Actions:'.NL;
	}
	function CONDITIONS() 	{
		if( Minted() ){
			return '';
		}
		return 'Conditions:'.NL;
	}
	function ENDT() 		{
		if( Minted() ){
			return Comment().'</trigger>'.NL;
		}
		return Comment().'}'.NL.NL.'//-----------------------------------------------------------------//'.NL;
	}
	
	
	// Other
	function getBinaryPower($n) {
		$k = 1;
		while ( $n >= pow(2,$k) ) {
			$k++;
		}
		return $k-1;
	}
	function CountTriggers($text){
		return substr_count($text, ENDT());
	}
	
	function AggrigateConditions($array, SwitchList &$switchlist, $add_ORs = false){
		$conditiontext = '';
		
		foreach($array as $condition){
			if( is_array($condition) ){
				list($c, $switches) = $condition;
				$switchlist->addSwitch($switches);
				
				if( isset($c) ){
					if($c instanceof Deathcounter){
						$conditiontext .= $c->atLeast(1);
					} else {
						$conditiontext .= $c;
					}
				}
				
			} else{
				if( isset($condition) ){
					if($condition instanceof Deathcounter){
						$conditiontext .= $condition->atLeast(1);
					} else {
						$conditiontext .= $condition;
					}
				}
			}
			if($add_ORs == true){
				$conditiontext .= _OR;
			}
		}
		
		// remove last _OR
		if($add_ORs == true){
			$conditiontext = substr($conditiontext, 0, -strlen(_OR));
		}
		
		return $conditiontext;
	}
	
	function AggrigateActions($array){
		$actiontext = '';
		
		foreach($array as $action){
			if($action !== null && $action !== e){
				if( is_string($action) ){
					$actiontext .= $action;
				} else{
					
					$type = gettype($action);
					if($type === gettype(new STDClass)){
						$type = get_class($action);
					}
					Error("Trigger was expecting a String for an Action, instead got a: $type");
				}
			}
		}
		
		return $actiontext;
	}
	
	function OutputTriggers($text, $linenumber = null, $prepend = false){
		// Remove extraneous triggers and replace playerx
		CullTriggers($text);
		PlayerReplace($text);
		
		// Count triggers
		global $TriggerCount;
		$trigcount = CountTriggers($text);
		$TriggerCount += $trigcount;
		
		// Insert Line analysis
		InsertAnalysis($linenumber,$trigcount);
		
		// If minted, output to XML
		if ( Minted() ){
			$xmltext = NL.str_replace(array('<cond_c>Memory</cond_c>',ACTIONS(),'</trigger>'), array('<cond_c>Deaths</cond_c><cond_u>0</cond_u>','',''), $text);
			
			if( !$prepend ){
				WriteToXML($xmltext);
			}
		}
		
		// Pretty up minted code
		if( Minted() && !OutputSuppressed() ){
			$text = str_replace(array(), '', $text);
			$search = array('<condition>', '<cond_c>', '<act_c>', '<action>' );
			$text = str_replace($search, '', $text);
			$search = array('<act_l>','</act_l>','<act_m>','</act_m>','<act_n>','</act_n>','<act_u>','</act_u>','<act_s>','</act_s>','<act_w>','</act_w>','<act_t>','</act_t>','<act_gs>','</act_gs>','<act_gf>',
							'</act_gf>','<cond_l>','</cond_l>','<cond_m>','</cond_m>','<cond_n>','</cond_n>','<cond_g>','</cond_g>','<cond_r>','</cond_r>','<cond_u>','</cond_u>','<cond_s>','</cond_s>','<adf/>');
			$text = str_replace($search, ' ', $text);
			$text = str_replace('</trigger>', '}'.NL,$text);
			$text = str_replace(array('</trig_group><trig_group>','</trig_group><trig/>'), ', ', $text);
			$text = str_replace('<trig_group>', 'Trigger{ ', $text);
			$text = str_replace(array('</act_c>','</cond_c>'), ':', $text);
			$text = str_replace(array('</condition>', '</action>'), '', $text);
		}
		
		if( OutputSuppressed() ){
			$text = '';
		}
		
		// And Output
		if( !$prepend ){
			echo $text;	
		} else {
			PrependTrigs($text, $xmltext);
		}
		
	}
	
	
	function PlayerReplace(&$text) {
		global $TriggerOwner;
		$text = str_replace(PLAYERX,$TriggerOwner,$text);
	}
	
	function CullTriggers(&$text) {
		global $SwitchArray;
		foreach($SwitchArray as $key=>$value) {
			if ( $value !== 0 && $value !== 5 ) {
				$pattern =
					array(
							HEADING().
							SwitchIsSet($key).
							ACTIONS().
							PreserveTrigger().
							SetSwitch($key, Clear).
							SetSwitch($key, Set).
							ENDT(),
						
							HEADING().
							SwitchIsSet($key).
							ACTIONS().
							SetSwitch($key, Set).
							PreserveTrigger().
							ENDT(),
					);
				$text = str_replace($pattern, '', $text);
			}
		}
	}
	
	function _OR() {
		return _OR;
	}
	
	function OrReplace(&$conditions, SwitchList &$switchlist, $nestswitch = null) {
		// Check if there are any _Ors to replace
		if ( !substr_count($conditions,_OR) ) {
			return;
		}
		
		$nesttext = '';
		if ( is_object($nestswitch) ) {
			$nesttext = $nestswitch->is_set();
		}
		
		$orswitch = new TempSwitch();
		
		$OrText = ACTIONS().
				  $orswitch->set().
				  PreserveTrigger().
				  ENDT().
				  HEADING().
				  $nesttext;
		
		$conditions = str_replace(_OR, $OrText, $conditions);
		
		$conditions .= 	ACTIONS().
						$orswitch->set().
						PreserveTrigger().
						ENDT().
						HEADING().
						$orswitch->is_set();
		
		$switchlist->addSwitch($orswitch);
	}
	
	function not($conditions) {
		
		$switch1 = new TempSwitch();
		$switch2 = new TempSwitch();
		
		$switchlist = new SwitchList($switch1, $switch2);
		
		if ( is_array($conditions) ){
			$switchlist->addSwitch($conditions[1]);
			$conditions = $conditions[0];
		}
		
		$text =		ACTIONS().
					$switch1->set().
					PreserveTrigger().
					ENDT().
					HEADING().
					$conditions.
					ACTIONS().
					PreserveTrigger().
					$switch2->set().
					ENDT().
					HEADING().
					$switch1->is_set().
					$switch2->is_clear();
					
		$returnarray = array($text, $switchlist);
		return $returnarray;
		
	}

	function orGroup($conditions) {

		// Check for _ORs
		if ( func_num_args() === 1 ){
			// If the end of the string is an _OR, then remove it
			if ( substr($conditions, -strlen(_OR)) == _OR ){
				$conditions = substr($conditions,0,-1*strlen(_OR));
			}
			// If there are no _ORs within theres no need to group
			if ( substr_count($conditions,_OR) === 0 ){ return $conditions; }
		}
		
		// Accumulate conditions and switchlist and add _ORs between
		$switchlist = new SwitchList();
		$conditions = AggrigateConditions(func_get_args(), $switchlist, true);
		
		/** // old code
		if ( $argnum > 0 ) {
			$conditions = '';
			$switchlist = new SwitchList();
			for ( $i=0; $i < $argnum; $i++ ) {
				$arg = func_get_arg($i);
				if( is_array($arg) ) {
					$conditions .= $arg[0];
					$switchlist->addSwitch($arg[1]);
				} elseif ( $arg ) {
					$conditions .= $arg;
				}
				// Insert Ors between conditions
				if( $i != $argnum-1 ){
					$conditions .= _OR;
				}
			}
		}
		/**/
		
		$switch1 = new TempSwitch();

		// Replace _ORs
		OrReplace($conditions, $switchlist);

		$text =	ACTIONS().
				$switch1->set().
				PreserveTrigger().
				ENDT().
				
				HEADING().
				$conditions.
				$switch1->is_set();
		
		$switchlist->addSwitch($switch1);
		$returnarray = array($text, $switchlist);
		return $returnarray;
		
	}
	
	function ShowAnalysis(){
		global $AnalysisRoot; global $RootFile;
		//$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		//$AnalysisRoot = $trace[0]['file'];
		$AnalysisRoot = $RootFile;
	}
	
	function InsertAnalysis($line, $count){
		global $AnalysisArray;
		$AnalysisArray[$line] += $count;
	}
	
	function Analyze($text){
		// Analysis handling
		global $AnalysisRoot;
		$line = null;
		if( $AnalysisRoot ){
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
			foreach($backtrace as $stack){
				if($stack['file'] == $AnalysisRoot){
					$line = $stack['line'];
					break;
				}
			}
		}
		
		// Accumulate actions
		$text = AggrigateActions(func_get_args());
		
		// Insert entry into array
		CullTriggers($text);
		InsertAnalysis($line, CountTriggers($text));
		return $text;
	}
	
	function Error($errormsg) {
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
		$error = FormatError($backtrace, $errormsg);
		//$error = json_encode($backtrace);
		trigger_error($error, E_USER_ERROR);
	}
	
	function FormatError($errortrace, $errormsg){
		// reverse array order and remove last element
		$errortrace = array_reverse($errortrace);
		unset($errortrace[count($errortrace)-1]);
		
		$text = "";
		$indent = 0;
		foreach($errortrace as $error){
			$text .= "<span style='margin-left: {$indent}px;'>".basename($error["file"])." line {$error["line"]}: ";
			if($error["class"]){
				if( $error["class"] === $error["function"]){
					$text .= "new ";
				} else{
					$text .= "{$error["class"]}{$error["type"]}";
				}
			}
			$text .= "{$error["function"]}()</span><br />".NL;
			$indent += 30;
		}
		$text .= "<pre style='margin: 0 0 0 {$indent}px;font-family: Times, serif;'>{$errormsg}</pre><br />".NL;
		return "Usage Error:<br /><br />".$text."</pre>";
	}
	
	function FriendlyErrorType($type){ 
	    switch($type){ 
	        case E_ERROR: // 1 // 
	            return 'E_ERROR'; 
	        case E_WARNING: // 2 // 
	            return 'E_WARNING'; 
	        case E_PARSE: // 4 // 
	            return 'E_PARSE'; 
	        case E_NOTICE: // 8 // 
	            return 'E_NOTICE'; 
	        case E_CORE_ERROR: // 16 // 
	            return 'E_CORE_ERROR'; 
	        case E_CORE_WARNING: // 32 // 
	            return 'E_CORE_WARNING'; 
	        case E_COMPILE_ERROR: // 64 // 
	            return 'E_COMPILE_ERROR'; 
	        case E_COMPILE_WARNING: // 128 // 
	            return 'E_COMPILE_WARNING'; 
	        case E_USER_ERROR: // 256 // 
	            return 'E_USER_ERROR'; 
	        case E_USER_WARNING: // 512 // 
	            return 'E_USER_WARNING'; 
	        case E_USER_NOTICE: // 1024 // 
	            return 'E_USER_NOTICE'; 
	        case E_STRICT: // 2048 // 
	            return 'E_STRICT'; 
	        case E_RECOVERABLE_ERROR: // 4096 // 
	            return 'E_RECOVERABLE_ERROR'; 
	        case E_DEPRECATED: // 8192 // 
	            return 'E_DEPRECATED'; 
	        case E_USER_DEPRECATED: // 16384 // 
	            return 'E_USER_DEPRECATED'; 
	    } 
	    return ""; 
	}
	
	function handleShutdown() {
		$error = error_get_last();
		if( $error !== NULL && 
			(   $error['type'] == E_COMPILE_ERROR || $error['type'] == E_ERROR || $error['type'] == E_PARSE || $error['type'] == E_USER_ERROR || 
				$error['type'] == E_RECOVERABLE_ERROR || $error['type'] == E_CORE_ERROR 
			) 
		  ){
			if( $error['type'] !== E_USER_ERROR ){
				echo "</textarea><br /><span style='color:red'>".FriendlyErrorType($error['type']).":<br />Error in ".basename($error['file'])." line ".$error['line'].": ".$error['message'] .PHP_EOL."</span>";
			} else {
				echo "</textarea><br /><span style='color:red'>".$error['message']."</span>";
			}
			if( Minted() ){
				global $XMLDocPath, $RetainTmpXML;
				if( $RetainTmpXML != true ){
					unlink($XMLDocPath);
				}
			}
		} 
		else {
			global $ScriptTimer;
			global $TriggerCount;
			global $DeathcounterUnits;
			global $SwitchArray;
			global $PropertyArray;
			global $AnalysisRoot;
			global $AnalysisArray;
			global $MapPath;
			global $OutputMapPath;
			global $OreoRoot;
			global $XMLDocPath;
			global $SwitchRange;
			global $ForemostSwitch;
			global $HyperPlayer;
			global $PrependXML;
			global $PrependMintXML;
            global $PlayerSwitchArray;
			
			// Append triggers to end of list
			if( $ForemostSwitch !== false ){
				/* @var PermSwitch $ForemostSwitch */
				$allplayers = new Player(P1, P2, P3, P4, P5, P6, P7, P8);
				$p1 = new Player(P1);
				$p2 = new Player(P2);
				$p3 = new Player(P3);
				$p4 = new Player(P4);
				$p5 = new Player(P5);
				$p6 = new Player(P6);
				$p7 = new Player(P7);
				$p8 = new Player(P8);
				
				$ForemostDC = new Deathcounter(CP,10);
				
				$allplayers->_if( $ForemostSwitch->is_clear() )->then(
					$ForemostSwitch->set(),
					$ForemostDC->P1->setTo(0),
					$ForemostDC->P2->setTo(0),
					$ForemostDC->P3->setTo(0),
					$ForemostDC->P4->setTo(0),
					$ForemostDC->P5->setTo(0),
					$ForemostDC->P6->setTo(0),
					$ForemostDC->P7->setTo(0),
					$ForemostDC->P8->setTo(0),
					$ForemostDC->AllPlayers->setTo(1),
				'');
				
				$p1->_if( $ForemostDC->P2->exactly(0),$ForemostDC->P3->exactly(0),$ForemostDC->P4->exactly(0),$ForemostDC->P5->exactly(0),$ForemostDC->P6->exactly(0),$ForemostDC->P7->exactly(0),$ForemostDC->P8->exactly(0) )->then(
					$ForemostSwitch->clear() );
				$p2->_if( $ForemostDC->P3->exactly(0),$ForemostDC->P4->exactly(0),$ForemostDC->P5->exactly(0),$ForemostDC->P6->exactly(0),$ForemostDC->P7->exactly(0),$ForemostDC->P8->exactly(0) )->then(
					$ForemostSwitch->clear() );
				$p3->_if( $ForemostDC->P4->exactly(0),$ForemostDC->P5->exactly(0),$ForemostDC->P6->exactly(0),$ForemostDC->P7->exactly(0),$ForemostDC->P8->exactly(0) )->then(
					$ForemostSwitch->clear() );
				$p4->_if( $ForemostDC->P5->exactly(0),$ForemostDC->P6->exactly(0),$ForemostDC->P7->exactly(0),$ForemostDC->P8->exactly(0) )->then(
					$ForemostSwitch->clear() );
				$p5->_if( $ForemostDC->P6->exactly(0),$ForemostDC->P7->exactly(0),$ForemostDC->P8->exactly(0) )->then(
					$ForemostSwitch->clear() );
				$p6->_if( $ForemostDC->P7->exactly(0),$ForemostDC->P8->exactly(0) )->then(
					$ForemostSwitch->clear() );
				$p7->_if( $ForemostDC->P8->exactly(0) )->then(
					$ForemostSwitch->clear() );
				$p8->_if( )->then(
					$ForemostSwitch->clear()
				);
				
			}
			
			// Generate Player Switch Triggers
			if( !empty($PlayerSwitchArray) ){
				$switchnum = count($PlayerSwitchArray);
				
				for($i=0;$i<$switchnum;$i++){
					if( ($i % 32) == 0 ){
						$PlayerSwitchDC = new Deathcounter(CP);
					}
					
					$switch = $PlayerSwitchArray[$i][0];
					$player = $PlayerSwitchArray[$i][1];
					$power = pow(2,31 - $i % 32);
					
					$player->prepend->_if( $PlayerSwitchDC->atLeast($power) )->then(
						$PlayerSwitchDC->subtract($power),
						$switch->set(),
					'');
					$player->_if( $switch->is_set() )->then(
						$PlayerSwitchDC->add($power),
						$switch->clear(),
					'');
				}
				
			}
			
			// Hypertriggers
			$WaitActions = '';
			for($i=1; $i<=62; $i++){
				$WaitActions .= Wait(0);
			}
			if ( $HyperPlayer === null ){
				$HyperPlayer = new Player(P8);
			}
			for($i=1; $i<=4; $i++){
				/**/
				$HyperPlayer->always(
					$WaitActions
				);
				/**/
			}
			
            // Count the total number of switches used and how many temp switches are left unkilled
			$SwitchCount = 0;
			$TempSwitchBleed = 0;
			foreach ($SwitchArray as $key=>$value){
                if ($value > 0 && $value < 4){ 
                    $SwitchCount++;
                    if( $value === 2 ){ $TempSwitchBleed++; }
                }
			}
			
			// Count the total number of deathcounters used and how many temp dcs are left unkilled
            $DCCount = 0;
            $DCTotal = 0;
            $TempDCBleed = 0;
			foreach($DeathcounterUnits as $unit => $playerArray ) {
                foreach($playerArray as $playerIndex=>$SlotUsage) {
                    $DCTotal++;
                    if ( is_numeric($SlotUsage) ) { $DCCount++; }
	                if ( $SlotUsage === 3 ) { $TempDCBleed++; }
                }
            }
			
			// Create properties textarea and output properties if ever used
			if( !empty($PropertyArray) ){
				echo '</textarea></td>';
				?><td>Unit Properties:<br /><textarea rows="40" cols="90" style="resize:none;font-family:Consolas;font-size:12;" readonly><?php
				foreach($PropertyArray as $index=>$propertyset){
					echo 'Unit Property '.$index.NL,
						 $propertyset;
				}
				?></textarea><?php
				if( Minted() ){
					$xmlhandle = fopen($XMLDocPath, "a");
					foreach($PropertyArray as $index=>$propertyset){
						fwrite($xmlhandle, $propertyset);
					}
					fclose($xmlhandle);
				}
			}
			
			// Display post-compile output
            $compiletime = round(microtime(true) - $ScriptTimer, 6);
			echo '</textarea></td></tr></table><br />',
				 '// Compile Complete'.'<br />',
				 '// Execution time: '.$compiletime.'<br />',
				 '// Triggers: '.$TriggerCount.'<br />',
				 '// Switches used: '.($SwitchCount).' / '.$SwitchRange.'<br />',
				 '// Deathcounter slots used: '.$DCCount.' / '.$DCTotal.'<br />';
			
			// Warn if any temp items are left unkilled
			if( $TempSwitchBleed == 1 ){
		        echo '// WARNING! A temp switch is unkilled!'.'<br />';
		    }elseif( $TempSwitchBleed >= 2) {
                echo '// WARNING! '.$TempSwitchBleed.' temp switches are unkilled!'.'<br />';
            }
            if( $TempDCBleed == 1 ){
                echo '// WARNING! A temp dc is unkilled!'.'<br />';
            }elseif( $TempDCBleed >= 2) {
                echo '// WARNING! '.$TempDCBleed.' temp dcs are unkilled!'.'<br />';
            }
			
			if ( $MapPath && !Minted() ){
				echo "<br />You need to specify an output map path<br />";
			}
			
			// Run Mint and display the output if Mint is used
			if( Minted() ){
				// Add closing mint tag
				$filecontents = file_get_contents($XMLDocPath);
				$xmlhandle = fopen($XMLDocPath, "w");
				fwrite($xmlhandle, "<mint>".NL.$PrependMintXML.$PrependXML.$filecontents.NL."</mint>");
				fclose($xmlhandle);
				
				$MintExePath = $OreoRoot.'\internal\mint\mint.exe';
				
				// Execute mint.exe
				$exec = "\"$MintExePath\" \"$MapPath\" \"$OutputMapPath\" \"$XMLDocPath\" 2>&1";
				exec($exec, $output);
				
				// Display
				$minttime = round(microtime(true) - $ScriptTimer - $compiletime, 6);
				echo '<br />// Minting Complete<br />';
				echo '// Execution time: '.$minttime.'<br />';
				foreach($output as $outputline){
					if( strstr($outputline, 'Invalid') || strstr($outputline, 'Error') ){
						if( $outputline === 'Error opening output map' ){ $outputline .= " (is it open in SC?)"; }
						echo "// <span style='color:red'>".$outputline."<br /></span>";
					} else {
						echo "// $outputline<br />";
					}
				}
				
				// Remove temp file
				global $RetainTmpXML;
				if( $RetainTmpXML != true ){
					unlink($XMLDocPath);
				}
			}
			
			// Display analysis if enabled
			if( $AnalysisRoot ){
				?><br /><br /><span style="font-family:Consolas;font-size:12;">Trigger Analysis:</span><div style="height:500px; width:1000px;overflow:scroll;border:solid 1px #d3d3d3"><pre style="font-family:Consolas;font-size:12;"><?php
				$linenumber = 0;
				$fp = fopen( $AnalysisRoot, 'r' );
				while( !feof($fp) ){
					$linenumber++;
					$line = fgets( $fp, 1024 );
				    $line = strip_tags(substr($line,0,-2));
					$line = str_replace(array(TAB,'analyze('),array('    ','<span  style="color: #d2b48c;">analyze</span>('),$line);
					$statstext = '';
					if( !empty($AnalysisArray[$linenumber]) ){
						$statstext = '<span style="color: #336699"> // trigger count: '.$AnalysisArray[$linenumber].'</span>';
					}
					echo $line.$statstext.NL;
				}
				?></pre></div><br /><br /><br /><?php
			}
			
			global $CheckForUpdates;
			if ( $CheckForUpdates ){
				?> 
				<div id="updatewrap" style="width:400px;position: fixed; bottom: 0; right: 20px;font-family: Arial, Arial, sans-serif; display: none">
					<div id="updatelog"  style="font-size: 13px; background-color: white;margin:10px 10px 0 10px; padding: 10px; display: none; 
					                           border: gray solid 1px; box-shadow: #666 0 0 6px;" class=hidden>
					</div>
					<div id="updatenote" style="outline: gray solid 1px;padding:5px; background-color: white;text-align: center; 
											    cursor: pointer;box-shadow: #666 0 0 6px;" onclick="toggleLog()">
						New Version Available
					</div>
				</div> 
				<?php
			}
			
			// Prepend prepended text using javascript
			global $PrependTrigs;
			if ( $PrependTrigs ){
				?><div id="prependdiv" style="display:none"><?php echo $PrependTrigs; ?></div><?php
				echo "</body></html>";
				?> 
				<script type="text/javascript">
					function prependtrigs(){
						var triggerfield = document.getElementById("triggerfield");
						var prependdiv = document.getElementById("prependdiv");
						triggerfield.innerHTML = prependdiv.innerHTML + triggerfield.innerHTML;
					}
					window.onload = prependtrigs;
				  </script> <?php
			}
			
			
			
			if ( $CheckForUpdates ){
				?> 
				<script type="text/javascript">
					<?php 
					function curPageURL() {
						$pageURL = 'http';
						if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
							$pageURL .= "://";
						if ($_SERVER["SERVER_PORT"] != "80") {
							$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
						} else {
							$pageURL .= $_SERVER["SERVER_NAME"];
						}
						return $pageURL;
					}
					global $oreo_version; global $OreoRoot;
					$OreoRootRel = str_replace($_SERVER['DOCUMENT_ROOT'], "", str_replace('\\', '/', $OreoRoot));
					$updatescript = curPageURL()."/$OreoRootRel/internal/UpdateCheck.php?vers=$oreo_version";
					
					?>
					
					// Update ajax
					window.onload = function() {
						<?php if($PrependTrigs){ echo "prependtrigs();"; } ?>
						
						var xmlhttp;
						if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
							xmlhttp=new XMLHttpRequest();
						} 
						else {// code for IE6, IE5
							xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
						}
						xmlhttp.onreadystatechange=function() {
							if (xmlhttp.readyState==4 && xmlhttp.status==200) {
								if( xmlhttp.responseText.substr(0,"SUCCESS".length) == "SUCCESS" ){
                                    document.getElementById("updatelog").innerHTML= xmlhttp.responseText.substr("SUCCESS".length);
                                    document.getElementById("updatewrap").style.display="block";
                                }
							}
						}
						xmlhttp.open("GET","<?php echo $updatescript; ?>",true);
						xmlhttp.send();
					}
					
					function toggleLog(){
						var logdiv = document.getElementById("updatelog");
						if( logdiv.style.display == "none" ){
                            logdiv.style.display = "block";
                        } else {
							logdiv.style.display = "none";
						}
					}
				</script>
				
				<?php
			}
			
		}
	}
	
	function ValidUnitCheck($unit) {
		if($unit === Men || $unit === AnyUnit || $unit === Buildings || $unit === Factories ){ return $unit; }
		$unitlower = strtolower($unit);
		global $ValidUnits;
		foreach($ValidUnits as $vUnit) {
			if( $unitlower == strtolower($vUnit) ){
				return $vUnit;
			}
		}
		$nearmatches = array();
		foreach($ValidUnits as $vUnit) {
			$LowerVUnit = strtolower($vUnit);
			if( levenshtein($LowerVUnit, $unitlower) <= 2 ) {
				$nearmatches[] = $vUnit;
			} elseif( substr_count($LowerVUnit, $unitlower) ) {
				$nearmatches[] = $vUnit;
			}
		}
		$matchcount = count($nearmatches);
		if ( $matchcount == 0 ){
			Error('Error: "'.$unit.'" is not a valid unit');
		} elseif ( $matchcount == 1 ) {
			Error('Error: "'.$unit.'" is not a valid unit, perhaps you meant to use "'.$nearmatches[0].'"');
		} else {
			$diestring = 'Error: "'.$unit.'" is not a valid unit, perhaps you meant to use one of the following:'.NL;
			foreach( $nearmatches as $match ){
				$diestring .= TAB.$match.NL;
			}
			Error($diestring);
		}
		
	}
	
	function IsStandardPlayer($player){
		return  $player === P1 || $player === P2 || $player === P3 || $player === P4 || $player === P5 || $player === P6 || $player === P7 || $player === P8 || 
				$player === P9 || $player === P10 || $player === P11 || $player === P12 || $player === AllPlayers || $player === Allies || $player === Foes || 
				$player === CP;
	}
	
	function GetLocName(&$location) {
		if ( $location instanceof Location ) {
			$location = $location->Name;
		}
	}
	
	function GetUnitType(&$unit) {
		if ( $unit instanceof UnitGroup ) {
			$unit = $unit->Unit;
		}
	}
	
	function GetScript(&$script) {
		if ( strlen($script) !== 4 ) {
			global $AiScripts;
			foreach ($AiScripts as $code=>$name) {
				if( strtolower($script) == strtolower($name) ){
					$script = $code;
					break;
				}
			}
		}
	}
	
	function GetUnitID($unit){
		global $ValidUnits;
		return array_search($unit, $ValidUnits);
	}
	
	function GetPropertyNumber($properties) {
		global $PropertyArray;
		
		$text = GeneratePropertiesSet($properties);
		
		// Get index if property set already used 
		foreach($PropertyArray as $key => $value){
			if($value == $text){
				return $key+1;
			}
		}
		
		// Get new index and insert property set
		$index = count($PropertyArray);
		$PropertyArray[$index] = $text;
		
		// Error if they use too many properties
		if ( $index >= 64 ){
			Error("Starcraft only supports at most 64 unique 'create unit with property' combinations. This is your 65th!");
		}
		
		return $index+1;
	}
	
	function GeneratePropertiesSet($properties){
		// Set default properties
		$hp = 100;
		$sp = 100;
		$ep = 100;
		$res = 0;
		$hangar = 0;
		$flags = '';
		
		if( is_array($properties) ){
			// Sort array alphabetically
			asort($properties);
			
			// Sort through property array and set values
			foreach($properties as $property=>$value){
				if( is_string($property) ){
					switch($property){
						case Health:
							$hp = $value;
							break;
						case Energy:
							$ep = $value;
							break;
						case Shields:
							$sp = $value;
							break;
						case Resources:
							$res = $value;
							break;
						case Hangar:
							$hangar = $value;
							break;
					}
				}
				switch($value){
					case Burrowed;
						$flags .= 'BURROWED ';
						break;
					case Cloaked:
						$flags .= 'CLOAKED ';
						break;
					case LiftedOff:
						$flags .= 'INTRANSIT ';
						break;
					case Hallucinated:
						$flags .= 'HALLUCINATED ';
						break;
					case Invincible:
						$flags .= 'INVINCIBLE ';
						break;
				}
			}
		} else{
			switch($properties){
				case Burrowed;
					$flags .= 'BURROWED ';
					break;
				case Cloaked:
					$flags .= 'CLOAKED ';
					break;
				case LiftedOff:
					$flags .= 'INTRANSIT ';
					break;
				case Hallucinated:
					$flags .= 'HALLUCINATED ';
					break;
				case Invincible:
					$flags .= 'INVINCIBLE ';
					break;
			}
		}
		// Build property set
		if ( Minted() ){
			$search = array('BURROWED','CLOAKED','INTRANSIT','HALLUCINATED','INVINCIBLE');
			$replace = array('<burrowed/>','<cloaked/>','<transit/>','<hallucinated/>','<invincible/>');
			$flags = str_replace($search,$replace,$flags);
			$text = "<uprp>".NL.
					TAB."<health>$hp</health>".NL.
					TAB."<shield>$sp</shield>".NL.
					TAB."<energy>$ep</energy>".NL.
					TAB."<resource>$res</resource>".NL.
					TAB."<hangar>$hangar</hangar>".NL.
					TAB.$flags.NL.
					"</uprp>".NL.NL;
		} else {
			$text = NL.'HP:	'.$hp.NL.
					'SP:	'.$sp.NL.
					'EP:	'.$ep.NL.
					'Res:	'.$res.NL.
					'Hangar Units:	'.$hangar.NL.
					'Flags:	'.$flags.NL.NL;
		}
		
		return $text;
	}
	
	function Mint($inputMapPath, $outputMapPath){
		if( func_num_args() !== 2 ){
			Error('You must specify an input map filepath and an output map filepath for mint to work!');
		}
		// Access globals
		global $MapPath;
		global $OutputMapPath;
		global $Minted;
		global $XMLDocPath;
		global $TriggerOwner;
		
		// Set path globals
		$MapPath = $inputMapPath;
		$OutputMapPath = $outputMapPath;
		
		// Change default trig owner, set minted to true
		$TriggerOwner = '<trig_group>Player 1</trig_group>';
		$Minted = true;
		
		// Create a temp file for xml
		$XMLDocPath = tempnam(dirname(__FILE__).'\mint','xml');
	}
	
	function Minted(){
		global $Minted;
		return $Minted;
	}
	
	function SuppressOutput(){
		if( !Minted() ){
			return;
		}
		echo "Output Suppressed";
		global $OutputSuppressed;
		$OutputSuppressed = true;
	}
	
	function OutputSuppressed(){
		global $OutputSuppressed;
		return $OutputSuppressed;
	}
	
	function RetainTmpXML($bool = true){
		if( !is_bool($bool) ){ Error("Expecting boolean argument"); }
		global $RetainTmpXML;
		$RetainTmpXML = $bool;
	}
	
	function GetMintStateConversion(&$state){
		if( $state === Enable )
			$state = Set;
		
		if( $state === Disable )
			$state = Clear;
	}
	
	function HandleStringOutput(&$string){
		$string = str_replace("<", "\\x03C", $string);
	}
	
	function XMLCondition($array){
		$text = '	<condition>';
		foreach($array as $tag=>$value){
			$text .= "<cond_$tag>$value</cond_$tag>";
		}
		return "$text</condition>\n";
	}
	
	function XMLAction($array){
		$text = '	<action>';
		foreach($array as $tag=>$value){
			$text .= "<act_$tag>$value</act_$tag>";
		}
		return "$text</action>\n";
	}
	
	function repeat($repetitions, $actions){
		$text = '';
		$actiongroup = '';
		if ( $actions ){ $actions = array_splice(func_get_args(),1); }
		foreach($actions as $action){
			$actiongroup .= $action;
		}
		for($i=1;$i<=$repetitions;$i++){
			$text .= $actiongroup;
		}
		return $text;
	}
	
	function tabs($n){
		$tabs = "";
		for($i=1;$i<=$n;$i++){
			$tabs .= "\t";
		}
		return $tabs;
	}
	
	function PrependTrigs(&$text, &$xml){
		global $PrependTrigs;
		global $PrependXML;
		$PrependTrigs .= $text;
		$PrependXML .= $xml;
	}
	
	function BundleConditions($conditions){
		
		// Accumulate conditions
		$switchlist = new SwitchList();
		$conditions = AggrigateConditions(func_get_args(), $switchlist);
		
		return array($conditions, $switchlist);
	}
	
	function GetPlayerShorthand($string){
		if( $string === P1 )
			return 'P1';
		if( $string === P2 )
			return 'P2';
		if( $string === P3 )
			return 'P3';
		if( $string === P4 )
			return 'P4';
		if( $string === P5 )
			return 'P5';
		if( $string === P6 )
			return 'P6';
		if( $string === P7 )
			return 'P7';
		if( $string === P8 )
			return 'P8';
		if( $string === CP )
			return 'CP';
		if( $string === AllPlayers )
			return 'AllPlayers';
		if( $string === Allies )
			return 'Allies';
		if( $string === Foes )
			return 'Foes';
		if( $string === F1 )
			return 'F1';
		if( $string === F2 )
			return 'F2';
		if( $string === F3 )
			return 'F3';
		if( $string === F4 )
			return 'F4';
		return $string;
	}
	
	function SCCharSwap1(&$text){
		global $CharSwap1;
		$text = strtr($text, $CharSwap1);
	}
	
	function SCCharSwap2(&$text){
		global $CharSwap2;
		$text = strtr($text, $CharSwap2);
	}
	
