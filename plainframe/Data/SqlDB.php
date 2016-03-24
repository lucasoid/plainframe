<?php
namespace plainframe\Data;

use PDO;
use PDOException;
use plainframe\Config;

/**
 * 
 * This class sets up a connection to a database using PDO.
 * 
 * If you don't pass any arguments to the constructor, the default database will be used.
 * You can also override the default values to connect to a different database.
 * The database type is set using the following priorities and fallbacks:</ol>
 * <li>The argument that is passed to the constructor.</li>
 * <li>The value set in the config file.</li>
 * <li>The default value of "MYSQL."</li>
 * </ol>
 * 
 */

class SqlDB {

	private $dbtype;
	private $host;
	private $dsn;
	private $user;
	private $password;
	private $handle;
	private $timezoneoffset;

	/**
	 * 
	 * @param string $host
	 * @param string $database
	 * @param string $user
	 * @param string $pw
	 * @param string $dbtype
	 * @param string $timezoneoffset
	 */
	public function __construct($host = null, $database = null, $user = null, $pw = null, $dbtype = null, $options = array()) {
		
		if(!empty($dbtype)) {
			$this->dbtype = $dbtype;
		}
		elseif(null != Config::get('db', 'driver')) {
			$this->dbtype = Config::get('db', 'driver');
		}
		else {
			$this->dbtype = "MYSQL";
		}
		
		if(!empty($host)) {
			$this->host = $host;
		}
		elseif(null != Config::get('db', 'host')) {
			$this->host = Config::get('db', 'host');
		}
		else {
			$this->host = "localhost";
		}
		
		if(!empty($user)) {
			$this->user = $user;
		}
		elseif(null != Config::get('db', 'user')) {
			$this->user = Config::get('db', 'user');
		}
		else {
			$this->user = '';
		}
		
		if(!empty($pw)) {
			$this->password = $pw;
		}
		elseif(null != Config::get('db', 'pw')) {
			$this->password = Config::get('db', 'pw');
		}
		else {
			$this->password = '';
		}
		
		if(!empty($database)) {
			$this->database = $database;
		}
		elseif(null != Config::get('db', 'database')) {
			$this->database = Config::get('db', 'database');
		}
		else {
			$this->database = '';
		}
		
		switch(strtoupper($this->dbtype)) {
			case("MYSQL"):
				$this->dsn = "mysql:host={$this->host};dbname={$this->database}";
				break;
			case("SQL"):
				$this->dsn = "sqlsrv:server={$this->host};Database={$this->database}";
				break;
			case("SQLITE"):
				$this->dsn = "sqlite:{$this->database}";
				break;
		}
		
		$this->options = array();
		
		//PDO options, e.g. PDO::MYSQL_ATTR_INIT_COMMAND => 'SET time_zone = \'-05:00\'')
		foreach($options as $key=>$value) {
			$this->options[$key] = $value; 
		}
		$config_options = Config::get('db', 'options');
		if(!empty($config_options)) {
			foreach($config_options as $key=>$value) {
				$this->options[$key] = $value;
			}
		}
	}
	
	/**
	 *
	 * @return string
	 */
	public function getDsn() {
		return $this->dsn;
	}
	
	/**
	 *
	 * @return \PDO
	 */
	public function getConnection() {
			
		if(!isset($this->handle)) {
			$this->handle = new PDO($this->dsn, $this->user, $this->password, $this->options);
		}
		return $this->handle;
	}
}

?>