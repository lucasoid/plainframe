<?php
namespace plainframe\Data;

/**
 * 
 * Defines a factory method for returning mapper objects
 * 
 */
class MapperFactory {
	
	/**
	 * 
	 * Returns a mapper of the specified type
	 * 
	 *  @param string $objecttype
	 *  @return \Data\Mapper|bool
	 */
	public static function makeMapper($objecttype) {
		$classname = "plainframe\Data\Mapper".$objecttype;
		if(!empty($objecttype) && class_exists($classname, false)) {
			$mapper = new $classname;
			if(is_a($mapper, "plainframe\Data\Mapper")) {
				return $mapper;
			}
		}
		
		return false;		
	}
}
?>