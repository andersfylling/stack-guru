<?php
declare(strict_types=1);

namespace StackGuru\Core;

use \PDO;


/**
 * Initiates a database connection to the server if possible.
 *
 * @author http://github.com/sciencefyll
 */
class Database
{
    /*
     * Database instance
     */
    protected $db = null;


    function __construct(array $options = [])
    {
        /*
         * Verify parameter to have required keys
         */
        $options = Utils\ResolveOptions::verify($options, ["file", "host", "port", "user", "pass", "schema"]);


        $this->file = $options["file"];

        /*
         * establish a new PDO connection
         */
        $this->db = new PDO(
            "mysql:host={$options['host']};port={$options['port']}", $options["user"], $options["pass"],
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
        );
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        /*
         * If the database does not exist, it is created.
         */
        $name = "`".str_replace("`","``", $options["schema"])."`";
        $this->db->query('CREATE DATABASE IF NOT EXISTS ' . $name);
        $this->db->query('use ' . $name);
        unset($name);


        /*
         * TODO: must be improved and based on the given file.
         * Don't do a check if the file isn't set.
         */
        if ($this->file === null) {
            return;
        }

        $exists = $this->db->query('SHOW TABLES');
        if (false && $exists->rowCount() == 0) { //TODO: this is disabled due to shitty code.
            //create content
            $this->db->exec( file_get_contents($this->file) );
        }
    }




    final public function saveUser(\Discord\Parts\User\Member $member)
    {
        if (null == $member) {
            return;
        }


        // To store a user, just user transactions:
        // 
        // BEGIN;
        // INSERT INTO User VALUES("282601055215157248");
        // INSERT INTO UserName VALUES(NULL, "282601055215157248", "Anders", 7237, "https://cdn.discordapp.com/avatars/228846961774559232/ff248f7cee4c3967c8d3309ccde90fdd.jpg?size=1024", FALSE, NULL);
        // COMMIT;
        // 
        // Or not.
        
        // Member id
        $id = $member->user->id;

        // User object
        $u = $member->user;
        

        // First create user
        $stmt = $this->db->prepare("INSERT IGNORE INTO `mydb`.`User` (`discord_id`) VALUES(:discord_id)");
        $stmt->execute([
            ":discord_id" => $id
        ]);

        // add specifics about this user
        $stmt = $this->db->prepare("INSERT IGNORE INTO `mydb`.`UserName`(`id`, `User_discord_id`, `username`, `discriminator`, `avatar`, `bot`, `timestamp`) VALUES(NULL, :discord_id, :username, :discriminator, :avatar, :isBot, NULL)");
        $stmt->execute([
            ":discord_id" => $id,
            ":username" => $u->username,
            ":discriminator" => $u->discriminator,
            ":avatar" => (null == $u->avatar ? "" : $u->avatar),
            ":isBot" => (null == $u->bot ? false : true)
        ]);
        
        // Now roles need to be added.
        // Check if the role exists, if not, use saveRole to save it.
        
        

    }

    final public function saveRole(\Discord\Parts\Guild\Role $role) 
    {
        if (null == $role) { 
            return; 
        } 
 
        // Roles 
        $id             = $role->id; 
        $name           = $role->name; 
        $color          = $role->color; 
        $managed        = $role->managed; 
        $hoist          = $role->hoist; 
        $position       = $role->position; 
        $mentionable    = $role->mentionable; 
        $permissions    = $role->permissions->bitwise; 
 
 
        // Add to DB 
        $stmt = $this->db->prepare("INSERT IGNORE INTO `mydb`.`Role`(`id`, `name`, `color`, `hoist`, `position`, `managed`, `mentionable`, `permissions`) VALUES(:id, :name, :color, :hoist, :position, :managed, :mentionable, :permissions)"); 

        $stmt->bindParam(":id",             $id,            \PDO::PARAM_STR);
        $stmt->bindParam(":name",           $name,          \PDO::PARAM_STR);
        $stmt->bindParam(":color",          $color,         \PDO::PARAM_STR);
        $stmt->bindParam(":hoist",          $hoist,         \PDO::PARAM_BOOL);
        $stmt->bindParam(":position",       $position,      \PDO::PARAM_INT);
        $stmt->bindParam(":managed",        $managed,       \PDO::PARAM_BOOL);
        $stmt->bindParam(":mentionable",    $mentionable,   \PDO::PARAM_BOOL);
        $stmt->bindParam(":permissions",    $permissions,   \PDO::PARAM_INT);

        $stmt->execute();
    }

