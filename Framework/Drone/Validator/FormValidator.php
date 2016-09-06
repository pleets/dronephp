<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Validator;

use Zend\Validator\NotEmpty;
use Zend\Validator\Digits;
use Zend\Validator\StringLength;
use Zend\Validator\Step;
use Zend\Validator\LessThan;
use Zend\Validator\GreaterThan;
use Zend\Validator\EmailAddress;
use Zend\Validator\Date;
use Zend\Validator\Uri;

use Drone\Dom\Element\Form as HtmlForm;

use Exception;

class FormValidator
{
    /**
     * @var boolean
     */
	private $valid;

    /**
     * @var array
     */
	private $messages = array();

    /**
     * @var Drone\Dom\Element\Form
     */
	private $formHandler;

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
     * @return boolean
     */
	public function setValid($valid)
	{
		$this->valid = $this->valid && $valid;
	}

    /**
     * Constructor
     *
     * @param array $rules
     */
	public function __construct(HtmlForm $formHandler)
	{
		$this->formHandler = $formHandler;
	}

    /**
     * Check all form rules
     *
     * @return null
     */
	public function validate()
	{
		$attribs = $this->formHandler->getAttributes();

		foreach ($attribs as $key => $attributes)
		{
			if (!array_key_exists($key, $attribs))
				throw new Exception("The field '$key' does not exists!");

			$label = (array_key_exists('label', array_keys($attributes))) ? $attributes["label"] : $key;

			$all_attribs = [];

			foreach ($attributes as $attr)
			{
				$all_attribs[$attr->getName()] = $attr->getValue();
			}

			foreach ($attributes as $name => $attr)
			{
				$name = $attr->getName();
				$value = $attr->getValue();

				$form_value = $this->formHandler->getAttribute($label, "value")->getValue();

				switch ($name)
				{
					case 'required':

						$validator = new NotEmpty();
						break;

					case 'minlength':

						$validator = new StringLength(['min' => $value]);
						break;

					case 'maxlength':

						$validator = new StringLength(['max' => $value]);
						break;

					case 'type':

						switch ($value)
						{
							case 'number':

								$validator = new Digits();
								break;

							case 'email':

								$validator = new EmailAddress();
								break;

							case 'date':

								$validator = new Date();
								break;

							case 'url':

								$validator = new Uri();
								break;
						}
						break;

					case 'min':

						if (array_key_exists('type', $all_attribs) && in_array($all_attribs['type'], ['number', 'range']))
							$validator = new GreaterThan(['min' => $value, 'inclusive' => true]);
						else
							throw new Exception("The input type must be 'range' or 'number'");

						break;

					case 'max':

						if (array_key_exists('type', $all_attribs) && in_array($all_attribs['type'], ['number', 'range']))
							$validator = new LessThan(['max' => $value, 'inclusive' => true]);
						else
							throw new Exception("The input type must be 'range' or 'number'");

						break;

					case 'step':

						$baseValue = (array_key_exists('min', $all_attribs)) ? $all_attribs['min'] : 0;

						if (array_key_exists('type', $all_attribs) && in_array($all_attribs['type'], ['range']))
							$validator = new Step(['baseValue' => $baseValue, 'step' => $value]);
						else
							throw new Exception("The input type must be 'range'");

						break;
				}

				if (in_array($name, ['required', 'digits', 'minlength', 'maxlength', 'type', 'min', 'max', 'date', 'step']))
				{
					$valid = $validator->isValid($form_value);
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
}