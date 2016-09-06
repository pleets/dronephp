<?php

return array(
    'modules' => array(
        'App'
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
                'controller' => 'Dashboard',
                'view' => 'index'
            ),
        ),
    ),
    'environment' => array(
        'base_path' => dirname(dirname($_SERVER['PHP_SELF'])),
        'dev_mode'  => true                       // set this to FALSE for production environments
    ),
);