    final public function saveChannel(\Discord\Parts\Channel\Channel $channel) 
    {
        if (null == $channel) { 
            return; 
        } 
 
        // Roles 
        $id             = $channel->id; 
        $guild_id       = $channel->guild_id; 
        $name           = $channel->name; 
        $type           = $channel->type; 
        $position       = $channel->position; 
        $is_private     = $channel->is_private; 
        $topic          = $channel->topic; 
        $bitrate        = $channel->bitrate; 
        $user_limit     = $channel->user_limit; 
 
 
        // Add to DB 
        $stmt = $this->db->prepare("INSERT IGNORE INTO `mydb`.`Channel` (`id`, `guild_id`, `name`, `type`, `position`, `is_private`, `permission_overwrites`, `topic`, `bitrate`, `user_limit`) VALUES (:id, :guild_id, :name, :type, :position, :is_private, NULL, :topic, :bitrate, :user_limit)"); 

        

        $stmt->bindParam(":id",                     $id,                    \PDO::PARAM_STR);
        $stmt->bindParam(":guild_id",               $guild_id,              \PDO::PARAM_STR);
        $stmt->bindParam(":name",                   $name,                  \PDO::PARAM_STR);
        $stmt->bindParam(":type",                   $type,                  \PDO::PARAM_STR);
        $stmt->bindParam(":position",               $position,              \PDO::PARAM_INT);
        $stmt->bindParam(":is_private",             $is_private,            \PDO::PARAM_BOOL);
        //$stmt->bindParam(":permission_overwrites",  $permission_overwrites, \PDO::PARAM_STR);
        $stmt->bindParam(":topic",                  $topic,                 \PDO::PARAM_STR);
        $stmt->bindParam(":bitrate",                $bitrate,               \PDO::PARAM_STR);
        $stmt->bindParam(":user_limit",             $user_limit,            \PDO::PARAM_STR);

        $stmt->execute();
    }


    final public function saveCommand(string $namespace, string $description, bool $activated) 
    {
        if ("" == $namespace || "" == $description || null == $activated) { 
            return; 
        }
 
        // Add to DB 
        $stmt = $this->db->prepare("INSERT IGNORE INTO `mydb`.`Command`(`namespace`, `description`, `activated`) VALUES(:namespace, :description, :activated)"); 

        $stmt->bindParam(":namespace",      $namespace,     \PDO::PARAM_STR);
        $stmt->bindParam(":description",    $description,   \PDO::PARAM_STR);
        $stmt->bindParam(":activated",      $activated,     \PDO::PARAM_BOOL);

        $stmt->execute();
    }


    final public function doesServiceExist(string $title): bool 
    {
        $stmt = $this->db->prepare("SELECT COUNT(`title`) FROM `Service` WHERE `title` = :title LIMIT 1");
        $stmt->bindParam(":title", $title, PDO::PARAM_STR);
        $stmt->execute();

        return 1 == $stmt->fetchColumn();
    }

    final public function enableService(string $title): bool 
    {
        $stmt = $this->db->prepare("INSERT IGNORE INTO `mydb`.`Service` (`title`) VALUES (:title)");
        $stmt->bindParam(":title", $title, PDO::PARAM_STR);
        $stmt->execute();

        return 1 === $stmt->rowCount();
    }

    final public function disableService(string $title): bool 
    {
        $stmt = $this->db->prepare("DELETE IGNORE FROM `mydb`.`Service` WHERE `title` = :title");
        $stmt->bindParam(":title", $title, PDO::PARAM_STR);
        $stmt->execute();

        return 1 === $stmt->rowCount();
    }

