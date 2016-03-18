<?php
namespace plainframe\Views;
use plainframe\Config;
/**
 * Template interface to render HTML pages
 *
 */

class PageMain extends Page {
	
	public $contentHead = '<script>console.log("additional scripts can be injected here.")</script>';
	public $contentFooter = '<script>console.log("additional footer scripts can be injected here.")</script>';
	public $content = 'Hello World!';
	
	public function renderBeforeContent() {
		$html = '<header>
		<h1>' . Config::get('site', 'title') . '</h1>
		<nav>
			<a href="/home"><i class="fa fa-home"></i></a>
			<a href="/books"><i class="fa fa-list"></i></a>
			<a href="/login"><i class="fa fa-sign-in"></i></a>
			<a href="/logout"><i class="fa fa-sign-out"></i></a>
		</nav>
		</header>
		
		<div id="content-wrap">';
		return $html;
	}
	public function renderAfterContent() {
		$html = '</div><footer></footer>';
		return $html;
	}
	
	
}

?>