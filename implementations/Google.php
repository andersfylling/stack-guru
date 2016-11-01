<?php
/**
 * Command for googling
 *
 * @author http://github.com/sciencefyll
 */

namespace Commands;


class Google implements Command
{
    private $googleURL = "http://www.google.com/search?q="; // Google+tutorial+create+link";
    private $googleURLDelimiter = "+";

    private $description;
    private $help;


    public function __construct()
    {
        $this->description  = Command::defaults["description"];
        $this->help         = Command::defaults["help"];
    }

    public function command ($args, $in)
    {
        if (sizeof($args) > 0) {
            $argsString = implode($this->googleURLDelimiter, $args);
            $url = $this->googleURL . htmlentities($argsString);
        }
        else {
            $url = "{$this->googleURL}Why+am+I+such+an+asshole?";
        }
        $msg = "Let me google that for you..\n" . $url;

        $in->reply($msg);
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