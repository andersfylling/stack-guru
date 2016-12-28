<?php

{
    $skip = __DIR__ . "/autoloader.php";

    /*
     * Assumed to sort filenames low to high before usage.
     */
    foreach (glob(__DIR__ . "/*.php") as $file)
    {
        if ($file === $skip) {
            continue;
        }

        require_once $file;
    }
}
