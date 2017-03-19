<?php
declare(strict_types=1);

namespace StackGuru\Commands\Coinflip;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils\Response;


class Coinflip extends AbstractCommand
{
    protected static $name = "coinflip";
    protected static $description = "coinflip word1 word2, returns one of the words as written";



    public function process(string $query, ?CommandContext $ctx): string
    {
        $words = explode(' ', $query, 3);
        if (empty($words)) {
            $words = ["tails", "heads"];
        }
        else if (1 == sizeof($words)) {
            $words[] = ["Other option."];
        }

        $winner = $words[mt_rand(0, 1)];

        Response::sendMessage($winner, $ctx->message);

        return '';
    }

}
