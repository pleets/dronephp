<?php

namespace Pleets\model;

Use Pleets\Mvc\AbstractionModel;

class Database extends AbstractionModel
{
	public function example()
	{
		#$query = $this->getEntityManager()->createQuery("SELECT u FROM User u");
		#return $users = $query->getResult();		
	}

	public function getUser($NUM_DOC_PAC)
	{
		return $this->getEntityManager()->find('\Auth\Model\Usuario', $NUM_DOC_PAC);
	}

	public function addUser(Usuario $usuario)
	{
		$this->getEntityManager()->persist($usuario);
		$this->getEntityManager()->flush();
		return $usuario;
	}

	public function updateUser(Usuario $usuario)
	{
		$usuario->close();
		$this->getEntityManager()->flush();
		return $usuario;
	}

	public function authenticate($NUM_DOC_PAC, $PAS_USU)
	{
		$user = $this->getEntityManager()->find('\Auth\Model\Usuario', $NUM_DOC_PAC);
		
		if (!is_null($user))
		{
			if (md5($PAS_USU) == $user->getPAS_USU())
				return true;
		}

		return false;
	}

	public function fetchAll()
	{
		return $this->getEntityManager()->getRepository('\Auth\Model\Usuario')->findAll();
	}
}