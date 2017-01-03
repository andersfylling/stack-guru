<?php

namespace StackGuru\Commands\Drug;

class Drug extends \StackGuru\Commands\BaseCommand
{
    const DESCRIPTION = "Drug stuff";
    const DEFAULT = "Info"; // default command

    public function __construct()
    {
        parent::__construct();
    }
}
