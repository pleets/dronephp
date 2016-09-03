<?php

namespace App\Model;

use Drone\Db\TableGateway;
use Drone\Db\Entity;

class MysqlUserTable extends TableGateway
{
	private $tableGateway;
	private $entity;

	public function __construct(Entity $entity, TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
		$this->entity = $entity;
	}

	public function fetch()
	{
		$result = $this->tableGateway->select();
		return $result;
	}
}