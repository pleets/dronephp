<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Mvc;

/**
 * View class
 *
 * This class manages views and parameters
 */
class View
{
    use \Drone\Util\ParamTrait;

    /**
     * View name
     *
     * @var string
     */
    protected $name;

    /**
     * The path where views are located.
     *
     * @var string
     */
    protected $path;

    /**
     * Returns the view name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets the view name
     *
     * @param string
     *
     * @return null
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Sets the path
     *
     * @param string
     *
     * @return string
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Constructor
     *
     * @param string $name
     * @param array  $parameters
     */
    public function __construct($name, Array $parameters = [])
    {
        $this->name  = $name;
        $this->setParams($parameters);
    }

    /**
     * Get the contents of the view
     *
     * @throws Exception\ViewNotFoundException
     *
     * @return  null
     */
    public function getContents()
    {
        $_view = $this->path . DIRECTORY_SEPARATOR . $this->name . '.phtml';

        if (!file_exists($_view))
            throw new Exception\ViewNotFoundException("The view '" .$this->name. "' does not exists");

        $contents = file_get_contents($_view);

        if ($contents === false)
            throw new Exception\ViewGetContentsErrorException("The view '" .$this->name. "' does not exists");

        return $contents;
    }

    /**
     * Loads the view
     *
     * @return  null
     */
    public function render()
    {
        if (count($this->getParams()))
            extract($this->getParams(), EXTR_SKIP);

        include $this->getContents();
    }
}