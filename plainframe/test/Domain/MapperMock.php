<?php
namespace plainframe\Data;

class MapperMock extends Mapper {
	
	protected function setBaseQuery() {}
		
	protected function setColumns() {}
	
	protected function setSaveColumns() {}
	
	protected function setIdField() {}
	
	protected function setIdSaveField() {}
	
	protected function setMappedClass() {}
	
	protected function setTable() {}
	
	protected function setDb() {}
	
	protected function setDbType() {}
	
	public function getCollection(array $filters, array $sortlevels, array $range) {
		include_once('Mock.php');
		
		$items = array(
			array(
				'id' => 1,
				'title' => 'Macbeth',
				'description' => 'Toil and trouble',
			),
			array(
				'id' => 2,
				'title' => 'Hamlet',
				'description' => 'A very palpable hit',
			),
			array(
				'id' => 3,
				'title' => 'Julius Caesar',
				'description' => 'Beware the ides of March',
			),
		);
						
		foreach($items as $item) {
			$obj = new \plainframe\Domain\Mock();
			$obj->mapParameters($item);
			$collection[] = $obj;
		}
		return $collection;
	}
}
?>