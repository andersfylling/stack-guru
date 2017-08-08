<?php
declare(strict_types=1);

namespace StackGuru\Commands\Bitcoin;

use StackGuru\Core\Command\AbstractCommand;
use StackGuru\Core\Command\CommandContext;
use StackGuru\Core\Utils\Response as Response;
use React\Promise\Promise as Promise;
use React\Promise\Deferred as Deferred;

class Bitcoin extends AbstractCommand
{
    protected static $name = "bitcoin";
    protected static $description = "Get the latest bitcoin exchange rate";

    private static $url = "https://bitpay.com/api/rates";


    public function process(string $query, CommandContext $ctx): Promise
    {
        $rate = $this->getBitcoinRate();
        return Response::sendMessage($rate, $ctx->message);
    }

    public function getBitcoinRate(): string
    {
        $json = file_get_contents(self::$url);
        $data = json_decode($json, true);

        $usd = $data[1]; // 1 == USD

        return $usd["rate"] . $usd["code"];
    }

}
