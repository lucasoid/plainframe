<?php
namespace plainframe\Controllers;
		
use plainframe\Data\MapperFactory;
use plainframe\Domain\Collection;
use plainframe\Auth\LoggedInUser;

class ControllerDownload extends Controller {
		
	public function index() {
		if(false == LoggedInUser::loggedIn()) {
			header('403', false, 403);
			echo 'Not authorized.';
		}
		else {
			$action = !empty($_GET['action']) ? $_GET['action'] : '';
			switch($action) {
				case('export'):
					$this->export();
					break;
				default:
					header('400', false, 400);
					echo 'invalid action';
					break;
			}
		}
	}
		
	private function export() {
		$resources = array('upload', 'book');
		if(empty($_GET['resource']) || !in_array(strtolower($_GET['resource']), $resources)) { 
			return false;
		}
		$params = !empty($_GET) ? $_GET : array();
		$results = $this->getCollection($_GET['resource'], $_GET);
		$filename = $_GET['resource'] . '-' . date('Y-m-d') . '-' . time();
		$cols = $data = array();
		if(!empty($results)) {
			foreach($results[0] as $field=>$val) {
				$cols[] = $field;
			}
			foreach($results as $result) {
				$datum = array();
				foreach($result as $field=>$val) {
					$datum[] = $val;
				}
				$data[] = $datum;
			}
		}
		else {
			$cols = array('No results.');
			$data = array(array());
		}
		
		$this->downloadCSV($filename, $cols, $data);
	}
			
	private function getCollection($resource, $params) {
		$constraints = $this->getConstraints($params);
		$filters = is_array($constraints['filters']) ? $constraints['filters'] : json_decode($constraints['filters'], true);
		$sortlevels = is_array($constraints['sortlevels']) ? $constraints['sortlevels'] : json_decode($constraints['sortlevels'], true);
		$range = is_array($constraints['range']) ? $constraints['range'] : json_decode($constraints['range'], true);
		
		if($collection = new Collection($resource, $constraints['filters'], $constraints['sortlevels'], $constraints['range'])) {
			return $collection->toArray();
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
	
	private function downloadCSV($filename, $columns, $data) {
		ini_set('max_execution_time', 300);
		$filename = urlencode($filename) . '.csv';
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename='.$filename.'');
		$output = fopen('php://output', 'w');
		fputcsv($output, $columns);
		foreach($data as $datum) {
			fputcsv($output, $datum);
		}
		fclose($output);
	}
	
}
?>