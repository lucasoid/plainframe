<?php
namespace plainframe\Auth;
use plainframe\Config;

/**
 * Class for providing information about the currently logged in user.
 * Information is stored in $_SESSION.
 */

class LoggedInUser {
	
	/**
	 * initializes the session if it is not yet started.
	 * Uses the SESS_NAME constant that is defined in config.php
	 */
	private static function loadSession() {
		if(!isset($_SESSION)) {		
			session_Name(Config::get('session', 'name'));
			session_start();
		}
	}
	
	/**
	 * function to log a user in via userid
	 *  
	 * @return boolean;
	 */
	public static function logIn($userid) {
		self::loadSession();
		if(PHP_SAPI !== 'cli') {
			session_regenerate_id(true);
		}
		$_SESSION[Config::get('session', 'user_key')] = $userid;
	}
		
	/**
	 * function to log out a user
	 *  
	 */
	public static function logOut() {
		self::loadSession();
		$params =  session_get_cookie_params();
		setcookie(session_Name(),'',0,$params['path'],$params['domain'],$params['secure'], isset($params['httponly']));
		session_destroy();
	}
	
	/**
	 * function to determine whether a user is logged in
	 *  
	 * @return boolean;
	 */
	public static function LoggedIn() {
		self::loadSession();
		if(!isset($_SESSION[Config::get('session', 'user_key')])) {
			return false;
		}
		return true;
	}
	
	/**
	 * function to get the logged-in user's id, stored in $_SESSION
	 * uses the SESS_USER_KEY constant that is defined in config.php
	 * 
	 * @return string;
	 */
	public static function getLoggedInUserId() {
		self::loadSession();
		if(isset($_SESSION[Config::get('session', 'user_key')])) {
			return $_SESSION[Config::get('session', 'user_key')];
		}
		return '';
	}
		
	/**
	 * function to create a CSRF token and store it in $_SESSION, with an expiration of 5 minutes.
	 *  
	 */
	public static function setCsrfToken() {
		if(!isset($_SESSION)) {
			session_Name(Config::get('session','name'));
			session_start();
			session_regenerate_id(true);
		}
		
		if(empty($_SESSION[Config::get('session', 'csrf_token')])) {
			$set = true;
		}
		elseif(!empty($_SESSION[Config::get('session', 'csrf_token_expires')]) && $_SESSION[Config::get('session', 'csrf_token_expires')] < time()) {
			$set = true;
		}
		else {
			$set = false;
		}
		
		if($set) {
			$_SESSION[Config::get('session', 'csrf_token_expires')] = time() + 300;
			$_SESSION[Config::get('session', 'csrf_token')] = sha1(uniqid(mt_rand(), true));
		}
	}
	
	/**
	 * function to retrieve a CSRF token from $_SESSION.
	 *  
	 * @return string;
	 */
	public static function getCsrfToken() {
		self::loadSession();		
		return $_SESSION[Config::get('session', 'csrf_token')];
	}
}

?>