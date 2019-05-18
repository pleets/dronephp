<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Dom\Element;

use Drone\Dom\Attribute;

/**
 * AbstractElement class
 *
 * To represent an abstract html element
 */
abstract class AbstractElement
{
    /**
     * The node name of the element
     *
     * @var string
     */
    const NODE_NAME = '';

    /**
     * Tells if the element has a end tag
     *
     * @var boolean
     */
    const HAS_END_TAG = true;

    /**
     * Start tag of the element
     *
     * @var string
     */
    protected $startTag;

    /**
     * End tag of the element
     *
     * @var string
     */
    protected $endTag;

    /**
     * Element attributes list
     *
     * @var Attribute[]
     */
    protected $attributes = [];

    /**
     * Children elements
     *
     * @var AbstractElement[]
     */
    protected $children = [];

    /**
     * Returns the startTag attribute
     *
     * @return string
     */
    public function getStartTag()
    {
        return $this->startTag;
    }

    /**
     * Returns the endTag attribute
     *
     * @return string
     */
    public function getEndTag()
    {
        return $this->endTag;
    }

    /**
     * Gets all attributes of the element
     *
     * @return Attribute[]
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Gets all children elements
     *
     * @return AbstractElement[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Checks if a particula child exists by label
     *
     * @param string $label
     *
     * @return boolean
     */
    public function hasChild($label)
    {
        if (array_key_exists($label, $this->children)) {
            return true;
        }

        return false;
    }

    /**
     * Returns a particular child
     *
     * @param string $label
     *
     * @return AbstractElement|null
     */
    public function getChild($label)
    {
        if (array_key_exists($label, $this->children)) {
            return $this->children[$label];
        }

        return null;
    }

    /**
     * Sets a child
     *
     * @param string          $label
     * @param AbstractElement $child
     *
     * @return null
     */
    public function setChild($label, AbstractElement $child)
    {
        $this->children[$label] = $child;
    }

    /**
     * Removes a particular child
     *
     * @param string $label
     *
     * @throws Exception\ChildNotFoundException
     *
     * @return AbstractElement|null
     */
    public function removeChild($label)
    {
        if (array_key_exists($label, $this->children)) {
            unset($this->children[$label]);
        } else {
            throw new Exception\ChildNotFoundException("The child to remove does not exists");
        }
    }

    /**
     * Checks if the element has the specified attribute
     *
     * @param string $name
     *
     * @return boolean
     */
    public function hasAttribute($name)
    {
        if (count($this->attributes)) {
            foreach ($this->attributes as $attrib) {
                if ($attrib->getName() == $name) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns a particular Attribute
     *
     * @param string $name
     *
     * @return Attribute|null
     */
    public function getAttribute($name)
    {
        if (count($this->attributes)) {
            foreach ($this->attributes as $attrib) {
                if ($attrib->getName() == $name) {
                    return $attrib;
                }
            }
        }

        return null;
    }

    /**
     * Sets an attribute
     *
     * @param Attribute $attribute
     *
     * @return null
     */
    public function setAttribute(Attribute $attribute)
    {
        $attrib = $this->getAttribute($attribute->getName());

        if (!is_null($attrib)) {
            foreach ($this->attributes as $key => $_attrib) {
                if ($_attrib->getName() == $attrib->getName()) {
                    $this->attributes[$key] = $attribute;
                }
            }
        } else {
            $this->attributes[] = $attribute;
        }
    }

    /**
     * Removes a particular Attribute
     *
     * @param string $name
     *
     * @throws Exception\AttributeNotFoundException
     *
     * @return null
     */
    public function removeAttribute($name)
    {
        if (count($this->attributes)) {
            foreach ($this->attributes as $key => $attrib) {
                if ($attrib->getName() == $name) {
                    unset($this->attributes[$key]);
                }
            }
        }

        throw new Exception\AttributeNotFoundException("The attribute to remove does not exists");
    }

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct()
    {
        if (static::HAS_END_TAG) {
            $this->startTag = "<" .strtolower(static::NODE_NAME). ">";
            $this->endTag   = "</" .strtolower(static::NODE_NAME). ">";
        } else {
            $this->startTag = "<" .strtolower(static::NODE_NAME);
            $this->endTag   = "/>";
        }
    }

    /**
     * Checks is an element is a form control
     *
     * @return boolean
     */
    public function isFormControl()
    {
        if (in_array(static::NODE_NAME, ['INPUT'])) {
            return true;
        }

        return false;
    }
}
