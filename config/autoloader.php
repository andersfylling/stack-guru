<?php

{
    $skip = __DIR__ . "/autoloader.php";

    foreach (glob(__DIR__ . "/*.php") as $file)
    {
        if ($file === $skip) {
            continue;
        }

        require_once $file;
    }
}