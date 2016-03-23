<?php
namespace plainframe\Domain;

class Book extends Object {
	
	public $title;
	public $author;
	public $published;
	public $pages;
	public $allpages;
	public $htmlAllowed = array('title', 'author', 'itemdescription');
	
	public function toArray() {
		return array(
			'id' => $this->id,
			'title' => $this->title,
			'author' => $this->author,
			'pages' => $this->pages,
			'allpages' => $this->allpages,
			'published' => $this->published,
			'itemdescription' => $this->title,
		);
	}
	
	public function toString() {
		return implode(' | ', $this->toArray);
	}
}
?>