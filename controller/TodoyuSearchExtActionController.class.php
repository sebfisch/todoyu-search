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
 * Search Ext action controller
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchExtActionController extends TodoyuActionController {

	/**
	 * Restrict access
	 *
	 * @param	Array		$params
	 */
	public function init(array $params) {
		Todoyu::restrict('search', 'general:area');
	}



	/**
	 * Render search view
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function defaultAction(array $params) {
		TodoyuFrontend::setActiveTab('search');

		TodoyuPage::init('ext/search/view/ext.tmpl');
		TodoyuPage::setTitle('search.ext.page.title');

			// Get tab parameter
		$activeTab	= $params['tab'];

			// If tab is set manually
		if( ! empty($activeTab) ) {
			$idFilterset= 0;
//			$filters 	= isset($params['filters']) ? json_decode($params['filters'], true) : array();
//			$conditions = TodoyuSearchManager::convertSimpleToFilterConditionArray($filters);
		} else {
				// Normal preferences rendering
			$activeTab	= TodoyuSearchPreferences::getActiveTab();
			$idFilterset= TodoyuSearchPreferences::getActiveFilterset($activeTab);
		}

		$panelWidgets	= TodoyuSearchRenderer::renderPanelWidgets();
		$tabs			= TodoyuSearchFilterAreaRenderer::renderTypeTabs($activeTab);
		$filterArea 	= TodoyuSearchFilterAreaRenderer::renderFilterArea($activeTab, $idFilterset);

		TodoyuPage::setPanelWidgets($panelWidgets);
		TodoyuPage::setTabs($tabs);
		TodoyuPage::set('filterArea', $filterArea);

		return TodoyuPage::render();
	}
}

?>