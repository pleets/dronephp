<?php

namespace App\Model;

use Drone\Db\Entity;

class MysqlUser extends Entity
{
    public $host;
    public $user;
    public $password;
}