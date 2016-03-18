<?php
namespace plainframe\Domain;

class Mock extends Object {
	
	public $id;
	public $title;
	public $description;
	public $htmlAllowed = array('description');
		
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