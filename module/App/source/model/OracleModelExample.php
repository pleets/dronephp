<?php

namespace App\Model;

class OracleModelExample extends \Pleets\Sql\AbstractionModel
{
    public function myQuery()
    {
        $sql = "SELECT * FROM HELP";
        $result = $this->getDb()->query($sql);
        return $this->getDb()->getArrayResult();
    }
}