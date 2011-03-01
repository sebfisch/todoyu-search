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
 * Filter condition manager
 * Add, remove and get filter conditions of filtersets
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchFilterConditionManager {

	/**
	 * @var	String		Default table for database requests
	 */
	const TABLE = 'ext_search_filtercondition';



	/**
	 * Get filter condition
	 *
	 * @param	Integer			$idFilterCondition
	 * @return	TodoyuSearchFilterCondition
	 */
	public static function getFilterCondition($idFilterCondition) {
		$idFilterCondition	= intval($idFilterCondition);

		return TodoyuRecordManager::getRecord('TodoyuFilterCondition', $idFilterCondition);
	}



	/**
	 * Get filter condition database record
	 *
	 * @param	Integer		$idFilterCondition
	 * @return	Arraay
	 */
	public static function getFilterConditionRecord($idFilterCondition) {
		$idFilterCondition	= intval($idFilterCondition);

		return Todoyu::db()->getRecord(self::TABLE, $idFilterCondition);
	}



	/**
	 * Get filter conditions to given filter set
	 *
	 * @param	Integer		$idFilterset
	 * @return	Array
	 */
	public static function getFiltersetConditions($idFilterset) {
		$idFilterset	= intval($idFilterset);

			// Get all conditions of filter set
		$where	= '	id_set	= ' . $idFilterset . ' AND deleted	= 0';
		$order	= 'id';

		$conditions = TodoyuRecordManager::getAllRecords(self::TABLE, $where, $order);

			// Get conditions to type of filter set
		$config = self::getTypeConditions(TodoyuSearchFiltersetManager::getFiltersetType($idFilterset));
			// Remove conditions without configuration
		foreach($conditions as $key => $condition) {
			if( ! $config[$condition['filter']] ) {
				unset($conditions[$key]);
			}
		}

		return array_values($conditions);
	}



	/**
	 * Get type conditions (filter options)
	 *
	 * @param	String		$type
	 * @return	Array
	 */
	public static function getTypeConditions($type = 'TASK') {
		$type	= strtoupper(trim($type));

		TodoyuExtensions::loadAllFilters();

		$conditionConfigs	= Todoyu::$CONFIG['FILTERS'][$type]['widgets'];

		if( ! is_array($conditionConfigs) ) {
			$conditionConfigs = array();
		}

		return $conditionConfigs;
	}



	/**
	 * Groups the filtercondition by their option group.
	 * Used for the filterwidget selector in the search extension
	 *
	 * @param	Array	$filters
	 * @return	Array
	 */
	public static function groupConditionsByType(array $conditions) {
		$grouped = array();

		foreach($conditions as $name => $condition) {
			$grouped[ $condition['optgroup'] ][$name] = $condition;
		}

		return $grouped;
	}



	/**
	 * Get conditions of a type, grouped by their optgroup attribute
	 *
	 * @param	String		$type
	 * @return	Array
	 */
	public static function getGroupedTypeConditions($type = 'TASK') {
		$conditions	= self::getTypeConditions($type);

		return self::groupConditionsByType($conditions);
	}



	/**
	 * Save all filterset conditions
	 *
	 * @param	Integer		$idFilterset
	 * @param	Array		$filterConditions
	 * @return	Array		Condition IDs
	 */
	public static function saveFilterConditions($idFilterset, array $filterConditions) {
		$idFilterset	= intval($idFilterset);
		$conditionIDs	= array();

			// Delete all conditions of the filterset
		self::deleteFiltersetConditions($idFilterset);

			// Save all conditions
		foreach($filterConditions as $condition) {
			$conditionIDs[] = self::addFilterCondition($idFilterset, $condition['condition'], $condition['value'], $condition['negate']);
		}

		return $conditionIDs;
	}



	/**
	 * Add a new filterset condition
	 *
	 * @param	Integer		$idFilterset		Parent filterset
	 * @param	String		$filterName			Name of the filter (-function)
	 * @param	Mixed		$value				String or Array value data
	 * @param	Boolean		$negate				Filter is negated
	 * @return	Integer		Condition ID
	 */
	public static function addFilterCondition($idFilterset, $filterName, $value, $negate = false) {
		$idFilterset= intval($idFilterset);
		$negate		= $negate ? 1 : 0;

		if( is_array($value) ) {
			$value = implode(',', $value);
		}

		$data = array(
			'id_set'		=> $idFilterset,
			'filter'		=> $filterName,
			'value'			=> $value,
			'negate'		=> $negate
		);

		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Delete all conditions of a filterset
	 *
	 * @param	Integer		$idFilterset
	 */
	public static function deleteFiltersetConditions($idFilterset) {
		$idFilterset = intval($idFilterset);

		Todoyu::db()->doDelete(self::TABLE, 'id_set = ' . $idFilterset);
	}



	/**
	 * Transform the filterconditions to a valid filter condition array
	 *
	 * @param	Array		$filterConditions
	 * @return	Array
	 */
	public static function buildFilterConditionArray(array $filterConditions = array()) {
		$conditions	= array();

		foreach($filterConditions as $condition) {
				// Join array values to a string
			if( is_array($condition['value']) ) {
				$condition['value'] = implode(',', $condition['value']);
			}

			$conditions[] = array(
				'name'		=> $condition['name'],
				'filter'	=> $condition['condition'],
				'negate'	=> $condition['negate'],
				'value'		=> $condition['value']
			);
		}

		return $conditions;
	}

}

?>