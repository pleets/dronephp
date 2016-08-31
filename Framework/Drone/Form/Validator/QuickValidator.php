<?php

namespace Drone\Form\Validator;

use Zend\Validator\NotEmpty;
use Zend\Validator\Digits;
use \Exception as Exception;

class QuickValidator
{
    /**
     * @var array
     */
	private $rules;

    /**
     * @var boolean
     */
	private $valid;

    /**
     * @var array
     */
	private $messages = array();

    /**
     * Get all failure messages
     *
     * @return array
     */
	public function getMessages()
	{
		return $this->messages;
	}

    /**
     * Get valid attribute after validation
     *
     * @return array
     */
	public function isValid()
	{
		return $this->valid;
	}

    /**
     * Set valid atribute after each validation
     *
     * @return array
     */
	public function setValid($valid)
	{
		return $this->valid && $valid;
	}

    /**
     * Constructor
     *
     * @param array $rules
     */
	public function __construct($rules)
	{
		$this->components = $rules;
	}

	public function validateWith($formArray)
	{
		foreach ($this->components as $key => $constraints)
		{
			if (!array_key_exists($key, $formArray))
				throw new Exception("El campo <strong>$key</strong> no existe!", 300);

			$label = (array_key_exists('label', array_keys($constraints))) ? $constraints["label"] : $key;

			foreach ($constraints as $name => $value)
			{
				switch ($name)
				{
					case 'required':

						$validator = new NotEmpty();

						break;

					case 'digits':

						$validator = new Digits();

						break;
				}

				$valid = $validator->isValid($formArray[$key]);
				$this->setValid($valid);

				if (!$valid)
				{
					if (!in_array($key, array_keys($this->messages)))
						$this->messages[$key] = array();

					$this->messages[$key] = array_merge($this->messages[$key], $validator->getOption("messages"));
				}

			}
		}
	}
}