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
 * Manager for the filterwidgets
 *
 */

class TodoyuFilterWidgetManager	{

	/**
	 * Get configuration array for a widget
	 *
	 * @param	String		$type
	 * @param	String		$widgetName
	 * @return	Array
	 */
	public static function getWidgetConfig($type, $widgetName) {
		TodoyuExtensions::loadAllFilters();

		$type		= strtoupper(trim($type));
		$widgetName	= trim($widgetName);

		$config		= $GLOBALS['CONFIG']['FILTERS'][$type]['config']['filterWidgets'][$widgetName];

		if( ! is_array($config) ) {
			$config = array();
			Todoyu::log('Filter widget not found', LOG_LEVEL_ERROR, array($type, $widgetName));
		}

		return $config;
	}


	/**
	 * Get type configuration for a field type of a widget
	 *
	 * @param	String		$fieldType
	 * @return	Array
	 */
	public static function getFieldTypeWidgetConfig($fieldType) {
		return $GLOBALS['CONFIG']['FILTERCONF']['FILTERWIDGETS'][$fieldType];
	}



	/**
	 * Get extended widget configuration
	 * Extends the normal widget config with:
	 * - widgetID
	 * - widgetDefinitions
	 * - widgetFilterName
	 * - value
	 * - negate
	 *
	 * @param	String		$type
	 * @param	String		$widgetKey
	 * @param	String		$widgetName
	 * @param	Mixed		$value
	 * @param	Bool		$negate
	 * @return	Array
	 */
	public static function getExtendedWidgetConfig($type, $widgetKey, $widgetName = 'new1', $value = '', $negate = false) {
		$config		= self::getWidgetConfig($type, $widgetKey);

		$extend		= array(
			'widgetID'				=> $widgetKey . '-' . $widgetName,
			'widgetDefinitions'		=> self::getFieldTypeWidgetConfig($config['widget']),
			'widgetFilterName'		=> $widgetKey,
			'value'					=> $value,
			'negate'				=> $negate
		);

		$config = array_merge($config, $extend);

		$config	= self::processConfigFunction($config);

		return $config;
	}



	/**
	 * Call widget config processing function if defined
	 * Return the modified config array
	 *
	 * @param	Array		$config
	 * @return	Array
	 */
	public static function processConfigFunction(array $config) {
		$funcRef	= $config['widgetDefinitions']['customDefinitionProcFunc'];

		if( ! is_null($funcRef) ) {
			$config = TodoyuDiv::callUserFunction($funcRef, $config);
		}

		return $config;
	}



	/**
	 * perpares the definition of a filterwidget
	 *
	 * @param	String	$filterType
	 * @param	String	$widgetName
	 * @param	String	$numOfWidget
	 * @param	Mixed	$value
	 * @return	Array
	 */
	public static function getFilterWidgetDefinitions($filterType, $widgetName, $numOfWidget, $value = '', $negate = false)	{
		$definitions = self::getFilterDefinitionsArray($filterType, $widgetName);

		$definitions['widgetDefinitions'] = self::getWidgetDefinitionsArray($definitions['widget']);

			// Create id for the widget
		$definitions['widgetID'] = $widgetName.'-'.$numOfWidget;

			// Add filtername to widget
		$definitions['widgetFilterName'] = $widgetName;

			// Add value to definitions
		$definitions['value'] = $value;

			// Add negate value to definitions
		$definitions['negate'] = $negate;

		$definitions = TodoyuFilterWidgetManager::checkOnCustomDefinitionProcFunc($definitions);

		return $definitions;
	}



	/**
	 * Checks if the given widget template exists
	 *
	 * @param	Array	$widgetDefinitions
	 * @return	Mixed	String / Bool
	 */
	public static function checkOnWidgetTemplate($widgetDefinitions)	{
		$file = $widgetDefinitions['widgetDefinitions']['tmpl'];

		return is_file($file) ? $file : false;
	}



