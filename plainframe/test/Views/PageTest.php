<?php

class PageTest extends PHPUnit_Framework_TestCase {
	
	public function setUp() {
		include_once('PageMock.php');
	}
	
	public function testRenderHeadIsValidHTML() {
		$page = new \plainframe\Views\PageMock();
		$doc = new \DomDocument();
		$head = $page->renderHead();
		$this->assertTrue($doc->loadXML($head));
	}
	
	public function testRenderHeadIncludesCustomContent() {
		$custom = '<script>MyCustomScriptConfigs();</script>';
		$page = new \plainframe\Views\PageMock();
		$page->contentHead = $custom;
		$head = $page->renderHead();
		$this->assertNotEquals(false, strpos($head, $custom));
	}
	
	public function testRenderFooterIsValidHTML() {
		$page = new \plainframe\Views\PageMock();
		$doc = new \DomDocument();
		$footer = $page->renderFooter();
		$this->assertTrue($doc->loadXML($footer));
	}
	
	public function testRenderFooterIncludesCustomContent() {
		$custom = '<script>MyCustomFooterScript();</script>';
		$page = new \plainframe\Views\PageMock();
		$page->contentFooter = $custom;
		$footer = $page->renderFooter();
		$this->assertNotEquals(false, strpos($footer, $custom));
	}
	
	public function testContentWrapperIsValidHtml() {
		$page = new \plainframe\Views\PageMock();
		$html = $page->renderBeforeContent();
		$html .= 'Some content.';
		$html .= $page->renderAfterContent();
		$doc = new \DomDocument();
		$this->assertTrue($doc->loadXML($html));
	}
	
	public function testRenderIsValidHtml() {
		$page = new \plainframe\Views\PageMock();
		$html = '';
		$this->setOutputCallback(array($this, 'outputCallback'));
		$page->render();
	}
	
	public function outputCallback($output) {
		$doc = new \DomDocument();
		$this->assertTrue($doc->loadXML($output));
	}
	
}
?>