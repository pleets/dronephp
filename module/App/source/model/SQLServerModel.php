<?php

namespace App\Model;

class SQLServerModelExample extends \Pleets\Sql\SQLServerAbstractionModel
{
    public function consulta()
    {
        $sql = "SELECT * FROM SYS.TABLES";
        $result = $this->connect->query($sql);
        return $this->connect->toArray(array('encode_utf8' => true));
    }
}