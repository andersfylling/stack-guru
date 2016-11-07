<?php
/**
 * Command for googling
 *
 * @author http://github.com/sciencefyll
 */

namespace Commands;


class Google implements Command
{
    const SEARCH_URL = "http://www.google.com/search";

    private $description;
    private $help;


    public function __construct()
    {
        $this->description  = Command::defaults["description"];
        $this->help         = Command::defaults["help"];
    }

    public function command ($args, $in)
    {
        if (sizeof($args) > 0) {
            $query = implode(" ", $args);
        }
        else {
            $query = "Why am I such an asshole?";
        }
        $url = $this->searchURL($query);
        $msg = "Let me google that for you..\n" . $url;

        $in->reply($msg);
    }

    public function linkDiscordObject ($callback)
    {
        //$this->discord = $callback();
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function getHelp() : string
    {
        return $this->help;
    }

    private function searchURL($query) : string
    {
        return self::SEARCH_URL . "?" . http_build_query(array('q' => $query));
    }

}
