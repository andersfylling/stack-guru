<?php

namespace StackGuru\CoreLogic\Utils;


class Commands
{
    //private $commands = [
        // string "command_name"    => [
        //      string "command_name"       => [boolean sudo, string class, string description],
        //      string "sub_command_name"   => [boolean sudo, string class, string description],
        // ],
    //];

    private static $commands = []; // mus run constructOverviewArray.. ugly..


    /**
     * Returns the command instance for the latest relative command in the string.
     * @return array instance and new query string after matched command
     */
	public static function getCommandInstance (string $query) : array
	{
        $prevCommand = null;
        $nextCommand = self::getFirstWordFromString($query);
        $options = self::$commands;
        $response = [
            "query" => $query, 
            "instance" => null
        ];

        $maxChain = 10;
        $depth = 0;

        while ($depth++ < $maxChain) {
            /*
             * Update command to the new first word..
             */
            

            $nextCommand = self::getFirstWordFromString($query);

            /*
             * The first word, aka command, wasn't valid.
             * The first word is not a command.
             *
             * assumption1: $options is always an array
             * 
             * assumption2: if there are no matches, it might be a default command..
             *                  Google.google = default..
             */
            if (!isset($options[$nextCommand])) {

                if (null === $prevCommand || !isset($options[$prevCommand])) {
                    return $response;
                }

                $className = $options[$prevCommand];
                $nextCommand = strtolower($className::DEFAULT); // get default command from main command class

                // check for programming mistakes
                if (null === $nextCommand) {
                    //error!!!!!
                    echo "ERROR! Commands default was null: $query", PHP_EOL;
                    return $response;
                }
            }


            /*
             * Handle the command logic
             */
            $options                = $options[$nextCommand]; // go into the new sub array or extract the object
            $query                  = $nextCommand === ltrim(substr($query, 0, strlen($nextCommand))) ? ltrim(substr($query, strlen($nextCommand))) : $query; // remove the command word from the query string if exists
            $prevCommand            = $nextCommand;
            $response["query"]      = trim($query);

            // if options isnt an array anymore, but an object. its a match!
            if (is_object($options)) {
                $response["instance"] = $options;

                return $response;
            }

        }

        return $response; // assumed to never be called..
	}

    public static function getCommands (string $key = null) : array 
    {
        if ($key !== null && isset(self::$commands[$key])) {
            return self::$commands[$key];
        }
        else {
            return self::$commands;
        }
    }

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

	public static function constructOverviewArray (array $options = []) : array
	{

        $options = ResolveOptions::verify($options, ["folder"]);

        // Find command files
        function dig (string $folder, bool $ignoreFiles = null) : array
        {
            $commands = [];
            foreach (glob($folder . "/*") as $path)
            {
                $file = substr(strrchr($path, "/"), 1);

                if (strpos($path, ".php") !== false) {
                    if ($ignoreFiles !== true) {
                        $commands[] = $file;
                    }
                }
                else if (is_dir($path)) {
                    $files = dig($path);
                    if (sizeof($files) > 0) {
                        $commands[] = $files;
                    }
                }
            }

            if ($ignoreFiles !== true) {
                return [$folder => $commands];
            } else {
                return $commands;
            }
        } // dig() END

        $commandFiles = dig($options["folder"], true);
        $_commands = [];

        // Load files
        foreach ($commandFiles as $fileSet) {
            foreach ($fileSet as $folder => $files) {
                foreach ($files as $filename) {
                    //$path = $folder."/".$filename;

                    $classNamespace = ucfirst(basename($folder));
                    $className = ucfirst(basename($filename, '.php'));
                    $class = "\\StackGuru\\Commands\\${classNamespace}\\${className}";
                    if (class_exists($class)) {
                        $interfaces = class_implements($class);
                        if (isset($interfaces["StackGuru\\CommandInterface"]) || $className === $classNamespace) {
                            $commandName = strtolower($className); // eg. Google, Service, etc.
                            $command = new $class();
                            $_commands[strtolower($classNamespace)][$commandName] = $command;
                        }
                    }
                }
            }
        } // foreach() END


        // update class gloval var
        self::$commands = $_commands;  

        // return result
        return $_commands;
	}
}