<?php
/**
 * Created by PhpStorm.
 * User: Anders
 * Date: 26.10.2016
 * Time: 03.04
 */

namespace Commands;

interface Command
{
    const defaults = [
        "description"   => "[IN DEVELOPMENT] This command has no description yet.",
        "help"          => "[IN DEVELOPMENT] This command has no help information.",
        "command"       => "[IN DEVELOPMENT] This command has yet no functionality."
    ];

    public function __construct();

    public function /*        */ command             (/* string[] */ $args, /* Object */ $in);
    public function /*        */ linkDiscordObject   (/* function () : Object */ $callback);

    public function /* string */ getDescription      () : string;
    public function /* string */ getHelp             () : string;

}