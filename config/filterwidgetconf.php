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

Todoyu::$CONFIG['EXT']['search']['widgettypes']['textinput'] = array(
	'tmpl'			=> 'ext/search/view/filterwidgets/filterwidget-textinput.tmpl',
	'configFunc'	=> 'TodoyuFilterWidgetManager::manipulateAutocompleteDefinitions'
);

Todoyu::$CONFIG['EXT']['search']['widgettypes']['dateinput'] = array(
	'tmpl'			=> 'ext/search/view/filterwidgets/filterwidget-dateinput.tmpl',
);

Todoyu::$CONFIG['EXT']['search']['widgettypes']['checkbox'] = array(
	'tmpl'			=> 'ext/search/view/filterwidgets/filterwidget-checkbox.tmpl'
);

Todoyu::$CONFIG['EXT']['search']['widgettypes']['select'] = array(
	'tmpl'			=> 'ext/search/view/filterwidgets/filterwidget-select.tmpl',
	'configFunc'	=> 'TodoyuFilterWidgetManager::prepareSelectionOptions'
);

?>