<?php
/**
 * Created by PhpStorm.
 * User: anders
 * Date: 11/6/16
 * Time: 2:18 AM
 */

namespace StackGuru\Commands\Service;


class Service extends \StackGuru\Commands\BaseCommand
{
    protected static $name = "service";
    protected static $description = "bot service commands";
    protected static $default = "shutdown";


    public function process (string $query, ?\StackGuru\Commands\CommandContext $ctx = null) : string
    {
        return "Not implemented yet";
    }
}
