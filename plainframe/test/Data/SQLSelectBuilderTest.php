<?php

class SQLSelectBuilderTest extends PHPUnit_Framework_TestCase {
		
	private $builder;
	public function setUp() {
		$this->builder = new \plainframe\Data\SQLSelectBuilder();
		$this->builder->baseQuery = 'SELECT id, author, title, descr AS description FROM books';
		$this->builder->columns = array('id', 'author', 'title', 'description');
		$this->builder->filters = array(
			array('field' => 'author', 'operator' => 'contains', 'value' => 'Dostoevsky')
		);
		$this->builder->sortlevels = array(
			array('orderby' => 'author', 'sort' => 'asc')
		);
		$this->builder->range = array('start' => 25, 'end' => 50);
	}
	
	public function testSortlevelsAreValid() {
		$this->assertTrue($this->builder->sortLevelsAreValid());
	}
	
	public function testSortLevelsAreValidNotAColumn() {
		$this->builder->sortlevels = array(array('orderby' => '; DELETE FROM users', 'sort' => 'asc'));
		$this->setExpectedException('Exception');
		$this->assertFalse($this->builder->sortLevelsAreValid());
	}
	
	public function testSortLevelsAreValidNotAscDesc() {
		$this->builder->sortlevels = array(array('orderby' => 'id', 'sort' => 'ascendual'));
		$this->setExpectedException('Exception');
		$this->assertFalse($this->builder->sortLevelsAreValid());
	}
	
	public function testSortLevelsAreValidEmpty() {
		$this->builder->sortlevels = array(array('orderby' => 'id'));
		$this->setExpectedException('Exception');
		$this->assertFalse($this->builder->sortLevelsAreValid());
		
		$this->builder->sortlevels = array(array('sort' => 'asc'));
		$this->setExpectedException('Exception');
		$this->assertFalse($this->builder->sortLevelsAreValid());
	}
	
	public function testRangesAreValid() {
		$this->assertTrue($this->builder->rangesAreValid());
	}
	
	public function testRangesAreValidNonNumeric() {
		$this->builder->range = array('start' => 'BobbyTables', 'end' => 50);
		$this->setExpectedException('Exception');
		$this->assertFalse($this->builder->rangesAreValid());
		
		$this->builder->range = array('start' => 25, 'end' => 'BobbyTables');
		$this->setExpectedException('Exception');
		$this->assertFalse($this->builder->rangesAreValid());
	}
	
	public function testRangesAreValidNegative() {
		$this->builder->range = array('start' => 50, 'end' => 25);
		$this->setExpectedException('Exception');
		$this->assertFalse($this->builder->rangesAreValid());
	}
	
	public function testRangesAreValidEmpty() {
		$this->builder->range = array('end' => 50);
		$this->setExpectedException('Exception');
		$this->assertFalse($this->builder->rangesAreValid());
		
		$this->builder->range = array('start' => 25);
		$this->setExpectedException('Exception');
		$this->assertFalse($this->builder->rangesAreValid());
	}
	public function testProcessFiltersExact() {
		$this->builder->filters = array(array('field' => 'author', 'operator' => 'notequals', 'value'=>'Dostoevsky'));
		$this->builder->processFilters();
		$conditions = $this->builder->getConditions();
		$this->assertEquals(1, count($conditions));
		$this->assertEquals('author != ?', $conditions[0]);
		
		$params = $this->builder->getParams();
		$this->assertEquals(1, count($params));
		$this->assertEquals('Dostoevsky', $params[0]);
	}
	
	public function testProcessFiltersLike() {
		$this->builder->filters = array(array('field' => 'author', 'operator' => 'contains', 'value'=>'Dostoevsky'));
		$this->builder->processFilters();
		$conditions = $this->builder->getConditions();
		$this->assertEquals(1, count($conditions));
		$this->assertEquals('author LIKE ?', $conditions[0]);
		
		$params = $this->builder->getParams();
		$this->assertEquals(1, count($params));
		$this->assertEquals('%Dostoevsky%', $params[0]);
	}
	
	public function testProcessFiltersExplodeParams() {
		$this->builder->filters = array(array('field' => 'author', 'operator' => 'in', 'value'=>'Dostoevsky|Tolstoy'));
		$this->builder->processFilters();
		$conditions = $this->builder->getConditions();
		$this->assertEquals(1, count($conditions));
		$this->assertEquals('author IN (?, ?)', $conditions[0]);
		
		$params = $this->builder->getParams();
		$this->assertEquals(2, count($params));
		$this->assertEquals('Tolstoy', $params[1]);
	}
	
	public function testProcessFiltersExplodeFilters() {
		$this->builder->filters = array(array('field' => 'author', 'operator' => 'or', 'value'=>'Dostoevsky|Tolstoy'));
		$this->builder->processFilters();
		$conditions = $this->builder->getConditions();
		$this->assertEquals(1, count($conditions));
		$this->assertEquals('author LIKE ? OR author LIKE ?', $conditions[0]);
		
		$params = $this->builder->getParams();
		$this->assertEquals(2, count($params));
		$this->assertEquals('Tolstoy', $params[1]);
	}
	
	
	public function testProcessFiltersContains() {
		$this->builder->filters = array(array('field' => 'description', 'operator' => 'contains', 'value'=>'Alyosha Karamazov'));
		$this->builder->processFilters();
		$conditions = $this->builder->getConditions();
		$this->assertEquals(1, count($conditions));
		$this->assertEquals('description LIKE ? AND description LIKE ?', $conditions[0]);
		
		$params = $this->builder->getParams();
		$this->assertEquals(2, count($params));
		$this->assertEquals('%Karamazov%', $params[1]);
	}
	
