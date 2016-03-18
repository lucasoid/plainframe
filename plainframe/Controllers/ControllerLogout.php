<?php
namespace plainframe\Controllers;

use plainframe\Auth\LoggedInUser;
use plainframe\Config;

class ControllerLogout extends Controller {
	
	public function index() {
		LoggedInUser::logout();
		Header("Location:" . Config::get('redirect', 'login'));
	}
}
?>