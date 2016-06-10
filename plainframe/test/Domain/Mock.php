<?php
namespace plainframe\Domain;

class Mock extends Object {
	
	public $id;
	public $title;
	public $description;
	public $date;
	public $htmlAllowed = array('description');
	public $dateFields = array('date');
		
	public function toArray() {
		return array(
			'id' => $this->id,
			'title' => $this->title,
			'description' => $this->description
		);
	}
	
	public function toString() {
		'mock object';
	}
}
?>