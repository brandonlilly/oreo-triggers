# Changelog

## 0.9.6
*	Added force casting to TempDC's, UnitGroups and IndexedUnits
*	Included dc->getFourthByte()
*	Added dc->loadPlayerMemory() to Deathcounter class
*	ModifyHealth, ModifyEnergy and ModifyShield actions can now accept a deathcounter for the $percent parameter
*	ModifyResource action can now accept a deathcounter for the $resourceAmount parameter
*	Fixed an error where prepended triggers weren't showing up when update checking was enabled
*	Fixed a potential bug with analysis sometimes not working
*	Added checkSpecificTargetIDs() and getSpecificTargetIDs() to IndexedUnit
*	Added CreateCondition function
*	Added deathcounter functions ->subtractDel($var) and ->addDel($var)
*	Fixed Error indent formatting
*	Added $unitdata array to data files
*	Added range and min function for deathcounters
*	Added enableDoodadState() and disableDoodadState() for UnitGroup class
*	Added isUnitType to IndexedUnit
*	Better error handling
*	Fixed a bug in $deathcounter->productOf()
*	Added SetClassFolder for autoloading classes.

## 0.9.3
*	SCTangent()
*	$by parameter on getorderYcoordinate
*	$enableInner killed properly in dc->productOf()
*	switches default to $switch->is_set() when used as string
*	Fixed SetScore for Mint (arguments were out of order)

*	Made it so you can do: $hero->P1->at("@>3x3") (Player casting for UnitGroups)
*	Made Deathcounter player casting cloning a bit more efficient
*	Made MintLocation return a Location object using the passed location name
*	You can specify a location in unitgroup ->giveTo()
*	You can specify a location in Location's ->centerOn(Unitgroup, location)
*	Lots of minor UnitGroup improvements (added ->bring and ->command)
*	Made SwitchIsClear() and SwitchIsSet() work with switch objects passed to them
*	Made it so UnitGroups ValidUnitCheck can accept Men, Factories, AnyUnit or Buildings for its unit
*	Added isNAIID() function to IndexedUnit
*	Added player casting to IndexedUnit
*	Added release() alias for kill() for TempDC and TempSwitch
*	Added $dc->All for player casting each player the deathcounter is specified for
*	Added $dc->getFourthByte *
*	Kill renamed to KillsOf

## 0.9.2
*	Fixed a bug in automatically creating hyper triggers when using mint
*	$n to UnitGroup->teleportTo
*	die() to trigger_error() in Location->centerOn()
*	removed addslashes() from prepended triggers (in handleshutdown)
*	Fixed centerView not working for Mint
*	Fixed bug where not() didn't work with multi-trigger conditions
*	Men, AnyUnit, Factories, Buildings work with Mint now
*	Added SuppressOutput() (stops Oreo from outputting triggers to the webpage when using Mint, which can reduce compile time significantly)
*	Added ModifyHangar($player, $unit, $n, $location, $amounttoadd)
*	Added Leaderboard functions:
	- LeaderBoardComputers()
	- LeaderBoardControl()
	- LeaderBoardControlAtLocation()
	- LeaderBoardKills()
	- LeaderBoardGreed()
	- LeaderBoardResources()
	- LeaderBoardGoalControl()
	- LeaderBoardGoalControlAtLocation()
	- LeaderBoardGoalKills()
	- LeaderBoardGoalResources(),
*	Added Deathcounter leaderboard() function, which displays the deathcounter's data on the leaderboard

## 0.9.1
*	Fixed a bug when using complex conditions within if else triggers
*	ForemostPlayer()

## 0.9.0
*	You can now specify an amount in giveTo, kill and remove methods of UnitGroup (defaults to all)
*	Mint provides an error message if your input or output maps fail
*	Fixed a bug when using Mint with the Order action
*	Added a MintMapRevealers function, which places map revealers spaced out across the map for the specified players
*	Added a repeat action function
*	Fixed a bug where Oreo triggers wasn't automatically removing extraneous triggers

## 0.8.5
*	Added UnitGroup notAt(), inside() and outside() functions
*	Added Gas() and Ore() Conditions
*	Made it so that if you try to center a location on multiple players it gives an error
*	LeaderBoard renamed to LeaderBoardPoints
*	Added Conditions:
	-	Kill
	-	CommandTheLeast
	-	CommandTheLeastAt
	-	CommandTheMost
	-	CommandTheMostAt
	-	LowestScore
	-	HighestScore
	-	LeastKills
	-	MostKills
	-	LeastResources
	-	MostResources
	-	Opponents




# Todo
* Vanilla
	*	Fix _switch so that subtriggers within it don't use conflicting switches
	*	Enable inputting a deathcounter into most actions
	*	Implement Forces and have them be usable in plain oreo
	*	Let deathcounters be declared for a single player (e.g. new Deathcounter(P8, 100);)

* Mint
	*	Make errors more specific e.g. 'Invalid location string' to 'Invalid location string: "_main"'
