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
 * Controller for filterarea (controlls + widgets + results)
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchFilterareaActionController extends TodoyuActionController {

	/**
	 * @param	Array	$params
	 */
	public function init(array $params) {
		restrict('search', 'general:area');
	}



	/**
	 * Load whole filter area.
	 * Needs a tab and a filterset
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function loadAction(array $params) {
		$tab		= $params['tab'];
		$idFilterset= intval($params['filterset']);

		TodoyuSearchPreferences::saveActiveTab($tab);

			// Save preferences
		if( $idFilterset !== 0 ) {
			TodoyuSearchPreferences::saveActiveFilterset($tab, $idFilterset);
		}

		return TodoyuSearchFilterAreaRenderer::renderFilterArea($tab, $idFilterset);
	}
}

?>