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
        $command = null;
        $options = $this->commands;
        $response = [$query, null];

        while (true) {
            /*
             * No command given, send a potential command
             */
            if ($command === null) {
                $command = $this->firstWordIsACommand($query);
                continue;
            }

            /*
             * The first word, aka command, wasn't valid.
             * The first word is not a command.
             */
            else if ($command === '') {
                return [$query, null];
            }


            /*
             * Handle the command logic
             */
            $options = $options[$command];

        }



        

        /*
         * Run command if set......
         * 1w: main
         * 2w: sub
         *
         * or
         *
         * 1w: main
         * 2w - nw: args
         */
        $word = $this->firstWordIsACommand($message);
        if ($word !== '') {

            // store command
            $command = $this->commands[$word];

            // remove valid command word from the string
            $query = ltrim(substr($word, $query));


            // if no sub command
            // go into the command.command.DEFAULT and use that class
            // 
        }
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

    public static function firstwordIsACommand (string $query) : string
    {
        $words = explode(" ", strtolower(trim($query)));

        return self::wordIsACommand($words[0]);
    }

    public static function wordIsACommand (string $word) : string
    {
        if (array_key_exists($word, self::$commands)) {
            return $word;
        }
        else {
            return '';
        }
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