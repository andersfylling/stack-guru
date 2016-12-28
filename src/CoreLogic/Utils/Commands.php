<?php

namespace StackGuru\CoreLogic\Utils;


class Commands
{
    private $commands = [
        // string "command_name"    => [
        //      string "command_name"       => [boolean sudo, string class, string description],
        //      string "sub_command_name"   => [boolean sudo, string class, string description],
        // ],
    ];

    public static test () {}


	public static function getClassInstance ()
	{

	}

	private function constructOverviewArray (array $options = []) : array
	{

        $options = Utils\ResolveOptions::verify($options, ["folder"]);

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
                            $this->commands[strtolower($classNamespace)][$commandName] = $command;
                        }
                    }
                }
            }
        } // foreach() END

        return $this->commands;
	}
}