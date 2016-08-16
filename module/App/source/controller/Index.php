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

		$modelo = new MySQLModelExample();
		//$modelo = new SQLServerModelExample();
		//$modelo = new OracleModelExample();

		try {

			$rows = $modelo->consulta();
			$data["datos"] = $rows;

		} catch (\Exception $e) {

			$data["standard_error"] = $e->getMessage();

			return $data;
		}

		return $data;
	}
}