<?php

namespace StackGuru\Commands\Service;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils;


class View extends AbstractCommand
{
    protected static $name = "view";
    protected static $description = "View / list available services";

    private static $printf1 = "%-26s";
    private static $printf2 = "%-8s";
    private static $printf3 = "%-8s";


    public function process(string $query, ?CommandContext $ctx): string
    {
    	// Check if service exists in folder.
    	//
    	$services = $ctx->services->getAll();

        $helptext = "```Markdown" . PHP_EOL;
        $this->listServices($helptext, $ctx, $services);
        $helptext .= "```";

        return $helptext;
    }


    private static function listServices(string &$helptext, CommandContext $ctx, array $services) : string
    {
        $nr = sizeof($services);

        $title = "";
        $title .= sprintf(self::$printf1, "# Available services [{$nr}]");

        if (0 !== $nr) {
            $title .= sprintf(self::$printf2, "Enabled");
            $title .= sprintf(self::$printf3, "Running");
            $title .= "Description";
        }
        
        $helptext .= $title . PHP_EOL;

        // Print all services
        foreach ($services as $name => $service) {
            $title = "* {$name}";
            $enabled = $service->isEnabled($ctx) ? "True" : "False";
            $running = $service->running() ? "True" : "False";
            $desc    = $service->getDescription();

            // Pad command names to align command descriptions
            $line  = sprintf(self::$printf1, $title);
            $line .= sprintf(self::$printf2, $enabled);
            $line .= sprintf(self::$printf3, $running);
            $line .= $desc;


            $helptext .= $line . PHP_EOL;
        }

        return $helptext;
    }

    private static function getTrueOrFalse($serviceEntry): string
    {

    }
}
