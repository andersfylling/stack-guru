<?php
/**
 * Just for giggles.
 *
 * @author http://github.com/sciencefyll
 */

namespace StackGuru\Commands;


class Awesome implements Command
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
        $in->reply("That's awesome.");
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
