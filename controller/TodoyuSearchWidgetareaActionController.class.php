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
 * Widget area controller
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchWidgetareaActionController extends TodoyuActionController {

	/**
	 * (Render and) add given search widget
	 *
	 * @param	Array	$params
	 */
	public function addAction(array $params) {
		$widgetName	= $params['name'];
		$condition	= $params['condition'];
		$type		= $params['type'];
		$value		= $params['value'];
		$negate		= intval($params['negate']) === 1;

		echo TodoyuFilterWidgetRenderer::renderWidget($type, $condition, $widgetName, $value, $negate);
	}



	/**
	 * Load widget area
	 *
	 * @param	Array		$params
	 * @return	String		rendered widget area
	 */
	public function loadAction(array $params) {
		$idFilterset= intval($params['filterset']);
		$tab		= $params['tab'];

			// No filterset given? get active filterset of tab
		if( $idFilterset === 0 ) {
			$idFilterset = TodoyuSearchPreferences::getActiveFilterset($tab);
		} else {
				// Filterset given? save as active set of tab
			TodoyuSearchPreferences::saveActiveFilterset($tab, $idFilterset);
		}

			// Filterset given? get rel. conditions, render and init widget area
		if( $idFilterset !== 0 ) {
			$filterSet	= TodoyuFiltersetManager::getFilterset($idFilterset);
			$conditions	= $filterSet->getConditions();
//			$conditions	= TodoyuFiltersetManager::getFiltersetConditions($idFilterset);

			TodoyuHeader::sendTodoyuHeader('conjunction', $filterSet->getConjunction());

				// Send widgets
			$content	= TodoyuFilterAreaRenderer::renderWidgetArea($idFilterset);
				// Add JS init for loaded widgets
			$content 	.= TodoyuString::wrapScript('Todoyu.Ext.search.Filter.initConditions(\'' . $tab . '\', ' . json_encode($conditions) . ');');
		} else {
			$content	= 'No widgets';
		}

		return $content;
	}

}

?>