<?php
namespace plainframe\Data;

class MapperPreset extends Mapper {
		
	protected function setBaseQuery() {
		$this->baseQuery = 'SELECT id, controller, userid, preset, name, primeflag FROM presets';
	}
	
	protected function setColumns() {
		$this->columns = array('id', 'controller', 'userid', 'preset', 'name', 'primeflag');
	}
	
	protected function setSaveColumns() {
		$this->savecolumns = array('id'=>'id', 'controller'=>'controller', 'userid'=>'userid', 'preset'=>'preset', 'name'=>'name', 'primeflag'=>'primeflag');
	}
	
	protected function setIdField() {
		$this->idFieldName = 'id';
	}
	
	protected function setIdSaveField() {
		$this->idSaveFieldName = 'id';
	}
	protected function setMappedClass() {
		$this->mappedClass = 'plainframe\Domain\Preset';
	}
	
	protected function setTable() {
		$this->table = 'presets';
	}
	
	protected function setDb() {
		$db = new SqlDB();
		$this->dbh = $db->getConnection();
	}
	
	protected function setDbType() {
		$this->dbtype = 'SQLITE';
	}
}
?>