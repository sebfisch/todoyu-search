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
 * Filterset Manager
 *
 * @package		Todoyu
 * @subpackage	Search
 */

class TodoyuFiltersetManager {

	const TABLE = 'ext_search_filterset';

	/**
	 * Get filter
	 *
	 * @param	Integer		$idFilter
	 * @return	Filterset
	 */
	public static function getFilterset($idFilterset) {
		$idFilterset	= intval($idFilterset);

		return TodoyuCache::getRecord('TodoyuFilterset', $idFilterset);
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
	 * @return	Bool		Updates successfully
	 */
	public static function updateFilterset($idFilterset, array $data) {
		$idFilterset	= intval($idFilterset);

		return TodoyuRecordManager::updateRecord(self::TABLE, $idFilterset, $data);
	}



	/**
	 * Delete filterset
	 *
	 * @param	Integer		$idFilterset			ID of the filterset
	 * @param	Bool		$deleteConditions		Delete linked conditions too
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
	 * Update filterset visibility
	 * Set hidden attribute of the filterset
	 *
	 * @param	Integer		$idFilterset
	 * @param	Bool		$isHidden
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
		$field	= 'type';
		$table	= self::TABLE;
		$where	= '';
		$group	= 'type';

		return Todoyu::db()->getColumn($field, $table, $where, $group);
	}



	/**
	 * Get filtersets of a type for the (current) user
	 *
	 * @param	String		$type
	 * @param	Integer		$idUser
	 * @return 	Array
	 */
	public static function getTypeFiltersets($type = 'TASK', $idUser = 0, $showHidden = true) {
		$type	= empty($type) ? 'TASK' : strtolower(trim($type));
		$idUser	= userid($idUser);

		$fields	= '*';
		$table	= self::TABLE;
		$where	= '	type 		= ' . Todoyu::db()->quote($type, true) . ' AND
					deleted		= 0 AND
					is_hidden 	= ' . ($showHidden ? 1 : 0) . ' AND (
						id_user	= ' . $idUser . ' OR
						id_user	= 0
					)';
		$order	= 'sorting';

		return Todoyu::db()->getArray($fields, $table, $where, '', $order);
	}



	/**
	 * Get task filtersets
	 *
	 * @param	Integer	$idUser
	 * @param	Bool	$showHidden
	 * @return	Array
	 */
	public static function getTaskFiltersets($idUser = 0, $showHidden = true) {
		$idUser	= intval($idUser);

		return self::getTypeFiltersets('TASK', $idUser, $showHidden);
	}



	/**
	 * Get filtersets (of a user and of a type)
	 * If no user defined, it gets filtersets for the current user
	 * If no type defined, it gets filtersets of all types
	 *
	 * @param	Integer		$idUser
	 * @param	String		$type
	 * @return	Array
	 */
	public static function getFiltersets($idUser = 0, $type = null) {
		$idUser	= userid($idUser);

		$fields	= '*';
		$table	= self::TABLE;
		$where	= '	id_user_create	= ' . $idUser . ' AND
					deleted			= 0';
		$order	= 'sorting, date_create';

		if( ! is_null($type) ) {
			$where .= ' AND type = ' . Todoyu::db()->quote($type, true);
		}

		return Todoyu::db()->getArray($fields, $table, $where, '', $order);
	}



	/**
	 * Get filterset titles (of a user and of a type)
	 * If no user defined, it gets filtersets for the current user
	 * If no type defined, it gets filtersets of all types
	 *
	 * @param	Integer		$idUser
	 * @param	String		$type
	 * @return	Array
	 */
	public static function getFiltersetTitles($idUser = 0, $type = null) {
		$idUser	= userid($idUser);

		$fields	= 'title';
		$table	= self::TABLE;
		$where	= '	id_user_create	= ' . $idUser . ' AND
					deleted			= 0';
		$order	= 'title';

		if( ! is_null($type) ) {
			$where .= ' AND type = ' . Todoyu::db()->quote($type, true);
		}

		return Todoyu::db()->getArray($fields, $table, $where, '', $order);
	}



	/**
	 * Updates given order of the filterset to the database
	 *
	 * @param	String	$type
	 * @param	Array	$order
	 */
	public static function updateOrder(array $orderedItems) {
		$sorting	= 0;

		foreach($orderedItems as $idItem) {
			$update	= array(
				'sorting'	=> $sorting++
			);

			Todoyu::db()->updateRecord(self::TABLE, $idItem, $update);
		}
	}



	/**
	 * Store submitted filterset data
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
	 *	@param	String	$title
	 *	@return String
	 */
	public static function validateTitle($title) {
		$allFilterSetTitles	= TodoyuArray::flatten( self::getFiltersetTitles() );

		if ( in_array($title, $allFilterSetTitles) ) {
			$title = self::validateTitle( $title . '-2' );
		}

		return $title;
	}










	### NOT YET CLEANED UP FUNCTIONS ###






	/**
	 * Filter after filter sets
	 *
	 * @param	Integer		$value
	 * @param	Bool		$negate
	 * @return	Array
	 * @todo 	Implement negation?
	 */
	public static function Filter_filterset($value, $negate = false)	{
		$idFilterset= intval($value);

		$localTables		= array();
		$localWhere 		= array();
		$localWhereString 	= '';
		$returnArray		= array();

		$type = self::getFiltersetType($idFilterset);

		$filterConditions = TodoyuFilterConditionManager::getFilterSetConditions($value);

		foreach($filterConditions as $condition)	{
			$conditionDefinition = TodoyuFilterWidgetManager::getFilterWidgetDefinitions($type, $condition['filter'], 0, $condition['value'], $condition['negate'] == 1);
			if( TodoyuDiv::isFunctionReference($conditionDefinition['funcRef']) ) {
				$result = TodoyuDiv::callUserFunction($conditionDefinition['funcRef'], $condition['value'], $condition['negate']);

				if( is_array($result['tables']) )	{
					foreach($result['tables'] as $table)	{
						$localTables[] = $table;
					}
				} else if( $result['tables'] )	{
					$localTables[] = $result['tables'];
				}

				if( is_array($result['where']) )	{
					foreach($result['where'] as $where)	{
						$localWhere[] = $where;
					}
				} else if( $result['where'] ) {
					$localWhere[] = $result['where'];
				}
			}
		}

		array_unique($localTables);
		$filter = TodoyuFiltersetManager::getFiltersetRecord($value);

		$conjunction = $filter['conjunction'] ? $filter['conjunction'] : 'AND';

		$localWhereString = implode(' '.$conjunction.' ', $localWhere);

		if( $localWhereString )	{
			$localWhereString = '('.$localWhereString.')';
			$returnArray['where']	= $localWhereString;
		}

		if(count($localTables) > 0)	{
			$returnArray['tables']	= $localTables;
		}

		return $returnArray;
	}



	/**
	 * The options of the filter selector.
	 * Used for filterWidget filterSet
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getFilterSetSelectionOptions($definitions)	{
		$optionsArray = array();

		$filtersets	= self::getTypeFiltersets('TASK', userid(),true);
		$filtersets	= array_merge($filtersets, self::getTypeFiltersets('TASK', userid(),false));

		$curFilter = TodoyuRequest::getParam('filterID') ? TodoyuRequest::getParam('filterID') : TodoyuSearchPreferences::getCurrentFilter();

		foreach($filtersets as $filterSet)	{
			if( $filterSet['id'] != $curFilter )	{
				if( self::checkIfFilterIsUsed($filterSet['id'], $curFilter) )	{
					$selected = ($filterSet['id'] == $definitions['value']);
					$optionsArray[$filterSet['id']] = array('label' => $filterSet['title'],
															'selected' => $selected);
				}
			}
		}

		$definitions['options'] = $optionsArray;

		return $definitions;
	}



	/**
	 * Check to avoid from endless loop.
	 *
	 * @param	Integer	$startFilter
	 * @param	Integer	$curFilter
	 * @return	Boolean
	 */
	protected static function checkIfFilterIsUsed($startFilter, $curFilter)	{
		$conditions = TodoyuFilterConditionManager::getFilterSetConditions($startFilter);

		foreach($conditions as $condition)	{
			if( $condition['filter'] == 'filterSet' )	{
				if( $condition['value'] == $curFilter )	{
					return false;
				} else {
					if(!self::checkIfFilterIsUsed($condition['value'], $curFilter))	{
						return false;
					}
				}
			}
		}

		return true;
	}

}


?>