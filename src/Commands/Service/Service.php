<?php
/**
 * Created by PhpStorm.
 * User: anders
 * Date: 11/6/16
 * Time: 2:18 AM
 */

namespace StackGuru\Commands\Service;


class Service extends \StackGuru\Command implements \StackGuru\CommandInterface
{
    protected $name = "service";
    protected $description = "bot service commands";
    protected $default = "Shutdown";


    public function process (string $query, \StackGuru\CommandContext $ctx = null) : string
    {
        return "Not implemented yet";
    }
}
