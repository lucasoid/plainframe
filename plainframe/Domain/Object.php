<?php
namespace plainframe\Domain;
use plainframe\Config;
/**
 * Defines a parent class for all domain objects
 */
abstract class Object {
	
	public $id;
	public $htmlAllowed = array();
	public $dateFields = array();
	public $readyToUpdate = array();
	
	public function __construct($id = null) {
		$this->id = $id;
	}
		
	/**
	 * provides a formatted version of a datetime string
	 * 
	 * @param string $date
	 * @return string
	 */
	public function displayDate($date) {
		if(!empty($date)) {
			return date(Config::get('format', 'date'), strtotime($date));
		}
	}
	
	/**
	 * provides a formatted version of a time string
	 * 
	 * @param unknown $time
	 * @return string
	 */
	public function displayTime($time) {
		return gmdate(Config::get('format', 'time'), $time);
	}
	
	/**
	 * This method sets a property's value and observes for changes. Sets the update flag to true only if changes were made. 
	 *
	 * @param string $property the name of the property to observe
	 * @param unknown $value the new value to be checked
	 */
	public function observe($property, $value) {
		if(property_exists($this, $property)) {
			if($this->{$property} !== $value || is_null($this->{$property})) {
				$this->readyToUpdate[$property] = true;
				$this->{$property} = $value;
			}
		}
	}
	
	/**
	 * This method takes an array of key=>val pairs and attempts to map them to the object's properties. 
	 * Integer values will be passed through untouched.
	 * String values will be stripped of HTML unless they are in the htmlAllowed array.
	 * For properties where HTML is allowed, their HTML will be sanitized using the HTMLPurifier library.
	 *
	 * @param array $array the array of key=>val pairs to map
	 */
	public function mapParameters(array $array) {
		foreach($array as $key=>$val) {
			if(property_exists($this, $key)) {
				if(in_array($key, $this->htmlAllowed)) {
					if(class_exists('\HTMLPurifier')) {
						$config = \HTMLPurifier_Config::createDefault();
						$purifier = new \HTMLPurifier($config);
						$clean_value = $purifier->purify($val);
					}
					else {
						$val = strip_tags($val);
						$clean_value = htmlentities($val, ENT_NOQUOTES);
					}				
				}
				elseif(in_array($key, $this->dateFields)) {
					$clean_value = $this->isDate($val) ? $val : null;
				}
				elseif(is_null($val)) {
					$clean_value = null;
				}
				elseif(is_int($val)) {
					$clean_value = $val;
				}
				else {
					$val = strip_tags($val);
					$clean_value = htmlentities($val, ENT_NOQUOTES);
				}
				$this->observe($key, $clean_value);
			}
		}
	}
	
	abstract public function toArray();
	
	abstract public function toString();
	
	public function toJson() {
		return json_encode($this->toArray());
	}
	
	public function forceZeros($val) {
		return empty($val) ? 0 : $val;
	}
	
	public function isDate($val) {
		try {
			$d = new \DateTime($val);
		}
		catch(\Exception $e) {
			$d = false;
		}
		return $d && $d->format('Y-m-d') === date('Y-m-d', strtotime($val));		
	}	
	
	
}

?>