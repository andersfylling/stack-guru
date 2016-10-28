<?php
/**
 * Created by PhpStorm.
 * User: Anders
 * Date: 28.10.2016
 * Time: 06.50
 */

namespace CoreLogic;
use \PDO, \PDOException;


class Database
{
    private $host   = "138.68.66.212";
    private $port   = "11237";
    private $user   = "devs";
    private $pass   = "e98de41bc5fa94d464bb831da129ab49ab5a0dffd54811fd";
    private $schema = "mydb";
    private $file   = "database_backup.sql";



    /*
     * Database instance
     */
    public static $db = NULL;


    function __construct ()
    {

        /*
         * establish a new PDO connection
         */
        echo "> Establishing database connection..";
        Database::$db = new PDO(
            "mysql:host={$this->host};port={$this->port}", $this->user, $this->pass,
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
        );
        Database::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        /*
         * If the database does not exist, it is created.
         */
        $name = "`".str_replace("`","``", $this->schema)."`";
        Database::$db->query('CREATE DATABASE IF NOT EXISTS ' . $name);
        Database::$db->query('use ' . $name);
        unset($name);

        echo "OK!", PHP_EOL, PHP_EOL;

        /*
         *  Now check if the tables exists, if not they are created.
         */
        $exists = Database::$db->query('SHOW TABLES');
        if ($exists->rowCount() == 0) {
            //create content
            Database::$db->exec( file_get_contents($this->file) );
        }
    }

}