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
}
