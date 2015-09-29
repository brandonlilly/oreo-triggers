# Oreo Triggers
Oreo Triggers provides a framework for compiling Staredit triggers. It adds modularity, intuitive triggering, EUD, Switch and Deathcounter variable support, helps you manage your resources and allows for custom built actions and conditions.

> [9/28/15, 4:30:50 PM] Samson Bradley: uuugggh  
> [9/28/15, 4:30:58 PM] Samson Bradley: Oreo actually turns me on  
> [9/28/15, 4:31:15 PM] Samson Bradley: most efficient way to make a map ever  
> [9/28/15, 4:30:50 PM] Samson Bradley: uuugggh  
> [9/28/15, 4:30:58 PM] Samson Bradley: Oreo actually turns me on  
> [9/28/15, 4:31:15 PM] Samson Bradley: most efficient way to make a map ever  

# Examples
Oreo gives the user variables (called Deathcounters) by allocating and handling the unit death table behind the scenes.

```php
$P1 = new Player("Player 1");
$spawnTimer = new Deathcounter();

$P1->_if( $spawnTimer->exactly(0) )->then(
  Display('Spawn timer set to 10 seconds'),
  $spawnTimer->setTo(10 * 12)
);

$P1->always( $spawnTimer->subtract(1) );
```

Calculations that take thousands of triggers can be done in a single line with the built-in math library
```php
$angle->getAngle($originx, $originy, $destx, $desty)
```

Provides access to custom conditions by reading from buffer overflows (EUDS)
```php
# not available in vanilla triggers
$P1->_if( $unit->isLockedDown() )->then(
  ClearText(),
  Display("Your unit is irradiated!")
);
```

Allows for nested trigger logic
```php
$Players->_if( $unit->enters($shop) )->then(
  Display("Welcome to my shop!"),

  _if( $visited->is_clear() )->then(
    Display("There's a one time entrance fee."),
    $gold->subtract(5),
    $visited->set()
  ),  

  Display("You're always free to look around!")
);
```

# Installation
1. Install a local php server (e.g. MAMP)
2. Clone or download the Oreo Triggers repository and place in your server's public facing folder (e.g. www, public)
3. Create a php file for your Oreo project in the same directory and require the Oreos initialize file
  ```php
  <?php require_once('oreo-triggers/initialize.php');
  ```
  This file (on the subsequent lines) is where you'll right your actual Oreo code.

4. Run your php server and visit your project file in a web browser (e.g. localhost/project.php) to compile.
