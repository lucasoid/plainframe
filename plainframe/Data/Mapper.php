<?php
namespace plainframe\Data;

/**
 * 
 * Defines a superclass for mapping database/API values to domain objects
 * 
 */

abstract class Mapper {
	
	protected $baseQuery;
	protected $columns;
	protected $idFieldName;
	protected $mappedClass;
	protected $table;
	protected $dbh;
	protected $dbtype;
	
	public function __construct() {
				
		$this->setDb();
		$this->setDbType();
		$this->setBaseQuery();
		$this->setColumns();
		$this->setSaveColumns();
		$this->setIdField();
		$this->setMappedClass();
		$this->setTable();
	}
	
	abstract protected function setDb();
	
	abstract protected function setDbType();
	
	abstract protected function setBaseQuery();
	
	abstract protected function setColumns();
	
	abstract protected function setSaveColumns();
	
	abstract protected function setIdField();
	
	abstract protected function setMappedClass();
	
	abstract protected function setTable();
	
	/**
	 * @param plainframe\Domain\Object $obj
	 * @return plainframe\Domain\Object|boolean
	 */
	protected function doUpdate(\plainframe\Domain\Object $obj) {
		$mappedFields = array();
		$params = array();
		foreach($this->savecolumns as $column) {
			if(property_exists($obj, $column) && array_key_exists($column, $obj->readyToUpdate) && true === $obj->readyToUpdate[$column]) {
				$mappedFields[] = $column . '=?';
				$params[] = $obj->{$column};
			}
		}
		$params[] = $obj->id;
		$qry = 'UPDATE ' . $this->table . ' SET ' . implode(', ', $mappedFields) . ' WHERE ' . $this->idFieldName . '=?';
		$this->dbh->beginTransaction();
		$stmt = $this->dbh->prepare($qry);
		$exec = $stmt->execute($params);
		if($this->dbh->commit() && $exec) {
			return $obj;
		}
		else {
			return false;
		}
	}
	
	/**
	 * @param plainframe\Domain\Object $obj
	 * @return plainframe\\Domain\Object|boolean
	 */
	protected function doInsert(\plainframe\Domain\Object $obj) {
				
		$cols = array();
		$vals = array();
		$params = array();
		foreach($this->savecolumns as $column) {
			if(property_exists($obj, $column) && array_key_exists($column, $obj->readyToUpdate) && true === $obj->readyToUpdate[$column]) {
				$cols[] = $column;
				$vals[] = '?';
				$params[] = $obj->{$column};
			}
		}
		$qry = 'INSERT INTO ' . $this->table . ' (' . implode(', ', $cols) . ') VALUES (' . implode(', ', $vals) . ')';
		
		$this->dbh->beginTransaction();
		$stmt = $this->dbh->prepare($qry);
		$exec = $stmt->execute($params);
		$id = null;
		try {
			$id = $this->dbh->lastInsertId();
		}
		catch(\PDOException $e) {
			//
		}
		
		if($this->dbh->commit() && $exec) {
			$obj->id = $id;
			return $obj;
		}
		else {
			return false;
		}
	}
	
	/**
	 * @param plainframe\Domain\Object $obj
	 * @return boolean
	 */
	protected function doDelete(\plainframe\Domain\Object $obj) {
		
		$qry = 'DELETE FROM ' . $this->table . ' WHERE ' . $this->idFieldName . '=?';
		$params = array($obj->id);
				
		$this->dbh->beginTransaction();
		$stmt = $this->dbh->prepare($qry);
		$exec = $stmt->execute($params);
		if($this->dbh->commit() && $exec) {
			return $exec;
		}
		else {
			return false;
		}
	}
			
	/**
	 * @param plainframe\Domain\Object $object
	 * @return plainframe\Domain\Object|boolean 
	 */
	public function save(\plainframe\Domain\Object $object) {
		if(is_a($object, $this->mappedClass)) {				
			if(!empty($object->id)) {
				return $this->doUpdate($object);
			}
			else{
				return $this->doInsert($object);
			}
		}
		else {
			throw new \Exception('wrong object type. Expected: ' . $this->mappedClass . ', actual : ' . get_class($object));
		}
	}

	/**
	 * @param plainframe\Domain\Object $object
	 * @throws Exception
	 * @return boolean
	 */
	public function delete(\plainframe\Domain\Object $object)	{
		$id = $object->id;
		if(!empty($id)) {
			return $this->doDelete($object);
		}
		else{
			throw new \Exception ('Object marked for deletion has no ID!');
		}
	}
	
	/**
	 * 
	 * @param array $filters
	 * @param array $sortlevels
	 * @param array $range
	 * 
	 * @return plainframe\Data\SqlSelectBuilder
	 */
	protected function getQueryBuilder(array $filters, array $sortlevels, array $range) {
		$builder = new SQLSelectBuilder();
		$builder->baseQuery = $this->baseQuery;
		
		$builder->columns = $this->columns;
		$builder->filters = $filters;
		$builder->sortlevels = $sortlevels;
		$builder->range = $range;
		return $builder;
	}
		
	/**
	 * @param int|string $id
	 * @return plainframe\Domain\Object
	 */
	public function findById($id) {
		$filters = array(array('field' => $this-> idFieldName, 'operator' => 'equals', 'value' => $id));
		$sortlevels = array(array('orderby' => $this->idFieldName, 'sort' => 'DESC'));
		$range = array('start' => 0, 'end' => 1);
		$querybuilder = $this->getQueryBuilder($filters, $sortlevels, $range);
		$querybuilder->baseQuery = $this->baseQuery;
		
		$qry = $querybuilder->getQuery($this->dbtype);
		$params = $querybuilder->getParams();
		
		$stmt = $this->dbh->prepare($qry);
		$stmt->execute($params);
		$results = $stmt->fetch(\PDO::FETCH_ASSOC);

		if(!empty($results)) {
			$obj = new $this->mappedClass($id);
			foreach($this->columns as $column) {
				if(property_exists($obj, $column)) {
					$obj->{$column} = $results[$column];
				}
			}
			return $obj;
		}
		return false;
	}
	
	/**
	 * 
	 * @param array $filters
	 * @param array $sortlevels
	 * @param array $range
	 * @return \Domain\Collection
	 */
	public function getCollection(array $filters, array $sortlevels, array $range) {
		$this->setColumns();
		
		$querybuilder = $this->getQueryBuilder($filters, $sortlevels, $range);
		$qry = $querybuilder->getQuery($this->dbtype);
		
		$params = $querybuilder->getParams();
		
		$stmt = $this->dbh->prepare($qry);
		$stmt->execute($params);
		$collection = array();
		
		while($results = $stmt->fetch(\PDO::FETCH_ASSOC)) {
			$obj = new $this->mappedClass($results[$this->idFieldName]);
			foreach($this->columns as $column) {
				if(property_exists($obj, $column)) {
					$obj->{$column} = $results[$column];
				}
			}
			$collection[] = $obj;
		}
		
		return $collection;
		
	}
		
	/**
	 * @param array $filters
	 * @return integer represents the number of rows returned from the base query.
	 */
	public function getCollectionCount(array $filters) {
				
		$querybuilder = $this->getQueryBuilder($filters, array(), array());
				
		$qry = $querybuilder->getCountQuery();
		$params = $querybuilder->getParams();
		
		$stmt = $this->dbh->prepare($qry);
		$stmt->execute($params);
		$row = $stmt->fetch();
		$count = $row['count'];
		if(empty($count)) {
			return 0;
		}
		return $count;
	}
	
}
?>