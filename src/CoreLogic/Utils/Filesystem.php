<?php

namespace StackGuru\CoreLogic\Utils;

class Filesystem
{
    /*
     * Finds all .php files in the given folder and all subfolders.
     */
    public static function dig (string $folder, bool $ignoreFiles = null) : array
    {
        $files = [];
        foreach (glob($folder . "/*") as $path)
        {
            $file = substr(strrchr($path, "/"), 1);

            if (strpos($path, ".php") !== false) {
                if ($ignoreFiles !== true) {
                    $files[] = $file;
                }
            }
            else if (is_dir($path)) {
                $dirFiles = self::dig($path);
                if (sizeof($dirFiles) > 0) {
                    $files[] = $dirFiles;
                }
            }
        }

        if ($ignoreFiles !== true) {
            return [$folder => $files];
        } else {
            return $files;
        }
    }
}
