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
		$content .= '<div style="background:#cecece;padding:10px"><p><a href="/books/ajax">Click here</a> for a demo of the AJAX API</p></div>';
		$page->content = $content;
		$page->render();
	}
	
	public function ajax() {
		$page = new PageMain();
		$content = <<<EOT
<h1>Ajax demo</h1>
<h2>GET requests</h2>
<form id="ajax-demo">
<div>
<h3>Add a filter</h3>
	<select id="field">
		<option value="title">Title</option>
		<option value="author">Author</option>
	</select>
	<select id="operator">
		<option value="contains">Contains</option>
		<option value="notcontains">Does not contain</option>
		<option value="equals">Equals</option>
		<option value="notequals">Does not equal</option>
	</select>
	<input type="text" id="value" />
</div>
<div>
<h3>Change sortation</h3>
	<select id="orderby">
		<option value="title">Title</option>
		<option value="author">Author</option>
	</select>
	<select id="sort">
		<option value="asc">Ascending</option>
		<option value="desc">Descending</option>
	</select>
</div>
<div>
<h3>Number of records to display</h3>
	<input type="number" id="rpp" />
</div>
<div>
<h3>Page to display</h3>
	<input type="number" id="page" />
</div>
<input type="submit" value="See results" />
</form>
<br/>
<div id="response" style="border:1px solid #cecece; padding:10px;">
<h3>Response</h3>
<h4>Collection:</h4>
<div id="response-collection"></div>
<h4>Count:</h4>
<div id="response-count"></div>
</div>
<script>
	function isEmpty(input) {
		return undefined == input || '' == input ? true : false;
	}
	
	$(document).on('submit', '#ajax-demo', function(e) {
		e.preventDefault();
		var sortlevels = [],
			filters = [],
			rpp = 25,
			page = 1;
			
		if(!isEmpty($('#field').val()) && !isEmpty($('#operator').val()) && !isEmpty('#value')) {
			filters.push({field:$('#field').val(), operator:$('#operator').val(),value:$('#value').val()});
		}
		if(!isEmpty($('#orderby').val()) && !isEmpty($('#sort').val())) {
			sortlevels.push({orderby:$('#orderby').val(), sort:$('#sort').val()});
		}
		if(!isEmpty($('#rpp').val())) {
			rpp = $('#rpp').val();
		}
		if(!isEmpty($('#page').val())) {
			page = $('#page').val();
		}
		$.ajax({
			url:'/api/book',
			data: {sortlevels:sortlevels, filters:filters, rpp:rpp, page:page},
			method:'GET',
			success:function(data) {
				$('#response-collection').html(data).show();
			},
			error:function(xhr, status) {
				$('#response-collection').html(xhr.responseText).show();
			},
		});
		$.ajax({
			url:'/api/book/count',
			data: {filters:filters},
			method:'GET',
			success:function(data) {
				$('#response-count').html(data).show();
			},
			error:function(xhr, status) {
				$('#response-count').html(xhr.responseText).show();
			},
		});
		
		
	});
</script>

<br/>
<h2>PUT requests</h2>
<h3>Modify a field for the record with ID 1</h3>
<form id="update-record">
	<input type="text" id="title" placeholder="title" />
	<input type="text" id="author" placeholder="author" />
	<input type="submit" value="Update" />
</form>

<br/>
<div style="border:1px solid #cecece; padding:10px;">
<h3>Response</h3>
<h4>Record:</h4>
<div id="response-update"></div>
</div>
<script>
		
	$(document).on('submit', '#update-record', function(e) {
		e.preventDefault();
		var title, author;
		if(!isEmpty($('#title').val())) { title = $('#title').val(); }
		if(!isEmpty($('#author').val())) { author = $('#author').val(); }
		
		$.ajax({
			url:'/api/book/1',
			data: JSON.stringify({title:title, author:author}),
			method:'PUT',
			success:function(data) {
				$('#response-update').html(data).show();
			},
			error:function(xhr, status) {
				$('#response-update').html(xhr.responseText).show();
			},
		});
	});
</script

<br/>
<h2>POST requests</h2>
<h3>Add a new record</h3>
<form id="add-record">
	<input type="text" id="add-title" placeholder="title" />
	<input type="text" id="add-author" placeholder="author" />
	<input type="submit" value="Add" />
</form>

<br/>
<div style="border:1px solid #cecece; padding:10px;">
<h3>Response</h3>
<h4>Record:</h4>
<div id="response-add"></div>
</div>
<script>
	
	$(document).on('submit', '#add-record', function(e) {
		e.preventDefault();
		var title, author;
		if(!isEmpty($('#add-title').val())) { title = $('#add-title').val(); }
		if(!isEmpty($('#add-author').val())) { author = $('#add-author').val(); }
		
		$.ajax({
			url:'/api/book',
			data: JSON.stringify({title:title, author:author}),
			method:'POST',
			success:function(data) {
				$('#response-add').html(data).show();
			},
			error:function(xhr, status) {
				$('#response-add').html(xhr.responseText).show();
			},
		});
	});
</script>

<br/>
<h2>DELETE requests</h2>
<h3>DELETE a record</h3>
<form id="delete-record">
	Enter an ID to delete:
	<input type="number" id="id" />
	<input type="submit" value="Delete" />
</form>

<br/>
<div style="border:1px solid #cecece; padding:10px;">
<h3>Response</h3>
<h4>Record:</h4>
<div id="response-delete"></div>
</div>
<script>
	
	$(document).on('submit', '#delete-record', function(e) {
		e.preventDefault();
		var id;
		if(!isEmpty($('#id').val())) { id = $('#id').val(); }
		
		$.ajax({
			url:'/api/book/' + id,
			method:'DELETE',
			success:function(data) {
				$('#response-delete').html(data).show();
			},
			error:function(xhr, status) {
				$('#response-delete').html(xhr.responseText).show();
			},
		});
	});
</script>
EOT;
		$page->content = $content;
		$page->render();
	}
}
?>