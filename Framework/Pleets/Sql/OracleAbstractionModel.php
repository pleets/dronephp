<?php

namespace Pleets\Sql;

abstract class OracleAbstractionModel
{
	private $conn;		# SQLServer connection

	public function __construct()
	{
		$dbsettings = include(__DIR__ . "/../../../config/database.oracle.config.php");

		$this->connect = new Oracle(
			$dbsettings["database"]["host"],
			$dbsettings["database"]["user"],
			$dbsettings["database"]["password"],
			$dbsettings["database"]["dbname"]
		);
	}

	/* Getters */
	public function getConn() { return $this->conn; }
}