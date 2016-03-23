<?php

namespace plainframe\Data;

class MapperBook extends Mapper {
		
	protected function setBaseQuery() {
		$this->baseQuery = 'SELECT id, title, author, pages, published FROM books';
		$this->baseQuery = 'SELECT id, title, author, pages, (SELECT SUM(pages) from books WHERE <<WHERE>>) AS allpages, published FROM books';
	}
	
	protected function setColumns() {
		$this->columns = array('id', 'title', 'author', 'allpages', 'pages', 'published');
	}
	
	protected function setSaveColumns() {
		$this->savecolumns = array('id', 'title', 'author', 'pages', 'published');
	}
	protected function setIdField() {
		$this->idFieldName = 'id';
	}
	
	protected function setMappedClass() {
		$this->mappedClass = 'plainframe\Domain\Book';
	}
	
	protected function setTable() {
		$this->table = 'books';
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