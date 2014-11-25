<?php

namespace Alcalyn\Elo\Tests;

use Alcalyn\Elo\EloSystem;
use Alcalyn\Elo\EloCoefficientException;

class EloSystemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Win, loss, draw between 1800 and 1500 Elo, no use of reliability (default 1.0 for both)
     */
    public function testStandardCalculateReturnsExpectedElos()
    {
        $elo = new EloSystem(16, 400, 10);
        
        $updated = $elo->calculate(1800, 1500, 1);
        
        $this->assertEquals(round($updated[0]), 1802);
        $this->assertEquals(round($updated[1]), 1498);
        
        $updated = $elo->calculate(1800, 1500, 0);
        
        $this->assertEquals(round($updated[0]), 1786);
        $this->assertEquals(round($updated[1]), 1514);
        
        $updated = $elo->calculate(1800, 1500, 0.5);
        
        $this->assertEquals(round($updated[0]), 1794);
        $this->assertEquals(round($updated[1]), 1506);
    }
    
    /**
     * Win, loss, draw between 1800 and 1500 Elo, and K factor = 36
     */
    public function testKFactor36CalculateReturnsExpectedElos()
    {
        $elo = new EloSystem(36, 400, 10);
        
        $updated = $elo->calculate(1800, 1500, 1);
        
        $this->assertEquals(round($updated[0]), 1805);
        $this->assertEquals(round($updated[1]), 1495);
        
        $updated = $elo->calculate(1800, 1500, 0);
        
        $this->assertEquals(round($updated[0]), 1769);
        $this->assertEquals(round($updated[1]), 1531);
        
        $updated = $elo->calculate(1800, 1500, 0.5);
        
        $this->assertEquals(round($updated[0]), 1787);
        $this->assertEquals(round($updated[1]), 1513);
    }
    
    /**
     * Win, loss, draw between 1800 and 1500 Elo, and 1500 has 0.0 reliability (newbie player)
     */
    public function testCalculateReliableEloAgainstUnreliableEloReturnsExpectedElos()
    {
        $elo = new EloSystem(16, 400, 10);
        
        $updated = $elo->calculate(1800, 1500, 1, 1.0, 0.0);
        
        $this->assertEquals(round($updated[0]), 1800);
        $this->assertEquals(round($updated[1]), 1498);
        
        $updated = $elo->calculate(1800, 1500, 0, 1.0, 0.0);
        
        $this->assertEquals(round($updated[0]), 1800);
        $this->assertEquals(round($updated[1]), 1514);
        
        $updated = $elo->calculate(1800, 1500, 0.5, 1.0, 0.0);
        
        $this->assertEquals(round($updated[0]), 1800);
        $this->assertEquals(round($updated[1]), 1506);
    }
    
    /**
     * Win, loss, draw between 1800 and 1500 Elo, both are not reliable (two newbie players)
     */
    public function testCalculateTwoUnreliableEloReturnsExpectedElos()
    {
        $elo = new EloSystem(16, 400, 10);
        
        $updated = $elo->calculate(1500, 1500, 1, 0.0, 0.0);
        
        $this->assertEquals(round($updated[0]), 1508);
        $this->assertEquals(round($updated[1]), 1492);
        
        $updated = $elo->calculate(1500, 1500, 0, 0.0, 0.0);
        
        $this->assertEquals(round($updated[0]), 1492);
        $this->assertEquals(round($updated[1]), 1508);
        
        $updated = $elo->calculate(1500, 1500, 0.5, 0.0, 0.0);
        
        $this->assertEquals(round($updated[0]), 1500);
        $this->assertEquals(round($updated[1]), 1500);
    }
    
    /**
     * Win, loss, draw between 1800 and 1500 Elo, custom elo interval
     */
    public function testCalculateEloCustomIntervalReturnsExpectedElos()
    {
        $elo = new EloSystem(16, 200, 10);
        
        $updated = $elo->calculate(2042, 1458, 1);
        
        $this->assertEquals(round($updated[0]), 2042);
        $this->assertEquals(round($updated[1]), 1458);
        
        $updated = $elo->calculate(2042, 1458, 0);
        
        $this->assertEquals(round($updated[0]), 2026);
        $this->assertEquals(round($updated[1]), 1474);
        
        $updated = $elo->calculate(1745, 1714, 0);
        
        $this->assertEquals(round($updated[0]), 1736);
        $this->assertEquals(round($updated[1]), 1723);
    }
    
    /**
     * Win, loss, draw between 1800 and 1500 Elo, custom elo power
     */
    public function testCalculateEloCustomPowerReturnsExpectedElos()
    {
        $elo = new EloSystem(16, 400, 2);
        
        $updated = $elo->calculate(2042, 1458, 1);
        
        $this->assertEquals(round($updated[0]), 2046);
        $this->assertEquals(round($updated[1]), 1454);
        
        $updated = $elo->calculate(2042, 1458, 0);
        
        $this->assertEquals(round($updated[0]), 2030);
        $this->assertEquals(round($updated[1]), 1470);
        
        $updated = $elo->calculate(1745, 1714, 0);
        
        $this->assertEquals(round($updated[0]), 1737);
        $this->assertEquals(round($updated[1]), 1722);
    }
    
    public function testCalculateAndAliasesWithMultipleValuesReturnsExpectedElos()
    {
        $tests = array(
            array(16, 400, 10, 2100, 1600, 1, 1.0, 1.0, null, null, 2101, 1599),
            array(16, 10, 2, 1800, 1600, 0, 1.0, 1.0, null, null, 1784, 1616),
            array(32, 100, 10, 1800, 1600, 0.5, 1.0, 1.0, null, null, 1784, 1616),
            array(32, 100, 10, 1800, 1600, 0.0, 0.0, 1.0, null, null, 1768, 1600),
            array(24, 400, 10, 1800, 1600, 1.0, 0.2, 1.0, null, null, 1806, 1599),
            array(24, 400, 10, 1800, 1200, 0.5, 0.2, 0.8, null, null, 1789, 1205),
            array(24, 400, 10, 1200, 1600, 1.0, 0.6, 0.8, null, null, 1222, 1583),
            array(24, 400, 10, 1200, 1600, 0.2, 0.6, 0.8, null, null, 1203, 1598),
            array(24, 400, 10, 1200, 1600, 0.75, 0.6, 0.8, null, null, 1216, 1587),
            array(12, 400, 10, 1800, 1500, 0.4, 0.6, 0.0, null, null, 1798, 1505),
            array(24, 400, 10, 1800, 1500, 0.5, 1.0, 1.0, 30, 10, 1790, 1503),
            array(24, 400, 10, 1800, 1500, 0.5, 1.0, 1.0, 10, 20, 1797, 1507),
            array(24, 400, 10, 1800, 1500, 0.0, 1.0, 1.0, 10, null, 1792, 1520),
            array(24, 400, 10, 1800, 1500, 1.0, 1.0, 1.0, null, 20, 1804, 1497),
        );
        
        foreach ($tests as $t) {
           $eloSystem = new EloSystem($t[0], $t[1], $t[2]);
           
           $updated = $eloSystem->calculate($t[3], $t[4], $t[5], $t[6], $t[7], $t[8], $t[9]);
           
           $values = print_r($t, true);
           
           $this->assertEquals(round($updated[0]), $t[10], "first updated elo unexpected with values $values");
           $this->assertEquals(round($updated[1]), $t[11], "second updated elo unexpected with values $values");
           
           $alias = null;
           
           if (1 === $t[5]) {
               $alias = 'win';
           }
           
           if (0 === $t[5]) {
               $alias = 'lose';
           }
           
           if (0.5 === $t[5]) {
               $alias = 'draw';
           }
           
           if (null !== $alias) {
               $aliasUpdated = $eloSystem->$alias($t[3], $t[4], $t[6], $t[7], $t[8], $t[9]);
               
               $this->assertEquals($updated[0], $aliasUpdated[0], "first updated elo with $alias alias method is different with values $values");
               $this->assertEquals($updated[1], $aliasUpdated[1], "second updated elo with $alias alias method is different with values $values");
           }
        }
    }
    
    public function testCalculateEloThrowsExceptionOnWinMoreThan1()
    {
        $this->setExpectedException('\Alcalyn\Elo\Exception\EloCoefficientException');
        $elo = new EloSystem();
        $elo->calculate(2042, 1458, 1.00001);
    }
    
    public function testCalculateEloThrowsExceptionOnWinLessThan0()
    {
        $this->setExpectedException('\Alcalyn\Elo\Exception\EloCoefficientException');
        $elo = new EloSystem();
        $elo->calculate(2042, 1458, -0.00001);
    }
    
    public function testCalculateEloThrowsExceptionOnReliabilityMoreThan1()
    {
        $this->setExpectedException('\Alcalyn\Elo\Exception\EloCoefficientException');
        $elo = new EloSystem();
        $elo->calculate(2042, 1458, 0.5, 1.00001, 1.00001);
    }
    
    public function testCalculateEloThrowsExceptionOnReliabilityLessThan0()
    {
        $this->setExpectedException('\Alcalyn\Elo\Exception\EloCoefficientException');
        $elo = new EloSystem();
        $elo->calculate(2042, 1458, 0.0, -0.00001, 1.0);
    }
}
