<?php

namespace Commands;

class Test extends \Commands\Command
{
    function __construct()
    {
        $this->command = "test";
        $this->description = "test command lol";
    }

}
