<?php

/*
 * INPUTS:
 * php stack-guru.php arg1 arg2 arg3 arg4 arg5
 * php stack-guru.php --dev
 * php stack-guru.php --devel
 * php stack-guru.php --development
 * php stack-guru.php --DEVELOPMENT
 * 
 */

$terminal_args = [
	/* Identifier => [alias1, alias2, alias3, ..., aliasN]*/
	"DEVELOPMENT" 	=> ["dev", "devel", "development", "devie"],
	"TESTING" 		=> ["testing", "tests", "test"]
];

{
	$finishedArgs = [];

	/*
	 * Skip first $argv as this is the file location for stack-guru.php
	 */
	for ($i = 1; $i < $argc; $i++) {
		/*
		 * If he two first isnt "--", the argument is ignored.
		 */
		$arg = $argv[$i];
		if ("--" !== substr($arg, 0, 2)) {
			continue;
		}

		/*
		 * Remove the first two characters: "--".
		 */
		$arg = substr($arg, 2);

		/*
		 * Find argument idetifier.
		 *
		 * Syntax issues:
		 * Not optimized to handled repeating arguments nor argument synonyms..
		 */
		$match = false;
		foreach ($terminal_args as $identifier => $alias) {
			/*
			 * If identifier has already been handled, skip it.
			 */
			if (isset($finishedArgs[$identifier])) {
				continue;
			}

			/*
			 * If the arg is the identifier or a identifier alias, convert its value to true 
			 * and store the identifier in finishedArgs so it cant be repeated.
			 */
			if ( $arg === $identifier || (is_array($alias) && in_array($arg, $alias)) ) {
				$terminal_args[$identifier] = true;

				$finishedArgs[$identifier] = true;

				$match = true;

				break;
			}
		}

		/*
		 * In case its a unknown argument, lets just add it...
		 */
		if (false === $match) {
			$terminal_args[$arg] = true;
		}
	}

	/*
	 * Go through the arguments array and set unspecified once to false.
	 */
	foreach ($terminal_args as $key => $value) {
		/*
		 * Skip boolean values
		 */
		if (true === is_bool($value)) {
			continue;
		}

		/*
		 * If it's not a bool value, it's simply wrong.
		 */
		$terminal_args[$key] = false;	
	}
}