<?php
/**
 * interface for all commands to be written
 *
 * @author http://github.com/sciencefyll
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