<?php

namespace StackGuru\Core\Utils;


/**
 * Commands contains helper functions for the command system.
 * These are mostly string functions.
 */
abstract class Commands
{
    public static function getFirstWordFromString(string $str): string
    {
        $result = strstr(ltrim($str), ' ', true);
        $result = (false === $result ? $str : $result);

        return trim($result);
    }

    public static function firstWordIsACommand(string $query, array $commands): string
    {
        return self::wordIsACommand(self::getFirstWordFromString($query), $commands);
    }

    public static function wordIsACommand(string $word, array $commands): string
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

    public static function wordIsASubCommand(string $word, array $subcommands): bool
    {
        return isset($subcommands[$word]);
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
    public static function normalizeCommandPath(array $cmdPath): array
    {
        $newCmdPath = [];

        foreach ($cmdPath as $part) {
            $n = strtolower($part);
            // TODO: Remove invalid characters
            $newCmdPath[] = $n;
        }

        return $newCmdPath;
    }

    /**
     * Determines whether a certain class name matches the criteria for a primary command.
     *
     * Specifically, this means that the class name is equivalent to the name
     * of the namespace it resides in.
     *
     * Example: isPrimaryCommand("Google/Google") == true
     *
     * @param string $className Full or relative class name of the command.
     *
     * @return bool Returns true if command is primary.
     */
    public static function isPrimaryCommand(string $className): bool
    {
        $parts = explode(NAMESPACE_SEPARATOR, $className);

        if (sizeof($parts) >= 2) {
            list($namespace, $class) = array_slice($parts, -2);

            // If class name is same as namespace, command is primary command.
            return $class == $namespace;
        }

        return false;
    }

    public static function isTopLevelCommand(string $className): bool
    {
        if (self::isPrimaryCommand($className)) {
            $parts = explode(NAMESPACE_SEPARATOR, $className);
            if (sizeof($parts) == 2)
                return true;
        }
        return false;
    }

    public static function getParentClass(string $className): ?string
    {
        $parts = explode(NAMESPACE_SEPARATOR, $className);

        if (sizeof($parts) >= 2) {
            // If command is a primary command, try to get the primary command
            // of the upper namespace.
            if (self::isPrimaryCommand($className)) {
                $path = array_slice($parts, 0, -2);
            } else {
                $path = array_slice($parts, 0, -1);
            }
            if (sizeof($path) == 0)
                return null;

            // Duplicate namespace name at the end of the class path, to get the
            // primary commands class name.
            list($namespace) = array_slice($path, -1);
            $path[] = $namespace;

            return implode(NAMESPACE_SEPARATOR, $path);
        }

        return null;
    }
}
