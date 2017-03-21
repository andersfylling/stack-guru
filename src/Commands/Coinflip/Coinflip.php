<?php
declare(strict_types=1);

namespace StackGuru\Commands\Coinflip;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils\Response as Response;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred;


class Coinflip extends AbstractCommand
{
    protected static $name = "coinflip";
    protected static $description = "coinflip word1 word2, returns one of the words as written";

    private static $coinflip_options = ["tails", "heads"];


    public function process(string $query, CommandContext $ctx): Promise
    {
        $winner = $this->getWinner($query);
        return Response::sendMessage($winner, $ctx->message);
    }

    public function getWinner(string $query): string
    {
        $words = self::$coinflip_options;
        $selections = explode(' ', $query, 3);

        if (isset($selections[0]) && '' != trim($selections[0])) {
            $words[0] = $selections[0];
        }
        
        if (isset($selections[1]) && '' != trim($selections[1])) {
            $words[1] = $selections[1];
        }

        $winner = $words[mt_rand(0, 1)];

        return $winner;
    }

    public function getOptions(): array 
    {
        return self::$coinflip_options;
    }

}