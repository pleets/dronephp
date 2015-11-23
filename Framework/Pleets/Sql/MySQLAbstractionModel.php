<?php

namespace Pleets\Sql;

abstract class MySQLAbstractionModel
{
	private $dbconn;		# MySQL connection

	public function __construct()
	{
		$dbsettings = include(__DIR__ . "/../../../config/database.mysql.config.php");

		$this->connect = new MySQL(
			$dbsettings["database"]["host"],
			$dbsettings["database"]["user"],
			$dbsettings["database"]["password"],
			$dbsettings["database"]["dbname"]
		);
	}

	/* Getters */
	public function getConn() { return $this->dbconn; }
}