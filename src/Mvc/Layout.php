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

use Drone\Mvc\AbstractController;
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
     * @var AbstractController
     */
    private $controller;

    /**
     * View path
     *
     * @var string
     */
    private $view;

    /**
     * Returns the instance of current controller
     *
     * @return AbstractController
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
     * Loads a view from a controller
     *
     * @throws Exception\PageNotFoundException
     *
     * @param AbstractController
     *
     * @return null
     */
    public function fromController(AbstractController $controller)
    {
        $this->setParams($controller->getParams());
        $this->controller = $controller;

        if (is_null($controller->getModule()))
            throw new \RuntimeException("No module instance found in controller '" . get_class($controller) . "'");

        if ($controller->getShowView())
            $this->view =
                $controller->getModule()->getModulePath() .'/'. $controller->getModule()->getModuleName() .'/'.
                $controller->getModule()->getViewPath()                .'/'.
                basename(str_replace('\\','/',get_class($controller))) .'/'.
                $controller->getMethod() . '.phtml';

        if ($controller->getTerminal())
        {
            if (file_exists($this->view))
                include $this->view;
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
     * Alias to return the base path of the application
     *
     * @return string
     */
    public function basePath()
    {
        return $this->controller->getModule()->getRouter()->getBasePath();
    }
}