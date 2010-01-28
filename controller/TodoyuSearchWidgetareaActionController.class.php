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

class TodoyuSearchWidgetareaActionController extends TodoyuActionController {

	public function addAction(array $params) {
		$widgetName	= $params['name'];
		$condition	= $params['condition'];
		$type		= $params['type'];
		$value		= $params['value'];
		$negate		= intval($params['negate']) === 1;

		echo TodoyuFilterWidgetRenderer::renderWidget($type, $condition, $widgetName, $value, $negate);
	}


	public function loadAction(array $params) {
		$idFilterset= intval($params['filterset']);
		$tab		= $params['tab'];
		$content	= '';

		if( $idFilterset === 0 ) {
			$idFilterset = TodoyuSearchPreferences::getActiveFilterset($tab);
		} else {
			TodoyuSearchPreferences::saveActiveFilterset($tab, $idFilterset);
		}

		if( $idFilterset !== 0 ) {
			$conditions	= TodoyuFiltersetManager::getFiltersetConditions($idFilterset);

				// Send widgets
			$content	= TodoyuFilterAreaRenderer::renderWidgetArea($idFilterset);
				// Add JS init for loaded widgets
			$content 	.= TodoyuDiv::wrapScript('Todoyu.Ext.search.Filter.initConditions(\'' . $tab . '\', ' . json_encode($conditions) . ');');
		} else {
			$content	= 'No widgets';
		}

		return $content;
	}


}

?>