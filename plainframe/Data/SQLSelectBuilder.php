<?php
namespace plainframe\Data;

class SQLSelectBuilder {
	
	public $baseQuery = '';
	public $filters = array();
	public $sortlevels = array();
	public $range = array();
	public $columns = array();
	private $conditions = array();
	private $params = array();
	private $sumconditions = array();
	private $sumparams = array();
	private $valid_operators = array(
				'contains' => array('condition'=>'%s LIKE %s', 'before_param'=>'%', 'after_param'=>'%', 'interpolate'=>array('field', 'binding'), 'explode_filter' => true, 'param_delimiter' => ' ', 'filter_glue' => ' AND '),
				'notcontains' => array('condition'=>'%s NOT LIKE %s', 'before_param'=>'%', 'after_param'=>'%', 'interpolate'=>array('field', 'binding'), 'explode_filter' => true, 'param_delimiter' => ' ', 'filter_glue' => ' AND '),
				'begins' => array('condition'=>'%s LIKE %s', 'after_param'=>'%', 'interpolate'=>array('field', 'binding')),
				'notbegins' => array('condition'=>'%s NOT LIKE %s', 'after_param'=>'%', 'interpolate'=>array('field', 'binding')),
				'gt' => array('condition'=>'%s > %s', 'interpolate'=>array('field', 'binding')),
				'gteq' => array('condition'=>'%s >= %s', 'interpolate'=>array('field', 'binding')),
				'lt' => array('condition'=>'%s < %s', 'interpolate'=>array('field', 'binding')),
				'lteq'=> array('condition' => '%s <= %s', 'interpolate'=>array('field', 'binding')),
				'equals' => array('condition' => '%s = %s', 'interpolate'=>array('field', 'binding')),
				'notequals' => array('condition' => '%s != %s', 'interpolate'=>array('field', 'binding')),
				'null' => array('condition' => '%s IS NULL', 'interpolate'=>array('field'), 'ignoreparam' => true),
				'notnull' => array('condition' => '%s IS NOT NULL', 'interpolate'=>array('field'),'ignoreparam' => true),
				'empty' => array('condition' => '%s IS NULL OR %s = \'\'', 'interpolate'=>array('field', 'field'), 'ignoreparam' => true),
				'notempty'=> array('condition' => '%s IS NOT NULL AND %s != \'\'', 'interpolate'=>array('field', 'field'), 'ignoreparam' => true),
				'in' => array('condition' => '%s IN (%s)', 'interpolate'=>array('field', 'binding'), 'explode_param' => true, 'param_delimiter'=>'|', 'param_glue' => ', '),
				'or' => array('condition' => '%s LIKE %s', 'interpolate'=>array('field', 'binding'), 'explode_filter' => true, 'param_delimiter' => '|', 'filter_glue' => ' OR '),
			);
	
	/**
	 * @return array
	 */
	public function getParams() {
		return $this->params;
	}
	
	/**
	 * @return array
	 */
	public function getSumParams() {
		return $this->sumparams;
	}
	
	/**
	 * @return array
	 */
	public function getConditions() {
		return $this->conditions;
	}
	
	/**
	 * @return array
	 */
	public function getSumConditions() {
		return $this->sumconditions;
	}	
	
	/**
	 * @param string $driver
	 * @return string
	 */
	public function getQuery($dbtype = '') {
		$this->processFilters();
				
		switch($dbtype) {
			case('MYSQL'):
				return $this->buildQueryMysql($dbtype);
				break;
			case('SQLITE'):
				return $this->buildQueryMysql($dbtype);
				break;
			case('SQL'):
				return $this->buildQuerySql($dbtype);
				break;
			default:
				return $this->buildQueryMysql($dbtype);
				break;
		}
	}
	
