<?php

use PHPUnit\Framework\TestCase;

class BotTest extends TestCase
{


    /**
     *
     */
    public function testUpdateCommands ()
    {
        $options = [
            "discordToken" => DISCORD_TOKEN,
            "commandsFolder" => __DIR__."/src/Commands/Command",
            "databaseFile" => "",
        ];

        $bot = $this->setupBot();

        $commands = new \StackGuru\CoreLogic\Bootstrapper();
        $commands->linkCommands();

        $this->assertEquals($commands->getCommands(), $bot->getCommands());
    }

    /**
     * @param array $options
     * @param callable|null $callback
     * @param bool|null $start
     * @return \StackGuru\CoreLogic\Bot
     */
    private function setupBot (array $options = [], Callable $callback = null, boolean $start = null)
    {
        $bot = new \StackGuru\CoreLogic\Bot($options);

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