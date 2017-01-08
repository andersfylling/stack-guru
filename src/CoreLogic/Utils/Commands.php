<?php

namespace StackGuru\CoreLogic\Utils;

/**
 * Commands contains helper functions for the command system.
 * These are mostly string functions.
 */
class Commands
{



    public static function getFirstWordFromString (string $str) : string
    {
        $result = strstr(ltrim($str), ' ', true);
        $result = (false === $result ? $str : $result);

        return trim($result);
    }

    public static function firstWordIsACommand (string $query, array $commands) : string
    {
        return self::wordIsACommand(self::getFirstWordFromString($query), $commands);
    }

    public static function wordIsACommand (string $word, array $commands) : string
    {
        if (self::wordIsASubCommand($word, $arr)) {
            return $word;
        }

        foreach ($commands as $val) {
            if (is_array($val)) {
                $vv = self::wordIsACommand($word, $val);
                if ('' !== $vv) {
                    return $vv;
                }
            }
        }

        return '';
    }

    public static function wordIsASubCommand (string $word, array $subcommands) : bool
    {
        return isset($subcommands[$word]);
    }



    /**
     * Assemble a fully qualified class name for the given relative class name
     * components.
     *
     * @param string $namespace Command namespace
     * @param string ...$shortClassName  Class name, or components, relative
     *                                   to the command namespace.
     *
     * @return string Fully qualified class name
     */
    public static function getFullClassName (string $namespace, string ...$shortClassName) : string {
        return $namespace . "\\" . implode("\\", $shortClassName);
    }

    /**
     * Normalize a given command path, removing invalid characters and making
     * it all lowercase.
     *
     * @param array $cmdPath Command path selector, as a list of strings for every
     *                       command in the chain.
     *
     * @return array Normalized command path.
     */
    public static function normalizeCommandPath (array $cmdPath) : array
    {
        $newCmdPath = [];

        foreach ($cmdPath as $part) {
            $n = strtolower($part);
            // TODO: Remove invalid characters
            $newCmdPath[] = $n;
        }

        return $newCmdPath;
    }
}
