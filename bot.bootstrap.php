<?php

require __DIR__.'/vendor/autoload.php';
require "./Command.php";

use Discord\Discord;

$config = [
    'token' => 'MjQwNjIwNjA3MDM1ODAxNjA3.CvF-7A.ugfb5OgkbalSMXOwUm3lcA-EUu4',
];

$discord = new Discord($config);

$commands = [
    // "command" => ["class", "description"]
];


/**
 * Bootstrap Command class.
 */

foreach (glob('./implementations/*.php') as $file)
{
    require_once $file;

    // get the file name of the current file without the extension
    // which is essentially the class name
    $class = "\\Commands\\" . basename($file, '.php');

    if (class_exists($class))
    {
        $obj = new $class;

        $commands[$obj->getCommand()] = [$class, $obj->getDescription()];
    }
}

$discord->on('ready', function ($this) use ($discord) {
    echo "Bot is ready!", PHP_EOL;

    // Listen for messages.
    $this->on('message', function ($in) use ($this, $discord) {
        if ($in->author->id == $this->id) {
            return;
        }

        if ($in->content == "@_stack-guru shutdown") {
            $in->reply("Shutting down..");
            $discord->close();
            return;
        }

        echo "Something was written.\n";
    });
});

$discord->run();

/**
 * object(Discord\Parts\Channel\Message)#1743 (15) {
["id"]=>
string(18) "240659390275780608"
["channel_id"]=>
string(18) "240622450084282387"
["content"]=>
string(3) "dfs"
["type"]=>
int(0)
["mentions"]=>
object(Discord\Helpers\Collection)#1713 (0) {
}
["author"]=>
object(Discord\Parts\User\Member)#1631 (9) {
["user"]=>
object(Discord\Parts\User\User)#1735 (5) {
["id"]=>
string(18) "228846961774559232"
["username"]=>
string(10) "anders_463"
["avatar"]=>
NULL
["discriminator"]=>
string(4) "7237"
["bot"]=>
NULL
}
["roles"]=>
object(Discord\Helpers\Collection)#247 (1) {
["240622539380883456"]=>
object(Discord\Parts\Guild\Role)#625 (9) {
["id"]=>
string(18) "240622539380883456"
["name"]=>
string(13) "Bot Engineers"
["color"]=>
int(15105570)
["managed"]=>
bool(false)
["hoist"]=>
bool(true)
["position"]=>
int(1)
["permissions"]=>
object(Discord\Parts\Permissions\RolePermission)#626 (24) {
["create_instant_invite"]=>
bool(true)
["kick_members"]=>
bool(false)
["ban_members"]=>
bool(false)
["administrator"]=>
bool(false)
["manage_channels"]=>
bool(false)
["manage_server"]=>
bool(false)
["change_nickname"]=>
bool(false)
["manage_nicknames"]=>
bool(false)
["manage_roles"]=>
bool(false)
["read_messages"]=>
bool(true)
["send_messages"]=>
bool(true)
["send_tts_messages"]=>
bool(true)
["manage_messages"]=>
bool(false)
["embed_links"]=>
bool(true)
["attach_files"]=>
bool(true)
["read_message_history"]=>
bool(false)
["mention_everyone"]=>
bool(true)
["voice_connect"]=>
bool(true)
["voice_speak"]=>
bool(true)
["voice_mute_members"]=>
bool(false)
["voice_deafen_members"]=>
bool(false)
["voice_move_members"]=>
bool(false)
["voice_use_vad"]=>
bool(true)
["bitwise"]=>
int(37018625)
}
["mentionable"]=>
bool(true)
["guild_id"]=>
string(18) "239926482674253825"
}
}
["deaf"]=>
bool(false)
["mute"]=>
bool(false)
["joined_at"]=>
object(Carbon\Carbon)#590 (3) {
["date"]=>
string(26) "2016-10-24 21:02:05.060949"
["timezone_type"]=>
int(1)
["timezone"]=>
string(6) "+00:00"
}
["guild_id"]=>
string(18) "239926482674253825"
["status"]=>
string(6) "online"
["game"]=>
object(Discord\Parts\User\Game)#250 (3) {
["name"]=>
NULL
["url"]=>
NULL
["type"]=>
NULL
}
["nick"]=>
NULL
}
["mention_everyone"]=>
bool(false)
["timestamp"]=>
object(Carbon\Carbon)#598 (3) {
["date"]=>
string(26) "2016-10-26 02:14:29.877000"
["timezone_type"]=>
int(1)
["timezone"]=>
string(6) "+00:00"
}
["edited_timestamp"]=>
NULL
["tts"]=>
bool(false)
["attachments"]=>
array(0) {
}
["embeds"]=>
array(0) {
}
["nonce"]=>
string(18) "240659390485495808"
["mention_roles"]=>
object(Discord\Helpers\Collection)#597 (0) {
}
["pinned"]=>
bool(false)
}

The $in object
 */
