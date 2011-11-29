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
 * Filterset object
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchFilterset extends TodoyuBaseObject {

	/**
	 * Initialize filterset
	 *
	 * @param	Integer		$idFilterset
	 */
	public function __construct($idFilterset) {
		parent::__construct($idFilterset, 'ext_search_filterset');
	}



	/**
	 * Get owner of the filterset
	 *
	 * @return	TodoyuContactPerson
	 */
	public function getPerson() {
		return $this->getPerson('create');
	}



	/**
	 * Get filterset conditions
	 *
	 * @return	Array
	 */
	public function getConditions() {
		return TodoyuSearchFiltersetManager::getFiltersetConditions($this->getID());
	}



	/**
	 * Get filterset conjunction
	 *
	 * @return	String
	 */
	public function getConjunction() {
		return $this->get('conjunction');
	}



	/**
	 * Get filterset title
	 *
	 * @return	String
	 */
	public function getTitle() {
		return $this->get('title');
	}



	/**
	 * Get filterset type
	 *
	 * @return	String
	 */
	public function getType() {
		return $this->get('type');
	}



	/**
	 * Get matching item IDs
	 *
	 * @param	Integer		$limit
	 * @return	Array
	 */
	public function getItemIDs($limit = 1000) {
		$limit			= intval($limit);
		$filterObject	= $this->getFilterObject();
		$itemIDs		= array();

		if( $filterObject !== false ) {
			$sorting= TodoyuSearchFilterManager::getFilterDefaultSorting($this->getType());

			return $filterObject->getItemIDs($sorting, $limit);
		}

		return $itemIDs;
	}



	/**
	 * Get type class
	 *
	 * @return		String
	 */
	public function getClass() {
		return TodoyuSearchFiltersetManager::getFiltersetTypeClass($this->getType());
	}



	/**
	 * Get filter object from filterset
	 *
	 * @return	TodoyuSearchFilterBase|Boolean
	 */
	public function getFilterObject() {
		$class	= $this->getClass();

		if( $class !== false ) {
			$conditions	= $this->getConditions();
			$conjunction= $this->getConjunction();

			return new $class($conditions, $conjunction);
		} else {
			return false;
		}
	}

}

?>