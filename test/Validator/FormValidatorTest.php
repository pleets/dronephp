<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace DroneTest\Validator;

use Drone\Dom\Element\ElementFactory;
use Drone\Validator\FormValidator;
use PHPUnit\Framework\TestCase;

class FormValidatorTest extends TestCase
{
    /**
     * Tests form validation fail
     *
     * @return null
     */
    public function testFormValidationFail()
    {
        $form = ElementFactory::create('FORM', [
            "action" => 'someurl',
            "method" => 'post'
        ], [
            "input" => [
                "username" => [
                    "type"      => 'text',
                    "maxlength" => 15,
                    "minlength" => 5
                ],
                "email" => [
                    "type"      => 'email',
                    "maxlength" => 15,
                    "minlength" => 5
                ],
                "password" => [
                    "type"      => 'password',
                    "maxlength" => 15,
                    "minlength" => 5
                ]
            ]
        ]);

        $form->fill([
            "username" => 'jobs',       # wrong because of minlength attr
            "password" => 'jVi7Qp4X',
            "email"    => 'j@'          # wrong because of type and minlength attr
        ]);

        $validator = new FormValidator($form, 'fr');
        $validator->validate();

        # testing validation
        $this->assertNotTrue($validator->isValid());

        $errors = $validator->getErrors();

        # testing error labeled
        $this->assertArrayHasKey("username", $errors);
        $this->assertArrayHasKey("email", $errors);

        # testing error grouping
        $this->assertEquals(1, count($errors["username"]));
        $this->assertEquals(2, count($errors["email"]));

        # testing locale
        $this->assertEquals("L'entrée contient moins de 5 caractères", $errors["username"][0]);
    }

    /**
     * Tests form validation success
     *
     * @return null
     */
    public function testFormValidationSuccess()
    {
        $form = ElementFactory::create('FORM', [
            "action" => 'someurl',
            "method" => 'post'
        ], [
            "input" => [
                "username" => [
                    "type"      => 'text',
                    "maxlength" => 15,
                    "minlength" => 5
                ],
                "email" => [
                    "type"      => 'email',
                    "maxlength" => 16,
                    "minlength" => 5
                ],
                "password" => [
                    "type"      => 'password',
                    "maxlength" => 15,
                    "minlength" => 5
                ]
            ]
        ]);

        $form->fill([
            "username" => 'steave.jobs',
            "password" => 'jVi7Qp4X',
            "email"    => 'jobs@example.com'
        ]);

        $validator = new FormValidator($form, 'en');
        $validator->validate();

        # testing validation
        $this->assertTrue($validator->isValid());

        $errors = $validator->getErrors();
        $this->assertEquals(0, count($errors));
    }

    /**
     * Tests form validation success when an element is or not required
     *
     * @return null
     */
    public function testFormValidationForRequiredAndNotRequired()
    {
        /*
         * Not required element not filled
         */

        $form = ElementFactory::create('FORM', [
            "action" => 'someurl',
            "method" => 'post'
        ], [
            "input" => [
                "username" => [
                    "type"      => 'text',
                    "maxlength" => 15,
                    "minlength" => 5,
                    "required"  => false     # could be ommited, this is the default behaviour
                ],
            ]
        ]);

        $validator = new FormValidator($form, 'en');
        $validator->validate();

        # testing validation, no element has been declared as 'required'
        $this->assertTrue($validator->isValid());

        $errors = $validator->getErrors();
        $this->assertEquals(0, count($errors));

        /*
         * Required element not filled
         */

        $form = ElementFactory::create('FORM', [
            "action" => 'someurl',
            "method" => 'post'
        ], [
            "input" => [
                "username" => [
                    "type"      => 'text',
                    "maxlength" => 15,
                    "minlength" => 5,
                    "required"  => true
                ],
            ]
        ]);

        $validator = new FormValidator($form, 'en');
        $validator->validate();

        # testing validation, the element has been declared as 'required'
        $this->assertNotTrue($validator->isValid());

        $errors = $validator->getErrors();
        $this->assertEquals(1, count($errors));

        /*
         * Not Required element filled
         */

        $form = ElementFactory::create('FORM', [
            "action" => 'someurl',
            "method" => 'post'
        ], [
            "input" => [
                "username" => [
                    "type"      => 'text',
                    "maxlength" => 15,
                    "minlength" => 5,
                    "required"  => false
                ],
            ]
        ]);

        $form->fill([
            "username" => 'jobs',       # wrong because of minlength attr
        ]);

        $validator = new FormValidator($form, 'en');
        $validator->validate();

        # testing validation, no element has been declared as 'required' but is fille, so it'll be validated
        $this->assertNotTrue($validator->isValid());

        $errors = $validator->getErrors();
        $this->assertEquals(1, count($errors));
    }

    /**
     * Tests form validation for n-dimensional arrays
     *
     * An n-dimensional array is created by PHP when the form control has a name like
     * name='product[]'
     * name='productdetail[0][]'
     *
     * @return null
     */
    public function testFormValidationWithNdimensionalArrays()
    {
        $form = ElementFactory::create('FORM', [
            "action" => 'someurl',
            "method" => 'post'
        ], [
            "input" => [
                # in this case product name
                "product" => [
                    "type"      => 'text',
                    "maxlength" => 15,
                    "minlength" => 5
                ],
                "price" => [
                    "type" => 'number',
                    "min"  => 1,
                ]
            ]
        ]);

        $form->fill([
            "product" => [
                "optical mouse",
                "-",                # wrong name
                "78"                # another wrong name
            ],
            "price" => [
                10,
                0,   # wrong price
                0    # another wrong price
            ]
        ]);

        $validator = new FormValidator($form, 'en');
        $validator->validate();

        # testing validation
        $this->assertNotTrue($validator->isValid());

        $errors = $validator->getErrors();

        # testing error labeled
        $this->assertArrayHasKey("product", $errors);
        $this->assertArrayHasKey("price", $errors);

        # testing error grouping
        $this->assertEquals(2, count($errors["product"]));
        $this->assertEquals(2, count($errors["price"]));
    }

    /**
     * Tests for Zend validations
     *
     * @return null
     */
    public function testZendValidations()
    {
        $form = ElementFactory::create('FORM', [
            "action" => 'someurl',
            "method" => 'post'
        ], [
            "input" => [
                "username" => [
                    "type"      => 'text',
                    "maxlength" => 15,
                    "minlength" => 5,
                    "data-validators" => [
                        "Alnum"  => ["allowWhiteSpace" => false]
                    ]
                ],
                "type" => [
                    "type" => 'text',
                    "data-validators" => [
                        "InArray"  => ["haystack" => ['admin', 'guest']]
                    ]
                ],
                "password" => [
                    "type"      => 'password',
                    "maxlength" => 15,
                    "minlength" => 5,
                ]
            ]
        ]);

        $form->fill([
            "username" => 'steave jobs',       # wrong because of minlength attr
            "password" => 'jVi7Qp4X',
            "type"     => 'moderator'          # wrong because moderator is not a valid needle
        ]);

        $validator = new FormValidator($form, 'en');
        $validator->validate();

        # testing validation
        $this->assertNotTrue($validator->isValid());

        $errors = $validator->getErrors();

        # testing error labeled
        $this->assertArrayHasKey("username", $errors);
        $this->assertArrayHasKey("type", $errors);

        # testing error grouping
        $this->assertEquals(1, count($errors["username"]));
        $this->assertEquals(1, count($errors["type"]));
    }
}