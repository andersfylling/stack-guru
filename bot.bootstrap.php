<?php

require __DIR__.'/vendor/autoload.php';
require "./Database.php";
require "./Command.php";
require "./Bootstrapper.php";

use \Discord\Discord;
use \Discord\WebSockets\Event;

/*
 * Retrieve commands available
 */
$bootstrapper = new \CoreLogic\Bootstrapper("implementations");
$bootstrapper->linkCommands();
$commands = $bootstrapper->getCommands(); //add linked commands
echo "DONE!", PHP_EOL, PHP_EOL;

/*
 * Setup database connection
 *
 * To use the Database::$db instance. add:
 *  use \CoreLogic\Database;
 *
 * then Database::$db; is use able.
 */
new \CoreLogic\Database();

/*
 * Configure bot
 */
echo "> Configuring bot connection..", PHP_EOL;

$config = [
    'token' => 'MjQwNjIwNjA3MDM1ODAxNjA3.CvF-7A.ugfb5OgkbalSMXOwUm3lcA-EUu4',
];

$discord = new Discord($config);




$discord->on('ready', function ($self) use ($discord, $commands) {
    echo "DONE!", PHP_EOL, PHP_EOL;



    /*
     * Listen to EVERY message. Even itself.
     */
    echo "> Bot is now listening.", PHP_EOL, PHP_EOL;
    $self->on("message", function ($in) use ($self, $discord, $commands) {

        /*
         * If the bot is talking, don't reference it.
         */
        if ($in->author->id == $self->id) {
            return;
        }

        /*
         * Check if bot is mentioned.
         * TODO: optimize it... scaling = death of everything
         */
        {
            $mentions           = json_decode(json_encode($in->mentions), true);

            $mentioned          = isset($mentions[$self->id]);
            $usedBotReference   = strpos($in->content, "<@&240626683487453184>") !== false;

            /*
             * Check if this message is to the bot
             */
            if (!$in->channel->is_private) {
                /*
                 * It's a public chat, check if the bot was mentioned.
                 */
                if (!($mentioned || $usedBotReference)) {
                    return; // Bot was not mentioned nor was @Bot used: <@&240626683487453184>
                }
            }
        }

        /*
         * This is a message for the bot.
         * therefor remove the first mention which is just the mention.
         *
         * eg.
         *  <@dfksj...> command arg1 arg2 arg3 arg4
         */
        $bot_command    = "";
        $bot_args       = [];
        {
            $in->content = str_replace("<@" . $self->id . "> ", "", $in->content); // removes the mention of bot

            $words      = explode(" ", $in->content);
            $command    = $words[0];

            if (array_key_exists($command, $commands) || $command == "help") {
                $bot_command    = $command;

                if (sizeof($words) > 1) {
                    $bot_args   = array_map('strtolower', array_slice($words, 1) );
                }
            }
            else {
                $in->reply("I'm sorry. It seems I cannot find your command. Please try the command: help");
                return;
            }
        }

        /*
         * For help
         */
        if ($bot_command == "help" && empty($bot_args)) {
            $msg = "Here's my list of commands you can use. Hope it helps!\n\n";

            foreach ($commands as $key => $value) {
                $msg .= "\t- {$key}:\t{$value[1]}\n"; //$value[0] = classname, $value[1] = class description
            }

            /*
             * should respond with a private message..
             */
            if ($in->channel->is_private) {
                $in->author->sendMessage($msg);
            }
            else {
                $in->author->user->sendMessage($msg);
                $in->reply("I sent you the details in PM.");
            }
            return;
        }

        /*
         * Check if the user is getting help info about a command
         */
        else if (!empty($bot_args) && array_key_exists($bot_args[0], $commands)) {
            $clazz = $commands[$bot_args[0]][0];
            $help_info = (new $clazz)->help();

            $in->reply("Showing help information for command: {$bot_args[0]}\n\n{$help_info}");
            return;
        }

        /*
         * Initiate command
         */
        $classname = $commands[$bot_command][0];
        $instance = new $classname();

        //if the class wants it can now use the $discord instance. must be override parent class Command!
        $instance->linkDiscordObject(function () use ($discord) {
            return $discord;
        });

        $instance->command($bot_args, $in);
    });

    /**
     * On GUILD JOIN
     */
    $self->on(Event::GUILD_MEMBER_ADD, function ($deferred, $data) {

        //var_dump($deferred);
        //var_dump($data);
    });
});

$discord->run();

/**
 *
object(Discord\Parts\Channel\Message)#1743 (15) {
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
