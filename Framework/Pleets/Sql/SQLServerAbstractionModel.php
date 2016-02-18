<?php

namespace Pleets\Sql;

abstract class SQLServerAbstractionModel
{
	private $db;		# SQLServer connection

	public function __construct($abstraction_connection_string = "default")
	{
		$dbsettings = include(__DIR__ . "/../../../config/database.sqlserver.config.php");

		$this->db = new SQLServer(
			$dbsettings[$abstraction_connection_string]["host"],
			$dbsettings[$abstraction_connection_string]["user"],
			$dbsettings[$abstraction_connection_string]["password"],
			$dbsettings[$abstraction_connection_string]["dbname"]
		);
	}

	/* Getters */
	public function getDb() { return $this->db; }
}