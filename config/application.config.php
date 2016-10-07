<?php

return [
    // Each new module must be declared as follow
    'modules' => [
        'App'
    ],
    'router' => [
        'routes' => [
            /* Default route:
            * The home route is the default route to the application. If any module,
            * controller or view are passed in the URL the application take the following
            * values
            */
            'defaults' => [
                'module' => 'App',
                'controller' => 'Dashboard',
                'view' => 'index'
            ],
        ],
    ],
    'environment' => [
        'base_path' => dirname(dirname($_SERVER['PHP_SELF'])),
        'dev_mode'  => true,                       // set this to FALSE for production environments
        'locale' => 'es'
    ],
];