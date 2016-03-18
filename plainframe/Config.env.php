<?php
namespace plainframe;

class Config {

	private static $settings = array(
	    
		/**
		 * Define database connection values, passed to the PDO constructor.
		 * available drivers: 'MYSQL', 'SQL', or 'SQLITE'
		 * options setting: see the documentation for PDO's constructor: http://php.net/manual/en/pdo.construct.php
		 */
		'db' => array(
			'driver' => 'SQLITE',
			'host' => '',
			'database' => 'my_db',
			'user' => '',
			'password' => '',
			'options' => array()
		),
		/**
		 * Define session values. name and user_key are used in authentication; other custom fields can be defined.
		 * 
		 */
		'session' => array(
			'name' => 'my-app',
			'user_key' => 'userid',
			'csrf_token' => 'csrf_token',
			'csrf_token_expires' => 'csrf_token_expires'
		),
		/**
		 * Define formatting values.
		 * 
		 */
		'format' => array(
			'date' => 'Y-m-d',
			'date_time' => 'Y-m-d H:i:s',
			'time' => 'H:i:s'
		),
		/**
		 * Define redirect locations.
		 * 
		 */
		'redirect' => array(
			'login' => '/login',
			'home' => '/',
		),
		/**
		 * Define sitewide variables.
		 * 
		 */
		'site' => array(
			'title' => 'My Site',
			'protocol' => 'http://',
			'url' => 'myurl.org',			
			 //Each item in 'meta' is an array of attributes that belong to a single meta tag.
			'meta' => array(
				array('charset'=>'utf-8'),
				array('http-equiv'=>'X-UA-Compatible', 'content'=>'IE=edge'),
				array('name'=>'viewport', 'content'=>'width=device-width, initial-scale=1'),
			),
			//Each item in 'css' is a string representing the href of the css file. They will be included in the order given here.
			'css' => array(
				'/assets/css/style.css',
				'//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css',
				'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css',
			),
			//Each item in 'js_header' is a string representing the src of the js file. They will be included in the order given here.
			'js_header' => array(
				'https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js',
				'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js',
			),
			//Each item in 'js_header' is a string representing the src of the js file. They will be included in the order given here.
			'js_footer' => array(
				/*'/components/angular/angular.js',*/
			),
		),
	
		/**
		 * Used for PHPUnit testing.
		 * 
		 */
		'unit_test' => array(
			'foo' => 'bar',
		),
			
	
	);
	
	/**
	 * Method for accessing config values.
	 * 
	 * @param string $option The set options whose values you wish to access
	 * @param string $key The key of the 
	 * @return boolean
	 */
	public static function get($option, $key) {
		if(isset(self::$settings[$option]) && isset(self::$settings[$option][$key])) {
			return self::$settings[$option][$key];
		}
		return null;
	}
}
?>