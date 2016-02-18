<?php

namespace App\Model;

class OracleModelExample extends \Pleets\Sql\OracleAbstractionModel
{
    public function consulta()
    {
        $sql = "SELECT * FROM HELP";
        $result = $this->getDb()->query($sql);
        return $this->getDb()->toArray(array('encode_utf8' => true));
    }
}