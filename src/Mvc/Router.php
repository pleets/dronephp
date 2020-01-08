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
 * Router class
 *
 * This class build the route and calls to specific application controller
 */
class Router
{
    /**
     * List of routes
     *
     * @var array
     */
    private $routes = [];

    /**
     * The Identifiers builds the route
     *
     * @var array
     */
    private $identifiers;

    /**
     * Default identifiers
     *
     * @var array
     */
    private $defaults;

    /**
     * Controller instance
     *
     * @var AbstractController
     */
    private $controller;

    /**
     * Indicates how the class name could be matched
     *
     * @var callable
     */
    private $classNameBuilder;

    /**
     * Zend\Router implementation
     *
     * @var \Zend\Router\SimpleRouteStack
     */
    private $zendRouter;

    /**
     * Returns all routes built
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Returns all identifiers
     *
     * @return array
     */
    public function getIdentifiers()
    {
        return $this->identifiers;
    }

    /**
     * Returns default identifiers
     *
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Returns the controller instance
     *
     * @throws \RuntimeException
     *
     * @return AbstractController
     */
    public function getController()
    {
        if (is_null($this->controller)) {
            throw new \RuntimeException("No controller matched, try to match first.");
        }

        return $this->controller;
    }

    /**
     * Returns the class name builder function
     *
     * @return callable
     */
    public function getClassNameBuilder()
    {
        return $this->classNameBuilder;
    }

    /**
     * Returns the Zend\Router\SimpleRouteStack object
     *
     * @return \Zend\Router\SimpleRouteStack
     */
    public function getZendRouter()
    {
        return $this->zendRouter;
    }

    /**
     * Sets identifiers
     *
     * @param string $module
     * @param string $controller
     * @param string $view
     *
     * @return null
     */
    public function setIdentifiers($module, $controller, $view)
    {
        $identifiers = ["module" => $module, "controller" => $controller, "view" => $view];

        foreach ($identifiers as $key => $identifier) {
            if (!is_string($identifier)) {
                throw new \InvalidArgumentException("Invalid type given for '$key'. String expected.");
            }
        }

        $this->identifiers = [
            "module"     => $module,
            "controller" => $controller,
            "view"       => $view,
        ];
    }

    /**
     * Sets default identifiers
     *
     * @param string $module
     * @param string $controller
     * @param string $view
     *
     * @return null
     */
    public function setDefaults($module, $controller, $view)
    {
        $identifiers = ["module" => $module, "controller" => $controller, "view" => $view];

        foreach ($identifiers as $key => $identifier) {
            if (!is_string($identifier)) {
                throw new \InvalidArgumentException("Invalid type given for '$key'. String expected.");
            }
        }

        $this->defaults = [
            "module"     => $module,
            "controller" => $controller,
            "view"       => $view,
        ];
    }

    /**
     * Sets the class name builder function
     *
     * @param callable $builder
     *
     * @return null
     */
    public function setClassNameBuilder(callable $builder)
    {
        $this->classNameBuilder = $builder;
    }

    /**
     * Constructor
     *
     * @param array $routes
     */
    public function __construct(array $routes = [])
    {
        if (count($routes)) {
            foreach ($routes as $route) {
                $this->addRoute($route);
            }
        }

        # schema for identifiers
        $this->identifiers = [
            "module"     => '',
            "controller" => '',
            "view"       => '',
        ];

        $this->defaults = [
            "module"     => '',
            "controller" => '',
            "view"       => '',
        ];

        # default class name builder
        $this->setClassNameBuilder(function ($module, $class) {
            return "\\$module\\$class";
        });

        $this->zendRouter = new \Zend\Router\SimpleRouteStack();
    }

