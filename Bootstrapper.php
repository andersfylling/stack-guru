<?php
/**
 * Created by PhpStorm.
 * User: Anders
 * Date: 27.10.2016
 * Time: 15.56
 */

namespace CoreLogic;


class Bootstrapper
{
    private $commands;
    private $folder;

    function __construct($folder)
    {
        $this->folder = $folder;
    }

    function linkCommands ()
    {
        $this->commands = []; //reset

        echo "> Linking commands:",PHP_EOL;

        foreach (glob("./{$this->folder}/*.php") as $file)
        {
            require_once $file;

            // get the file name of the current file without the extension
            // which is essentially the class name
            $basename = basename($file, '.php');
            $command = strtolower($basename);
            $class = "\\Commands\\" . $basename;

            if (class_exists($class)) {
                echo "\t{$command}.. ";

                $this->commands[$command] = [$class, (new $class)->getDescription()];

                echo "OK!", PHP_EOL;
            }
        }
    }

    function getCommands ()
    {
        return $this->commands;
    }

}