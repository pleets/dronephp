<?php

namespace App\Controller;

use Drone\Mvc\AbstractionController;
use Drone\Form\Validator\QuickValidator;
Use \Exception as Exception;

class Form extends AbstractionController
{
	public function validator()
	{
		# data to view
		$data = array();

		if ($this->isPost())
		{
			$data["success"] = true;

			$rules = array(
				"fname" => [
					"required" => true,
					"minlength" => 3,
					"maxlength" => 10,
					#"alnumWhiteSpace" => true,
					"label" => "First name"
				],
				"lname" => [
					"required" => true,
					"minlength" => 3,
					"maxlength" => 5,
					#"alnumWhiteSpace" => true,
					"label" => "Last name"
				],
				"age" => [
					"type" => "number",
					"required" => true,
					"min" => 18,
					"max" => 90,
					"label" => "Age"
				],
				"email" => [
					"type" => "email",
					"required" => true,
					"label" => "Email"
				],
				"date" => [
					"type" => "date",
					"required" => true,
					"label" => "Date"
				]
			);

			try {
				$validator = new QuickValidator($rules);
				$validator->validateWith($_POST);

				if (!$validator->isValid())
				{
					$data["success"] = false;
					$data["messages"] = $validator->getMessages();
				}

			}
			catch (Exception $e)
			{
				$data["success"] = false;
				$data["message"] = $e->getMessage();
				return $data;
			}
		}

		return $data;
	}
}