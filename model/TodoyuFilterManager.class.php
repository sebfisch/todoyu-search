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
 * Manage filters
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuFilterManager {

	/**
	 * Get configuration of a filter (or a widget)
	 *
	 * @param	String		$type
	 * @param	String		$name
	 * @return	Array		Or FALSE
	 */
	public static function getFilterConfig($type, $name) {
		$base	=& Todoyu::$CONFIG['FILTERS'][$type];
		$config	= false;

		if( isset($base['filters'][$name]) && is_array($base['filters'][$name]) ) {
			$config	= $base['filters'][$name];
		} elseif( isset($base['widgets'][$name]) && is_array($base['widgets'][$name]) ) {
			$config	= $base['widgets'][$name];
		}

		return $config;
	}



	/**
	 * Get configuration of a filtertype (like task or project)
	 *
	 * @param	String		$type
	 * @param	String		$key
	 * @return	Mixed
	 */
	public static function getFilterTypeConfig($type, $key = null) {
		TodoyuExtensions::loadAllFilters();

		$base =& Todoyu::$CONFIG['FILTERS'][strtoupper($type)]['config'];

		return is_null($key) ? $base : $base[$key];
	}



	/**
	 * Get available filter types (project,task,etc)
	 *
	 * @param	Boolean		$sort		Sort types by position flag
	 * @return	Array
	 */
	public static function getFilterTypes($sort = false) {
		TodoyuExtensions::loadAllFilters();

		$types	= array_keys(Todoyu::$CONFIG['FILTERS']);

		if( $sort ) {
			$sorting = array();

			foreach($types as $type) {
				$sorting[Todoyu::$CONFIG['FILTERS'][$type]['config']['position']] = $type;
			}

			ksort($sorting);

			$types = $sorting;
		}

		return $types;
	}



	/**
	 * Get filter class for a type
	 *
	 * @param	String		$type
	 * @return	String
	 */
	public static function getFilterTypeClass($type) {
		TodoyuExtensions::loadAllFilters();

		$class	= Todoyu::$CONFIG['FILTERS'][strtoupper($type)]['config']['class'];

		return is_null($class) ? false : $class;
	}



	/**
	 * Get label of a filter type (Ex: Task)
	 *
	 * @param	String		$type
	 * @return	String
	 */
	public static function getFilterTypeLabel($type) {
		TodoyuExtensions::loadAllFilters();

		return TodoyuLanguage::getLabel(Todoyu::$CONFIG['FILTERS'][strtoupper($type)]['config']['label']);
	}



	/**
	 * Get results renderer function for a filter type
	 *
	 * @param	String		$type
	 * @return	String
	 */
	public static function getFilterTypeResultsRenderer($type) {
		TodoyuExtensions::loadAllFilters();

		$type	= strtoupper($type);

		return Todoyu::$CONFIG['FILTERS'][$type]['config']['resultsRenderer'];
	}

}

?>