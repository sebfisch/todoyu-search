<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2011, snowflake productions GmbH, Switzerland
* All rights reserved.
*
* This script is part of the todoyu project.
* The todoyu project is free software; you can redistribute it and/or modify
* it under the terms of the BSD License.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
* for more details.
*
* This copyright notice MUST APPEAR in all copies of the script.
*****************************************************************************/

/**
 * Filter base. Implements the basic filter logic and fetches
 *
 * @package		Todoyu
 * @subpackage	Filter
 */

abstract class TodoyuSearchFilterBase {

	/**
	 * All active filters
	 *
	 * @var	Array
	 */
	protected $activeFilters;

	/**
	 * Filter type. Function references are stored in the config array under the specific type
	 *
	 * @var	String
	 */
	protected $type;

	/**
	 * The table the filter gets the IDs from. This table needs to be in the request, so it's added by default
	 *
	 * @var	String
	 */
	protected $defaultTable;

	/**
	 * Extra tables to be used by the filter
	 *
	 * @var	Array
	 */
	protected $extraTables = array();

	/**
	 * Extra where clauses for the filter
	 *
	 * @var	Array
	 */
	protected $extraWhere	= array();

	/**
	 * @var	Array
	 */
	protected $rightsFilters	= array();

	/**
	 * Logical conjunction
	 *
	 * @var String
	 */
	protected $conjunction = 'AND';




	/**
	 * Initialize filter object
	 *
	 * @param	String		$type				Type of the filter (funcRefs are stored in the config unter this type)
	 * @param	String		$defaultTable		Table to get the IDs from
	 * @param	Array		$activeFilters		Active filters of the current request
	 */
	protected function __construct($type, $defaultTable, array $activeFilters = array(), $conjunction = 'AND') {
		$this->type					= strtoupper($type);
		$this->defaultTable			= $defaultTable;
		$this->activeFilters		= $activeFilters;
		$this->conjunction			= $conjunction;

		TodoyuExtensions::loadAllFilters();
	}



	/**
	 * Get conjunction of the filterset
	 *
	 * @return	String
	 */
	public function getConjunction() {
		return $this->conjunction;
	}



	/**
	 * Add an extra table for the request query
	 *
	 * @param	String		$table
	 */
	public function addExtraTable($table) {
		$this->extraTables[] = $table;
	}



	/**
	 * Add an extra where clause for the request query
	 *
	 * @param	String		$where	WHERE clause
	 */
	public function addExtraWhere($where) {
		$this->extraWhere[] = $where;
	}



	/**
	 * Add an extra filter
	 *
	 * @param	String		$name		Filter name
	 * @param	String		$value
	 * @param	Boolean		$negate
	 */
	public function addFilter($name, $value, $negate = false) {
		$this->activeFilters[] = array(
			'filter'	=> $name,
			'value'		=> $value,
			'negate'	=> $negate
		);
	}



	/**
	 * Add a rights filter where is always added with AND
	 *
	 * @param	String		$name
	 * @param	Mixed		$value
	 */
	public function addRightsFilter($name, $value) {
		 $this->rightsFilters[] = array(
			 'filter'	=> $name,
			 'value'	=> $value,
			 'negate'	=> false
		 );
	}



	/**
	 * Check whether filter exists in config
	 *
	 * @param	String		$filter
	 * @return	Boolean
	 */
	protected function isFilter($filter) {
		$filterMethod = $this->getFilterMethod($filter);

		return method_exists($filterMethod[0], $filterMethod[1]);
	}



	/**
	 * Check first if its a filterWidget. then return class Todoyu and method
	 *
	 * else build it from current type and filter
	 *
	 * @param	String		$filter
	 * @return	Array		[0]=> classname [1]=> methodname
	 */
	protected function getFilterMethod($filter) {
		$method	= 'Filter_' . $filter;

			// Check if filter is a local function
		if( method_exists($this, $method) ) {
			return array($this, $method);
		} else {
			$config	= TodoyuSearchFilterManager::getFilterConfig($this->type, $filter);

			TodoyuDebug::printInFireBug($config, 'config');

			if( $config !== false ) {
				return explode('::', $config['funcRef']);
			}
		}

			// If no function reference found, log error
		Todoyu::log('Filter method "' . $filter . '" (table: ext_search_filtercondition) not found for type ' . $this->type);

		return false;
	}



