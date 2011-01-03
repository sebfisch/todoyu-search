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



class TodoyuSearchActionPanelManager {



	/**
	 * @static
	 * @return String
	 */
	public static function renderActionPanel($activeTab)	{
		$tmpl = 'ext/search/view/actionpanel.tmpl';

		$data = array(
			'exports'	=> self::getExportOfType($activeTab)
		);

		return render($tmpl, $data);
	}



	/**
	 * @static
	 * @param  $name
	 * @param  $tab
	 * @param  $params
	 * @return void
	 */
	public static function dispatchExport($name, $type, $idFilterset, $conditions, $conjunction = array())	{
		$export	= self::getExport($type, $name);

		$idFilterset	= intval($idFilterset);
		$conjunction	= strtoupper($conjunction) === 'OR' ? 'OR' : 'AND';

			// If filterset is given, use its conditions
		if( $idFilterset !== 0 ) {
			$conditions = TodoyuFilterConditionManager::getFilterSetConditions($idFilterset);
		} else {
			$conditions = TodoyuFilterConditionManager::buildFilterConditionArray($conditions);
		}

			// Build filter
		$typeClass	= TodoyuFilterManager::getFilterTypeClass($type);

		/**
		 * @var	TodoyuFilterBase	$typeFilter
		 */
		$typeFilter	= new $typeClass($conditions, $conjunction);

		$sorting	= TodoyuFilterManager::getFilterDefaultSorting($type);

		if( $typeFilter->hasActiveFilters() ) {
			$itemIDs	= $typeFilter->getItemIDs($sorting);
		} else {
			$itemIDs	= array();
		}

		$checkExport	= explode('::', $export['method']);

		if(method_exists($checkExport[0], $checkExport[1]))	{
			return call_user_func($export['method'], $itemIDs);
		} else {
			Todoyu::log("Tried to call undefined method: " . $export['method'] . ' in ' . __CLASS__ . ' on line ' . __LINE__, TodoyuLogger::LEVEL_ERROR);
		}
	}



	/**
	 * @static
	 * @param  $type
	 * @param  $name
	 * @param  $method
	 * @return void
	 */
	public static function addExport($type, $name, $method, $label, $htmlClass = '')	{
		Todoyu::$CONFIG['EXT']['search']['filter'][$type]['actionpanel']['export'][$name] = array(
			'method'	=> $method,
			'htmlClass'	=> ($htmlClass ? $htmlClass : $name),
			'label'		=> $label
		);
	}



	/**
	 * @static
	 * @param  $type
	 * @param  $name
	 * @return 
	 */
	public static function getExport($type, $name)	{
		return Todoyu::$CONFIG['EXT']['search']['filter'][$type]['actionpanel']['export'][$name];
	}



	/**
	 * @static
	 * @param  $type
	 * @return 
	 */
	public static function getExportOfType($type)	{
		return Todoyu::$CONFIG['EXT']['search']['filter'][$type]['actionpanel']['export'];
	}
}

?>