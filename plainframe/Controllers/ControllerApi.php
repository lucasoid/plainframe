<?php
namespace plainframe\Controllers;
		
use plainframe\Data\MapperFactory;
use plainframe\Domain\Collection;
use plainframe\Auth\LoggedInUser;

class ControllerApi extends Controller {
		
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
			$resources = array('book', 'upload', 'me', 'preset');
			if(!empty($path[0]) && in_array(strtolower($path[0]), $resources)) {
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
		$filters = is_array($constraints['filters']) ? $constraints['filters'] : json_decode($constraints['filters'], true);
		$sortlevels = is_array($constraints['sortlevels']) ? $constraints['sortlevels'] : json_decode($constraints['sortlevels'], true);
		$range = is_array($constraints['range']) ? $constraints['range'] : json_decode($constraints['range'], true);
		
		if($collection = new Collection($resource, $constraints['filters'], $constraints['sortlevels'], $constraints['range'])) {
			echo $collection->toJson();
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
		
	public function me() {
		$id = LoggedInUser::getLoggedInUserId();
		$mapper = MapperFactory::makeMapper('User');
		if($user = $mapper->findById($id)) {
			echo $user->toJson();
		}
		else {
			header('400', false, 400);
			echo json_encode('Error retrieving item.');
		}
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
		$filters = is_array($filters) ? $filters : json_decode($filters, true);
		$sortlevels = is_array($sortlevels) ? $sortlevels : json_decode($sortlevels, true);
				
		$page = isset($params['page']) && is_numeric($params['page']) && $params['page'] > 0 ? $params['page'] : '1';
		$rpp = isset($params['rpp']) && is_numeric($params['rpp']) && $params['rpp'] > 0? $params['rpp'] : '100';
		$offset = ($page - 1) * $rpp;
		$end = $offset + $rpp;
		$range = array('start' => $offset, 'end' => $end);
		
		return array('filters'=>$filters, 'sortlevels'=>$sortlevels, 'range'=>$range);
	}
	
}
?>