<?php
namespace plainframe\Controllers;
		
use plainframe\Data\MapperFactory;
use plainframe\Domain\Collection;
use plainframe\Auth\LoggedInUser;

class ControllerApi extends Controller {
	/*
	support these types of requests:
		GET api/items with JSON constraints
		GET api/items/count with constraints
		GET api/items/17
		POST api/items with JSON definitions
		PUT api/items/17 with JSON definitions
		DELETE api/items/17
	*/
	
	public function index() {
		if(false == LoggedInUser::loggedIn()) {
			header('403', false, 403);
			echo 'Not authorized.';
		}
		else {
			$request = $_SERVER['REQUEST_METHOD'];
			
			$uri = trim($_SERVER['REQUEST_URI'],"/");
			$uri = trim($_SERVER['REQUEST_URI'],"/");
			$path = explode("?", $uri);
			$path = explode("/", $path[0]);
			array_shift($path); // remove the /api part
			$resources = array('book', 'me');
			if(!empty($path[0]) && in_array($path[0], $resources)) {
				$resource = ucwords(array_shift($path));
			}			
			else {
				header('400', false, 400);
				die('Please specify a valid resource.');
			}
				
			if(!empty($path[0])) {
				$id = array_shift($path);
			}
			else {
				$id = 'collection';
			}
						
			switch($request) {
				case('GET'):
					$params = !empty($_GET) ? $_GET : array();
					$this->get($resource, $id, $params);
					break;
				case('POST'):
					$payload = file_get_contents('php://input');
					$params = json_decode($payload, true);
					$this->post($resource, $params);
					break;
				case('PUT'):
					$payload = file_get_contents('php://input');
					$params = json_decode($payload, true);
					$this->put($resource, $id, $params);
					break;
				case('DELETE'):
					$this->delete($resource, $id);
					break;
				default:
					$this->get($resource, $id, $params);
					break;
			}
		}
	}
		
	private function get($resource, $id, $params) {
		if($resource == 'me') {
			return $this->me();
		}
		
		switch($id) {
			case('collection'):
				return $this->getCollection($resource, $params);
				break;
			case('count'):
				return $this->getCount($resource, $params);
				break;
			default:
				return $this->getItem($resource, $id);
				break;
		}
	}
	
	private function post($resource, $params) {
		$classname = "\plainframe\Domain\\" . $resource;
		$object = new $classname;
		$mapper = MapperFactory::makeMapper($resource);
		$this->save($mapper, $object, $params);
	}
	private function put($resource, $id, $params) {
		$mapper = MapperFactory::makeMapper($resource);
		$object = $mapper->findById($id);
		$this->save($mapper, $object, $params);
	}
	
	public function delete($resource, $id) {
		$mapper = MapperFactory::makeMapper($resource);
		if($object = $mapper->findById($id)) {
			$mapper->delete($object);
			echo $object->toJson();
		}
		else {
			header('400', false, 400);
			echo json_encode('Error deleting the item.');
		}
	}
	
	private function getCollection($resource, $params) {
		$constraints = $this->getConstraints($params);
		$collection = new Collection($resource, $constraints['filters'], $constraints['sortlevels'], $constraints['range']);
		if($collection->valid()) {
			echo $collection->toJson();
		}
		else {
			header('400', false, 400);
			echo json_encode('Error retrieving collection.');
		}
	}
	
	private function getCount($resource, $params) {
		$constraints = $this->getConstraints($params);
		$mapper = MapperFactory::makeMapper($resource);
		$count = $mapper->getCollectionCount($constraints['filters']);
		$count = is_numeric($count) ? array('count'=>$count) : array('count'=>0);
		echo json_encode($count, true);
	}
	
	private function getItem($resource, $id) {
		$mapper = MapperFactory::makeMapper($resource);
		if($item = $mapper->findById($id)) {
			echo $item->toJson();
		}
		else {
			header('400', false, 400);
			echo json_encode('Error retrieving item.');
		}
	}
		
	private function me() {
		echo \plainframe\Auth\LoggedInUser::getUser()->toJson();
	}
	
	private function save($mapper, $object, $params) {		
		$object->mapParameters($params);
		if($mapper->save($object)) {
			echo $object->toJson();
		}
		else {
			header('400', false, 400);
			echo json_encode('There was an error saving the record.');
		}
	}
	
	private function getConstraints($params = array()) {
		$filters = $sortlevels = $range = array();
		$sortlevels = !empty($params['sortlevels']) ? $params['sortlevels'] : array();
		$filters = !empty($params['filters']) ? $params['filters'] : array();
		$page = isset($params['page']) && is_numeric($params['page']) && $params['page'] > 0 ? $params['page'] : '1';
		$rpp = isset($params['rpp']) && is_numeric($params['rpp']) && $params['rpp'] > 0? $params['rpp'] : '100';
		$offset = ($page - 1) * $rpp;
		$end = $offset + $rpp;
		$range = array('start' => $offset, 'end' => $end);
		return array('filters'=>$filters, 'sortlevels'=>$sortlevels, 'range'=>$range);
	}
	
}
?>