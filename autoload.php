<?php

// configs
require_once __DIR__."/config/constants.php";
require_once __DIR__."/config/discord.php";

// composer autoload
require __DIR__."/vendor/autoload.php";

// class autoload
require __DIR__."/src/AutoLoader.php";

$loader = new \StackGuru\AutoLoader;
$loader->register();
$loader->addNamespace('StackGuru', __DIR__.'/src');
