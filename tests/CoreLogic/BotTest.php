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

    public function testLoadCommands ()
    {
        $s = true;
        $bot = new \StackGuru\CoreLogic\Bot([], $s);

        $config = [
            "folder" => "./src/Commands"
        ];

        $commands = $bot->loadCommands($config);

        //var_dump($commands);

        /*
         * Check that commands are loaded
         */
        $this->assertArrayHasKey("google",      $commands);
        $this->assertArrayHasKey("service",     $commands);


        /*
         * Check that the commands have loaded all their sub-commands
         */

        $google = $commands["google"];// doesn't load Google.php, Image.php. only Search.php
        $this->assertArrayHasKey("google",      $google);
        $this->assertArrayHasKey("search",      $google);
        $this->assertArrayHasKey("image",       $google);

        $service = $commands["service"];// doesn't load Service.php, nor Crash.php
        $this->assertArrayHasKey("service",     $service);
        $this->assertArrayHasKey("shutdown",    $service);
        $this->assertArrayHasKey("crash",       $service);

    }



    /*************************************************
     * ********************************************* *
     *************************************************/


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