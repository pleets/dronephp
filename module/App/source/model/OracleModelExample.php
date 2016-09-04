<?php

namespace App\Model;

class OracleModelExample extends \Drone\Sql\AbstractionModel
{
    public function myQuery()
    {
        $sql = "SELECT * FROM ALL_TABLES WHERE OWNER NOT IN ('SYS', 'SYSTEM', 'OUTLN', 'CTXSYS', 'XDB', 'MDSYS', 'HR', 'APEX_040000')";
        $result = $this->getDb()->query($sql);
        return $this->getDb()->getArrayResult();
    }
}