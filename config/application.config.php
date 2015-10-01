<?php

return array(
    'modules' => array(
        'App',
        'Pleets',
    ),
    'router' => array(
        'routes' => array(
            /* Default route:
            * The home route is the default route to the application. If any module,
            * controller or view are passed in the URL the application take the following
            * values
            */
            'defaults' => array(
                'module' => 'App',
                'controller' => 'Index',
                'view' => 'index'
            ),
        ),
    ),
    'app' => array(
        'base_path' => dirname(dirname($_SERVER['PHP_SELF'])),
        'development_environment' => true                       // set this to FALSE for production environments
    ),
);