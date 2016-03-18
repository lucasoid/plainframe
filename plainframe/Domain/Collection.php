<?php
namespace plainframe\Domain;
use \Iterator;

/**
 * This class is an instance of the collection pattern; it can contain collections of any Domain object.
 *
 */
class Collection implements Iterator {
	protected $array = array();
	protected $position = 0;
	protected $type;
		
	/**
	 * 
	 * @param string $type corresponds to the base name of one of the Domain classes
	 * @param array $filters
	 * @param array $sortlevels
	 * @param array $range 
	 */
	public function __construct($type, array $filters, array $sortlevels, array $range) {
		$name = "plainframe\Data\Mapper".$type;
		if(class_exists($name)) {
			$mapper = new $name;
			$this->array = $mapper->getCollection($filters, $sortlevels, $range);		
			$this->position = 0;
		}
	}
	
	public function current() {
		if(array_key_exists($this->position, $this->array)) {
			return $this->array[$this->position];
		}
		else {
			return null;
		}
	}
	
	public function key() {
		return $this->position;
	}
	public function next() {
		++$this->position;
	}
	public function previous() {
		--$this->position;
	}
	public function rewind() {
		$this->position = 0;
	}
	public function valid() {
		return isset($this->array[$this->position]);
	}
	
	public function count() {
		return count($this->array);
	}
	
	/**
	 * This function can be used to create a custom array that can be iterated over, instead of one of the default Domain object collections.
	 * 
	 * @param array $array
	 */
	public function overrideCollection(array $array) {
		if(empty($array)) {
			$this->array = array();
		}
		else {
			if(is_object($array[0])) {
				$this->array = $array;
			}
			else {
				throw new \Exception("a collection array must contain objects");
			}
		}
	}
	
	/**
	 * sorts an array of objects by one of the properties of the object
	 * 
	 * @param string $field
	 * @throws \Exception
	 */
	public function sortBy($field) {
		if(!empty($this->array)) {
			$obj = get_class($this->array[0]);
			if(!property_exists($obj, $field)) {
				throw new \Exception("That property doesn't exist for the object");
			}
			usort($this->array,
			function($a, $b) use ($field) {
				
				$a = $a->{$field};
				$b = $b->{$field};
				if($a == $b) {
					return 0;
				}
				return $a>$b ? 1 : -1;
			}
			);
		}

	}
	
	/**
	 * reverses the order of the array in the collection
	 * 
	 */
	public function reverseSort() {
		$this->array = array_reverse($this->array);
	}
	
	/**
	 * removes a single element from the array at the pointer's current position
	 */
	public function remove() {
		array_splice($this->array, $this->position, 1);
	}
	
	/**
	 * leaves the array as a slice of the original array
	 * 
	 * @param number $offset
	 * @param number $length
	 * @param boolean $preserve_keys
	 */
	public function sliceArray($offset, $length) {
		$this->array = array_slice($this->array, $offset, $length, false);
	}
	
	public function toArray() {
		$array = array();
		foreach($this->array as $object) {
			$array[] = $object->toArray();
		}
		return $array;
	}
	
	public function toJson() {
		$array = $this->toArray();
		return json_encode($array, true);
	}
	
}

?>