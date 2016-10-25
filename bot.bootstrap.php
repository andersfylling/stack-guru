<?php

include __DIR__.'/vendor/autoload.php';

use Discord\Discord;

$discord = new Discord([
    'token' => 'MjQwNjIwNjA3MDM1ODAxNjA3.CvF-7A.ugfb5OgkbalSMXOwUm3lcA-EUu4',
]);

$discord->on('ready', function ($discord) {
    echo "Bot is ready!", PHP_EOL;

    // Listen for messages.
    $discord->on('message', function ($message, $discord) {
        echo "{$message->author->username}: {$message->content}",PHP_EOL;
    });
});

$discord->run();
