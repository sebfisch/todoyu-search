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
 * Manager class Todoyufor the search extension
 *
 */

class TodoyuSearchManager {

	/**
	 * function returns the inline tabs
	 * rendered on top of the filter area
	 *
	 */
	public static function getInlineTabHeads()	{
		$tabs = TodoyuFilterBase::getPossibleFilterTypes();

		return $tabs;
	}



	/**
	 * Convert a simple filter array (from url) to a search filter array
	 *
	 * @param	Array		$simpleFilterConditions
	 * @return	Array
	 */
	public static function convertSimpleToFilterConditionArray(array $simpleFilterConditions) {
		$conditions = array();

		foreach($simpleFilterConditions as $filterName => $filterValue) {
			$conditions[] = array(
				'type'		=> $filterName,
				'negate'	=> false,
				'value'		=> $filterValue
			);
		}

		return $conditions;
	}



	/**
	 * Enter description here...
	 *
	 * @param unknown_type $type
	 * @param unknown_type $methodSearch
	 * @param unknown_type $methodSuggest
	 * @param unknown_type $labelSuggest
	 * @param unknown_type $labelMode
	 * @param unknown_type $position
	 */
	public static function addSearchEngine($type, $methodSearch, $methodSuggest, $labelSuggest, $labelMode = '', $position = 100) {
		$type		= strtolower(trim($type));
		$position	= intval($position);

		if( $labelMode === '' ) {
			$labelMode = $labelSuggest;
		}

		$GLOBALS['CONFIG']['EXT']['search']['engines'][$type] = array(
			'type'			=> $type,
			'search'		=> $methodSearch,
			'suggestion'	=> $methodSuggest,
			'labelSuggest'	=> $labelSuggest,
			'labelMode'		=> $labelMode,
			'position'		=> $position
		);
	}



	/**
	 * Enter description here...
	 *
	 * @return	Array
	 */
	public static function getSearchEngines() {
		if( is_array($GLOBALS['CONFIG']['EXT']['search']['engines']) ) {
			return TodoyuArray::sortByLabel($GLOBALS['CONFIG']['EXT']['search']['engines'], 'position');
		} else {
			return array();
		}
	}

}

?>