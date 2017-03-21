<?php

use \PHPUnit\Framework\TestCase;


class CoinflipTest extends TestCase 
{

    public function testOutput()
    {

        $ctx = new \StackGuru\Core\Command\CommandContext();
        $coinflip = new \StackGuru\Commands\Coinflip\Coinflip();
        $runs = 100;

        // make sure either coin or tails comes up
        $possibilities = $coinflip->getOptions();
        for ($i = 0; $i < $runs; $i++) {
            $this->assertTrue(in_array($coinflip->getWinner(""), $possibilities, true));
        }

        // make sure either test1 or tails comes up
        $possibilities = $coinflip->getOptions();
        $possibilities[0] = "test1";
        for ($i = 0; $i < $runs; $i++) {
            $this->assertTrue(in_array($coinflip->getWinner("test1"), $possibilities, true));
        }

        // make sure either test1 or test2
        $possibilities = [];
        $possibilities[0] = "test1";
        $possibilities[1] = "test2";
        for ($i = 0; $i < $runs; $i++) {
            $this->assertTrue(in_array($coinflip->getWinner("test1 test2"), $possibilities, true));
        }
    }
}
