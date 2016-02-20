<?php
/**
 * Created by PhpStorm.
 * User: parthpatel1001
 * Date: 2/19/16
 * Time: 6:52 PM
 */

namespace Analyzer\Helpers;

// TODO hook up better math stuff here
class Math
{
    public function add(float $a, float $b)
    {
        return $a + $b;
    }

    public function mult(float $a, float $b)
    {
        return $a * $b;
    }

    public function div(float $a, float $b)
    {
        return $a/b;
    }

    public function sub(float $a, float $b)
    {
        return $a - $b;
    }

    public function average(array $nums)
    {
        return array_sum($nums)/count($nums);
    }

    public function percentChange(float $a, float $b)
    {
        return ($b/$a) - 1;
    }
}