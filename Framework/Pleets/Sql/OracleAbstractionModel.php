<?php

namespace Pleets\Sql;

abstract class OracleAbstractionModel
{
	private $db;		# Oracle connection

	public function __construct($abstraction_connection_string = "default")
	{
		$dbsettings = include(__DIR__ . "/../../../config/database.oracle.config.php");

		$this->db = new Oracle(
			$dbsettings[$abstraction_connection_string]["host"],
			$dbsettings[$abstraction_connection_string]["user"],
			$dbsettings[$abstraction_connection_string]["password"],
			$dbsettings[$abstraction_connection_string]["dbname"]
		);
	}

	/* Getters */
	public function getDb() { return $this->db; }
}