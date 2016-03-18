<?php

namespace plainframe\Data;

class MapperMockChild extends Mapper {
	
	private $var = 'foo';
	
	protected function setBaseQuery() {
		$this->baseQuery = 'SELECT id, title, description FROM books';
	}
	
	protected function setColumns() {
		$this->columns = array('id', 'title', 'description');
	}
	
	protected function setIdField() {
		$this->idFieldName = 'id';
	}
	
	protected function setMappedClass() {
		$this->mappedClass = '\plainframe\Domain\MockChild';
	}
	
	protected function setTable() {
		$this->table = 'books';
	}
	
	protected function setDb() {
		$pdo = new \PDO('sqlite::memory:', '', '', array());
		$qry = 'CREATE TABLE books (id INTEGER PRIMARY KEY, title TEXT, description TEXT)';
		$stmt = $pdo->prepare($qry);
		$stmt->execute(array());
		$this->dbh = $pdo;
	}
	
	protected function setDbType() {
		$this->dbtype = 'SQLITE';
	}
}

?>