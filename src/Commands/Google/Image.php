<?php

namespace StackGuru\Commands\Google;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils;


class Image extends AbstractCommand
{
    use UrlHelper;
    
    protected static $name = "image";
    protected static $description = "search images";


    public function process (string $query, ?CommandContext $ctx) : string
    {
        $args = explode(' ', $query);

        if (sizeof($args) >= 1 && 0 !== strlen($args[0])) {
            $query = implode(" ", $args);
        } else {
            $query = "Why am I such an asshole?";
        }

        $link = self::buildSearchUrl([
            'q' => $query,
            "tbm" => "isch"
        ]);

        return $link;
    }
}
