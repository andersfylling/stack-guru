<?php

namespace StackGuru\Commands\Google;

use StackGuru\CoreLogic\Utils;

class Image extends Google implements \StackGuru\CommandInterface
{
    const COMMAND_NAME = "google";
    const DESCRIPTION = "something about the search command";
    const SEARCH_URL = Google::URL . "search?";

    public function process (string $query, \StackGuru\CommandContext $ctx = null) : string
    {
        $args = explode(' ', trim($query) . ' ');
        
        if (sizeof($args) !== 0) {
            $query = implode(" ", $args);
        } else {
            $query = "Why am I such an asshole?";
        }

        $link = Search::SEARCH_URL . $this->queryBuilder(['q' => $query]);

        $msg = "Let me google that for you..\n" . $link;
        if ($ctx)
            Utils\Response::sendResponse($msg, $ctx->message);

        return $link;
    }
}
