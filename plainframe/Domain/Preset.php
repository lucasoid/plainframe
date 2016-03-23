<?php
namespace plainframe\Domain;

class Preset extends Object {
	
	public $controller;
	public $userid;
	public $preset;
	public $name;
	public $primeflag;
	public $htmlAllowed = array('preset');

	public function toArray() {
		return array(
				'userid'=>$this->userid,
				'controller'=>$this->controller,
				'name'=>$this->name,
				'preset'=>$this->preset,
				'primeflag'=>$this->primeflag,
				'id'=>$this->id,
		);
	}
	
	public function toString() {
		return $this->name;
	}
	
}

?>