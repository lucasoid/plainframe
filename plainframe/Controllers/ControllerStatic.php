<?php
namespace plainframe\Controllers;

use plainframe\Config;
use plainframe\Data\MapperFactory;

class ControllerStatic extends Controller {
		
	public function index() {
		$uri = trim($_SERVER['REQUEST_URI'],"/");
		$uri = trim($_SERVER['REQUEST_URI'],"/");
		$path = explode("?", $uri);
		$path = explode("/", $path[0]);
		array_shift($path); // remove the /static part
		if(!empty($path[0])) {
			$mapper = MapperFactory::makeMapper('Upload');
			$upload = $mapper->findById($path[0]);
			$filepath = Config::get('uploads', 'dir') . DIRECTORY_SEPARATOR . $upload->filename;
			header('Content-Type: ' . $upload->mimetype); //this content-type was filtered on upload
			$filename = urlencode($upload->title);
			header('Content-Disposition: attachment; filename='.$filename);
			readfile($filepath);
		}
	}
}
?>
