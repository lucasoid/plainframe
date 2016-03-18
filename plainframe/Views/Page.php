<?php
namespace plainframe\Views;
use plainframe\Config;
/**
 * Template interface to render HTML pages
 *
 */

abstract class Page {
	
	public $content = '';
	public $contentHead = '';
	public $contentFooter = '';
		
	public function render() {
		
echo '<!DOCTYPE html>' . "\n";
echo '<html>' . "\n";
echo $this->renderHead();
echo '<body>' . "\n";
echo $this->renderBeforeContent() . "\n";
echo $this->content . "\n";
echo $this->renderAfterContent() . "\n";
echo $this->renderFooter() . "\n";
echo '</body>' . "\n";
echo '</html>';

	}
	
	public function renderHead() {
		$head = '<head>' . "\n";
		foreach(Config::get('site', 'meta') as $meta) {
			$attributes = array();
			foreach($meta as $attribute=>$val) {
				$attributes[] = $attribute . '="' . $val . '"';
			}
			$head .= '<meta ' . implode(' ', $attributes) . " />\n";
		}
		$head .= '<title>' . Config::get('site', 'title') . "</title>\n";
		$head .= '<!-- Styles -->' . "\n";
		foreach(Config::get('site', 'css') as $css) {
			$head .= '<link href="' . $css . '" rel="stylesheet" type="text/css" />' . "\n";
		}
		$head .= '<!-- JS -->' . "\n";
		foreach(Config::get('site', 'js_header') as $js) {
				$head .= '<script src="' . $js . '"></script>' . "\n";
				
		}
		if(!empty($this->contentHead)) {
			$head .= $this->contentHead;
		}
		
		$head .= "\n</head>\n";
		return $head;
	}
	
	public function renderFooter() {
		$footer = '<footer>' . "\n";
		$footer .= '<!-- Footer JS -->' . "\n";
		foreach(Config::get('site', 'js_footer') as $js) {
				$footer .= '<script src="' . $js . '"></script>' . "\n";
		}
		if(!empty($this->contentFooter)) {
			$footer .= $this->contentFooter . "\n";
		}
		$footer .= '</footer>';
		return $footer;
	}
	
	public function renderBeforeContent() {
		return '<div id="content-wrap">';
	}
	
	public function renderAfterContent() {
		return '</div>';
	}
	
	
}

?>