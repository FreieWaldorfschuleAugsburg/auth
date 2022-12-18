<?php

namespace App\models;

class IdTokenUser
{

    public string $sub;
    public string $preferred_username;
    public string $given_name;
    public string $family_name;
    public string $email;
    public array $groups;

    /**
     * @param string $sub
     * @param string $preferred_username
     * @param string $given_name
     * @param string $family_name
     * @param string $email
     * @param array $groups
     */
    public function __construct(string $sub, string $preferred_username, string $given_name, string $family_name, string $email, array $groups)
    {
        $this->sub = $sub;
        $this->preferred_username = $preferred_username;
        $this->given_name = $given_name;
        $this->family_name = $family_name;
        $this->email = $email;
        $this->groups = $groups;
    }



}
