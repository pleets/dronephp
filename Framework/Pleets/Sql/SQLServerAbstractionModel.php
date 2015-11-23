<?php

namespace Pleets\Sql;

abstract class SQLServerAbstractionModel
{
	private $conn;		# SQLServer connection

	public function __construct()
	{
		$dbsettings = include(__DIR__ . "/../../../config/database.sqlserver.config.php");

		$this->connect = new SQLServer(
			$dbsettings["database"]["host"],
			$dbsettings["database"]["user"],
			$dbsettings["database"]["password"],
			$dbsettings["database"]["dbname"]
		);
	}

	/* Getters */
	public function getConn() { return $this->conn; }
}