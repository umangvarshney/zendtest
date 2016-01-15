<?php

namespace Album\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Album
{
	public $id;
	public $artist;
	public $title;
	protected $inputFilter;
	
	public function exchangeArray($data){
		$this->id = (!empty($data['id']))?$data['id']:NULL;
		$this->artist = (!empty($data['artist']))?$data['artist']:NULL;
		$this->title = (!empty($data['title']))?$data['title']:NULL;
	}
	
	public function getArrayCopy() { 
		return get_object_vars($this); 
	}
	
	public function setInputFilter(InputFilterInterface $inputFilter) {
		
		throw new Exception("Not used");
	}
	
	public function getInputFilter() {
		if(!$this->inputFilter) {
			$inputFilter = new InputFilter();
			
			$inputFilter->add(array(
			'name'=>'id',
			'required'=>true,
			'filters'=>array(
			array('name'=>'Int'),
			)
			));
			
			$inputFilter->add(array(
			'name'=>'artist',
			'required'=>true,
			'filters'=>array(
			array(
			'name'=>'SripTags',
			'name'=>'StringTrim',
			)
			),
			'validators'=>array(
			array(
			'name'=>'StringLength',
			'options'=>array(
			'encoding'=>'UTF-8',
			'min'=>1,
			'max'=>100,
			),
			),
			),
			));
			
			$inputFilter->add(array(
			'name'=>'title',
			'required'=>true,
			'filters'=>array(
			array(
			'name'=>'SripTags',
			'name'=>'StringTrim',
			)
			),
			'validators'=>array(
			array(
			'name'=>'StringLength',
			'options'=>array(
			'encoding'=>'UTF-8',
			'min'=>1,
			'max'=>100,
			),
			),
			),
			));
			
			$this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
	}
}