<?php

{
    $skip = __DIR__ . "/config/autoloader.php";

    foreach (glob(__DIR__ . "/config/*.php") as $file)
    {
        if ($file === $skip) {
            continue;
        }

        require_once $file;
    }
}