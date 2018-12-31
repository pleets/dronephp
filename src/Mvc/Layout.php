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

use Drone\Mvc\AbstractionController;
use Drone\Mvc\Exception;

/**
 * Layout class
 *
 * This class manages templates from views
 */
class Layout
{
    use \Drone\Util\ParamTrait;

    /**
     * Controller instance
     *
     * @var AbstractionController
     */
    private $controller;

    /**
     * View path
     *
     * @var string
     */
    private $view;

    /**
     * Document title
     *
     * @var string
     */
    private $title;

    /**
     * Document description
     *
     * @var string
     */
    private $description;

    /**
     * Document image
     *
     * @var string
     */
    private $image;

    /**
     * Base path
     *
     * @var string
     */
    private $basePath;

    /**
     * Returns the instance of current controller
     *
     * @return AbstractionController
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Returns the view
     *
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Returns the document title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns the document description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Returns the document image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets the document title
     *
     * @param string $title
     *
     * @return null
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Sets the document description
     *
     * @param string $description
     *
     * @return null
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Sets the document image
     *
     * @param string $image
     *
     * @return null
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * Sets the view
     *
     * @param AbstractionModule $module
     * @param string $view
     *
     * @return null
     */
    public function setView($module, $view)
    {
        $config = $module->getConfig();

        if (!array_key_exists($view, $config["view_manager"]["view_map"]) || !file_exists($config["view_manager"]["view_map"][$view]))
            throw new Exception\ViewNotFoundException("The 'view' template " . $view . " does not exists");

        $this->view = $config["view_manager"]["view_map"][$view];
    }

    /**
     * Sets the base path
     *
     * @param string $path
     *
     * @return null
     */
    public function setBasePath($path)
    {
        $this->basePath = $path;
    }

    /**
     * Constructor
     *
     * All modifiable attributes (i.e. with setter method) can be passed as key
     *
     * @param array $params
     *
     * @throws Exception\PageNotFoundException
     */
    public function __construct(array $params = [])
    {
        foreach ($params as $param => $value)
        {
            if (property_exists(__CLASS__, strtolower($param)) && method_exists($this, 'set'.$param))
                $this->{'set'.$param}($value);
        }
    }

    /**
     * Loads a view from a controller
     *
     * @throws Exception\PageNotFoundException
     *
     * @param AbstractionController
     *
     * @return null
     */
    public function fromController(AbstractionController $controller)
    {
        // str_replace() is needed in linux systems
        $this->setParams($controller->getParams());
        $this->basePath = $controller->getBasePath();
        $this->controller = $controller;

        if ($controller->getShowView())
        {
            $view = 'module/'      . $controller->getModule()->getModuleName() .
                    '/source/view/'. basename(str_replace('\\','/',get_class($controller))) .
                    '/'            . $controller->getMethod() . '.phtml';

            $this->view = $view;
        }

        if ($controller->getTerminal())
        {
            if (file_exists($view))
                include $view;
        }
        else
        {
            if (!is_null($this->view) && !file_exists($this->view))
                throw new Exception\ViewNotFoundException("The 'view' template " . $this->view . " does not exists");

            $config = $controller->getModule()->getConfig();

            $layout = $controller->getLayout();

            if (!array_key_exists($controller->getLayout(), $config["view_manager"]["template_map"]))
                throw new Exception\PageNotFoundException("The 'template' " . $layout . " was not defined in module.config.php");

            $template = $config["view_manager"]["template_map"][$controller->getLayout()];

            if (!file_exists($template))
                throw new Exception\PageNotFoundException("The 'template' " . $template . " does not exists");

            include $template;
        }
    }

    /**
     * Loads a view from a template file
     *
     * @throws Exception\PageNotFoundException
     *
     * @param AbstractionModule $module
     * @param string $template
     *
     * @return null
     */
    public function fromTemplate($module, $template)
    {
        $config = $module->getConfig();
        include $config["view_manager"]["template_map"][$template];
    }

    /**
     * Includes the file view
     *
     * @return null
     */
    public function content()
    {
        if (!file_exists($this->view))
            throw new Exception\ViewNotFoundException("The 'view' template " . $this->view . " does not exists");

        include $this->view;
    }

    /**
     * Returns the base path of the application
     *
     * @return string
     */
    public function basePath()
    {
        return $this->basePath;
    }
}