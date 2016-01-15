<?php
namespace Album\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Album;
use Album\Form\AlbumForm;

class AlbumController extends AbstractActionController
{
	protected $albumTable;
    public function indexAction()
    {
        return new ViewModel(array(
            'albums' => $this->getAlbumTable()->fetchAll(),
        )); //Fetch all results from model
    }
    
    public function addAction() {
    	$form = new AlbumForm();
    	$form->get('submit')->setValue('Add');
    	$request = $this->getRequest(); //generate request
    	
    	if($request->isPost()) { //check wheather request is POST or Not
    		$album = new Album(); //create a instance of Album Model
    		$form->setInputFilter($album->getInputFilter()); //Validation filter set of type album on form
    		$form->setData($request->getPost()); //set data on form
    		if($form->isValid()){ //Check wheather form data is valid or not
    			$album->exchangeArray($form->getData()); //fill the object value of Model
    			$this->getAlbumTable()->saveAlbum($album);//save the data of Model
    			
    			//Redirect to list of album
    			return $this->redirect()->toRoute('album');//Redirection after complete
    		}
    	}
    	return array('form'=>$form);
    }
    
    public function editAction() {
    	$id = (int) $this->params()->fromRoute('id',0); //Get the id from url parmas
    	if(!$id){
    		return $this->redirect()->toRoute('album',array('action'=>'add')); //Wheather id is null then redirect to add function
    	}
    	try{
    		$album = $this->getAlbumTable()->getAlbum($id); //get the album data of that particular id
    	}
    	catch (Exception $e) {
    		return $this->redirect()->toRoute('album',array('action'=>'add'));
    	}
    	
    	$form = new AlbumForm();//Create a Instance of AlbumForm
    	$form->bind($album);//Bind the album data to the form
    	$form->get('submit')->setAttribute('value','Edit'); //Change submit to Edit Button
    	$request = $this->getRequest();//Generate Request
    	if($request->isPost()) {//check wheather request is POST or Not
    		$album = new Album(); //create a instance of Album Model
    		$form->setInputFilter($album->getInputFilter());//Validation filter set of type album on form
    		$form->setData($request->getPost());//set data on form
    		if($form->isValid()){//Check wheather form data is valid or not
    			$this->getAlbumTable()->saveAlbum($form->getData());//Save updated data from form
    			
    			//Redirect to list of album
    			return $this->redirect()->toRoute('album');
    		}
    	}
    	return array('id'=>$id,'form'=>$form);
    	
    }
    
    public function deleteAction() {
    	$id = (int) $this->params()->fromRoute('id',0);
    if(!$id){
    		return $this->redirect()->toRoute('album',array('action'=>'add'));
    	}
    	$request = $this->getRequest();
    	if($request->isPost()) {
    		$del = $request->getPost('del','No');
    		if($del == "Yes") {
    			$id = (int)$request->getPost('id');
    			$this->getAlbumTable()->deleteAlbum($id);
    		}
    		
    		return $this->redirect()->toRoute('album');
    	}
    	return array(
    	'id'=>$id,
    	'album'=>$this->getAlbumTable()->getAlbum($id),
    	);
    }
  
	public function getAlbumTable() {
		if(!$this->albumTable) {
			$sm = $this->getServiceLocator();
			$this->albumTable = $sm->get('Album\Model\AlbumTable');
		}
		return $this->albumTable;
		/* $factoryData = $this->getServiceLocator()->get('Model');
		return $factoryData->get('Album'); */
	}
}