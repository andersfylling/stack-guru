<?php
const MIN_PHP_VERSION = "7.1.0";

{
    // Make sure that the php version is correct
    if (version_compare(PHP_VERSION, MIN_PHP_VERSION) < 0) {
        echo "Your php version is too old: ", phpversion(), PHP_EOL;
        echo "Please upgrade to version or higher: ", MIN_PHP_VERSION, PHP_EOL;
        exit;
    }

    // Handle incomming terminal arguments.
    $terminal_args = [];
    require __DIR__."/terminalArgumentsHandler.php";

    // Autoload classes
    require __DIR__."/autoload.php";


    // bot config
    //
    $discord    = ST_DISCORD_SETTINGS;
    $database   = ST_DATABASE_SETTINGS;
    $services   = [
        "folder"    => PROJECT_DIR . "/src/Services", 
        "namespace" => "StackGuru\\Services"
    ];
    $commands   = [
        "folder"    => PROJECT_DIR . "/src/Commands", 
        "namespace" => "StackGuru\\Commands"
    ];

    // Setup bot instance
    $bot = new \StackGuru\Core\Bot([
        "discord"   => $discord,
        "database"  => $database,
        "services"  => $services,
        "commands"  => $commands
    ]);

    // Run the bot
    $bot->run();
}