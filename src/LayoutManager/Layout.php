<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/Drone
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <dario@pleets.org>
 */

namespace Drone\LayoutManager;

use Drone\Mvc\AbstractionController;
use Drone\Mvc\PageNotFoundException;

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
     * Returns the document title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
     * Sets the base path
     *
     * @param string $basePath
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
     * @throws PageNotFoundException
     */
    public function __construct()
    {
        // nothing to do
    }

    /**
     * Loads a view from a controller
     *
     * @throws PageNotFoundException
     *
     * @param AbstractionController
     */
    public function fromController(AbstractionController $controller)
    {
        // str_replace() is needed in linux systems
        $view = 'module/' . $controller->getModule()->getModuleName() .'/source/view/'. basename(str_replace('\\','/',get_class($controller))) . '/' . $controller->getMethod() . '.phtml';

        $this->setParams($controller->getParams());
        $this->basePath = $controller->getBasePath();
        $this->controller = $controller;
        $this->view = $view;

        if ($controller->getTerminal())
        {
            if (file_exists($view))
                include $view;
        }
        else
        {
            if (!file_exists($view))
                throw new PageNotFoundException("The 'view' template $view does not exists");

                $config = $controller->getModule()->getConfig();
                include $config["view_manager"]["template_map"][$controller->getLayout()];
        }
    }

    /**
     * Loads a view from a template file
     *
     * @throws PageNotFoundException
     *
     * @param Drone\Mvc\AbstractionModule $module
     * @param string $template
     * @param array $params
     */
    public function fromTemplate($module, $template, $params = [])
    {
        $this->setParams($params);

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