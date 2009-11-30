<?php

	// load assets which are registered in the filters array.
	// its mandatory to load them to render the filterlist correctly
$assets = TodoyuFilterBase::getTypesAssets();
foreach($assets as $assetArray)	{
	TodoyuPage::addExtAssets($assetArray['ext'], $assetArray['type']);
}

	// Add assets
TodoyuPage::addExtAssets('search', 'public');

	// Add menu entry, add JS inits
if( TodoyuAuth::isLoggedIn() ) {
	TodoyuFrontend::addMenuEntry('search', 'LLL:search.page.title', '?ext=search', 99);

		// Add filtertypes as submenu
	$filterTypes= TodoyuSearchManager::getFilters();
	$filterTypes= TodoyuArray::sortByLabel($filterTypes, 'position');

	foreach($filterTypes as $type => $typeConfig) {
		TodoyuFrontend::addSubmenuEntry('search', 'search', $typeConfig['config']['label'], '?ext=search&tab=' . $typeConfig['key'], $typeConfig['config']['position']+100);
	}

	TodoyuPage::addJsOnloadedFunction('Todoyu.Ext.search.init.bind(Todoyu.Ext.search)');
}

?>