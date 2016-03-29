<?php
/**
 * Created by PhpStorm.
 * User: parthpatel1001
 * Date: 2/19/16
 * Time: 6:52 PM
 */

namespace Analyzer\Helpers;

// TODO hook up better float handling library here
class Math
{
    public function add($a, $b)
    {
        return $a + $b;
    }

    public function mult($a, $b)
    {
        return $a * $b;
    }

    public function div($a, $b)
    {
        return $a/$b;
    }

    public function sub($a, $b)
    {
        return $a - $b;
    }

    public function average(array $nums)
    {
        return array_sum($nums)/count($nums);
    }

    public function percentChange($a, $b)
    {
        return ($b/$a) - 1;
    }
}