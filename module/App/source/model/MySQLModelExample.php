<?php

namespace App\Model;

class MySQLModelExample extends \Drone\Sql\AbstractionModel
{
    public function myQuery()
    {
        $sql = "SELECT host, user, password FROM mysql.user";
        $result = $this->getDb()->query($sql);
        return $this->getDb()->getArrayResult();
    }
}