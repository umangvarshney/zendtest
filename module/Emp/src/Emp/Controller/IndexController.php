<?php
namespace Emp\Controller;

use \Zend\Mvc\Controller\AbstractActionController;
use \Zend\View\Model\ViewModel;
//use Emp\Model\Factory;

class IndexController extends AbstractActionController {
	
	/**
	 * Index action
	 * @author Umang
	 */
	public function indexAction(){
		$factoryData = $this->getServiceLocator()->get('Model');
		$tableModel = $factoryData->get('Emp');
		
		$result = $tableModel->fetchAll();
		
		$view = new ViewModel();
		$view->result = $result;
		return $view;
	}
}