<?php

class ControllerFactoryTest extends PHPUnit_Framework_TestCase {
		
	protected function setUp() {
		include('ControllerMock.php');
	}
		
	public function testMakeController() {	
		
		$name = "Faulty";
		$ctrl = \plainframe\Controllers\ControllerFactory::makeController($name);
		$this->assertFalse($ctrl);
		
		$name = "Mock";
		$ctrl = \plainframe\Controllers\ControllerFactory::makeController($name);
		$this->assertObjectHasAttribute('var', $ctrl);
	}
	
}
?>