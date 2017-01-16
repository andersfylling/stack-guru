<?php

namespace StackGuru\Core\Command;

use \StackGuru\Core\Utils;


trait QueryRouter
{
    /**
     * Returns the command node and trimmed query for the latest relative command in the string.
     *
     * @param string $query The full query to the bot.
     *
     * @return array CommandEntry and new query string after matched command
     */
    public function parseCommandQuery(string $query): array
    {
        // Local vars
        $command = null;
        $token = Utils\StringParser::getFirstWord($query);

        // Search names and aliases for first token
        if (!empty($token))
        {
            if (isset($this->commands[$token])) {
                $command = $this->commands[$token];
            } else if (isset($this->commandAliases[$token])) {
                $command = $this->commandAliases[$token];
            }
        }

        // Iterate over words (tokens) in query to find the command with the longest
        // matching sub-command chain.
        if ($command !== null) {
            for ($depth = 1; $depth <= static::MAX_DEPTH; ++$depth) {
                // Update command to the new first word..
                $token = Utils\StringParser::getFirstWord($query);
                if (empty($token))
                    break;

                // Check if next word is a subcommand
                $child = $command->getChild($token);
                if ($child === null) {
                    // Try to use default subcommand
                    $child = $command->getDefaultChild();
                    if ($child !== null) {
                        // Skip trimming the query when using default subcommand
                        $command = $child;
                        break;
                    } else {
                        // Use last found command and break out of loop
                        break;
                    }
                }

                // Command was found, assign subcommand as working command.
                $command = $child;

                // Trim command word from the query string
                // if ($token === ltrim(substr($query, 0, strlen($token))))
                $query = ltrim(substr($query, strlen($token)));
            }
        }

        return [
            "command" => $command,
            "query" => $query
        ];
    }
}
