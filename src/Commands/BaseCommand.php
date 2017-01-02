<?php

namespace StackGuru\Commands;

class BaseCommand
{
    protected $method;
    const DEFAULT = null;

    public function __construct()
    {
        $this->response             = "[IN DEVELOPMENT] This command has yet no functionality.";
        $this->method               = "";
        $this->defaultSubcommand    = null;
    }


    public final function getMethod() : string
    {
        return $this->response;
    }
}
