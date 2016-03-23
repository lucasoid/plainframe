<?php
namespace plainframe\Domain;

class Upload extends Object {
			
	public $creatorid;
	public $title;
	public $updated;
	public $filename;
	public $mimetype;
	
	public function toArray() {
		return array(
			'id' => $this->id,
			'creatorid' => $this->creatorid,
			'title' => $this->title,
			'updated' => $this->displayDate($this->updated),
			'filename' => $this->filename,
			'mimetype' => $this->mimetype,
			'itemdescription' => $this->title,
		);
	}
		
	public function toString() {
		return implode(', ', $this->toArray());
	}
}
?>