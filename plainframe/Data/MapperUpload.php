<?php
namespace plainframe\Data;

class MapperUpload extends Mapper {
		
	protected function setBaseQuery() {
		$this->baseQuery = 'SELECT id, creatorid, title, updated, filename, mimetype from uploads';
	}
	
	protected function setColumns() {
		$this->columns = array('id', 'creatorid', 'title', 'updated', 'filename', 'mimetype');
	}
	
	protected function setSaveColumns() {
		$this->savecolumns = array('id'=>'id', 'creatorid'=>'creatorid', 'title'=>'title', 'updated'=>'updated', 'filename'=>'filename', 'mimetype'=>'mimetype');
	}
	
	protected function setIdField() {
		$this->idFieldName = 'id';
	}
	
	protected function setIdSaveField() {
		$this->idSaveFieldName = 'id';
	}
	
	protected function setMappedClass() {
		$this->mappedClass = 'plainframe\Domain\Upload';
	}
	
	protected function setTable() {
		$this->table = 'uploads';
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