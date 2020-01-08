<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Validator;

use Drone\Dom\Element\Form;
use Zend\Validator\Date;
use Zend\Validator\Digits;
use Zend\Validator\EmailAddress;
use Zend\Validator\GreaterThan;
use Zend\Validator\LessThan;
use Zend\Validator\NotEmpty;
use Zend\Validator\Step;
use Zend\Validator\StringLength;
use Zend\Validator\Uri;

/**
 * FormValidator class
 *
 * Form validation implements Zend validator to check html form parameters.
 * n-dimensional arrays (name='example[][]') are supported.
 */
class FormValidator
{
    use \Drone\Error\ErrorTrait;

    /**
     * The result of latest validation
     *
     * It's null before validate() execution
     *
     * @var boolean|null
     */
    private $valid;

    /**
     * Form instance
     *
     * @var Form
     */
    private $form;

    /**
     * Element options
     *
     * @var array
     */
    private $options;

    /**
     * Translator object
     *
     * @var \Zend\Mvc\I18n\Translator
     */
    private $translator;

    /**
     * Returns the valid attribute after validation
     *
     * @return boolean
     */
    public function isValid()
    {
        if (is_null($this->valid)) {
            # This error is thrown because of 'setValid' method has not been executed.
            throw new \LogicException('No validation has been executed!');
        }

        return $this->valid;
    }

    /**
     * Sets valid atribute after each validation
     *
     * @param boolean $valid
     *
     * @return null
     */
    private function setValid($valid)
    {
        $this->valid = (is_null($this->valid) ? true : $this->valid) && $valid;
    }

    /**
     * Constructor
     *
     * @param Form   $form
     * @param string $locale
     */
    public function __construct(Form $form, $locale = null)
    {
        $this->form = $form;

        if (is_null($locale)) {
            $locale = 'en';
        }

        $i18nTranslator = \Zend\I18n\Translator\Translator::factory(
            [
                'locale'  => "$locale",
                'translation_files' => [
                    [
                        "type" => 'phparray',
                        "filename" => "vendor/zendframework/zend-i18n-resources/languages/$locale/Zend_Validate.php",
                    ],
                ],
            ]
        );

        $this->translator = new \Zend\Mvc\I18n\Translator($i18nTranslator);
    }

    /**
     * Checks all form rules
     *
     * @throws LogicException
     * @throws RuntimeException
     *
     * @return null
     */
    public function validate()
    {
        $this->valid = null;

        $this->setValid(true);
        $elements = $this->form->getChildren();

        foreach ($elements as $label => $element) {
            if (!$element->isFormControl()) {
                continue;
            }

            $attribs = $element->getAttributes();

            $all_attribs = [];

            foreach ($attribs as $attr) {
                $all_attribs[$attr->getName()] = $attr->getValue();
            }

            $required = array_key_exists('required', $all_attribs) ? $all_attribs["required"] : false;

            foreach ($attribs as $attr) {
                $name  = $attr->getName();
                $value = $attr->getValue();

                $attrib = $element->getAttribute("value");
                $form_value = (!is_null($attrib)) ? $attrib->getValue() : null;

                $validator = null;

                switch ($name) {
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
                        switch ($value) {
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
                        if (array_key_exists('type', $all_attribs) &&
                            in_array($all_attribs['type'], ['number', 'range'])
                        ) {
                            $validator = new GreaterThan(['min' => $value, 'inclusive' => true]);
                        } else {
                            throw new \LogicException("The input type must be 'range' or 'number'");
                        }

                        break;

                    case 'max':
                        if (array_key_exists('type', $all_attribs) &&
                            in_array($all_attribs['type'], ['number', 'range'])
                        ) {
                            $validator = new LessThan(['max' => $value, 'inclusive' => true]);
                        } else {
                            throw new \LogicException("The input type must be 'range' or 'number'");
                        }

                        break;

                    case 'step':
                        $baseValue = (array_key_exists('min', $all_attribs)) ? $all_attribs['min'] : 0;

                        if (array_key_exists('type', $all_attribs) && in_array($all_attribs['type'], ['range'])) {
                            $validator = new Step(['baseValue' => $baseValue, 'step' => $value]);
                        } else {
                            throw new \LogicException("The input type must be 'range'");
                        }

                        break;

                    case 'data-validators':
                        if (!is_array($value)) {
                            throw new \InvalidArgumentException(
                                "Invalid type given. Array expected in 'data-validators' attribute."
                            );
                        }

                        foreach ($value as $class => $params) {
                            $className = "\Zend\Validator\\" . $class;

                            if (!class_exists($className)) {
                                $className = "\Zend\I18n\Validator\\" . $class;

                                if (!class_exists($className)) {
                                    throw new \RuntimeException(
                                        "The class '$userInputClass' or '$className' does not exists"
                                    );
                                }
                            }

                            $validator = new $className($params);

                            $validator->setTranslator($this->translator);
                            $this->validation($validator, $form_value, $label, $required);
                        }

                        break;
                }

                if (in_array(
                    $name,
                    ['required', 'digits', 'minlength', 'maxlength', 'type', 'min', 'max', 'date', 'step']
                ) && !is_null($validator)) {
                    $validator->setTranslator($this->translator);
                    $this->validation($validator, $form_value, $label, $required);
                }
            }
        }
    }

    /**
     * Validate all field values iteratively
     *
     * Supports n-dimensional arrays (name='example[][]')
     *
     * @param \Zend\Validator $validator
     * @param mixed           $form_value
     * @param string          $label
     * @param boolean         $required
     *
     * @return null
     */
    private function validation($validator, $form_value, $label, $required)
    {
        if (gettype($form_value) != 'array') {
            $val = $form_value;

            # Check if the value is required. If it is, check the other rules.
            $v = new NotEmpty();
            $v->setTranslator($this->translator);
            $notEmpty = $v->isValid($val);

            if (!$required && !$notEmpty) {
                return null;
            }

            $valid = $validator->isValid($val);
            $this->setValid($valid);

            if (!$valid) {
                foreach ($validator->getMessages() as $message) {
                    $this->error($label ."-~-". (count($this->getErrors()) + 1), $message);
                }
            }
        } else {
            foreach ($form_value as $val) {
                $this->validation($validator, $val, $label, $required);
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getErrors()
    {
        $errors = [];

        if (count($this->errors)) {
            foreach ($this->errors as $key => $value) {
                $errorLbl = explode("-~-", $key);
                $label = array_shift($errorLbl);

                if (!array_key_exists($label, $errors)) {
                    $errors[$label] = [];
                }

                $errors[$label][] = $value;
            }
        }

        return $errors;
    }
}
