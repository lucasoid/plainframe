<?php
namespace plainframe\Domain;

class Book extends Object {
	
	public $title;
	public $author;
	
	public function toArray() {
		return array(
			'id' => $this->id,
			'title' => $this->title,
			'author' => $this->author
		);
	}
	
	public function toString() {
		return implode(' | ', $this->toArray);
	}
}
?>