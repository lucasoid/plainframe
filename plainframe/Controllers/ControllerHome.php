<?php
namespace plainframe\Controllers;
use plainframe\Views\PageMain;
use plainframe\Data\SqlDB;
use plainframe\Auth\Authenticate;

class ControllerHome extends Controller {
	
	public function index() {
		$page = new PageMain();
		$page->content = <<<EOT
		<p>Welcome!</p>
		<p>Get started:</p>
		<ul>
			<li><a href="/home/setup">set up a test database and user</a></li>
			<li><a href="/login">log in (run the setup first)</a></li>
			<li><a href="/books">see some sample data (login required)</a></li>
		</ul>
EOT;
		$page->render();
	}
	public function setup() {
		$page = new PageMain();
		
		$db = new SqlDB();
		$conn = $db->getConnection();
		
		//INIT USERS TABLE
		$qry = "CREATE TABLE IF NOT EXISTS users (userid TEXT PRIMARY KEY UNIQUE, hashed_pass TEXT)";
		$stmt = $conn->prepare($qry);
		$stmt->execute(array());
		$hashed_pass = Authenticate::createHashedPassword('welcome');
		$qry = "INSERT INTO users (userid, hashed_pass) VALUES (?,?)";
		$stmt = $conn->prepare($qry);
		$stmt->execute(array('me', $hashed_pass));
		
		//INIT BOOKS TABLE
		$qry = "CREATE TABLE IF NOT EXISTS books (id INTEGER PRIMARY KEY, title TEXT UNIQUE, author TEXT)";
		$stmt = $conn->prepare($qry);
		$stmt->execute(array());
		$books = array(
			array('title' => 'Notes From Underground', 'author' => 'Dostoevsky, Fyodor'),
			array('title' => 'The Sound & The Fury', 'author' => 'Faulkner, William'),
			array('title' => 'A Tramp Abroad', 'author' => 'Twain, Mark'),
			array('title' => 'A Tale of Two Cities', 'author' => 'Dickens, Charles'),
		);
		foreach($books as $book) {
			$qry = "INSERT INTO books (title, author) VALUES (?, ?)";
			$stmt = $conn->prepare($qry);
			$stmt->execute(array($book['title'], $book['author']));
		}
		
		$page->content = 'Database set up and user created.<br/>
		userid: me<br/>
		password: welcome<br/>
		';
		
		$page->render();
	}
}

?>