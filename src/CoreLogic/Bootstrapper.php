<?php
/**
 * Loads all the possible commands.
 *
 * @author http://github.com/sciencefyll
 */

namespace StackGuru\CoreLogic;


class Bootstrapper
{
    private $commands;
    private $folder;

    function __construct(string $folder = "")
    {
        $this->folder = $folder;
    }

    function linkCommands ()
    {
        $this->commands = []; //reset

        foreach (glob("{$this->folder}/*.php") as $file)
        {
            require_once $file;

            // get the file name of the current file without the extension
            // which is essentially the class name
            $basename = basename($file, '.php');
            $command = strtolower($basename);
            $class = "\\StackGuru\\Commands\\" . $basename;

            if (class_exists($class)) {
                $this->commands[$command] = [$class, (new $class)->getDescription()];
                echo "> Loaded command '{$command}'", PHP_EOL;
            }
        }
    }

    function getCommands ()
    {
        return $this->commands;
    }

}
