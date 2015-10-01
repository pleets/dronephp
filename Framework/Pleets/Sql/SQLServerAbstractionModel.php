<?php

namespace Pleets\Sql;

abstract class SQLServerAbstractionModel
{
	private $conn;		# SQLServer connection

	public function __construct()
	{
		$this->connect = new SQLServer();
	}

	/* Getters */
	public function getConn() { return $this->conn; }
}