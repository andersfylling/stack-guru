<?php
declare(strict_types=1);

namespace StackGuru\Core\Utils;


abstract class Filesystem
{
    /*
     * Finds all .php files in the given folder and all subfolders.
     *
     * @param string $folder Folder to search recursively.
     * @param bool $includeFolder Toggle whether to create a folder => files map.
     * @param bool $ignoreFiles Toggle whether to ignore files and only scan through
     *                          subfolders.
     *
     * @return array List of files and folder => files mappings.
     */
    public static function dig(string $folder, bool $includeFolder = null, bool $ignoreFiles = null): array
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
                $dirFiles = self::dig($path, true, false);
                if (sizeof($dirFiles) > 0) {
                    $files[] = $dirFiles;
                }
            }
        }

        if ($includeFolder === true) {
            return [$folder => $files];
        } else {
            return $files;
        }
    }
}