	/**
	 * @return string
	 */
	public function getCountQuery() {
		$this->processFilters();
		
		$conditions = !empty($this->conditions) ? implode(' AND ', $this->conditions) : '1=1';
		$sumconditions = !empty($this->sumconditions) ? implode(' AND ', $this->sumconditions) : '1=1';
		
		//To inject WHERE clauses into subqueries, such as aggregate functions:
		if(false !== strpos($this->baseQuery, "<<WHERE>>")) {
			$this->baseQuery = str_replace("<<WHERE>>", $conditions, $this->baseQuery);
			foreach($this->params as $value) {
				$this->params[] = $value;
			}
		}
		$where = ' WHERE ' . $conditions . ' AND ' . $sumconditions;
		
		foreach($this->sumparams as $value) {
			$this->params[] = $value;
		}	
		
		$qry = 'SELECT COUNT(*) AS count FROM (' . $this->baseQuery . ') AS x' . $where;
		return $qry;
	}
	
		
	private function buildQueryMysql($dbtype) {
		
		$conditions = !empty($this->conditions) ? implode(' AND ', $this->conditions) : '1=1';
		$sumconditions = !empty($this->sumconditions) ? implode(' AND ', $this->sumconditions) : '1=1';
		
		//To inject WHERE clauses into subqueries, such as aggregate functions:
		if(false !== strpos($this->baseQuery, "<<WHERE>>")) {
			$this->baseQuery = str_replace("<<WHERE>>", $conditions, $this->baseQuery);
			foreach($this->params as $value) {
				$this->params[] = $value;
			}
		}
		$where = ' WHERE ' . $conditions . ' AND ' . $sumconditions;
		
		foreach($this->sumparams as $value) {
			$this->params[] = $value;
		}	
				
		$_sortlevels = array();
		$collation = $dbtype == 'SQLITE' ? 'COLLATE NOCASE' : '';
		if(!empty($this->sortlevels) && $this->sortLevelsAreValid()) {
			foreach($this->sortlevels as $level) {
				$_sortlevels[] = $level['orderby'] . ' ' . $collation . ' ' . $level['sort'];
			}
		}
		$orderby = !empty($_sortlevels) ? ' ORDER BY ' . implode(', ', $_sortlevels) : '';
		
		$limit = '';
		if(!empty($this->range) && $this->rangesAreValid()) {
			$num = intval($this->range['end']) - intval($this->range['start']);
			$limit = ' LIMIT ' . $this->range['start'] . ', ' . $num;
		}
		$qry = 'SELECT * FROM (' . $this->baseQuery . ') AS x' . $where . $orderby . $limit;
		return $qry;
	}
	
	private function buildQuerySql($dbtype) {
		
		$conditions = !empty($this->conditions) ? implode(' AND ', $this->conditions) : '1=1';
		$sumconditions = !empty($this->sumconditions) ? implode(' AND ', $this->sumconditions) : '1=1';
		
		//To inject WHERE clauses into subqueries, such as aggregate functions:
		if(false !== strpos($this->baseQuery, "<<WHERE>>")) {
			$this->baseQuery = str_replace("<<WHERE>>", $conditions, $this->baseQuery);
			foreach($this->params as $value) {
				$this->params[] = $value;
			}
		}
		$where = ' WHERE ' . $conditions . ' AND ' . $sumconditions;
		
		foreach($this->sumparams as $value) {
			$this->params[] = $value;
		}
		
		$orderby = '';
		if(!empty($this->sortlevels) && $this->sortLevelsAreValid()) {
			$orderarray = array();
			foreach($this->sortlevels as $level) {
				$orderarray[] = $level['orderby'] . ' ' . $level['sort'];
			}
			$orderby = 'ORDER BY ' . implode(", ", $orderarray);
		}
		else {
			$orderby = "(SELECT 0)";
		}
		$range = '';
		if(!empty($this->range) && $this->rangesAreValid()) {
			$range = ' WHERE rowid between ' . $this->range['start'] . ' AND ' . $this->range['end'];
		}
		
		$qry = 'WITH results AS (SELECT ROW_NUMBER() OVER (' . $orderby . ') AS rowid, * FROM (' . $this->baseQuery . ') AS x' . $where . ') SELECT * FROM results' . $range;
		return $qry;
	}		
	
