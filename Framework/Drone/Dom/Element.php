<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/Drone
 * @copyright Copyright (c) 2014-2016 DronePHP. (http://www.dronephp.com)
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
     * @var boolean
     */
    protected $endTag;

    /**
     * @var array
     */
	protected $attributes;

    /**
     * Get all attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get startTag
     *
     * @return string
     */
    public function getStartTag()
    {
        return $this->startTag;
    }

    /**
     * Get endTag
     *
     * @return string
     */
	public function getEndTag()
	{
		return $this->endTag;
	}

    /**
     * Set start tag
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
     * Set end tag
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
     * Get a particular Attribute object
     *
     * @return Drone\Dom\Atribute
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
     * Set only one attribute
     *
     * @param array $attributes
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
     * Add all attributes passed as parameter
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