<?php

include_once('../plainframe/Config.php');

$uri = trim($_SERVER['REQUEST_URI'],"/");
$path = explode("?", $uri);
$path = explode("/", $path[0]);
$controllername = !empty($path[0]) ? $path[0] : 'Home';
$controller = \plainframe\Controllers\ControllerFactory::makeController(ucwords($controllername));
if(false !== $controller) {
	$action = !empty($path[1]) && method_exists($controller, $path[1]) ? $path[1] : 'index';
	$controller->{$action}();
}
else {
	//$controller = \plainframe\Controllers\ControllerFactory::makeController('404');
	$controller = new \plainframe\Controllers\Controller404;
	$controller->index();
}

?>