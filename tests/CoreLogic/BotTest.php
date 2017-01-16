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
        $bot = new \StackGuru\Core\Bot([], $s);


        //$this->assertEquals($commands->getCommands(), $bot->getCommands());
    }

    public function testLoadCommands ()
    {
        return; //TODO UPDATE THIS PLEASE, new file called Commands now..

        $s = true;
        $bot = new \StackGuru\Core\Bot([], $s);

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

        /*
         * Test some of the object types for sub commands
         */
        $this->assertInstanceOf(\StackGuru\Commands\Google\Google::class,       $commands["google"]["google"]);
        $this->assertInstanceOf(\StackGuru\Commands\Google\Search::class,       $commands["google"]["search"]);
        $this->assertInstanceOf(\StackGuru\Commands\Google\Image::class,        $commands["google"]["image"]);

        $this->assertInstanceOf(\StackGuru\Commands\Service\Service::class,     $commands["service"]["service"]);
        $this->assertInstanceOf(\StackGuru\Commands\Service\Shutdown::class,    $commands["service"]["shutdown"]);
        $this->assertInstanceOf(\StackGuru\Commands\Service\Crash::class,       $commands["service"]["crash"]);

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
        $bot = new \StackGuru\Core\Bot($options, $shutup);

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
