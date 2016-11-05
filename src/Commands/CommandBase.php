<?php
/**
 * Created by PhpStorm.
 * User: anders
 * Date: 11/5/16
 * Time: 4:44 PM
 */

namespace StackGuru\Commands;


class CommandBase
{
    protected $response;
    protected $method;

    public function __construct()
    {
        $this->response     = "[IN DEVELOPMENT] This command has yet no functionality.";
        $this->method       = "";
    }

    public final function getResponse() : string
    {
        return $this->response;
    }

    public final function getMethod() : string
    {
        return $this->response;
    }
}