    final public function getCommandRoles(string $namespace) 
    {
        $stmt = $this->db->prepare("SELECT Role_id FROM `Command_has_Role` WHERE `Command_namespace` = :namespace");
        $stmt->bindParam(":namespace", $namespace, PDO::PARAM_STR);
        $stmt->execute();

        $roleColumns = 0 === $stmt->rowCount() ? [] : $stmt->fetchAll(PDO::FETCH_NUM);

        $roles = [];
        foreach ($roleColumns as $roleRow) {
            foreach ($roleRow as $role) {
                $roles[] = $role;
            }
        }

        return $roles;
    }

    final public function commandHasRole(string $namespace, string $roleid): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(`Role_id`) FROM Command_has_Role WHERE `Command_namespace` = :namespace AND `Role_id` = :roleid LIMIT 1");
        $stmt->bindParam(":namespace", $namespace, PDO::PARAM_STR);
        $stmt->bindParam(":roleid", $roleid, PDO::PARAM_STR);
        $stmt->execute();

        return 1 == $stmt->fetchColumn();
    }



    final public function addCommandRole(string $namespace, string $roleid): bool 
    {
        $stmt = $this->db->prepare("INSERT IGNORE INTO `mydb`.`Command_has_Role` (`Command_namespace`, `Role_id`) VALUES (:namespace, :roleid)");
        $stmt->bindParam(":roleid", $roleid, PDO::PARAM_STR);
        $stmt->bindParam(":namespace", $namespace, PDO::PARAM_STR);
        $stmt->execute();

        return 1 === $stmt->rowCount();
    }


    final public function getCommandDetails(string $namespace) 
    {
        $stmt = $this->db->prepare("SELECT description, activated FROM `Command` WHERE `namespace` = :namespace LIMIT 1");
        $stmt->bindParam(":namespace", $namespace, PDO::PARAM_STR);
        $stmt->execute();
        $content = $stmt->fetch(PDO::FETCH_ASSOC);
        $content["activated"] = 1 == $content["activated"] ? true : false;


        $stmt = null;
        $stmt = $this->db->prepare("SELECT title FROM `CommandAlias` WHERE `Command_namespace` = :namespace");
        $stmt->bindParam(":namespace", $namespace, PDO::PARAM_STR);
        $stmt->execute();

        $aliases = 0 === $stmt->rowCount() ? [] : $stmt->fetchAll(PDO::FETCH_NUM);

        $content["aliases"] = [];
        foreach ($aliases as $alias) {
            foreach ($alias as $a) {
            $content["aliases"][$a] = $a; // for easier checking later.
            }
        }

        return $content;
    }

    final public function saveCommandAlias(string $namespace, string $alias): bool 
    {
        $stmt = $this->db->prepare("INSERT IGNORE INTO `mydb`.`CommandAlias` (`title`, `Command_namespace`) VALUES (:title, :namespace)");
        $stmt->bindParam(":title", $alias, PDO::PARAM_STR);
        $stmt->bindParam(":namespace", $namespace, PDO::PARAM_STR);
        $stmt->execute();

        return 1 === $stmt->rowCount();
    }

    final public function updateCommandDescription(string $namespace, string $description): bool 
    {
        $stmt = $this->db->prepare("UPDATE IGNORE `mydb`.`Command` SET `description` = :description WHERE `namespace` = :namespace");
        $stmt->bindParam(":description", $description, PDO::PARAM_STR);
        $stmt->bindParam(":namespace", $namespace, PDO::PARAM_STR);
        $stmt->execute();

        return 1 === $stmt->rowCount();
    }

    final public function chatlog_saveMessage(string $id, string $channel_id, string $user_id, bool $deleted = false): bool
    {
        // INSERT INTO `mydb`.`Message`
        // (`id`,
        // `deleted`,
        // `Channel_id`,
        // `User_discord_id`)
        // VALUES
        // (<{id: }>,
        // <{deleted: }>,
        // <{Channel_id: }>,
        // <{User_discord_id: }>);
        // 


        $stmt = $this->db->prepare("INSERT IGNORE INTO `mydb`.`Message` (`id`, `deleted`, `Channel_id`, `User_discord_id`) VALUES(:id, :del, :channel_id, :user_id)");
        $stmt->bindParam(":id",                 $id,                PDO::PARAM_STR);
        $stmt->bindParam(":del",                $deleted,           PDO::PARAM_BOOL);
        $stmt->bindParam(":channel_id",         $channel_id,        PDO::PARAM_STR);
        $stmt->bindParam(":user_id",            $user_id,           PDO::PARAM_STR);
        $stmt->execute();

        return 1 === $stmt->rowCount();
    }

    final public function chatlog_saveMessageContent(string $content, string $message_id): bool
    {
        // INSERT INTO `mydb`.`MessageContent`
        // (`content`,
        // `timestamp`,
        // `Message_id`)
        // VALUES
        // (<{content: }>,
        // <{timestamp: CURRENT_TIMESTAMP}>,
        // <{Message_id: }>);

        $stmt = $this->db->prepare("INSERT IGNORE INTO `mydb`.`MessageContent` (`content`, `timestamp`, `Message_id`) VALUES(:c, NULL, :m)");
        $stmt->bindParam(":c",  $content,       PDO::PARAM_STR);
        $stmt->bindParam(":m",  $message_id,    PDO::PARAM_STR);
        $stmt->execute();

        return 1 === $stmt->rowCount();
    }

    final public function chatlog_updateMessage(string $id, bool $deleted = false): bool
    {
        $stmt = $this->db->prepare("UPDATE IGNORE `mydb`.`Message` SET `deleted` = :del WHERE `id` = :id");
        $stmt->bindParam(":id",     $id,      PDO::PARAM_STR);
        $stmt->bindParam(":del",    $deleted, PDO::PARAM_BOOL);
        $stmt->execute();

        return 1 === $stmt->rowCount();
    }
    final public function chatlog_updateMessageContent() {}

    final public function chatlog_deleteMessage() {}
    final public function chatlog_deleteMessageContent() {}

    final public function chatlog_loggableChannel(string $id): bool
    {

        $stmt = $this->db->prepare("SELECT COUNT(`Channel_id`) FROM `mydb`.`Command_chatlog_loggable_channels` WHERE `loggable` = TRUE AND `Channel_id` = :id LIMIT 1");
        $stmt->bindParam(":id", $id, PDO::PARAM_STR);
        $stmt->execute();

        return 1 == $stmt->fetchColumn();
    }


    final public function saveLoggableChannel(\Discord\Parts\Channel\Channel $channel) 
    {
        if (null == $channel) { 
            return; 
        }

        $id = $channel->id;
 
        // Add to DB 
        $stmt = $this->db->prepare("INSERT IGNORE INTO `mydb`.`Command_chatlog_loggable_channels`(`Channel_id`,`loggable`) VALUES(:id,FALSE)");
        $stmt->bindParam(":id", $id, \PDO::PARAM_STR);
        $stmt->execute();
    }


    final public function chatlog_setChannelAsLoggable(string $id): bool 
    {
        $stmt = $this->db->prepare("UPDATE IGNORE `mydb`.`Command_chatlog_loggable_channels` SET `loggable` = TRUE WHERE `Channel_id` = :id");
        $stmt->bindParam(":id", $id, \PDO::PARAM_STR);
        $stmt->execute();

        return 1 === $stmt->rowCount();
    }

    final public function chatlog_getChannels(?bool $logged = true) 
    {
        $stmt = $this->db->prepare("SELECT `Channel`.`name` FROM `mydb`.`Channel` INNER JOIN `mydb`.`Command_chatlog_loggable_channels` ON `Command_chatlog_loggable_channels`.`Channel_id` = `Channel`.`id` WHERE `Command_chatlog_loggable_channels`.`loggable` = :loggable");
        $stmt->bindParam(":loggable", $logged, PDO::PARAM_BOOL);
        $stmt->execute();

        $channels = 0 === $stmt->rowCount() ? [] : $stmt->fetchAll(PDO::FETCH_NUM);

        return $channels;
    }

}
