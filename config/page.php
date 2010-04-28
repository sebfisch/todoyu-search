<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions GmbH, Switzerland
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

	// Add add JS inits, menu entry
if( allowed('search', 'general:use') ) {
//	TodoyuPage::addJsOnloadedFunction('Todoyu.Ext.search.init.bind(Todoyu.Ext.search)', 100);

		// Menu entries
	if( allowed('search', 'general:area') ) {
		TodoyuFrontend::addMenuEntry('search', 'LLL:search.page.title', '?ext=search', 50);

			// Add filtertypes as submenu
		$filterTypes= TodoyuSearchManager::getFilters();
		$filterTypes= TodoyuArray::sortByLabel($filterTypes, 'position');

		foreach($filterTypes as $type => $typeConfig) {
			TodoyuFrontend::addSubmenuEntry('search', 'search' . ucfirst($typeConfig['key']), $typeConfig['config']['label'], '?ext=search&tab=' . $typeConfig['key'], $typeConfig['config']['position']+100);
		}
	}

		// Add quick search headlet
	TodoyuHeadManager::addHeadlet('TodoyuHeadletQuickSearch');
}

?>