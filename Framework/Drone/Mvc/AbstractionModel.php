<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2014-2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Mvc;

abstract class AbstractionModel
{
	private $entityManager;

	public function __construct()
	{
		$this->entityManager = include("bootstrap.php");
	}

	/* Getters */
	public function getEntityManager() { return $this->entityManager; }

	public function __destruct()
	{
		// $this->getEntityManager()->flush();
	}
}