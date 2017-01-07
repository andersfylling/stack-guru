<?php

/*
 * Run autoloaders for subfolders.
 */
$dir = __DIR__;
require "$dir/config/autoloader.php";
require "$dir/vendor/autoload.php";
require "$dir/src/AutoLoader.php";

$loader = new \StackGuru\AutoLoader;
$loader->register();
$loader->addNamespace("StackGuru", "$dir/src");
$loader->addNamespace("StackGuru", "$dir/tests");
