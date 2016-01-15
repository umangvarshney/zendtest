<?php
/**
 * Test controller for testing purpose
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class TestController extends AbstractActionController {
  
	public function indexAction(){
		$viewModel = new ViewModel(array('message'=>"This is my testing Phase"));
		$viewModel->setTemplate('application/index/test.phtml'); //Application(Module)/Index(Controller)/test.phtml(Template)
		return $viewModel;
	}
}