	/**
	 * returns the function to render the searchresults
	 *
	 * @param	String	$type
	 * @return	String
	 */
	public static function getFilterRenderFunction($type = 'task') {
		return TodoyuSearchFilterManager::getFilterTypeResultsRenderer($type);
	}



	/**
	 * Get query parts provided by all active filters
	 *
	 * @return	Array|Boolean		Array with sub arrays named 'tables' and 'where' OR false of no query is active
	 */
	protected function fetchFilterQueryParts() {
		$queryParts	= array(
			'tables'	=> array(
				$this->defaultTable
			),
			'where'		=> array(),
			'join'		=> array()
		);

			// Add extra tables and WHERE parts
		$queryParts['tables'] 	= TodoyuArray::merge($queryParts['tables'], $this->extraTables);
		$queryParts['where'] 	= TodoyuArray::merge($queryParts['where'], $this->extraWhere);

			// Fetch all query parts from the filters
		foreach($this->activeFilters as $filter) {
			if( $this->isFilter($filter['filter']) ) {
					// Get array which references the filter function
				$funcRef	= $this->getFilterMethod($filter['filter']);

					// Filter function parameters
				$params		= array(
					$filter['value'],
					$filter['negate'] == 1
				);

					// Call filter function to get query parts for filter
				$filterQueryParts = call_user_func_array($funcRef, $params);

					// Check if return value is an array
				if( ! is_array($filterQueryParts) ) {
					continue;
				}

					#### Add queryParts from filter ####
					// Add tables
				if( is_array($filterQueryParts['tables']) ) {
					$queryParts['tables'] = TodoyuArray::merge($queryParts['tables'], $filterQueryParts['tables']);
				}
					// Add where
				if( is_string($filterQueryParts['where']) ) {
					$queryParts['where'][] = $filterQueryParts['where'];
				}
					// Add join where
				if( is_array($filterQueryParts['join']) ) {
					$queryParts['join'] = TodoyuArray::merge($queryParts['join'], $filterQueryParts['join']);
				}
			} else {
				Todoyu::log('Unknown filter: ' . $filter['filter'], TodoyuLogger::LEVEL_ERROR);
			}
		}

			// Remove double entries
		foreach($queryParts as $partName => $partValues) {
			$queryParts[$partName] = array_unique($partValues);
		}

		return $queryParts;
	}



	/**
	 * Fetch extra rights parts for query
	 * They are always added with AND
	 *
	 * @return	Array|Boolean
	 */
	protected function fetchRightsQueryParts() {
		$where		= array();
		$tables		= array();
		$join		= array();

		if( TodoyuAuth::isAdmin() || sizeof($this->rightsFilters) === 0 ) {
			return false;
		}

		foreach($this->rightsFilters as $filter) {
			$funcRef	= $this->getFilterMethod($filter['filter']);

			$params		= array(
					$filter['value'],
					false
				);

				// Call filter function to get query parts for filter
			$filterQueryParts = call_user_func_array($funcRef, $params);

			if( is_array($filterQueryParts['tables']) ) {
				$tables = TodoyuArray::merge($tables, $filterQueryParts['tables']);
			}
			if( $filterQueryParts !== false && array_key_exists('where', $filterQueryParts) ) {
				$where[] = $filterQueryParts['where'];
			}
			if( is_array($filterQueryParts['join']) ) {
				$join = TodoyuArray::merge($join, $filterQueryParts['join']);
			}
		}

		return array(
			'tables'	=> array_unique($tables),
			'where'		=> '(' . implode(' AND ', $where) . ')',
			'join'		=> array_unique($join)
		);
	}



