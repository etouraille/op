<?php
// tests/Service/NewsletterGeneratorTest.php
namespace App\Tests\Datetime;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class test extends KernelTestCase
{
    public function testSomething()
    {

        $start = new \DateTime('2022-11-25 17:44:28');
        $end = new \Datetime('2022-11-26 06:34:11');
        $delta = $start->diff($end)->format("%r%a");


        $this->assertEquals(2, $delta);
    }
}