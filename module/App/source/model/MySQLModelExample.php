<?php

namespace App\Model;

class SQLServerModelExample extends \Pleets\Sql\MySQLAbstractionModel
{
    public function consulta()
    {
        $sql = "SELECT * FROM mysql.users";
        $result = $this->connect->query($sql);
        return $this->connect->toArray(array('encode_utf8' => true));
    }
}