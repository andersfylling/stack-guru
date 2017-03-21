<?php

/*
 * phpunit wont let me use $argv..
 */
$argv = [null, "--testing"];
$argc = sizeof($argv);

$terminal_args = [];
require __DIR__."/terminalArgumentsHandler.php";

require __DIR__."/autoload.php";
