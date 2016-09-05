<?php

namespace App\Controller;

use Drone\Mvc\AbstractionController;

use App\Model\MySQLModelExample;
use App\Model\SQLServerModelExample;
use App\Model\OracleModelExample;

use App\Model\MysqlUser;
use App\Model\MysqlUserTable;
use Drone\Db\TableGateway;

class Db extends AbstractionController
{
	private $mysqlUserTable;

	public function getMysqlUserTable()
	{
		if (!is_null($this->mysqlUserTable))
			return $this->mysqlUserTable;

		$tableGateway = new TableGateway();
		$tableGateway->bind("mysql.user");

		$entity = new MysqlUser();
		$this->mysqlUserTable = new MysqlUserTable($entity, $tableGateway);

		return $this->mysqlUserTable;
	}

	public function mysql()
	{
		$data = array();
		$data["process"] = "success";

		try {

			$model = new MySQLModelExample();

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


		try {

			$model = new OracleModelExample();

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


		try {

			$model = new SqlServerModelExample();

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