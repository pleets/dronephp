<?php

namespace App\Controller;

use Drone\Mvc\AbstractionController;

use App\Model\MySQLModelExample;
use App\Model\SQLServerModelExample;
use App\Model\OracleModelExample;

class Db extends AbstractionController
{

	private $mysqlUserTable;

	public function getMysqlUserTable()
	{
		if (!is_null($this->mysqlUserTable))
			return $this->mysqlUserTable;

		$tableGateway = new \Drone\Db\TableGateway();
		$tableGateway->bind("mysql.user");

		$entity = new \App\Model\MysqlUser();
		$this->mysqlUserTable = new \App\Model\MysqlUserTable($entity, $tableGateway);

		return $this->mysqlUserTable;
	}

	public function mysql()
	{
		$data = array();
		$data["process"] = "success";

		$model = new MySQLModelExample();

		try {

			# no entity
			# $rows = $model->myQuery();

			# entity
			$rows = $this->getMysqlUserTable()->fetch();
			$data["data"] = $rows;

		} catch (\Exception $e) {

			$data["message"] = $e->getMessage();
			$data["process"] = "error";

			return $data;
		}

		return $data;
	}

	public function oracle()
	{
		$data = array();
		$data["process"] = "success";

		$model = new OracleModelExample();

		try {

			$rows = $model->myQuery();
			$data["data"] = $rows;

		} catch (\Exception $e) {

			$data["message"] = $e->getMessage();
			$data["process"] = "error";

			return $data;
		}

		return $data;
	}

	public function sqlserver()
	{
		$data = array();
		$data["process"] = "success";

		$model = new SqlServerModelExample();

		try {

			$rows = $model->myQuery();
			$data["data"] = $rows;

		} catch (\Exception $e) {

			$data["message"] = $e->getMessage();
			$data["process"] = "error";

			return $data;
		}

		return $data;
	}
}