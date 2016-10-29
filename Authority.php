<?php
/**
 * Created by PhpStorm.
 * User: Anders
 * Date: 26.10.2016
 * Time: 14.40
 */

namespace Commands;


class Authority
{
    private $users;
    private $tokens; //used to identify someone as idk a developer, admin what not
    private $roles;

    function __construct()
    {
        //this should be stored in a json file. tokens randomized

        //roles
        $this->roles = [
            "developers",
            "administrators",
            "moderators",

        ];

        // username + discriminator
        $this->users = [
            "developers" => [
                "anders_463#7237"
            ],
            "administrators" => [
            ]
        ];

        $this->tokens = [
            "developers" => "m9frKuu0z1h7r5W1O",
            "administrators" => "FVqC5DgVotIYZGT8s",
            "moderators" => "sXjlT0JeXxrQQkSbb"
        ];
    }

    /**
     * @param $id String: username + discriminator (discord style)
     */
    function getRole ($id)
    {

    }

}