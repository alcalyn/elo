<?php

namespace Alcalyn\Elo\Exception;

class EloCoefficientException extends \Exception
{
    /**
     * @param float $coef
     * @param string $variableName
     */
    public function __construct($coef, $variableName)
    {
        parent::__construct("'$variableName' must be in range [0;1], got '$coef'", 0, null);
    }
}