	public function testProcessFiltersNotContains() {
		$this->builder->filters = array(array('field' => 'description', 'operator' => 'notcontains', 'value'=>'Alyosha Karamazov'));
		$this->builder->processFilters();
		$conditions = $this->builder->getConditions();
		$this->assertEquals(1, count($conditions));
		$this->assertEquals('description NOT LIKE ? AND description NOT LIKE ?', $conditions[0]);
		
		$params = $this->builder->getParams();
		$this->assertEquals(2, count($params));
		$this->assertEquals('%Karamazov%', $params[1]);
	}
	
	public function testProcessFiltersSum() {
		$this->builder->filters = array(
			array('field' => 'id', 'operator' => 'gt', 'value'=>22, 'sum' => true),
			array('field' => 'id', 'operator' => 'lt', 'value'=>99, 'sum' => false),
		);
		$this->builder->processFilters();
		$sumconditions = $this->builder->getSumConditions();
		$this->assertEquals(1, count($sumconditions));
		
		$sumparams = $this->builder->getSumParams();
		$this->assertEquals(1, count($sumparams));
		$this->assertEquals(22, $sumparams[0]);
		
		$conditions = $this->builder->getConditions();
		$this->assertEquals(1, count($conditions));
		
		$params = $this->builder->getParams();
		$this->assertEquals(1, count($params));
		$this->assertEquals(99, $params[0]);
	}
	
	public function testGetQueryMySql() {
		$qry = $this->builder->getQuery('MYSQL');
		$expected = 'SELECT * FROM (SELECT id, author, title, descr AS description FROM books) AS x WHERE author LIKE ? AND 1=1 ORDER BY author  asc LIMIT 25, 25';
		$this->assertEquals($expected, $qry);
	}
	
	public function testGetQuerySql() {
		$qry = $this->builder->getQuery('SQL');
		$expected = 'WITH results AS (SELECT ROW_NUMBER() OVER (ORDER BY author asc) AS rowid, * FROM (SELECT id, author, title, descr AS description FROM books) AS x WHERE author LIKE ? AND 1=1) SELECT * FROM results WHERE rowid between 25 AND 50';
		$this->assertEquals($expected, $qry);
	}
	
	public function testGetQueryMySqlInjectSubquery() {
		$this->builder->columns[] = 'sum';
		$this->builder->filters[] = array('field' => 'sum', 'operator' => 'gt', 'value'=>2000, 'sum' => true);
		$this->builder->baseQuery = 'SELECT id, author, title, descr AS description, (SELECT sum(qty) FROM sales WHERE id=published.bookid AND <<WHERE>>) AS sum FROM books';
		$qry = $this->builder->getQuery('MYSQL');
		$expected = 'SELECT * FROM (SELECT id, author, title, descr AS description, (SELECT sum(qty) FROM sales WHERE id=published.bookid AND author LIKE ?) AS sum FROM books) AS x WHERE author LIKE ? AND sum > ? ORDER BY author  asc LIMIT 25, 25';
		$this->assertEquals($expected, $qry);
	}
	
	public function testGetQuerySqlInjectSubquery() {
		$this->builder->columns[] = 'sum';
		$this->builder->filters[] = array('field' => 'sum', 'operator' => 'gt', 'value'=>2000, 'sum' => true);
		$this->builder->baseQuery = 'SELECT id, author, title, descr AS description, (SELECT sum(qty) FROM sales WHERE id=published.bookid AND <<WHERE>>) AS sum FROM books';
		$qry = $this->builder->getQuery('SQL');
		$expected = 'WITH results AS (SELECT ROW_NUMBER() OVER (ORDER BY author asc) AS rowid, * FROM (SELECT id, author, title, descr AS description, (SELECT sum(qty) FROM sales WHERE id=published.bookid AND author LIKE ?) AS sum FROM books) AS x WHERE author LIKE ? AND sum > ?) SELECT * FROM results WHERE rowid between 25 AND 50';
		
		$this->assertEquals($expected, $qry);
	}
	
	public function testGetCountQuery() {
		$qry = $this->builder->getCountQuery();
		$expected = 'SELECT COUNT(*) AS count FROM (SELECT id, author, title, descr AS description FROM books) AS x WHERE author LIKE ? AND 1=1';
		$this->assertEquals($expected, $qry);
	}
	
	public function testGetCountQueryInjectSubquery() {
		$this->builder->columns[] = 'sum';
		$this->builder->baseQuery = 'SELECT id, author, title, descr AS description, (SELECT sum(qty) FROM sales WHERE id=published.bookid AND <<WHERE>>) AS sum FROM books';
		$this->builder->filters[] = array('field' => 'sum', 'operator' => 'gt', 'value'=>2000, 'sum' => true);
		$qry = $this->builder->getCountQuery();
		$expected = 'SELECT COUNT(*) AS count FROM (SELECT id, author, title, descr AS description, (SELECT sum(qty) FROM sales WHERE id=published.bookid AND author LIKE ?) AS sum FROM books) AS x WHERE author LIKE ? AND sum > ?';
		$this->assertEquals($expected, $qry);
	}	
}
?>