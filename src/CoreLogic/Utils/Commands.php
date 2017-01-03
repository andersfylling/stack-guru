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

    public static function firstWordIsACommand (string $query) : string
    {
        return self::wordIsACommand(self::getFirstWordFromString($query));
    }

    public static function wordIsASubCommand (string $word, array $arr) : bool
    {
        return isset($arr[$word]);
    }

    public static function wordIsACommand (string $word, array $arr = null) : string
    {
        if (null === $arr) {
            return self::wordIsACommand($word, self::$commands);
        }

        if (self::wordIsASubCommand($word, $arr)) {
            return $word;
        }

        foreach ($arr as $val) {
            if (is_array($val)) {
                $vv = self::wordIsACommand($word, $val);
                if ('' !== $vv) {
                    return $vv;
                }
            }
        }

        return '';
    }
}
