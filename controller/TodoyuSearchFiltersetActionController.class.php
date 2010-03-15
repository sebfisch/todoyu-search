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
		$title		= $params['title'];
		$conjunction= $params['conjunction'];

		$data = array(
			'filterset'	=> 0,
			'type'		=> $type,
			'title'		=> TodoyuFiltersetManager::validateTitle($title),
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