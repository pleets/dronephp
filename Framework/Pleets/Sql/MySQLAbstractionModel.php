<?php

namespace Pleets\Sql;

abstract class MySQLAbstractionModel
{
	private $db;		# MySQL connection

	public function __construct($abstraction_connection_string = "default")
	{
		$dbsettings = include(__DIR__ . "/../../../config/database.mysql.config.php");

		$this->db = new MySQL(
			$dbsettings[$abstraction_connection_string]["host"],
			$dbsettings[$abstraction_connection_string]["user"],
			$dbsettings[$abstraction_connection_string]["password"],
			$dbsettings[$abstraction_connection_string]["dbname"]
		);
	}

	/* Getters */
	public function getDb() { return $this->db; }
}