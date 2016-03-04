<?php

return array(
    'router' => array(
        'routes' => array(
        	'Api' => array(
        		'module' => 'Api',
        		'controller' => 'Index',
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