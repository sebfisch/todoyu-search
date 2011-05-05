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
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchActionPanelManager {

	/**
	 * Render action panel HTML
	 *
	 * @param	String	$activeTab		e.g. 'task' / 'project'
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
	 * Export result items of given filter conditions with registered export function
	 *
	 * @param	String		$name
	 * @param	String		$type			'task' / 'project' etc.
	 * @param	Array		$conditions
	 * @param	String		$conjunction	logical conjunction ('AND' / 'OR')
	 */
	public static function dispatchExport($name, $type, $conditions, $conjunction) {
		$export	= self::getExportOfTypeAndName($type, $name);

		$conjunction= strtoupper($conjunction) === 'OR' ? 'OR' : 'AND';
		$conditions = TodoyuSearchFilterConditionManager::buildFilterConditionArray($conditions);

			// Build filter
		$typeClass	= TodoyuSearchFiltersetManager::getFiltersetTypeClass($type);
		$typeFilter	= new $typeClass($conditions, $conjunction);

		if( $typeFilter->hasActiveFilters() ) {
			$sorting	= TodoyuSearchFilterManager::getFilterDefaultSorting($type);
			$itemIDs	= $typeFilter->getItemIDs($sorting);
		} else {
			$itemIDs	= array();
		}

		$checkExport	= explode('::', $export['method']);

		if( method_exists($checkExport[0], $checkExport[1]) ) {
			return call_user_func($export['method'], $itemIDs);
		} else {
			TodoyuLogger::logError("Tried to call undefined method: " . $export['method'] . ' in ' . __CLASS__ . ' on line ' . __LINE__);
		}
	}



	/**
	 * Add result items type to search action panel
	 *
	 * @param	String	$type
	 * @param	String	$name
	 * @param	String	$callback
	 * @param	String	$label
	 * @param	String	$htmlClass
	 * @param	String	$right
	 */
	public static function addExport($type, $name, $callback, $label, $htmlClass = '', $right = '') {
		if( strlen($right) != '' ) {
			$rightArray = explode(':', $right);
			$rightExt	= $rightArray[0];
			unset($rightArray[0]);
			$rightRight	= implode(':', $rightArray);
		}

		Todoyu::$CONFIG['EXT']['search']['filter'][$type]['actionpanel']['export'][$name] = array(
			'method'	=> $callback,
			'htmlClass'	=> ($htmlClass ? $htmlClass : $name),
			'label'		=> $label,
			'right'		=> array('ext'	=> $rightExt,
								 'right'=> $rightRight)
		);
	}



	/**
	 * Get export configuration of given type/name
	 *
	 * @param	String		$type
	 * @param	String		$name
	 * @return	Array
	 */
	public static function getExportOfTypeAndName($type, $name) {
		return Todoyu::$CONFIG['EXT']['search']['filter'][$type]['actionpanel']['export'][$name];
	}



	/**
	 * Get type export configuration
	 *
	 * @param	String	$type
	 * @return	String
	 */
	public static function getExportOfType($type) {
		return Todoyu::$CONFIG['EXT']['search']['filter'][$type]['actionpanel']['export'];
	}
}

?>