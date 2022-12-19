<?php

namespace App\models\interfaces;

interface Token
{
    public function getJWT();

    public static function config();

}
