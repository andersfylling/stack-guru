<?php

namespace Commands;

class Test extends Command
{

    function __construct()
    {
        Test::$description = "test command lol";
    }

    function command ($args, $in, $self)
    {
        $in->reply("test logic here.");
    }

}