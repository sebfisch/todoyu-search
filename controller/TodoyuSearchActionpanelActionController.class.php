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



class TodoyuSearchActionpanelActionController extends TodoyuActionController	{

	/**
	 * @param array $params
	 * @return void
	 */
	public function init(array $params) {
		restrict('search', 'general:area');
	}


	/**
	 * Controller to catch export from the search area
	 *
	 * @param  $params
	 * @return void
	 */
	public function exportAction($params) {
		$exportName	= $params['exportname'];
		$type		= $params['tab'];
		$idFilterset= intval($params['idFilterSet']);
		$conditions	= json_decode($params['conditions'], true);
		$conjunction= $params['conjunction'];

		TodoyuSearchActionPanelManager::dispatchExport($exportName, $type, $idFilterset, $conditions, $conjunction);
	}
}

?>