<?php

namespace plainframe\Domain;

class MockChild extends Object {
	
	public $id;
	public $title;
	public $description;
	
	public function toArray() {
		return array(
			'id' => $this->id,
			'title' => $this->title,
			'description' => $this->description
		);
	}
	
	public function toString() {
		return $this->title . $this->description;
	}
	
}
?>