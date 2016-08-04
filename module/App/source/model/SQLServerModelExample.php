<?php

namespace App\Model;

class SQLServerModelExample extends \Pleets\Sql\AbstractionModel
{
    public function consulta()
    {
        $sql = "SELECT * FROM SYS.TABLES";
        $result = $this->getDb()->query($sql);
        return $this->getDb()->getArrayResult();
    }
}