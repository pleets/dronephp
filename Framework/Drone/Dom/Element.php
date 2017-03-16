<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Dom;

abstract class Element
{
    /**
     * @var string
     */
    protected $startTag;

    /**
     * Defines if the element has a end tag
     *
     * @var boolean
     */
    protected $endTag;

    /**
     * @var array
     */
	protected $attributes;

    /**
     * Gets all attributes of the element
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Returns startTag attribute
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
     * @return boolean
     */
	public function getEndTag()
	{
		return $this->endTag;
	}

    /**
     * Sets the startTag attribute
     *
     * @param string $startTag
     *
     * @return null
     */
    public function setStartTag($startTag)
    {
        $this->startTag = $startTag;
    }

    /**
     * Sets the endTag attribute
     *
     * @param boolean $endTag
     *
     * @return null
     */
    public function setEndTag($endTag)
    {
        $this->endTag = (boolean) $endTag;
    }

    /**
     * Returns a particular Attribute
     *
     * @param string $label
     * @param string $name
     *
     * @return Drone\Dom\Atribute|null
     */
    public function getAttribute($label, $name)
    {
        $attribs = $this->getAttributes();

        foreach ($attribs[$label] as $attrib)
        {
            if ($attrib->getName() == $name)
                return $attrib;
        }
    }

    /**
     * Sets only one attribute
     *
     * @param string $label
     * @param string $name
     * @param mixed $value
     *
     * @return null
     */
	public function setAttribute($label, $name, $value)
	{
        $attribs = $this->getAttributes();

        if (array_key_exists($label, $attribs))
        {

            if (!array_key_exists($name, $attribs))
                $this->attributes[$label][] = new Attribute($name, $value);
            else
                $this->getAttribute($label, $name)->setValue($value);
        }
        else {
            $this->attributes[$label][] = new Attribute($name, $value);
        }
	}

    /**
     * Adds all attributes passed as parameter
     *
     * @param array $definition
     *
     * @return null
     */
    public function addAttributes($definition)
    {
        foreach ($definition as $label => $attributes)
        {
            $this->attributes[$label] = [];

            foreach ($attributes as $name => $value)
            {
                $this->attributes[$label][] = new Attribute($name, $value);
            }
        }
    }

    /**
     * Constructor
     *
     * @param array $options
     */
	public function __construct($options)
	{
        if (!array_key_exists('endTag', $options))
            $this->setEndTag(false);

		foreach ($options as $key => $value)
        {
            switch ($key)
            {
                case 'attributes':
                    $this->addAttributes($value);
                    break;

                case 'startTag':
                    $this->setStartTag($value);
                    break;

                case 'endTag':
                    $this->setEndTag($value);
                    break;
            }
		}
	}
}