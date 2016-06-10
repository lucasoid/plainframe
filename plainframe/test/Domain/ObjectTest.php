<?php

class ObjectTest extends PHPUnit_Framework_TestCase {
		
	protected function setUp() {
		include_once('Mock.php');
	}
		
	public function testConstructor() {	
		$id = 45;
		$mock = new \plainframe\Domain\Mock($id);
		$this->assertEquals(45, $mock->id);
	}
	
	public function testDisplayDate() {
		$id = 45;
		$mock = new \plainframe\Domain\Mock($id);
		
		$input = 'Feb 11 2015 04:14:23';
		$format = \plainframe\Config::get('format', 'date');
		$expected = date($format, strtotime($input));
		
		$this->assertEquals($expected, $mock->displayDate($input));
		
	}
		
	public function testDisplayTime() {
		$id = 45;
		$mock = new \plainframe\Domain\Mock($id);
		
		$input = time();
		$format = \plainframe\Config::get('format', 'time');
		$expected = gmdate($format, $input);
				
		$this->assertEquals($expected, $mock->displayTime($input));
		
	}		
	public function testObserveWithChange() {
		
		$mock = new \plainframe\Domain\Mock(45);
		$mock->title = 'Hamlet';
		$mock->observe('title', 'Macbeth');
		$this->assertTrue($mock->readyToUpdate['title']);
	}
	
	public function testObserveWithoutChange() {
		$mock = new \plainframe\Domain\Mock(45);
		$mock->title = 'Hamlet';
		$mock->observe('title', 'Hamlet');
		$this->assertEmpty($mock->readyToUpdate);
	}
	
	public function testObserveWithStrongTyping() {
		$mock = new \plainframe\Domain\Mock(45);
		$mock->observe('id', '45');
		$this->assertTrue($mock->readyToUpdate['id']);
	}
	
	public function testObserveWithNullValue() {
		$mock = new \plainframe\Domain\Mock(45);
		$mock->observe('date', null);
		$this->assertTrue($mock->readyToUpdate['date']);
	}
	
	public function testMapParametersForNonexistentKey() {
		$array = array('nonexistent'=>'value');
		$mock = new \plainframe\Domain\Mock(45);
		$original = clone $mock;
		$mock->mapParameters($array);
		$this->assertEquals($original, $mock);
	}
	
	public function testMapParametersForExistingKey() {
		$array = array('title'=>'Hamlet');
		$mock = new \plainframe\Domain\Mock(45);
		$original = clone $mock;
		$mock->mapParameters($array);
		$this->assertNotEquals($original, $mock);
		$this->assertEquals('Hamlet', $mock->title);
	}
	
	public function testMapParametersWithStrippedHtml() {
		$array = array('title' => '<h1>Crime & Punishment</h1>');
		$mock = new \plainframe\Domain\Mock(45);
		$mock->mapParameters($array);
		$this->assertEquals('Crime &amp; Punishment', $mock->title);
	}
	
	public function testParametersWithAllowedHtml() {
		$desc = '<p>Tis nobler in the mind to suffer the slings &amp; arrows</p>';
		$array = array('title' => '<h1>Hamlet</h1>', 'description'=> $desc);
		$mock = new \plainframe\Domain\Mock(45);
		$mock->mapParameters($array);
		$this->assertEquals($desc, $mock->description);
	}
	
	public function testParametersWithMaliciousHtml() {
		$desc = '<p>Tis nobler in the mind to suffer the slings &amp; arrows</p><script>alert("Attack!")</script>';
		$array = array('title' => '<h1>Hamlet</h1>', 'description'=> $desc);
		$mock = new \plainframe\Domain\Mock(45);
		$mock->mapParameters($array);
		$this->assertEquals('<p>Tis nobler in the mind to suffer the slings &amp; arrows</p>', $mock->description);
	}
	
	public function testMapParametersWithInteger() {
		$array = array('title' => 22);
		$mock = new \plainframe\Domain\Mock(45);
		$mock->mapParameters($array);
		$this->assertEquals(22, $mock->title);
	}
	
	public function testMapParametersWithDate() {
		$array = array('date' => '2015-12-31');
		$mock = new \plainframe\Domain\Mock(45);
		$mock->mapParameters($array);
		$this->assertEquals('2015-12-31', $mock->date);
	}
	
	public function testMapParametersWithFalseDate() {
		$array = array('date' => 'NotADateValue');
		$mock = new \plainframe\Domain\Mock(45);
		$mock->mapParameters($array);
		$this->assertEquals(null, $mock->date);
	}
	
	public function testToJson() {
		$properties = array('id' => 45, 'title' => 'Hamlet', 'description' => 'Full of sound and fury, signifying nothing');
		$mock = new \plainframe\Domain\Mock(45);
		$mock->mapParameters($properties);
		$this->assertEquals(json_encode($properties), $mock->toJson());
	}
	
	
}
?>