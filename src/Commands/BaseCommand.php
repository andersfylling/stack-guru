<?php

namespace StackGuru\Commands;

class BaseCommand
{
    protected $method;

    public function __construct()
    {
        $this->response     = "[IN DEVELOPMENT] This command has yet no functionality.";
        $this->method       = "";
    }

    public final function getMethod() : string
    {
        return $this->response;
    }
}
