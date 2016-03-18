<?php

class SqlDBTest extends PHPUnit_Framework_TestCase {
		
	public function testConstructorMySQL() {
		$host = 'localhost';
		$database = 'mydatabase';
		$username = $password = '';
		$dbtype = 'MYSQL';
		$db = new \plainframe\Data\SqlDB($host, $database, $username, $password, $dbtype, array());
		$this->assertEquals('mysql:host=localhost;dbname=mydatabase', $db->getDsn());
	}
	
	public function testConstructorSqlServer() {
		$host = 'localhost';
		$database = 'mydatabase';
		$username = $password = '';
		$dbtype = 'SQL';
		$db = new \plainframe\Data\SqlDB($host, $database, $username, $password, $dbtype, array());
		$this->assertEquals('sqlsrv:server=localhost;Database=mydatabase', $db->getDsn());
	}
	
	public function testConstructorSqlite() {
		$host = '';
		$database = 'mydatabase.sqlite';
		$username = $password = '';
		$dbtype = 'SQLITE';
		$db = new \plainframe\Data\SqlDB($host, $database, $username, $password, $dbtype, array());
		$this->assertEquals('sqlite:mydatabase.sqlite', $db->getDsn());
	}
	
	public function testGetConnection() {
		$db = new \plainframe\Data\SqlDB('', ':memory:', '', '', 'SQLITE', array());
		$conn = $db->getConnection();
		$this->assertInstanceOf('\PDO', $conn);
	}
		
	public function testGetConnectionError() {
		$db = new \plainframe\Data\SqlDB('foo', 'bar', 'uid', 'pw', 'MYSQL', array());
		$this->setExpectedException('PDOException');
		$conn = $db->getConnection();
	}
	
}
?>