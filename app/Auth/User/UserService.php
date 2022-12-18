<?php

namespace App\Auth\User;


use App\models\IdTokenUser;
use LdapRecord\Models\ActiveDirectory\User;

class UserService
{
    public function getUserData(string $distinguishedName): IdTokenUser
    {
        $user = User::find($distinguishedName);
        $sub = $user->getFirstAttribute('distinguishedname');
        $preferredUsername = $user->getFirstAttribute('samaccountname');
        $givenName = $user->getFirstAttribute('givenname');
        $familyName = $user->getFirstAttribute('sn');
        $email = $user->getFirstAttribute('mail');
        $groups = $this->getUserGroups($user);
        return new IdTokenUser($sub, $preferredUsername, $givenName, $familyName, $email, $groups);


    }

    protected function getUserGroups(User $user): array
    {
        $groups = [];
        foreach ($user->groups()->recursive()->get() as $userGroup) {
            $groups[] = $userGroup->getName();
        }
        return $groups;
    }


}
