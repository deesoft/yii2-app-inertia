<?php

namespace app\helpers;

/**
 * Description of Numeric
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Numeric
{
    
    /**
     *
     * @param float $val1
     * @param float $val2
     * @return bool
     */
    public static function isEqual($val1, $val2)
    {
        return abs($val1 - $val2) < TOLERANCE;
    }

    /**
     *
     * @param float $val
     * @return bool
     */
    public static function isZero($val)
    {
        return abs($val) < TOLERANCE;
    }

    /**
     *
     * @param float $val
     * @return bool
     */
    public static function isPositive($val)
    {
        return $val > TOLERANCE;
    }

    /**
     *
     * @param float $val
     * @return bool
     */
    public static function isNegative($val)
    {
        return $val < -TOLERANCE;
    }
}
