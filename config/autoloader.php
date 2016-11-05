<?php

{
    $skip = __DIR__ . "/autoloader.php";

    foreach (glob(__DIR__ . "/*.php") as $file)
    {
        if ($file === $skip) {
            continue;
        }

        echo $file . PHP_EOL;

        require_once $file;
    }
}