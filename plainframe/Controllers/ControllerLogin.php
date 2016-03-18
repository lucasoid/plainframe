<?php
namespace plainframe\Controllers;

use plainframe\Views\PageMain;
use plainframe\Auth\Authenticate;
use plainframe\Auth\LoggedInUser;
use plainframe\Data\SqlDB;

class ControllerLogin extends Controller {
	
	public function index() {
		
		$content = '';
		
		if(!empty($_POST['userid']) && !empty($_POST['password'])) {
			$db = new SqlDB();
			$conn = $db->getConnection();
			
			$qry = "CREATE TABLE IF NOT EXISTS users (userid TEXT PRIMARY KEY UNIQUE, hashed_pass TEXT)";
			$stmt = $conn->prepare($qry);
			$stmt->execute(array());
			
			if(Authenticate::authenticate($_POST['userid'], $_POST['password'], $conn)) {
				LoggedInUser::logIn($_POST['userid']);
				header('Location:/books');
			}
			else {
				$content .= '<p>Sorry, that login is invalid. Please try again.</p>';
			}
		}
		
		$page = new PageMain();
		$content .= '<form action="" method="POST">
		<p>User id: <input type="text" name="userid" /></p>
		<p>Password: <input type="password" name="password"/></p>
		<input type="submit" value="Log in" />';
		$page->content = $content;
		$page->render();
	
	}
}
?>