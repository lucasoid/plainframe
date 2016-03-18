<?php

class AuthenticateTest extends PHPUnit_Framework_TestCase {
	
	protected $dbh;
	protected $hashedpass;
	
	protected function setUp() {
		$this->dbh = new \PDO('sqlite::memory:', null, null, array());
		$hasher = new \plainframe\Auth\PasswordHash(8, false);
		$this->hashedpass = $hasher->HashPassword('the_pass_is_hashed');
		$qry = "CREATE TABLE IF NOT EXISTS users (userid text, hashed_pass text)";
		$stmt = $this->dbh->prepare($qry);
		$stmt->execute(array());
		
		$qry = "INSERT INTO users (userid, hashed_pass) VALUES (?, ?)";
		$stmt = $this->dbh->prepare($qry);
		$stmt->execute(array('billybuzz', $this->hashedpass));
			
	}
	
	protected function tearDown() {
		$this->dbh = null;
	}
	
	public function testAuthenticate() {	
		
		$userid = 'billybuzz';
		$pw = 'the_pass_is_hashed';
		$this->assertTrue(\plainframe\Auth\Authenticate::authenticate($userid, $pw, $this->dbh));
		
		$userid = 'billybuzz';
		$pw = 'the_pass_is_bogus';
		$this->assertFalse(\plainframe\Auth\Authenticate::authenticate($userid, $pw, $this->dbh));
		
		$userid = 'bogus_name';
		$pw = 'the_pass_is_hashed';
		$this->assertFalse(\plainframe\Auth\Authenticate::authenticate($userid, $pw, $this->dbh));
		
	}
	
	public function testCreateHashedPassword() {		
		$pw = 'hash_me';
		$hashed = \plainframe\Auth\Authenticate::createHashedPassword($pw);
		$this->assertNotEmpty($hashed);
		$this->assertGreaterThan(8, strlen($hashed));
	}
}
?>