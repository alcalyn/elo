<?php

namespace Alcalyn\Elo;

class EloSystem
{
    /**
     * F Factor. Determines how quickly elo scores change.
     * Default is 16
     * 
     * @var float
     */
    private $kFactor;
    
    /**
     * Defines the range of elo.
     * Default is 400
     * 
     * @var integer 
     */
    private $interval;
    
    /**
     * Defines approximatively the power between intervals.
     * Default is 10
     * 
     * @var float
     */
    private $pow;
    
    /**
     * Constructor
     * 
     * @param float $kFactor
     * @param integer $interval
     * @param float $pow
     */
    public function __construct($kFactor = 16, $interval = 400, $pow = 10)
    {
        $this->kFactor = $kFactor;
        $this->interval = $interval;
        $this->pow = $pow;
    }
    
    /**
     * Calculate new elo scores
     * 
     * @param float $elo0 elo score of player 0
     * @param float $elo1 elo score of player 1
     * @param float $win win coef for player 0. Set 1 to say that player 0 won
     * @param float $reliability0 elo reliability for player 0
     * @param float $reliability1 elo reliability for player 1
     * @param float $kFactor0 override k factor for player 0
     * @param float $kFactor1 override k factor for player 1
     * 
     * @return array with new elo score at indexes 0 and 1 for player 0 and 1
     */
    public function calculate($elo0, $elo1, $win, $reliability0 = 1, $reliability1 = 1, $kFactor0 = -1, $kFactor1 = -1)
    {
        self::checkCoef($reliability0, 'reliability0');
        self::checkCoef($reliability1, 'reliability1');
        self::checkCoef($win, 'win');
        
        $kFactor0 = ($kFactor0 < 0) ? $this->kFactor : $kFactor0;
        $kFactor1 = ($kFactor1 < 0) ? $this->kFactor : $kFactor1;
        
        /**
         * Calculate probability 0 have to beat 1
         */
        $proba = self::proba($elo0, $elo1, $this->interval, $this->pow);
        
        /**
         * Calculate elo changement
         */
        $eloUpdate0 = $win - $proba;
        $eloUpdate1 = -$eloUpdate0;
        
        /**
         * Calculate local reliability to avoid 0 and 0 reliability for new players
         * (if two new players have 0 and 0.1 reliability, they are rectified to 0.9 and 1)
         */
        $reliabilityRectification = 1 - max($reliability0, $reliability1);
        
        $reliability0 = $reliability0 + $reliabilityRectification;
        $reliability1 = $reliability1 + $reliabilityRectification;
        
        /**
         * Apply coefs K-factor and reliability of each other
         */
        $eloUpdate0 *= $kFactor0 * $reliability1 ;
        $eloUpdate1 *= $kFactor1 * $reliability0 ;
        
        return array($elo0 + $eloUpdate0, $elo1 + $eloUpdate1);
    }
    
    /**
     * Player 0 beat player 1
     * 
     * @param float $elo0 elo score of player 0
     * @param float $elo1 elo score of player 1
     * @param float $reliability0 elo reliability for player 0
     * @param float $reliability1 elo reliability for player 1
     * 
     * @return array with new elo score at indexes 0 and 1 for player 0 and 1
     */
    public function win($elo0, $elo1, $reliability0 = 1, $reliability1 = 1)
    {
        return $this->calculate($elo0, $elo1, 1, $reliability0, $reliability1);
    }
    
    /**
     * Player 0 lose against player 1
     * 
     * @param float $elo0 elo score of player 0
     * @param float $elo1 elo score of player 1
     * @param float $reliability0 elo reliability for player 0
     * @param float $reliability1 elo reliability for player 1
     * 
     * @return array with new elo score at indexes 0 and 1 for player 0 and 1
     */
    public function lose($elo0, $elo1, $reliability0 = 1, $reliability1 = 1)
    {
        return $this->calculate($elo0, $elo1, 0, $reliability0, $reliability1);
    }
    
    /**
     * Player 0 and player 1 have made a draw game
     * 
     * @param float $elo0 elo score of player 0
     * @param float $elo1 elo score of player 1
     * @param float $reliability0 elo reliability for player 0
     * @param float $reliability1 elo reliability for player 1
     * 
     * @return array with new elo score at indexes 0 and 1 for player 0 and 1
     */
    public function draw($elo0, $elo1, $reliability0 = 1, $reliability1 = 1)
    {
        return $this->calculate($elo0, $elo1, 0.5, $reliability0, $reliability1);
    }
    
    /**
     * Return probability rate that $elo0 beat $elo1
     * 
     * @param float $elo0
     * @param float $elo1
     * 
     * @return float
     */
    public static function proba($elo0, $elo1, $interval = 400, $pow = 10)
    {
        return 1 / (1 + pow($pow, ($elo1 - $elo0) / $interval)) ;
    }
    
    /**
     * Check if $coef is in range [0;1]
     * 
     * @param float $coef
     * 
     * @return boolean
     */
    private static function checkCoef($coef, $variableName = 'coefficient')
    {
        if (($coef < 0) || ($coef > 1)) {
            throw new Exception($variableName.' must be in range [0;1]');
        }
    }
}
