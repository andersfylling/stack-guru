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




    public function saveUser(?\Discord\Parts\User\Member $member)
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
        $stmt = $this->db->prepare("INSERT INTO `mydb`.`UserName`(`id`, `User_discord_id`, `username`, `discriminator`, `avatar`, `bot`, `timestamp`) VALUES(NULL, :discord_id, :username, :discriminator, :avatar, :isBot, NULL)");
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

    public function saveRole(){}

    /**
     * Store the guild id to database.
     * This is then extracted on bot restart or boot time, to get correct guild object.
     * 
     * @return [bool] [True if successfully inserted.]
     */
    public function saveGuildID(string $guildid) : bool
    {
        // Remove all other entries
        $stmt = $this->db->prepare("TRUNCATE TABLE Guild");


        $stmt = $this->db->prepare("INSERT INTO `mydb`.`Guild` (`guild_id`) VALUES(:guild_id)");
        $stmt->bindParam(":guild_id", $guildid);

        // This does not log any potential errors.. whopsy.
        // TODO: add error support.
        return $stmt->execute(); // true on success.
    }

    public function getGuildID() : string 
    {
        $stmt = $this->db->prepare("SELECT * FROM Guild LIMIT 1"); // There will only be one entry, always unless someone ruins the script.
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (null == $row) {
            return null;
        }
        else {
            return $row["guild_id"];
        }
    }

    public function doesServiceExist(string $title): bool 
    {
        $stmt = $this->db->prepare("SELECT COUNT(`title`) FROM `Service` WHERE `title` = :title LIMIT 1");
        $stmt->bindParam(":title", $title, PDO::PARAM_STR);
        $stmt->execute();

        return 1 == $stmt->fetchColumn();
    }

    public function enableService(string $title): bool 
    {
        $stmt = $this->db->prepare("INSERT IGNORE INTO `mydb`.`Service` (`title`) VALUES (:title)");
        $stmt->bindParam(":title", $title, PDO::PARAM_STR);
        $stmt->execute();

        return 1 === $stmt->rowCount();
    }

    public function disableService(string $title): bool 
    {
        $stmt = $this->db->prepare("DELETE IGNORE FROM `mydb`.`Service` WHERE `title` = :title");
        $stmt->bindParam(":title", $title, PDO::PARAM_STR);
        $stmt->execute();

        return 1 === $stmt->rowCount();
    }

}