    /**
     * Builds the current route and calls the controller
     *
     * @throws Exception\PageNotFoundException
     * @throws Exception\RouteNotFoundException
     * @throws \LogicException
     *
     * @return  null
     */
    public function match()
    {
        if (!is_callable($this->classNameBuilder)) {
            throw \LogicException("No class name builder found");
        }

        /*
         *  Key value pairs builder:
         *  Searches for the pattern /var1/value1/var2/value2 and converts it to  var1 => value1, var2 => value2
         */
        if (array_key_exists('params', $_GET)) {
            $keypairs = $this->parseRequestParameters($_GET["params"]);
            unset($_GET["params"]);
            $_GET = array_merge($_GET, $keypairs);
        }

        /*
         *  Route builder:
         *  The route is built by default from the URL as follow
         *  www.example.com/module/controller/view
         */

        $match = false;

        if (count($this->routes)) {
            foreach ($this->routes as $key => $route) {
                if ($route["module"]     == $this->identifiers["module"]     &&
                    $route["controller"] == $this->identifiers["controller"] &&
                    $route["view"]       == $this->identifiers["view"]
                ) {
                    $module     = $route["module"];
                    $controller = $route["controller"];
                    $view       = $route["view"];

                    $match = true;
                    break;
                }
            }
        }

        if (count($this->defaults) && !$match) {
            if (!empty($this->defaults["module"])     &&
                !empty($this->defaults["controller"]) &&
                !empty($this->defaults["view"])
            ) {
                $module     = $this->defaults["module"];
                $controller = $this->defaults["controller"];
                $view       = $this->defaults["view"];

                $match = true;
            }
        }

        if (!$match) {
            throw new Exception\RouteNotFoundException("The route has not been matched");
        }

        $fqn_controller = call_user_func($this->classNameBuilder, $module, $controller);

        if (class_exists($fqn_controller)) {
            try {
                $this->controller = new $fqn_controller;
            } catch (Exception\MethodNotFoundException $e) {
                # change context, in terms of Router MethodNotFoundException or
                # PrivateMethodExecutionException is a PageNotfoundException
                throw new Exception\PageNotFoundException($e->getMessage(), $e->getCode(), $e);
            } catch (Exception\PrivateMethodExecutionException $e) {
                throw new Exception\PageNotFoundException($e->getMessage(), $e->getCode(), $e);
            }

            # in controller terms, a view is a method
            $this->controller->setMethod($view);
        } else {
            throw new Exception\ControllerNotFoundException("The control class '$fqn_controller' does not exists!");
        }
    }

    /**
     * Execute the method matched in the controller
     *
     * @return mixed
     */
    public function run()
    {
        return $this->controller->execute();
    }

    /**
     * Adds a new route to router
     *
     * @param Array $route
     *
     * @throws LogicException
     *
     * @return null
     */
    public function addRoute(array $route)
    {
        $key = array_keys($route);

        if (count($key) > 1) {
            throw new \InvalidArgumentException("So many keys in a simple route");
        }

        $key = array_shift($key);

        $identifiers = ["module", "controller", "view"];

        foreach ($identifiers as $identifier) {
            if (!array_key_exists($identifier, $route[$key])) {
                throw new \InvalidArgumentException("The identifier '$identifier' does not exists in the route");
            }

            if (!is_string($route[$key][$identifier])) {
                throw new \InvalidArgumentException("Invalid type given for '$identifier'. String expected.");
            }
        }

        if (array_key_exists($key, $this->routes)) {
            throw new \LogicException("The key '$key' was already defined as a route");
        }

        $this->routes = array_merge($this->routes, $route);
    }

    /**
     * Adds a new route to router
     *
     * @param string $name
     * @param Zend\Router\Http\RouteInterface $route
     *
     * @throws LogicException
     *
     * @return null
     */
    public function addZendRoute($name, \Zend\Router\Http\RouteInterface $route)
    {
        $this->zendRouter->addRoute($name, $route);
    }

    /**
     * Parse key value pairs from a string
     *
     * Searches for the pattern /var1/value1/var2/value2 and converts it to
     *
     * var1 => value1
     * var2 => value2
     *
     * @param string $unparsed
     *
     * @return array
     */
    private function parseKeyValuePairsFrom($unparsed)
    {
        $params = explode("/", $unparsed);

        $vars = $values = [];

        $i = 1;
        foreach ($params as $item) {
            if ($i % 2 != 0) {
                $vars[] = $item;
            } else {
                $values[] = $item;
            }
            $i++;
        }

        $vars_count = count($vars);

        $result = [];

        for ($i = 0; $i < $vars_count; $i++) {
            if (array_key_exists($i, $values)) {
                $result[$vars[$i]] = $values[$i];
            } else {
                $result[$vars[$i]] = '';
            }
        }

        return $result;
    }
}
