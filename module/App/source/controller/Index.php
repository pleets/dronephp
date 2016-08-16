<?php

namespace App\Controller;

use Pleets\Mvc\AbstractionController;

use App\Model\MySQLModelExample;
//use App\Model\SQLServerModelExample;
//use App\Model\OracleModelExample;

class Index extends AbstractionController
{
	public function index()
	{
		return array();
	}

	public function start()
	{
		$data = array();
		$data["process"] = "success";

		$modelo = new MySQLModelExample();
		//$modelo = new SQLServerModelExample();
		//$modelo = new OracleModelExample();

		try {

			$rows = $modelo->myQuery();
			$data["data"] = $rows;

		} catch (\Exception $e) {

			$data["message"] = $e->getMessage();
			$data["process"] = "error";

			return $data;
		}

		return $data;
	}
}