<?php

class ConfigTest extends PHPUnit_Framework_TestCase {
	
	public function testGet() {
		$this->assertEquals(null, plainframe\Config::get('bad', 'val'));
		$this->assertEquals(null, plainframe\Config::get('unit_test', 'bad'));
		$this->assertEquals('bar', plainframe\Config::get('unit_test', 'foo'));
	}
}
?>