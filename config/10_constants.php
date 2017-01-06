<?php

/**
 * This allows for unit testing.
 * Should be set to false when added to master.
 *
 * @see \StackGuru\CoreLogic\Utils\Response Response class uses this to echo replies rather than sending them.
 */
define("DEVELOPMENT", 		$terminal_args["DEVELOPMENT"]);
define("TESTING", 			$terminal_args["TESTING"]);
define("STARTUP_TIME",		time());


// PHP_VERSION_ID
// Used to check php version in a interger format. 7.0.14 => 70014
if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);

    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}