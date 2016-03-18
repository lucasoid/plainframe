<?php

class LoggedInUserTest extends PHPUnit_Framework_TestCase {
	
	protected function setUp() {
		if(!isset($_SESSION)) {
			@session_start();
			$_SESSION = array();
		}
	}
	
	public function testLogIn() {
		\plainframe\Auth\LoggedInUser::logIn('billybuzz');
		$uid = $_SESSION[\plainframe\Config::get('session', 'user_key')];
		$this->assertEquals('billybuzz', $uid);
	}
	
	public function testLogOut() {
		//hmm... nothing to test here from command line, the function is too closely coupled to $_SESSION.
	}
		
	public function testLoggedIn() {
		$this->assertFalse(\plainframe\Auth\LoggedInUser::LoggedIn());
		\plainframe\Auth\LoggedInUser::logIn('baobao');
		$this->assertTrue(\plainframe\Auth\LoggedInUser::LoggedIn());
	}
	
	public function testGetLoggedInUserId() {	
		$this->assertEquals('', \plainframe\Auth\LoggedInUser::getLoggedInUserId());
		\plainframe\Auth\LoggedInUser::logIn('pete');
		$this->assertEquals('pete', \plainframe\Auth\LoggedInUser::getLoggedInUserId());
	}
	
	public function testSetCsrfToken() {
		\plainframe\Auth\LoggedInUser::setCsrfToken();
		$this->assertNotEmpty($_SESSION[\plainframe\Config::get('session', 'csrf_token')]);
		$this->assertNotEmpty($_SESSION[\plainframe\Config::get('session', 'csrf_token_expires')]);
	}
	
	public function testGetCsrfToken() {
		\plainframe\Auth\LoggedInUser::setCsrfToken();
		$token = \plainframe\Auth\LoggedInUser::getCsrfToken();
		$this->assertNotEmpty($token);
	}
}
?>