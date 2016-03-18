<?php
namespace plainframe\Controllers;

use plainframe\Views\PageMain;
use plainframe\Auth\LoggedInUser;
use plainframe\Domain\Collection;
use plainframe\Config;

class ControllerBooks extends Controller {
	
	public function index() {
		if(false === LoggedInUser::LoggedIn()) {
			header("Location:" . Config::get('redirect', 'login'));
			die();
		}
		$page = new PageMain();
		$content = '<form action="" method="GET">
		Order by:
		<select name="orderby">
		<option value="author">Author</option>
		<option value="title">Title</option>
		</select>
		<select name="sort">
		<option value="asc">Ascending</option>
		<option value="desc">Descending</option>
		</select>
		<input type="submit" value="Update" />
		</form>';
		$sortlevels = array();
		$sort = !empty($_GET['sort']) ? $_GET['sort'] : '';
		$orderby = !empty($_GET['orderby']) ? $_GET['orderby'] : '';
		if(!empty($sort) && !empty($orderby)) {
			$sortlevels[] = array('orderby' => $orderby, 'sort' => $sort);
		}		
		$books = new Collection('Book', array(), $sortlevels, array());
		foreach($books as $book) {
			$content .= '<p>' . $book->author . ': ' . $book->title . '</p>';
		}
		
		$page->content = $content;
		$page->render();
	}
}
?>