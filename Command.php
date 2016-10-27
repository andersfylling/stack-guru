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

    public static $description = "undefined description huh?";

    protected $discord = NULL;


    public function getDescription ()
    {
        return Command::$description;
    }

    function command ($args, $in, $self)
    {
        $in->reply("Command method for has not yet been implemented.");
    }

    /**
     * The discord object is huge, so use this method that have a returning callback.
     * The discord object will then be saved. this wont make us pass it as a parameter
     * for all commands incoming which saves memory and time.
     *
     * @param $callback
     */
    function discordRelated ($callback) {
        //$discord = $callback();
    }

}