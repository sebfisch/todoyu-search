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

class TodoyuSearchSearchresultsActionController extends TodoyuActionController {

	public function updateAction(array $params) {
		$tab			= $params['tab'];
		$idFilterset	= intval($params['filterset']);
		$conditions		= $params['conditions'];
		$conditions		= empty($conditions) ? array() : json_decode($conditions, true) ;
		$conjunction	= $params['conjunction'];

		if( $idFilterset > 0 || sizeof($conditions) > 0)	{
			return TodoyuFilterAreaRenderer::renderResults($tab, $idFilterset, $conditions, $conjunction);
		}
	}


}

?>