Elo system
==========

Another **Elo** implementation in **PHP** ! But this one introduce a **reliability** purpose.


## Reliability purpose

**A history**: You have a good player **A** which played many games and have a score of **2100 Elo**.
A new player **B** subscribe to the game website, so his Elo score is initialized to **1500**.
But in fact, he is a very good player, better than **A**, and beat him like crushing an ant.

**The problem**: New player **B** will win many Elo because he won against a **2100 Elo** player. That's ok.
But player **A** (**2100 Elo**) will lose many Elo because he lost against a **1500 Elo** player, but in fact strongest.

The fact is that the **new player Elo score is not reliable**, so it should not impact others players Elo scores.

**The solution**: This library. It introduces a **reliability coefficient** (decimal between 0.0 and 1.0) for Elo A and Elo B.


## Usage

- Instantiate a standard Elo system

``` php
use Alcalyn/Elo/EloSystem;

$eloSystem = new EloSystem();
```

- Calculate updated Elo scores from old Elo

``` php
/**
 * A player with 1650 Elo beat another with 1920
 */
$updatedElos = $eloSystem->calculate(1650, 1920, 1);

print_r($updatedElos);
/* Output:
    Array
    (
        [0] => 1663.2084157978
        [1] => 1906.7915842022
    )
*/
```

- Set **reliability** coefficient to Elo scores

``` php
/**
 * A player with 1907 Elo (1.0 reliability)
 * lose against a new player with 1500 (and reliability to 0.0)
 */
$updatedElos = $eloSystem->calculate(1907, 1500, 0, 1.0, 0.0);

print_r($updatedElos);
/* Output:
Array
(
    [0] => 1907
    [1] => 1514.5978664353
)
*/
```

- Using method aliases for win, lose or draw

``` php
/**
 * Method Aliases
 */
$elo->win(2100, 1500, 1.0, 0.0);
$elo->lose(2100, 1500, 1.0, 0.0);
$elo->draw(2100, 1500, 1.0, 0.0);
```

- Instanciate a system with a different K factor (default is 16)

``` php
/**
 * Use a different K factor in your Elo system
 */
$eloSystemK32 = new EloSystem(32);
```

## Detailled examples

**A new player**:

Player **A** has **2100 Elo**, reliability **1.0**<br />
Player **B** has **1500 Elo**, reliability **0.0**<br />

**A** wins: Expected result,     so **B** loses a small amount of Elo, and **A** win nothing.<br />
**B** wins: NOT expected result, so **B** wins a BIG amount of Elo, and **A** lose nothing.<br />

*A* Elo score will not be updated when he play versus a new player with an unreliable Elo score.

(*And new player* ***B*** *should have its Elo reliability increased by something like 1/10.*)

``` php
$elo = new EloSystem();

/**
 * Result without reliability
 */
print_r($elo->lose(2100, 1500));

/* Output:
    Array
    (
        [0] => 2084.4904548805 // lose -16 Elo
        [1] => 1515.5095451195 // win  +16 Elo
    )
*/

/**
 * Result with reliability
 */
print_r($elo->lose(2100, 1500, 1.0, 0.0));

/* Output:
    Array
    (
        [0] => 2100 // don't lose Elo against new player
        [1] => 1515.5095451195 // win +16 Elo vs reliable Elo score
    )
*/
```

**Another example: two newbies players**:

Player **A** has **1500 Elo**, reliability **0.0**<br />
Player **B** has **1500 Elo**, reliability **0.0**<br />

There is two new players, so their reliabilities are both 0.0:
**the algorithm takes them like if they were both 1.0**.

And if player **A** had an Elo reliability equal to **0.4**, and player **B** equal to **0.0**,
the algorithm adds them **+0.6** so one of reliabilities reaches **1.0**.


## License

This project is under [MIT Lisense](https://github.com/alcalyn/elo/blob/master/LICENSE)
