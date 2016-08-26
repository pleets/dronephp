<?php

namespace App\Model;

class SQLServerModelExample extends \Drone\Sql\AbstractionModel
{
    public function myQuery()
    {
        $sql = "SELECT * FROM SYS.TABLES";
        $result = $this->getDb()->query($sql);
        return $this->getDb()->getArrayResult();
    }
}