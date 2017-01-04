<?php

{
    $skip = __DIR__ . "/autoloader.php";

    /*
     * Assumed to sort filenames low to high before usage.
     */
    $files = [];
    foreach (glob(__DIR__ . "/*.php") as $file)
    {
        if ($file === $skip) {
            continue;
        }

        $files[] = $file;
    }

    // Sort the files array, just in case...
    asort($files);

    // Load in each file
    foreach ($files as $file) {
        require_once $file;
    }
}
