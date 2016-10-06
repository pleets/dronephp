<?php

return [
    'router' => [
        'routes' => [
        	'App' => [
        		'module' => 'App',
        		'controller' => 'Dashboard',
        		'view' => 'index'
        	]
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'default'	=> __DIR__ . '/../view/layout/layout.phtml',
        ],
    ],
];