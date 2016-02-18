<?php

namespace App\Model;

class SQLServerModelExample extends \Pleets\Sql\SQLServerAbstractionModel
{
    public function consulta()
    {
        $sql = "SELECT * FROM SYS.TABLES";
        $result = $this->getDb()->query($sql);
        return $this->getDb()->toArray(array('encode_utf8' => true));
    }
}