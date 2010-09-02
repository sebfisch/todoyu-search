<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions GmbH, Switzerland
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

abstract class TodoyuFilterBase {

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
	public function addExtraFilter($name, $value, $negate = false) {
		$this->activeFilters[] = array(
			'filter'	=> $name,
			'value'		=> $value,
			'negate'	=> $negate
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
			$config	= TodoyuFilterManager::getFilterConfig($this->type, $filter);

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
	public static function getFilterRenderFunction($type = 'task')	{
		return TodoyuFilterManager::getFilterTypeResultsRenderer($type);
	}



	/**
	 * Get query parts provided by all active filters
	 *
	 * @return	Array|Boolean		Array with sub arrays named 'tables' and 'where' OR false of no query is active
	 */
	protected function fetchFilterQueryParts() {
		$runQuery	= false;
		$queryParts	= array(
			'tables'	=> array($this->defaultTable),
			'where'		=> array()
		);

			// Add extra tables and WHERE parts
		$queryParts['tables'] 	= array_merge($queryParts['tables'], $this->extraTables);
		$queryParts['where'] 	= array_merge($queryParts['where'], $this->extraWhere);

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

					// This filter is active, so query can be done
				$runQuery = true;

					// Add query parts
				foreach($filterQueryParts as $partName => $partValues) {
						// Filter can only add tables and where part, all others are ignored
					if( !array_key_exists($partName, $queryParts) ) {
						Todoyu::log('Filter ' . $funcRef['class'] . '::' . $funcRef['method'] . ' returned and unknow filter part (\'' . $partName . '\')', TodoyuLogger::LEVEL_NOTICE, $partValues);
						continue;
					}

						// Merge if $partValues is an array, else add the string
					if( is_array($partValues) ) {
						$queryParts[$partName] = array_merge($queryParts[$partName], $partValues);
					} else {
						$queryParts[$partName][] = $partValues;
					}
				}
			} else {
				Todoyu::log('Unknown filter: ' . $filter['filter'], TodoyuLogger::LEVEL_ERROR);
			}
		}

			// Return false if non of the filters is active
		if( $runQuery === false ) {
			return false;
		}


			// Remove double entries
		foreach($queryParts as $partName => $partValues) {
			$queryParts[$partName] = array_unique($partValues);
		}

		return $queryParts;
	}



	/**
	 * Gets the query array which is merged from all filters
	 * Array contains the strings for the following parts:
	 * fields, tables, where, group, order, limit
	 *
	 * @param	String		$orderBy	Optional order by for query
	 * @param	String		$limit		Optional limit for query
	 * @param	Boolean		$showDeleted
	 * @return	Array|Boolean
	 */
	public function getQueryArray($orderBy = '', $limit = '', $showDeleted = false) {
		$queryParts	= $this->fetchFilterQueryParts();

			// Don't build a query if no filters are active
		if( $queryParts === false ) {
			return false;
		}

		$connection	= $this->conjunction ? $this->conjunction : 'AND';
		$queryArray	= array();

		$queryArray['fields']	= $this->defaultTable . '.id';
		$queryArray['tables']	= implode(', ', $queryParts['tables']);
		$queryArray['where'][0]	= implode(' ' . $connection . ' ', $queryParts['where']);
		$queryArray['group']	= $this->defaultTable . '.id';
		$queryArray['order']	= $orderBy;
		$queryArray['limit']	= $limit;

		if( $queryArray['where'][0] )	{
			$queryArray['where'][0] = '('.$queryArray['where'][0].')';
		} else {
			unset($queryArray['where'][0]);
		}

		if( $showDeleted === false ) {
			$queryArray['where'][1] = $this->defaultTable . '.deleted = 0';
		}

		$queryArray['where'] = implode(' AND ', $queryArray['where']);

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
	 * Get item IDs from default table which match to all active filters
	 *
	 * @param	String		$orderBy	Optional order by for query
	 * @param	String		$limit		Optional limit for query
	 * @param 	Boolean		$showDeleted
	 * @return	Array		List of IDs of matching records
	 */
	protected function getItemIDs($orderBy = '', $limit = '', $showDeleted = false) {
		$queryArray = $this->getQueryArray($orderBy, $limit, $showDeleted);

			// If query was not built, return an empty array
		if( $queryArray === false ) {
			TodoyuDebug::printInFireBug('No filter active, no query was sent');
			return array();
		}

//		TodoyuDebug::printInFireBug($queryArray, 'queryArray');
//		TodoyuDebug::printInFireBug(Todoyu::db()->buildSELECTquery($queryArray['fields'], $queryArray['tables'], $queryArray['where'], $queryArray['group'], $queryArray['order'], $queryArray['limit'], 'id'), 'query');

//		return array();

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