<?php 

namespace Emp;

use Zend\Mvc\RouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\AuthenticationService;

/**
 * Module for routing
 */

class Module {
	
	/**
	 * get configuration
	 *
	 */
	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}
	
	/**
	 * get autoloader
	 */
	public function getAutoloaderConfig() {
		return array(
				'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
				));
	}
	
	/**
	 * Get Service configuration
	 */
	public function getServiceConfig() {
		
		return array(
				'factories'=>array(
						'Model'=> function($sm) {
							$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
							\Zend\Db\TableGateway\Feature\GlobalAdapterFeature::setStaticAdapter($dbAdapter);
							return \Emp\Model\Factory::getInstance();
						}
				),
		);
	}
}