<?php

class MapperTest extends PHPUnit_Framework_TestCase {
		
	private $mapper;
		
	protected function setUp() {
		include_once('MapperMockChild.php');
		include_once('MockChild.php');
		include_once('MockFaulty.php');
		$this->mapper = new \plainframe\Data\MapperMockChild();
	}
	
	public function testSaveWrongObject() {
		$obj = new \plainframe\Domain\MockFaulty;
		$this->setExpectedException('Exception');
		$this->mapper->save($obj);
	}
	
	public function testSaveInsert() {	
		$obj = new \plainframe\Domain\MockChild();
		$obj->observe('title', 'Pippi Longstocking');
		$obj->observe('description', 'A tale of two pigtails');
		$obj = $this->mapper->save($obj);
		$this->assertEquals(1, $obj->id);
	}
	
	public function testSaveUpdate() {
		$obj = new \plainframe\Domain\MockChild();
		$obj->observe('title', 'Pippi Longstocking');
		$obj->observe('description', 'A tale of two pigtails');
		$obj = $this->mapper->save($obj);
		$this->assertEquals(1, $obj->id);
		
		$obj->observe('title', 'Pippi Longstocking Returns');
		$obj = $this->mapper->save($obj);
		$this->assertEquals(1, $obj->id);
		$this->assertEquals('Pippi Longstocking Returns', $obj->title);
		
	}
	
	public function testDelete() {
		$obj = new \plainframe\Domain\MockChild();
		$obj->observe('title', 'Pippi Longstocking');
		$obj->observe('description', 'A tale of two pigtails');
		$obj = $this->mapper->save($obj);
		$obj = $this->mapper->delete($obj);
		$this->assertEquals(true, $obj);
	}
	
	public function testDeleteNoId() {
		$obj = new \plainframe\Domain\MockChild();
		$obj->observe('title', 'Pippi Longstocking');
		$obj->observe('description', 'A tale of two pigtails');
		$this->setExpectedException('Exception');
		$obj = $this->mapper->delete($obj);
	}
	
	
		
	public function testFindById() {
		$obj = new \plainframe\Domain\MockChild();
		$obj->observe('title', 'Pippi Longstocking');
		$obj->observe('description', 'A tale of two pigtails');
		$obj = $this->mapper->save($obj);
		$newObj = $this->mapper->findById(1);
		$this->assertNotEmpty($newObj->id);
	}
	
	public function testGetCollection() {
		$obj = new \plainframe\Domain\MockChild();
		$obj->observe('title', 'Pippi Longstocking');
		$obj->observe('description', 'A tale of two pigtails');
		$obj = $this->mapper->save($obj);
		
		$obj_2 = new \plainframe\Domain\MockChild();
		$obj_2->observe('title', 'Flowers For Algernon');
		$obj_2->observe('description', 'A cautionary tale');
		$obj_2 = $this->mapper->save($obj_2);
		
		$collection = $this->mapper->getCollection(array(), array(), array());
		$this->assertEquals(2, count($collection));
		
	}
	
	public function testGetCollectionCount() {
		$obj = new \plainframe\Domain\MockChild();
		$obj->observe('title', 'Pippi Longstocking');
		$obj->observe('description', 'A tale of two pigtails');
		$obj = $this->mapper->save($obj);
		
		$obj_2 = new \plainframe\Domain\MockChild();
		$obj_2->observe('title', 'Flowers For Algernon');
		$obj_2->observe('description', 'A cautionary tale');
		$obj_2 = $this->mapper->save($obj_2);
		
		$count = $this->mapper->getCollectionCount(array(), array(), array());
		$this->assertEquals(2, $count);
	}
	
	
}
?>