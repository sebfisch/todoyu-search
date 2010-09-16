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
 * Generic search.
 * Calls all registered search engines
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearch {

	/**
	 * Get suggestions for the search
	 *
	 * @param	String	$query
	 * @param	String	$mode
	 * @param	Boolean	$limit
	 * @return	Array
	 */
	public static function getSuggestions($query, $mode = 'all', $limit = false) {
		$query	= trim($query);
		$find	= $ignore	= $results= array();
		$configLimit	= $mode == 'all' ? Todoyu::$CONFIG['EXT']['search']['suggestLimitAll'] : Todoyu::$CONFIG['EXT']['search']['suggestLimit'];

		$limit	= $limit === false ? $configLimit : intval($limit) ;

			// Empty search? abort
		if( $query === '' ) {
			return array();
		}

			// Prepare search: split into words, trim, add to array of terms to be found
		$words	= TodoyuArray::trimExplode(' ', $query, true);
		foreach($words as $word) {
			if( substr($word, 0, 1) === '-' ) {
				$ignore[] = substr($word, 1);
			} else {
				$find[] = $word;
			}
		}

			// Fetch suggestions of registered search engines
		$engines = TodoyuSearchManager::getEngines();
		foreach($engines as $engineConfig) {
			if( $mode === 'all' || $mode === $engineConfig['type'] ) {
				if( TodoyuFunction::isFunctionReference($engineConfig['suggestion']) ) {
					$results[$engineConfig['type']]	= array(
						'results'	=> TodoyuFunction::callUserFunction($engineConfig['suggestion'], $find, $ignore, $limit),
						'label'		=> TodoyuLanguage::getLabel($engineConfig['labelSuggest'])
					);
				}
			}
		}

		return $results;
	}



	/**
	 * Get results
	 *
	 * @param	String	$query
	 * @todo 	Check / remove?
	 */
	public static function getResults($query) {

	}



	/**
	 * Get search modes
	 *
	 * @return	Array
	 */
	public static function getSearchModes() {
		$modes	= TodoyuArray::sortByLabel(Todoyu::$CONFIG['EXT']['search']['modes'], 'position');

		return $modes;
	}



	/**
	 * Search table
	 *
	 * @param	String	$table
	 * @param	Array	$fields
	 * @param	Array 	$find
	 * @param	Array	$ignore
	 * @param	Integer	$limit
	 * @return	String
	 */
	public static function searchTable($table, array $fields, array $find, array $ignore = array(), $limit = 200, $addToWhere = ' AND deleted = 0') {
		$field	= 'id';
		$where	= Todoyu::db()->buildLikeQuery($find, $fields);
		$where	.= $addToWhere;
		$limit	= intval($limit);

		if( sizeof($ignore) > 0 ) {
			$where .= ' AND NOT (' . Todoyu::db()->buildLikeQuery($ignore, $fields) . ')';
		}

		return Todoyu::db()->getColumn($field, $table, $where, '', '', $limit);
	}


}

?>