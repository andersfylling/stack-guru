<?php

namespace StackGuru\Commands\Google;

use StackGuru\CoreLogic\Utils;


class Image extends \StackGuru\Commands\BaseCommand
{
    use UrlHelper;

    protected static $name = "image";
    protected static $description = "search images";


    public function process (string $query, ?\StackGuru\Commands\CommandContext $ctx = null) : string
    {
        $args = explode(' ', trim($query) . ' ');

        if (sizeof($args) !== 0) {
            $query = implode(" ", $args);
        } else {
            $query = "Why am I such an asshole?";
        }

        $link = $this->buildSearchUrl(['q' => $query]);

        $msg = "Let me google that for you..\n" . $link;
        if ($ctx)
            Utils\Response::sendResponse($msg, $ctx->message);

        return $link;
    }
}
