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
define("PROJECT_DIR",		__DIR__);
