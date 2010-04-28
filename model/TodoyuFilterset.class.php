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
 * Fitlerset object
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuFilterset extends TodoyuBaseObject {

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
	 * @return	TodoyuPerson
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
		return TodoyuFiltersetManager::getFiltersetConditions($this->id);
	}

}

?>