<?php

class CollectionTest extends PHPUnit_Framework_TestCase {
		
	private $collection;
	
	protected function setUp() {
		include_once('Mock.php');
		include_once('MapperMock.php');
		$this->collection = new \plainframe\Domain\Collection('Mock', array(), array(), array());
	}
	
	public function testKey() {
		$this->assertEquals(0, $this->collection->key());
	}
	
	public function testNext() {
		$this->collection->next();
		$this->assertEquals(1, $this->collection->key());
	}
	
	public function testPrevious() {
		$this->collection->next();
		$this->collection->previous();
		$this->assertEquals(0, $this->collection->key());
	}
	
	public function testRewind() {
		$this->collection->next();
		$this->collection->next();
		$this->assertEquals(2, $this->collection->key());
		$this->collection->rewind();
		$this->assertEquals(0, $this->collection->key());
	}
	
	public function testValid() {
		for($i = 0; $i < 5; $i++) {
			$this->collection->next();
		}
		$this->assertEquals(5, $this->collection->key());
		$this->assertFalse($this->collection->valid());
	}
	
	public function testCount() {
		$this->assertEquals(3, $this->collection->count());
	}
	
	public function testCurrent() {
		$first = $this->collection->current();
		$this->assertEquals('Macbeth', $first->title);
		
		$this->collection->next();
		$second = $this->collection->current();
		$this->assertEquals('Hamlet', $second->title);
		
		$this->collection->next();
		$this->collection->next();
		$fourth = $this->collection->current();
		$this->assertNull($fourth);
	}
	
	public function testOverrideCollection() {
		$new = array(
			array('id' => 4, 'title' => 'Twelfth Night', 'Music be the food of love'),
		);
		$override = array();
		foreach($new as $item) {
			$obj = new \plainframe\Domain\Mock();
			$obj->mapParameters($item);
			$override[] = $obj;
		}
		
		$this->collection->overrideCollection($override);
		$this->assertEquals(1, $this->collection->count());
	}
	
	public function testOverrideCollectionEmpty() {
		$override = array();
		$this->collection->overrideCollection($override);
		$this->assertEquals(0, $this->collection->count());
	}
	
	public function testOverrideCollectionNonObject() {
		$override = array(
			array('id' => 4, 'title' => 'Twelfth Night', 'Music be the food of love'),
		);
		$this->setExpectedException('Exception');
		$this->collection->overrideCollection($override);
	}
	
	public function testSortBy() {
		$this->collection->sortBy('title');
		$this->collection->rewind();
		$first = $this->collection->current();
		$this->assertEquals('Hamlet', $first->title);
	}
	
	public function testSortByWithInvalidField() {
		$this->setExpectedException('Exception');
		$this->collection->sortBy('wordcount');
	}
	
	public function testReverseSort() {
		$this->collection->reverseSort();
		$this->collection->rewind();
		$first = $this->collection->current();
		$this->assertEquals('Julius Caesar', $first->title);
	}
	
	public function testRemove() {
		$this->collection->rewind();
		$this->collection->remove();
		$this->assertEquals(2, $this->collection->count());
		$current = $this->collection->current();
		$this->assertEquals('Hamlet', $current->title);
	}
	
	public function testSliceArray() {
		$offset = 1;
		$length = 1;
		$preserve_keys = true;
		$this->collection->sliceArray($offset, $length);
		$this->assertEquals(1, $this->collection->count());
		$this->collection->rewind();
		$current = $this->collection->current();
		$this->assertEquals('Hamlet', $current->title);
	}
	
	public function testToArray() {
		$this->assertEquals(3, count($this->collection->toArray()));
	}
	
	public function testToJson() {
		$this->collection->sliceArray(0, 1);
		$expected = array(array('id' => 1, 'title' => 'Macbeth', 'description' => 'Toil and trouble'));
		$this->assertEquals(json_encode($expected), $this->collection->toJson());
	}
	
}
?>