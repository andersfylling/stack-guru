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
    const DESCRIPTION = "Service command description";
    const DEFAULT = "Shutdown";

    public function __construct()
    {
        parent::__construct();
    }

}