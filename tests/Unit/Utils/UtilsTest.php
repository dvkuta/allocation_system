<?php

require __DIR__ . '/../../bootstrap.php';

use Tester\Assert;

class UtilsTest extends Tester\TestCase
{
    public function testOne()
    {
        $actual = \App\Tools\Utils::getAllocationString(5, 40);
        $expected = '5h (FTE: 0.125)';
        Assert::same($expected,$actual,"Spatny text alokace");
    }

    public function testTwo()
    {
        $actual = \App\Tools\Utils::getAllocationString(40, 40);
        $expected = '40h (FTE: 1)';
        Assert::same($expected,$actual,"Spatny text alokace");
    }

    public function testThree()
    {
        $actual = \App\Tools\Utils::getAllocationString(40, 0);
        $expected = '';
        Assert::same($expected,$actual,"Spatny text alokace");
    }

}

# Spuštění testovacích metod
(new UtilsTest())->run();