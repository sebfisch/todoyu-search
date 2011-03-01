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
 * @todo		comment
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchActionPanelManager {

	/**
	 * @todo	comment
	 *
	 * @return	String
	 */
	public static function renderActionPanel($activeTab) {
		$tmpl = 'ext/search/view/actionpanel.tmpl';

		$data = array(
			'exports'	=> self::getExportOfType($activeTab)
		);

		return render($tmpl, $data);
	}



	/**
	 * @todo	comment
	 *
	 * @param	$name
	 * @param	$tab
	 * @param	$params
	 */
	public static function dispatchExport($name, $type, $idFilterset, $conditions, $conjunction = array()) {
		$export	= self::getExport($type, $name);

		$idFilterset	= intval($idFilterset);
		$conjunction	= strtoupper($conjunction) === 'OR' ? 'OR' : 'AND';

			// If filterset is given, use its conditions
		if( $idFilterset !== 0 ) {
			$conditions = TodoyuSearchFilterConditionManager::getFilterSetConditions($idFilterset);
		} else {
			$conditions = TodoyuSearchFilterConditionManager::buildFilterConditionArray($conditions);
		}

			// Build filter
		$typeClass	= TodoyuSearchFilterManager::getFilterTypeClass($type);
		$typeFilter	= new $typeClass($conditions, $conjunction);

		$sorting	= TodoyuSearchFilterManager::getFilterDefaultSorting($type);

		if( $typeFilter->hasActiveFilters() ) {
			$itemIDs	= $typeFilter->getItemIDs($sorting);
		} else {
			$itemIDs	= array();
		}

		$checkExport	= explode('::', $export['method']);

		if( method_exists($checkExport[0], $checkExport[1]) ) {
			return call_user_func($export['method'], $itemIDs);
		} else {
			Todoyu::log("Tried to call undefined method: " . $export['method'] . ' in ' . __CLASS__ . ' on line ' . __LINE__, TodoyuLogger::LEVEL_ERROR);
		}
	}



	/**
	 * @todo	comment
	 *
	 * @param	$type
	 * @param	$name
	 * @param	$method
	 */
	public static function addExport($type, $name, $method, $label, $htmlClass = '', $right = '') {
		$rightArray = explode(':', $right);

		Todoyu::$CONFIG['EXT']['search']['filter'][$type]['actionpanel']['export'][$name] = array(
			'method'	=> $method,
			'htmlClass'	=> ($htmlClass ? $htmlClass : $name),
			'label'		=> $label,
			'right'		=> array('ext'	=> $rightArray[0],
								 'right'=> $rightArray[1])
		);
	}



	/**
	 * @todo	comment
	 *
	 * @param	$type
	 * @param	$name
	 * @return	String
	 */
	public static function getExport($type, $name) {
		return Todoyu::$CONFIG['EXT']['search']['filter'][$type]['actionpanel']['export'][$name];
	}



	/**
	 * @todo	comment
	 *
	 * @param	String	$type
	 * @return	String
	 */
	public static function getExportOfType($type) {
		return Todoyu::$CONFIG['EXT']['search']['filter'][$type]['actionpanel']['export'];
	}
}

?>