<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class DatetimeTest extends TestCase
{
    public function testSomething(): void
    {
        $start = new \DateTime('2022-11-27 17:44:28');
        $start->setTime(0, 0, 0);
        $end = new \Datetime('2022-11-26 06:34:11');
        $end->setTime(0, 0, 0);
        $delta = (int) $start->diff($end)->format("%r%a");


        $this->assertEquals(2, $delta);
    }
}
