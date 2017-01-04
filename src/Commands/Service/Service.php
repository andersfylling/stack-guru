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
    const DESCRIPTION = "bot service commands";
    const DEFAULT = "Shutdown";

    public function __construct()
    {
        parent::__construct();
    }

    public function process (string $query, \StackGuru\CommandContext $ctx = null) : string
    {
        echo "Not yet implemented", PHP_EOL;
    }
}