	/**
	 * Gets the query array which is merged from all filters
	 * Array contains the strings for the following parts:
	 * fields, tables, where, group, order, limit
	 * Extra fields for internal use: whereNoJoin, join
	 *
	 * @param	String		$orderBy					Optional order by for query
	 * @param	String		$limit						Optional limit for query
	 * @param	Boolean		$showDeleted				Show deleted records
	 * @param	Boolean		$noResultOnEmptyConditions	Return false if no condition is active
	 * @return	Array|Boolean
	 */
	public function getQueryArray($orderBy = '', $limit = '', $showDeleted = false, $noResultOnEmptyConditions = false) {
			// Get normal query parts
		$queryParts	= $this->fetchFilterQueryParts();

			// If no conditions in where clause and $noResultOnEmptyConditions flag set, return flag (no sql query performed)
		if( $noResultOnEmptyConditions === true && sizeof($queryParts['where']) === 0 ) {
			return false;
		}

			// Get rights query parts
		$rightsParts= $this->fetchRightsQueryParts();

			// Combine join from filter and rights
		$join	= array_unique(TodoyuArray::merge($queryParts['join'], $rightsParts['join']));
		$tables	= array_unique(TodoyuArray::merge($queryParts['tables'], $rightsParts['tables']));


		$connection	= $this->conjunction ? $this->conjunction : 'AND';
		$queryArray	= array();

		$queryArray['fields']	= $this->defaultTable . '.id';
		$queryArray['tables']	= implode(', ', $tables);
		$queryArray['where']	= ''; // Where clause is added later
		$queryArray['group']	= $this->defaultTable . '.id';
		$queryArray['order']	= $orderBy;
		$queryArray['limit']	= $limit;


		$whereParts	= array();

			// Deleted
		if( $showDeleted === false ) {
			$whereParts[] = $this->defaultTable . '.deleted = 0';
		}

			// Rights
		if( $rightsParts !== false && !empty($rightsParts['where']) ) {
			$whereParts[] = $rightsParts['where'];
		}

			// Make a backup of the where parts which are required to be AND
		$queryArray['whereAND']	= $whereParts;

			// Filter
		if( sizeof($queryParts['where']) > 0 ) {
			$basicFilterWhere			= implode(' ' . $connection . ' ', $queryParts['where']);
			$whereParts[] 				= $basicFilterWhere;
				// Make a backup of the basic filters which are combined by the conjunction of the filterset
			$queryArray['whereBasic'] 	= $basicFilterWhere;
		}

			// Join
		if( sizeof($join) > 0 ) {
			$whereParts[] = implode(' AND ', $join);
				// Save joins for further use
			$queryArray['join'] = $join;
		}

		if( sizeof($whereParts) > 0 ) {
			$queryArray['where'] = '(' . implode(') AND (', $whereParts) . ')';
		}

		return $queryArray;
	}



	/**
	 * Get the full query array. This is just for debugging
	 *
	 * @param	String		$orderBy	Optional order by for query
	 * @param	String		$limit		Optional limit for query
	 * @param 	Boolean		$showDeleted
	 * @return	String
	 */
	public function getQuery($orderBy = '', $limit = '', $showDeleted = false) {
		$queryArray = $this->getQueryArray($orderBy, $limit, $showDeleted);

		$query	= Todoyu::db()->buildSELECTquery(
			$queryArray['fields'],
			$queryArray['tables'],
			$queryArray['where'],
			$queryArray['group'],
			$queryArray['order'],
			$queryArray['limit']
		);

		return $query;
	}



	/**
	 * Check if filter has conditions
	 *
	 * @return	Boolean
	 */
	public function hasActiveFilters() {
		return $this->getQueryArray('', '', false, true) !== false;
	}



	/**
	 * Get item IDs from default table which match to all active filters
	 *
	 * @param	String		$orderBy					Optional order by for query
	 * @param	String		$limit						Optional limit for query
	 * @param 	Boolean		$showDeleted				Show deleted records
	 * @return	Array		List of IDs of matching records
	 */
	protected function getItemIDs($orderBy = '', $limit = '', $showDeleted = false) {
		$queryArray = $this->getQueryArray($orderBy, $limit, $showDeleted, false);



			// If query was not built, return an empty array
//		if( $queryArray === false ) {
//			TodoyuDebug::printInFireBug('No filter active, no query was sent');
//			return array();
//		}

		$ids = Todoyu::db()->getColumn(
			$queryArray['fields'],
			$queryArray['tables'],
			$queryArray['where'],
			$queryArray['group'],
			$queryArray['order'],
			$queryArray['limit'],
			'id'
		);

		return $ids;
	}
}

?>