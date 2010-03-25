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
 * Extension main file for project extension
 *
 * @package		Todoyu
 * @subpackage	Search
 */

	// Declare ext ID, path
define('EXTID_SEARCH', 115);
define('PATH_EXT_SEARCH', PATH_EXT . '/search');

	// Register module locales
TodoyuLanguage::register('search', PATH_EXT_SEARCH . '/locale/ext.xml');
TodoyuLanguage::register('panelwidget-searchfilterlist', PATH_EXT_SEARCH . '/locale/panelwidget-searchfilterlist.xml');

	// Request configurations
require_once( PATH_EXT_SEARCH . '/config/extension.php' );
require_once( PATH_EXT_SEARCH . '/config/filterwidgetconf.php' );
require_once( PATH_EXT_SEARCH . '/config/panelwidgets.php' );

?>