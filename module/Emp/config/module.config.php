<?php
return array(
		'controllers' => array(
				'invokables' => array(
						'Emp\Controller\Index' => 'Emp\Controller\IndexController',
				),
		),
		// The following section is new and should be added to your file
		'router' => array(
				'routes' => array(
						'emp' => array(
								'type'    => 'segment',
								'options' => array(
										'route'    => '/emp[/:action][/:id]',
										'constraints' => array(
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id'     => '[0-9]+',
										),
										'defaults' => array(
												'controller' => 'Emp\Controller\Index',
												'action'     => 'index',
										),
								),
						),
				),
		),
		'view_manager' => array(
				'template_path_stack' => array(
						'emp' => __DIR__ . '/../view',
				),
		),
);