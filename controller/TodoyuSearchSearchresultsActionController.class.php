<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions gmbh
* All rights reserved.
*
* This script is part of the todoyu project.
* The todoyu project is free software; you can redistribute it and/or modify
* it under the terms of the BSC License.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
* for more details.
*
* This copyright notice MUST APPEAR in all copies of the script.
*****************************************************************************/

/**
 * Searchresults action controller
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchSearchresultsActionController extends TodoyuActionController {

	/**
	 * Update search result for the submitted conditions
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function updateAction(array $params) {
		$tab			= $params['tab'];
		$idFilterset	= intval($params['filterset']);
		$conditions		= $params['conditions'];
		$conditions		= empty($conditions) ? array() : json_decode($conditions, true) ;
		$conjunction	= $params['conjunction'];

		if( $idFilterset > 0 || sizeof($conditions) > 0)	{
			return TodoyuFilterAreaRenderer::renderResults($tab, $idFilterset, $conditions, $conjunction);
		} else {
			return TodoyuFilterAreaRenderer::renderResults($tab, 0, array(), $conjunction);
		}
	}

}

?>