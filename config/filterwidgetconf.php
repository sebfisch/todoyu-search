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

$GLOBALS['CONFIG']['FILTERCONF']['FILTERWIDGETS'] = array(
	'textinput'	=> array(
		'class'						=> 'filterWidgetTextInput',
		'tmpl'						=> 'ext/search/view/filterwidgets/filterwidget-textinput.tmpl',
		'customDefinitionProcFunc'	=> 'TodoyuFilterWidgetManager::manipulateAutocompleteDefinitions'
	),
	'dateinput'	=> array(
		'class'						=> 'filterWidgetDateInput',
		'tmpl'						=> 'ext/search/view/filterwidgets/filterwidget-dateinput.tmpl',
	),
	'checkbox'	=> array(
		'class'						=> 'filterWidgetCheckbox',
		'tmpl'						=> 'ext/search/view/filterwidgets/filterwidget-checkbox.tmpl'
	),
	'select'						=> array(
		'class'						=> 'filterWidgetSelect',
		'tmpl'						=> 'ext/search/view/filterwidgets/filterwidget-select.tmpl',
		'customDefinitionProcFunc'	=> 'TodoyuFilterWidgetManager::prepareSelectionOptions'
	)
);

?>