	public function sortLevelsAreValid() {
		foreach($this->sortlevels as $level) {
			if(!isset($level['orderby']) || !isset($level['sort'])) {
				throw new \Exception('Missing sort parameters');
				return false;
			}
			if(!in_array($level['orderby'], $this->columns)) {
				throw new \Exception('Invalid orderby value');
				return false;
			}
			if(strtolower($level['sort']) !== 'asc' && strtolower($level['sort']) !== 'desc') {
				throw new \Exception('Invalid sort value');
				return false;
			}
		}
		return true;
	}
	
	public function rangesAreValid() {
		
		if(!isset($this->range['start']) || !isset($this->range['end'])) {
			throw new \Exception("invalid range: missing start or end value");
			return false;
		}
		
		$start = $this->range['start'];
		$end = $this->range['end'];
		if($start>$end) {
			throw new \Exception("invalid range: first value must not be greater than second value");
			return false;
		}
		if(!is_numeric($start) || !is_numeric($end)) {
			throw new \Exception("arguments must be numeric.");
			return false;
		}
		return true;
	}
		
	public function processFilters() {
		
		foreach($this->filters as $filter) {
			$_params = array();
			if(!empty($filter['field']) && !empty($filter['operator'])) {
				if(!in_array($filter['field'], $this->columns)) {
					throw new \Exception($filter['field'] . ' is an invalid field name');
				}
				$op = !empty($this->valid_operators[$filter['operator']]) ? $this->valid_operators[$filter['operator']] : array();			
				if(array_key_exists('explode_filter', $op) && true == $op['explode_filter'] && !empty($op['filter_glue'])) {
					$conditions = array();
					$values = explode($op['param_delimiter'], $filter['value']);
					foreach($values as $value) {
						if(array_key_exists('interpolate', $op)) {
							$values = array('field' => $filter['field'], 'binding' => '?');
							$conditions[] = $this->constructCondition($op['condition'], $op['interpolate'], $values);
						}
						if(!array_key_exists('ignoreparam', $op) || $op['ignoreparam'] != true) {
							$_params[] = (!empty($op['before_param']) ? $op['before_param'] : '') . $value . (!empty($op['after_param']) ? $op['after_param'] : '');
						}
					}
					$_filt = implode($op['filter_glue'], $conditions);
				}
				elseif(array_key_exists('explode_param', $op) && true == $op['explode_param'] && !empty($op['param_glue'])) {
					$values = explode($op['param_delimiter'], $filter['value']);
					$bindings = array();
					foreach($values as $value) {
						$bindings[] = '?';
						if(!array_key_exists('ignoreparam', $op) || $op['ignoreparam'] != true) {
							$_params[] = (!empty($op['before_param']) ? $op['before_param'] : '') . $value . (!empty($op['after_param']) ? $op['after_param'] : '');
						}
					}
					
					if(array_key_exists('interpolate', $op)) {
						$values = array('field' => $filter['field'], 'binding' => implode($op['param_glue'], $bindings));
						$_filt = $this->constructCondition($op['condition'], $op['interpolate'], $values);
					}
				}
				else {
					if(array_key_exists('interpolate', $op)) {
						$_filt = $this->constructCondition($op['condition'], $op['interpolate'], array('field' => $filter['field'], 'binding' => '?'));
					}
					if(!array_key_exists('ignoreparam', $op) || $op['ignoreparam'] != true) {
						$_params[] = (!empty($op['before_param']) ? $op['before_param'] : '') . (!empty($filter['value']) ? $filter['value'] : '') . (!empty($op['after_param']) ? $op['after_param'] : '');
					}
				}
			}
			
			if(array_key_exists('sum', $filter) && true == $filter['sum']) {
				$this->sumconditions[] = $_filt;
				$this->sumparams = array_merge($this->sumparams, $_params);
			}
			else {
				$this->conditions[] = $_filt;
				$this->params = array_merge($this->params, $_params);
			}
		}
	}
	
	private function constructCondition($string, $keys, $values) {
		$interpolate = array();
		foreach($keys as $key) {
			if(array_key_exists($key, $values)) {
				$interpolate[] = $values[$key];
			}
		}
		return vsprintf($string, $interpolate);
	}
	
}
?>