<?php
namespace plainframe\Domain;

class User extends Object {
	
	public $userid;
		
	public function toArray() {
		return array(
			'userid' => $this->userid,
		);
	}
	
	public function toString() {
		return implode(' | ', $this->toArray);
	}
}
?>