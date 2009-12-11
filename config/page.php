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

	// Add menu entry, add JS inits
if( allowed('search', 'use') ) {
	TodoyuFrontend::addMenuEntry('search', 'LLL:search.page.title', '?ext=search', 99);

		// Add filtertypes as submenu
	$filterTypes= TodoyuSearchManager::getFilters();
	$filterTypes= TodoyuArray::sortByLabel($filterTypes, 'position');

	foreach($filterTypes as $type => $typeConfig) {
		TodoyuFrontend::addSubmenuEntry('search', 'search' . ucfirst($typeConfig['key']), $typeConfig['config']['label'], '?ext=search&tab=' . $typeConfig['key'], $typeConfig['config']['position']+100);
	}

	TodoyuPage::addJsOnloadedFunction('Todoyu.Ext.search.init.bind(Todoyu.Ext.search)');
}

?>