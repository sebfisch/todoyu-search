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

class TodoyuSearchFiltersetActionController extends TodoyuActionController {

	/**
	 * Save current conditions with their settings as new Filter(set)
	 *
	 * @param	Array	$params
	 * @return	Integer
	 */
	public function saveAsNewAction(array $params) {
		$type		= $params['type'];
		$conditions	= $params['conditions'];
		$conditions = empty($conditions) ? array() : json_decode($conditions, true);
		$title		= trim($params['title']);
		$conjunction= $params['conjunction'];

		$data = array(
			'filterset'	=> 0,
			'type'		=> $type,
			'title'		=> TodoyuFiltersetManager::validateTitle($type, $title),
			'conjunction'=> $conjunction,
			'conditions'=> $conditions
		);

		$idFilterset = TodoyuFiltersetManager::saveFilterset($data);

		TodoyuSearchPreferences::saveActiveFilterset($type, $idFilterset);

		return $idFilterset;
	}



	/**
	 * Save conditions as filterset
	 *
	 * @param	Array	$params
	 */
	public function saveAction(array $params) {
		$idFilterset= intval($params['filterset']);
		$conditions	= $params['conditions'];
		$conditions = empty($conditions) ? array() : json_decode($conditions, true);
		$conjunction= $params['conjunction'];
		$tab		= $params['tab'];

		$data = array(
			'conjunction'=> $conjunction,
		);
		TodoyuFiltersetManager::updateFilterset($idFilterset, $data);
		TodoyuFilterConditionManager::saveFilterConditions($idFilterset, $conditions);

		TodoyuSearchPreferences::saveActiveFilterset($tab, $idFilterset);

		return $idFilterset;
	}

}

?>