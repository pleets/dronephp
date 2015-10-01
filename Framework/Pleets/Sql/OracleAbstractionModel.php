<?php

namespace Pleets\Sql;

abstract class OracleAbstractionModel
{
	private $conn;		# SQLServer connection

	public function __construct()
	{
		$this->connect = new Oracle();
	}

	/* Getters */
	public function getConn() { return $this->conn; }
}