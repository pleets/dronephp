<?php

namespace App\Controller;

use Drone\Mvc\AbstractionController;

use App\Model\MySQLModelExample;
use App\Model\SQLServerModelExample;
use App\Model\OracleModelExample;

class Db extends AbstractionController
{
	public function mysql()
	{
		$data = array();
		$data["process"] = "success";

		$model = new MySQLModelExample();

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