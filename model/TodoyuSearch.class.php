<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 snowflake productions gmbh
*  All rights reserved
*
*  This script is part of the todoyu project.
*  The todoyu project is free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License, version 2,
*  (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html) as published by
*  the Free Software Foundation;
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

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

		$limit	= $limit === false ? $GLOBALS['CONFIG']['EXT']['search']['suggestLimit'] : intval($limit) ;

		if( $query === '' ) {
			return array();
		}

		$words	= TodoyuDiv::trimExplode(' ', $query, true);

		foreach($words as $word) {
			if( substr($word, 0, 1) === '-' ) {
				$ignore[] = substr($word, 1);
			} else {
				$find[] = $word;
			}
		}

		$engines = TodoyuSearchManager::getSearchEngines();

		foreach($engines as $engineConfig) {
			if( $mode === 'all' || $mode === $engineConfig['type'] ) {
				if( TodoyuDiv::isFunctionReference($engineConfig['suggestion']) ) {
					$results[$engineConfig['type']]	= array(
						'results'	=> TodoyuDiv::callUserFunction($engineConfig['suggestion'], $find, $ignore, $limit),
						'label'		=> TodoyuLocale::getLabel($engineConfig['labelSuggest'])
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
		$modes	= TodoyuArray::sortByLabel($GLOBALS['CONFIG']['EXT']['search']['modes'], 'position');

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
	public static function searchTable($table, array $fields, array $find, array $ignore = array(), $limit = 200) {
		$field	= 'id';
		$where	= Todoyu::db()->buildLikeQuery($find, $fields);
		$limit	= intval($limit);

		if( sizeof($ignore) > 0 ) {
			$where .= ' AND NOT (' . Todoyu::db()->buildLikeQuery($ignore, $fields) . ')';
		}

		return Todoyu::db()->getColumn($field, $table, $where, '', '', $limit);
	}


}


?>