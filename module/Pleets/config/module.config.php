<?php

return array(
    'router' => array(
        'routes' => array(
        	'Pleets' => array(
        		'module' => 'Pleets',
        		'controller' => 'App',
        		'view' => 'index'
        	)
        ),
    ),
   'view_manager' => array(
    	'template_map'  => array(
        	'default'	=> __DIR__ . '/../view/layout/layout.phtml',
        	'error'    	=> __DIR__ . '/../view/layout/error.phtml',
     	),
   ),
);