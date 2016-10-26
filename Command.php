<?php
/**
 * Created by PhpStorm.
 * User: Anders
 * Date: 26.10.2016
 * Time: 03.04
 */

namespace Commands;


class Command
{

    protected $command;
    protected $description;



    function __construct()
    {
        $this->command = "undefined";
        $this->description = "undefined description huh?";
    }

    function getCommand ()
    {
        return $this->command;
    }

    function getDescription ()
    {
        return $this->description;
    }

    function command ($msg, $discord, $discordCC)
    {
        echo "Command method for " , $this->command, " has not yet been implemented.\n";
    }

}