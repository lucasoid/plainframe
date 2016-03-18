<?php

class MapperFactoryTest extends PHPUnit_Framework_TestCase {
		
	protected function setUp() {
		include_once('MapperMockChild.php');
	}
		
	public function testMakeMapper() {	
		
		$name = "Faulty";
		$mapper = \plainframe\Data\MapperFactory::makeMapper($name);
		$this->assertFalse($mapper);
		
		$name = "MockChild";
		$mapper = \plainframe\Data\MapperFactory::makeMapper($name);
		$this->assertObjectHasAttribute('var', $mapper);
	}
	
}
?>