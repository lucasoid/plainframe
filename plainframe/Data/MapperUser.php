<?php

namespace plainframe\Data;

class MapperUser extends Mapper {
		
	protected function setBaseQuery() {
		$this->baseQuery = 'SELECT userid FROM users';
	}
	
	protected function setColumns() {
		$this->columns = array('userid');
	}
	
	protected function setSaveColumns() {
		$this->savecolumns = array('userid');
	}	
	
	protected function setIdField() {
		$this->idFieldName = 'userid';
	}
	
	protected function setMappedClass() {
		$this->mappedClass = 'plainframe\Domain\User';
	}
	
	protected function setTable() {
		$this->table = 'users';
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