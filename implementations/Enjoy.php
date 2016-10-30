<?php
/**
 * Just for giggles.
 *
 * @author http://github.com/sciencefyll
 */

namespace Commands;


class Enjoy implements Command
{

    private $description;
    private $help;


    public function __construct()
    {
        $this->description  = Command::defaults["description"];
        $this->help         = Command::defaults["help"];
    }

    public function command ($args, $in)
    {
        $in->reply("HAHHA *CLAP CLAP CLAP*, yes that's very funny. Ah, much good, very delicious.");
    }

    public function linkDiscordObject ($callback)
    {
        //$this->discord = $callback();
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function getHelp() : string
    {
        return $this->help;
    }

}