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
     * @var boolean|null
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
	 * @var array
	 */
	private $options;

    /**
     * Returns all failure messages
     *
     * @return array
     */
	public function getMessages()
	{
		return $this->messages;
	}

    /**
     * Returns valid attribute after validation
     *
     * @return array
     */
	public function isValid()
	{
		return $this->valid;
	}

    /**
     * Sets valid atribute after each validation
     *
     * @return boolean
     */
	public function setValid($valid)
	{
		$this->valid = (is_null($this->valid) ? true : $this->valid) && $valid;
	}

    /**
     * Gets an option
     *
     * @param string $option
     *
     * @return mixed
     */
	public function getOption($key, $name)
	{
		if (!array_key_exists($key, $this->options))
			throw new Exception("The option '$option' does not exists");

		return array_key_exists($name, $this->options[$key]) ? $this->options[$key][$name] : null;
	}

    /**
     * Constructor
     *
     * @param HtmlForm $formHandler
     */
	public function __construct(HtmlForm $formHandler, $options)
	{
		$this->formHandler = $formHandler;
		$this->options = (is_array($options)) ? $options : array();
	}

    /**
     * Checks all form rules
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

			$label = (array_key_exists('label', array_keys($this->options))) ? $attributes["label"] : $key;

			$all_attribs = [];

			foreach ($attributes as $attr)
			{
				$all_attribs[$attr->getName()] = $attr->getValue();
			}

			foreach ($attributes as $name => $attr)
			{
				$name = $attr->getName();
				$value = $attr->getValue();

				$attrib = $this->formHandler->getAttribute($label, "value");
				$form_value = (!is_null($attrib)) ? $attrib->getValue() : null;

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

		foreach ($this->options as $key => $options)
		{
			if (isset($options["validators"]) and is_array($options["validators"]))
			{
				foreach ($options["validators"] as $class => $params)
				{
					$className = "\Zend\Validator\'" . $class;

					if (!class_exists($className))
						throw new Exception("The class '$className' does not exists");

					$validator = new $className($params);

					$form_value = $this->formHandler->getAttribute($key, "value")->getValue();

					$validator->setTranslator($this->translator);
					$valid = $validator->isValid($form_value);
					$this->setValid($valid);

					if (!$valid)
					{
						if (!in_array($key, array_keys($this->messages)))
							$this->messages[$key] = array();

						$this->messages[$key] = array_merge($this->messages[$key], $validator->getMessages());
					}
				}
			}
		}
	}
}