<?php

namespace Pleets\Sql;

abstract class MySQLAbstractionModel
{
	private $dbconn;		# MySQL connection

	public function __construct()
	{
		$this->connect = new MySQL();
	}

	/* Getters */
	public function getConn() { return $this->dbconn; }
}