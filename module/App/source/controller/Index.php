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

	public function inicio()
	{
		$return_data = array();

		$modelo = new MySQLModelExample();
		//$modelo = new SQLServerModelExample();
		//$modelo = new OracleModelExample();

		try {

			$datos = $modelo->consulta();

			$return_data["datos"] = $datos;

		} catch (\Exception $e) {

			$sql_errors = array_merge_recursive($modelo->connect->getErrors());

			if (count($sql_errors))
			{
				$return_data["sql_errors"] = $sql_errors;
				$this->SQLTransacException = new SQLTransacException($sql_errors, $e);
				$this->SQLTransacException->crearLogSQLTransac();
			}

			$return_data["standard_error"] = $e->getMessage();

			return $return_data;
		}

		return $return_data;
	}
}