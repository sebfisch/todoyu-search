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

class TodoyuSearchExtActionController extends TodoyuActionController {

	public function defaultAction(array $params) {
		TodoyuFrontend::setActiveTab('search');

		TodoyuPage::init('ext/search/view/ext.tmpl');
		TodoyuPage::setTitle('LLL:search.page.title');

			// Add assets
		TodoyuPage::addExtAssets('search', 'public');

		$activeTab	= $params['tab'];

		if( ! empty($activeTab) ) { 	// If tab is set manualy
			$filters 	= isset($params['filters']) ? json_decode($params['filters'], true) : array();

			$idFilterset= 0;
			$conditions = TodoyuSearchManager::convertSimpleToFilterConditionArray($filters);
		} else {	// Normal preferences rendering
			$activeTab	= TodoyuSearchPreferences::getActiveTab();
			$idFilterset= TodoyuSearchPreferences::getActiveFilterset($activeTab);

			if( $idFilterset !== 0 ) {
				$conditions	= TodoyuFilterConditionManager::getFiltersetConditions($idFilterset);
			} else {
				$conditions	= array();
			}
		}

			// panel widgets
		$panelWidgets = TodoyuSearchRenderer::renderPanelWidgets();
		TodoyuPage::set('panelWidgets', $panelWidgets);

			// Filter area
		$filterArea = TodoyuFilterAreaRenderer::renderFilterArea($activeTab, $idFilterset, $conditions, false);
		TodoyuPage::set('filterArea', $filterArea);

			// Add JS init command
		TodoyuPage::addJsOnloadedFunction('Todoyu.Ext.search.Filter.init.bind(Todoyu.Ext.search.Filter, \'' . $activeTab . '\', \'' . $idFilterset . '\', ' . json_encode($conditions) . ')');

			// Add assets of all search types
		$assets = TodoyuFilterBase::getTypesAssets();
		foreach($assets as $assetArray)	{
			TodoyuPage::addExtAssets($assetArray['ext'], $assetArray['type']);
		}

		return TodoyuPage::render();
	}
}

?>