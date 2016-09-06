<?php

return array(
    'router' => array(
        'routes' => array(
        	'App' => array(
        		'module' => 'App',
        		'controller' => 'Dashboard',
        		'view' => 'index'
        	)
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'default'	=> __DIR__ . '/../view/layout/layout.phtml',
        ),
    ),
);