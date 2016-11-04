<?php
/**
 * For interacting with the interface
 *
 * @author http://github.com/sciencefyll
 */

namespace StackGuru\Commands;
use \ReflectionClass, \ReflectionMethod;

class Service
    implements Command
{
    private $discord = null;

    private $description;
    private $help;

    private $in;


    public function __construct()
    {
        $this->description  = Command::defaults["description"];
        $this->help         = Command::defaults["help"];
    }

    public function command ($args, $in)
    {
        $this->in = $in;
        //$in->reply("lol");

        /*
         * If there are not arguments behind the command, theres nothing to do here.
         */
        if (empty($args)) {
            $this->in->reply("You must specify an argument!");
            return;
        }

        /*
         * This Service class requires the $discord instance to work.
         *
         */
        if ($this->discord == NULL) {
            $this->in->reply("Discord instances was not set!");
            return;
        }

        /*
         * store first arg as action
         */
        $action = $args[0];

        /**
         * Command interactions below
         */
        $class = new ReflectionClass($this);
        $methods = $class->getMethods(ReflectionMethod::IS_PRIVATE);


        $result = false;
        for ($i = sizeof($methods) - 1; $i >= 0; $i -= 1) {
            if ($methods[$i]->name == $action) {
                $result = $methods[$i]->name;
            }
        }

        if ($result === false) {
            $in->reply("That's not an option!");
            return;
        }

        $this->{$result}();

    }

    /**
     * Returns $discord instance
     *
     * @param $callback
     * @return mixed
     */
    public function linkDiscordObject ($callback)
    {
        $this->discord = $callback();
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function getHelp() : string
    {
        return $this->help;
    }


    /**
     * Sub commands
     */

    private function shutdown ()
    {
        $this->in->reply("Shutting down..");
        $this->in->reply("Bye bye!")->then(function () {
            $this->discord->close();

            echo "Shutting down!", PHP_EOL;
            exit();
        });
    }

    private function crash ()
    {
        trigger_error("Fatal error", E_USER_ERROR);
    }
}
