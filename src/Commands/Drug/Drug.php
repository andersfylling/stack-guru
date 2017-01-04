<?php

namespace StackGuru\Commands\Drug;

class Drug extends \StackGuru\Command
{
    const DESCRIPTION = "find drug information";
    const DEFAULT = "Info"; // default command

    public function __construct()
    {
        parent::__construct();
    }
}
