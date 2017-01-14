<?php

namespace StackGuru\Commands\Google;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils;


class Search extends AbstractCommand
{
    protected static $name = "search";
    protected static $description = "search";


    public function process (string $query, ?CommandContext $ctx) : string
    {
        $args = explode(' ', $query);

        if (sizeof($args) >= 1 && 0 !== strlen($args[0])) {
            $query = implode(" ", $args);
        } else {
            $query = "Why am I such an asshole?";
        }

        $link = $this->buildSearchUrl(['q' => $query]);

        return $link;
    }
}
