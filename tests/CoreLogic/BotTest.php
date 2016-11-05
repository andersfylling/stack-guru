<?php

use PHPUnit\Framework\TestCase;

class BotTest extends TestCase
{


    /**
     *
     */
    public function testUpdateCommands ()
    {
        $s = true;
        $bot = new \StackGuru\CoreLogic\Bot([], $s);


        //$this->assertEquals($commands->getCommands(), $bot->getCommands());
    }

    /**
     * @param array $options
     * @param callable|null $callback
     * @param bool|null $start
     * @return \StackGuru\CoreLogic\Bot
     */
    private function setupBot (boolean $shutup, array $options = [], Callable $callback = null, boolean $start = null)
    {
        $bot = new \StackGuru\CoreLogic\Bot($options, $shutup);

        if ($start !== null) {
            $bot->run();
        }

        if ($callback !== null) {
            $callback($bot);
        } else {
            return $bot;
        }

        if ($start !== null) {
            $bot->stop();
        }
    }
}