<?php

namespace StackGuru\Commands\Google;

use StackGuru\CoreLogic\Utils;

class Image extends \StackGuru\Command implements \StackGuru\CommandInterface
{
    protected $name = "image";
    protected $description = "search images";


    public function process (string $query, \StackGuru\CommandContext $ctx = null) : string
    {
        $args = explode(' ', trim($query) . ' ');

        if (sizeof($args) !== 0) {
            $query = implode(" ", $args);
        } else {
            $query = "Why am I such an asshole?";
        }

        $link = Google::SEARCH_URL . Gooogle::queryBuilder(['q' => $query]);

        $msg = "Let me google that for you..\n" . $link;
        if ($ctx)
            Utils\Response::sendResponse($msg, $ctx->message);

        return $link;
    }
}
