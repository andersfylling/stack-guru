<?php
/**
 * Initiates a database connection to the server if possible.
 *
 * @author http://github.com/sciencefyll
 */

namespace StackGuru\CoreLogic;

use \PDO;


class Database
{
    /*
     * Database instance
     */
    protected $db = null;


    function __construct (array $options = [])
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
        if ($exists->rowCount() == 0) {
            //create content
            $this->db->exec( file_get_contents($this->file) );
        }
    }

}