	/**
	 * handles the autocompletion input.
	 *
	 * @return	Array
	 */
	public static function handleAutocompletion()	{
		$widgetIDParam	= TodoyuRequest::getParam('completionID');
		$filterType		= TodoyuRequest::getParam('filtertype');
		$searchWord		= TodoyuRequest::getParam('sword');

		$widgetIDArray = split('-', $widgetIDParam);

		$widgetName		= $widgetIDArray[0];
		$numOfWidget	= $widgetIDArray[1];

		$definitions = self::getFilterWidgetDefinitions($filterType, $widgetName, $numOfWidget);

		$funcRefString = $definitions['wConf']['FuncRef'];
		$funcRefParams = $definitions['wConf']['FuncParams'];

		if( TodoyuDiv::isFunctionReference($funcRefString) ) {
			$data = TodoyuDiv::callUserFunction($funcRefString, $searchWord, $funcRefParams);
		} else {
			Todoyu::log('Invalid AC-callback function', LOG_LEVEL_ERROR, array('widget'=>$widgetName, 'acFunc'=>$funcRefString));
			$data = array();
		}

		return array('widgetID' => $widgetIDParam, 'results' => $data);
	}



	/**
	 * Handles the option func of every select-filter-widget.
	 * Function given from config array.
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public function prepareSelectionOptions($definitions)	{
		$optionMethod = $definitions['wConf']['FuncRef'];

		if(TodoyuDiv::isFunctionReference($optionMethod))	{
			$definitions = TodoyuDiv::callUserFunction($optionMethod, $definitions);
		}

		return $definitions;
	}



	/**
	 * handles the given manipulation function for autocompletion to set the correct label
	 *
	 * defined in filters config (LabelFuncRef)
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public function manipulateAutocompleteDefinitions($definitions)	{
		if($definitions['wConf']['autocomplete'] == true && intval($definitions['value']) > 0)	{
			if($definitions['wConf']['LabelFuncRef'])	{
				$definitions = self::proceedLabelFunc($definitions);
			}
		}

		return $definitions;
	}



	/**
	 * Gets Negation labels
	 *
	 * @param	String	$widgetName
	 * @param	String	$label
	 * @return string
	 */
	public static function getFilterWidgetNegationLabel($widgetName, $label)	{
		$filterType = TodoyuSearchPreferences::getCurrentTab();

		return $GLOBALS['CONFIG']['FILTERS'][strtoupper($filterType)]['config']['filterWidgets'][$widgetName]['wConf']['negation'][$label];
	}



	/**
	 * Proceeds the label function
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	protected static function proceedLabelFunc($definitions)	{
		$methodString = $definitions['wConf']['LabelFuncRef'];

		if(TodoyuDiv::checkOnMethodString($methodString))	{
			$definitions = TodoyuDiv::callUserFunction($methodString, $definitions);
		}

		return $definitions;
	}



	/**
	 * Gets the filter definitions
	 *
	 * @param	String	$filterType
	 * @param	String	$widgetName
	 * @return	Array
	 */
	protected static function getFilterDefinitionsArray($filterType, $widgetName) {
		TodoyuExtensions::loadAllFilters();

		$filterType	= strtoupper(trim($filterType));
		$widgetName	= trim($widgetName);

		$definitions	= $GLOBALS['CONFIG']['FILTERS'][$filterType]['config']['filterWidgets'][$widgetName];

		if( ! is_array($definitions) ) {
			$definitions = array();
			Todoyu::log('Widget definitions not found', LOG_LEVEL_ERROR, array($filterType,$widgetName));
		}

		return $definitions;
	}



	/**
	 * Gets the widget Definitions
	 *
	 * @param	String	$widgetType
	 * @return	Array
	 */
	protected static function getWidgetDefinitionsArray($widgetType)	{
		return $GLOBALS['CONFIG']['FILTERCONF']['FILTERWIDGETS'][$widgetType];
	}



	/**
	 * Checks if any manipulation function for the definition is given for the current filter-widget
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	protected static function checkOnCustomDefinitionProcFunc($definitions)	{
		$customDefinitionProcFunc = $definitions['widgetDefinitions']['customDefinitionProcFunc'];
		
		if( TodoyuDiv::isFunctionReference($customDefinitionProcFunc) ) {
			$definitions = TodoyuDiv::callUserFunction($customDefinitionProcFunc, $definitions);
		}
		
		return $definitions;
	}
}

?>