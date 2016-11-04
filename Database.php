<?php
/**
 * Initiates a database connection to the server if possible.
 *
 * @author http://github.com/sciencefyll
 */

namespace CoreLogic;
use \PDO;
use \Symfony\Component\OptionsResolver\Exception\MissingOptionsException;


class Database
{
    private $host   = "138.68.66.212";
    private $port   = "11237";
    private $user   = "devs";
    private $pass   = "e98de41bc5fa94d464bb831da129ab49ab5a0dffd54811fd";
    private $schema = "mydb";
    private $file   = null;



    /*
     * Database instance
     */
    protected $db = null;


    function __construct (array $options = [])
    {
        /*
         * Verify parameter to have required keys
         */
        $options = $this->resolveOptions($options);


        $this->file = $options["databaseFile"];

        /*
         * establish a new PDO connection
         */
        $this->db = new PDO(
            "mysql:host={$this->host};port={$this->port}", $this->user, $this->pass,
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
        );
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        /*
         * If the database does not exist, it is created.
         */
        $name = "`".str_replace("`","``", $this->schema)."`";
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

    /**
     * Resolves the options.
     *
     * @param array $options Array of options.
     *
     * @return array Options.
     */
    protected function resolveOptions(array $options = [])
    {
        $required = [
            "databaseFile"
        ];

        foreach ($required as $key) {
            if (!key_exists($key, $options)) {
                throw new \ErrorException("Key file must be specified (and contain a value): {$key}");
            }
        }

        return $options;
    }

}