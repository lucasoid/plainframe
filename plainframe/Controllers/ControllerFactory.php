<?php
namespace plainframe\Controllers;

class ControllerFactory {
	
	public static function makeController($controllername) {
		$classname = '\plainframe\Controllers\Controller'  . $controllername;
		if(class_exists($classname)) {
			$controller = new $classname();
			if(is_a($controller, "\plainframe\Controllers\Controller")) {
				return $controller;
			}
		}
		return false;
	}
	
}
?>