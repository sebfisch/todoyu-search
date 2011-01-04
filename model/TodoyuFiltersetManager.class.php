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
 * Filterset Manager
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuFiltersetManager {

	/**
	 * @var	String		Default table for database requests
	 */
	const TABLE = 'ext_search_filterset';



	/**
	 * Get filter
	 *
	 * @param	Integer		$idFilter
	 * @return	TodoyuFilterset
	 */
	public static function getFilterset($idFilterset) {
		$idFilterset	= intval($idFilterset);

		return TodoyuRecordManager::getRecord('TodoyuFilterset', $idFilterset);
	}



	/**
	 * Get filterset database record
	 *
	 * @param	Integer		$idFilterset
	 * @return	Array
	 */
	public static function getFiltersetRecord($idFilterset) {
		$idFilterset	= intval($idFilterset);

		return Todoyu::db()->getRecord(self::TABLE, $idFilterset);
	}



	/**
	 * Add a new filterset
	 *
	 * @param	Array		$data
	 * @return	Integer		Filterset ID
	 */
	public static function addFilterset(array $data) {
		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Update filterset
	 *
	 * @param	Integer		$idFilterset
	 * @param	Array		$data
	 * @return	Boolean		Updates successfully
	 */
	public static function updateFilterset($idFilterset, array $data) {
		$idFilterset	= intval($idFilterset);

		return TodoyuRecordManager::updateRecord(self::TABLE, $idFilterset, $data);
	}



	/**
	 * Delete filterset
	 *
	 * @param	Integer		$idFilterset			ID of the filterset
	 * @param	Boolean		$deleteConditions		Delete linked conditions too
	 */
	public static function deleteFilterset($idFilterset, $deleteConditions = true)	{
		$idFilterset	= intval($idFilterset);

		Todoyu::db()->deleteRecord(self::TABLE, $idFilterset);

		if( $deleteConditions ) {
			TodoyuFilterConditionManager::deleteFiltersetConditions($idFilterset);
		}
	}



	/**
	 * Get conditions of filterset
	 *
	 * @param	Integer	$idSet
	 * @return	Array
	 */
	public static function getFiltersetConditions($idFilterset) {
		$idFilterset= intval($idFilterset);

		return TodoyuFilterConditionManager::getFiltersetConditions($idFilterset);
	}



	/**
	 * Get the type of the filterset
	 *
	 * @param	Integer		$idFilterset
	 * @return	String
	 */
	public static function getFiltersetType($idFilterset) {
		$idFilterset= intval($idFilterset);
		$filterset	= self::getFiltersetRecord($idFilterset);

		return $filterset['type'];
	}



	/**
	 * Get result items to given set of filter conditions
	 *
	 * @param	Integer		$idFilterset
	 * @return	Array
	 */
	public static function getFiltersetResultItemIDs($idFilterset) {
		$idFilterset	= intval($idFilterset);

		$typeKey		= self::getFiltersetType($idFilterset);
		$filterClass	= TodoyuFilterManager::getFilterTypeClass($typeKey);
		$sorting		= TodoyuFilterManager::getFilterDefaultSorting($typeKey);

		if( $filterClass !== false ) {
			$conditions		= self::getFiltersetConditions($idFilterset);

			$typeFilter	= new $filterClass($conditions);

			return $typeFilter->getItemIDs($sorting);
		} else {
			return array();
		}
	}



	/**
	 * Get items IDs for all filtersets
	 * Combination: OR
	 *
	 * @param	Array		$filtersetIDs
	 * @return	Array
	 */
	public static function getFiltersetsResultItemIDs(array $filtersetIDs) {
		$filtersetIDs	= TodoyuArray::intval($filtersetIDs, true, true);
		$allResultItems	= array();

		foreach($filtersetIDs as $idFilterset) {
			$allResultItems[] = self::getFiltersetResultItemIDs($idFilterset);
		}

		$resultItems	= array_unique(TodoyuArray::mergeSubArrays($allResultItems));

		return $resultItems;
	}



	/**
	 * Get number of result items for a filterset
	 *
	 * @param	Integer		$idFilterset
	 * @return	Integer
	 */
	public static function getFiltersetCount($idFilterset) {
		$itemIDs	= self::getFiltersetResultItemIDs($idFilterset);

		return sizeof($itemIDs);
	}



	/**
	 * Get result items count for the combination of all filtersets
	 *
	 *
	 * @param array $filtersetIDs
	 * @return unknown
	 */
	public static function getFiltersetsCount(array $filtersetIDs) {
		$resultItems	= self::getFiltersetsResultItemIDs($filtersetIDs);

		return sizeof($resultItems);
	}



	/**
	 * Update filterset title
	 *
	 * @param	Integer		$idFilterset
	 * @param	String		$title
	 */
	public static function renameFilterset($idFilterset, $title) {
		$idFilterset	= intval($idFilterset);
		$data = array(
			'title'	=> $title
		);

		self::updateFilterset($idFilterset, $data);
	}



	/**
	 * Update filterset visibility: Set hidden attribute of the filterset
	 *
	 * @param	Integer		$idFilterset
	 * @param	Boolean		$isHidden
	 */
	public static function updateFiltersetVisibility($idFilterset, $visible = true) {
		$data = array(
			'is_hidden'	=> $visible ? 0 : 1
		);

		self::updateFilterset($idFilterset, $data);
	}



	/**
	 * Get all filterset types
	 *
	 * @return	Array
	 */
	public static function getFiltersetTypes() {
		TodoyuExtensions::loadAllFilters();

		if( is_array(Todoyu::$CONFIG['FILTERS']) ) {
			$keys	= array_keys(Todoyu::$CONFIG['FILTERS']);
		} else {
			$keys	= array();
		}


		return array_map('strtolower', $keys);

//		$field	= 'type';
//		$table	= self::TABLE;
//		$where	= '';
//		$group	= 'type';
//
//		return Todoyu::db()->getColumn($field, $table, $where, $group);
	}



	/**
	 * Get filtersets of a type for the (current) person
	 *
	 * @param	String		$type
	 * @param	Integer		$idPerson
	 * @return 	Array
	 */
	public static function getTypeFiltersets($type = 'TASK', $idPerson = 0, $showHidden = false) {
		$type		= empty($type) ? 'TASK' : strtolower(trim($type));
		$idPerson	= personid($idPerson);

		$fields	= '*';
		$table	= self::TABLE;
		$where	= '		type 		= ' . Todoyu::db()->quote($type, true) .
				  ' AND	deleted		= 0
				    AND ' .	($showHidden ? '' : 'is_hidden 	= 0 AND') .
					' ( id_person_create	= ' . $idPerson . '	)';
		$order	= 'sorting';

		return Todoyu::db()->getArray($fields, $table, $where, '', $order);
	}



	/**
	 * Get filtersets (of a person and a type)
	 * If no person defined, it gets filtersets for the current person
	 * If no type defined, it gets filtersets of all types (of installed extensions)
	 *
	 * @param	Integer		$idPerson
	 * @param	String		$type
	 * @return	Array
	 */
	public static function getFiltersets($idPerson = 0, $type = null) {
		$idPerson		= personid($idPerson);
		$filtersetTypes	= TodoyuFiltersetManager::getFiltersetTypes();
		$typeList		= TodoyuArray::implodeQuoted($filtersetTypes);

		$fields	= '*';
		$table	= self::TABLE;
		$where	= '		id_person_create= ' . $idPerson .
				  ' AND	deleted			= 0
				    AND	type IN(' . $typeList . ')';
		$order	= 'sorting, date_create';

		if( ! is_null($type) ) {
			$where .= ' AND type = ' . Todoyu::db()->quote($type, true);
		}

		return Todoyu::db()->getArray($fields, $table, $where, '', $order);
	}



	/**
	 * Get IDs of filtersets of given person. if no type given: get all types
	 *
	 * @param	Integer	$idPerson
	 * @param	String	$type
	 * @return	Array
	 */
	public static function getFiltersetIDs($idPerson = 0, $type = null) {
		$idPerson	= personid($idPerson);

		$fields	= 'id';
		$table	= self::TABLE;
		$where	= '		id_person_create= ' . $idPerson .
				  ' AND	deleted			= 0';
		$order	= 'title';

		if( ! is_null($type) ) {
			$where .= ' AND type = ' . Todoyu::db()->quote($type, true);
		}

		return Todoyu::db()->getArray($fields, $table, $where, '', $order);
	}



	/**
	 * Get filterset titles (of a person and of a type)
	 * If no person defined, it gets filtersets for the current person
	 * If no type defined, it gets filtersets of all types
	 *
	 * @param	Integer		$idPerson
	 * @param	String		$type
	 * @return	Array
	 */
	public static function getFiltersetTitles($idPerson = 0, $type = null) {
		$idPerson	= personid($idPerson);

		$fields	= 'title';
		$table	= self::TABLE;
		$where	= '		id_person_create= ' . $idPerson .
				  ' AND	deleted			= 0';
		$order	= 'title';

		if( ! is_null($type) ) {
			$where .= ' AND type = ' . Todoyu::db()->quote($type, true);
		}

		return Todoyu::db()->getColumn($fields, $table, $where, '', $order);
	}



	/**
	 * Updates given order of the filterset in the database
	 *
	 * @param	Array	$items
	 */
	public static function updateOrder(array $items) {
		$sorting	= 0;

		foreach($items as $idItem) {
			$update	= array(
				'sorting'	=> $sorting++
			);

			TodoyuRecordManager::updateRecord(self::TABLE, $idItem, $update);
		}
	}



	/**
	 * Store submitted filterset data.
	 * Updates or creates a filterset and (re-)creates the conditions in the database
	 *
	 * @param	Array		$filterData
	 * @return	Integer
	 */
	public static function saveFilterset(array $filterData)	{
		$idFilterset= intval($filterData['filterset']);

		$filtersetData	= array(
			'type'			=> $filterData['type'],
			'title'			=> $filterData['title'],
			'conjunction'	=> $filterData['conjunction']
		);

			// Add or update filterset
		if( $idFilterset === 0 ) {
			$idFilterset = self::addFilterset($filtersetData);
		} else {
			self::updateFilterset($idFilterset, $filtersetData);
		}

			// Save conditions
		TodoyuFilterConditionManager::saveFilterConditions($idFilterset, $filterData['conditions']);

		return $idFilterset;
	}



	/**
	 * Validate filterset title (ensure uniqueness)
	 *
	 * @param	String		$type
	 * @param	String		$title
	 * @return	String
	 */
	public static function validateTitle($type, $title) {
		$typeFiltersets	= self::getFiltersetTitles(0, $type);

		if( in_array($title, $typeFiltersets) ) {
			$title = self::validateTitle($type, $title . '-2');
		}

		return $title;
	}



	/**
	 * Merges FilterObjects as one query
	 *
	 * @static
	 * @param	Array		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public static function Filter_filterObject(array $value, $negate = false)	{
		$queryParts		= false;

		$tables		= array();
		$wheres		= array();
		$joins		= array();

		foreach($value as $filterSet) {
			/**
			 * @var	TodoyuFilterBase	$filterSet
			 */
			$queryArray = $filterSet->getQueryArray('', '', false, true);

				// If filterset is active
			if( $queryArray !== false ) {
					// Add where part (without join)
				$wheres[] 	= '(' . implode(') ' . $filterSet->getConjunction() . ' (', $queryArray['whereNoJoin']) . ')';
					// Add tables (they are already concatenated as string, so explode)
				$tables	= array_merge($tables, explode(',', $queryArray['tables']));
					// Add joins
				if( is_array($queryArray['join']) ) {
					$joins	= array_merge($joins, $queryArray['join']);
				}
			}
		}

			// If conditions found, build query parts
		if( sizeof($wheres) > 0 ) {
				// Remove double tables
			$tables	= array_unique($tables);
			$where	= '(' . implode(' AND ', $wheres) . ')';
			$joins	= array_unique($joins);

			$queryParts	= array(
				'tables'=> $tables,
				'where'	=> $where,
				'join'	=> $joins
			);
		}

		return $queryParts;
	}



	/**
	 * Filter after filter sets
	 *
	 * @param	Integer		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 * @todo 	Implement negation?
	 */
	public static function Filter_filterset($value, $negate = false) {
		$filtersetIDs	= TodoyuArray::intExplode(',', $value, true, true);

			// Prepare return values
		$filter	= array();

			// Process all filtersets
		foreach($filtersetIDs as $idFilterset) {
			$filterSet		= self::getFilterset($idFilterset);
			$className		= 'Todoyu'.ucfirst($filterSet['type']).'Filter';
			if(class_exists($className))	{
				$filter[] = new $className($filterSet->getConditions(), $filterSet->getConjunction());
			}
		}


		return (sizeof($filter) > 0) ? self::Filter_filterObject($filter, $negate) : array();
	}



		### NOT YET CLEANED UP FUNCTIONS ###



	/**
	 * The options of the filter selector. Used for filterWidget filterSet
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getFilterSetSelectionOptions($definitions)	{
		$allFiltersets	= self::getTypeFiltersets('TASK', personid(), true);

		$activeFilterset = TodoyuSearchPreferences::getActiveFilterset('task');

		foreach($allFiltersets as $filterset)	{
				// Prevent adding the filterset to itself
			if( $filterset['id'] != $activeFilterset ) {
				if( ! self::isFiltersetUsed($filterset['id'], $activeFilterset) )	{
					$definitions['options'][] = array(
						'value'		=> $filterset['id'],
						'label'		=> $filterset['title']
					);
				}
			}
		}

		return $definitions;
	}



	/**
	 * Check to avoid from endless loop.
	 *
	 * @param	Integer		$idFilterset
	 * @param	Integer		$idFiltersetToCheck
	 * @return	Boolean
	 */
	protected static function isFiltersetUsed($idFilterset, $idFiltersetToCheck)	{
		$conditions = TodoyuFilterConditionManager::getFilterSetConditions($idFilterset);

		foreach($conditions as $condition)	{
			if( $condition['filter'] === 'filterSet' ) {
				$subFiltersetIDs	= explode(',', $condition['value']);

				if( in_array($idFiltersetToCheck, $subFiltersetIDs) ) {
					return true;
				} else {
					foreach($subFiltersetIDs as $subFiltersetID) {
						$check = self::isFiltersetUsed($subFiltersetID, $idFiltersetToCheck);

						if( $check === true ) {
							return true;
						}
					}
				}
			}
		}

		return false;
	}

}

?>