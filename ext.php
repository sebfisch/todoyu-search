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

/**
 * Extension main file for project extension
 *
 * @package		Todoyu
 * @subpackage	Search
 */

if( ! defined('TODOYU') ) die('NO ACCESS');



	// Declare ext ID, path
define('EXTID_SEARCH', 115);
define('PATH_EXT_SEARCH', PATH_EXT . '/search');

	// Register module locales
TodoyuLocale::register('search', PATH_EXT_SEARCH . '/locale/ext.xml');
TodoyuLocale::register('panelwidget-searchfilterlist', PATH_EXT_SEARCH . '/locale/panelwidget-searchfilterlist.xml');

	// Request configurations
require_once( PATH_EXT_SEARCH . '/config/extension.php' );
require_once( PATH_EXT_SEARCH . '/config/filterwidgetconf.php' );
require_once( PATH_EXT_SEARCH . '/config/panelwidgets.php' );

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

	$entries 	= TodoyuFilterBase::getPossibleFilterTypes();
	$entryNum	= 100;
	foreach($entries as $filterType	=> $filterData) {
		TodoyuFrontend::addSubmenuEntry('search', 'search', $filterData['config']['label'], '?ext=search&tab=' . $filterType, $entryNum);
		$entryNum++;
	}

	TodoyuPage::addJsOnloadedFunction('Todoyu.Ext.search.init.bind(Todoyu.Ext.search)');
}

?>