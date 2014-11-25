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

``` php
$elo = new Elo();

// Elo 2100 beat Elo 1500, no reliability usage
$elo->calculate(2100, 1500, 1);

/* Output:
    Array
    (
        [0] => 2084.4904548805
        [1] => 1515.5095451195
    )
*/

// Elo 2100 (reliability 1.0) beat Elo 1500 (reliability 0.0), 
$elo->calculate(2100, 1500, 1, 1.0, 0.0);

/* Output:
    Array
    (
        [0] => 2100
        [1] => 1515.5095451195
    )
*/

/**
 * Aliases
 */
$elo->win(2100, 1500, 1.0, 0.0);
$elo->lose(2100, 1500, 1.0, 0.0);
$elo->draw(2100, 1500, 1.0, 0.0);
```


## Examples

**This one above**:

Player **A** has **2100 Elo**, reliability **1.0**<br />
Player **B** has **1500 Elo**, reliability **0.0**<br />

**A** wins: Expected result,     so **B** loses a small amount of Elo, and **A** win nothing, because **B** reliability is **0.0**.<br />
**B** wins: NOT expected result, so **B** wins a BIG amount of Elo, and **A** lose nothing,   because **B** reliability is **0.0**.<br />

*And new player* ***B*** *should have its Elo reliability increased by something like 1/10.*<br />

``` php
$elo = new Elo();

/**
 * Calculate without reliability
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
 * Calculate with reliability
 */
print_r($elo->lose(2100, 1500, 1.0, 0.0));

/* Output:
    Array
    (
        [0] => 2100 // don't lose Elo against new player
        [1] => 1515.5095451195 // win +16 Elo
    )
*/
```

**Another example: two newbies players**:

Player **A** has **1500 Elo**, reliability **0.0**<br />
Player **B** has **1500 Elo**, reliability **0.0**<br />

There is two new players, so their reliabilities are both 0.0: **the algorithm takes them like if they were both 1.0**.

And if player **A** had an Elo reliability equal to **0.4**, and player **B** equal to **0.0**,
the algorithm adds them **+0.6** so one of reliabilities is equal to **1.0**.


## License

This project is under [MIT Lisense](https://github.com/alcalyn/elo/blob/master/LICENSE)
