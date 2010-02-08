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

		$config		= TodoyuFilterManager::getFilterConfig($type, $widgetName);

			// Add default negation labels if negation is just true
		if( gettype($config['wConf']['negation']) === 'string' ) {
			$config['wConf']['negation'] = array(
				'labelTrue'	=> 'LLL:search.negation.' . $config['wConf']['negation'] . '.true',
				'labelFalse'=> 'LLL:search.negation.' . $config['wConf']['negation'] . '.false'
			);
		}

			// If no configuration available, log
		if( sizeof($config) === 0 ) {
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
	public static function getWidgetTypeConfig($type) {
		return TodoyuArray::assure($GLOBALS['CONFIG']['EXT']['search']['widgettypes'][$type]);
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
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public static function getExtendedWidgetConfig($type, $widgetKey, $widgetName = 'new1', $value = '', $negate = false) {
		$config		= self::getWidgetConfig($type, $widgetKey);

		$extend		= array(
			'widgetID'				=> $widgetKey . '-' . $widgetName,
			'widgetDefinitions'		=> self::getWidgetTypeConfig($config['widget']),
			'widgetFilterName'		=> $widgetKey,
			'value'					=> $value,
			'negate'				=> $negate
		);

		$config = array_merge($config, $extend);

		if( TodoyuDiv::isFunctionReference($config['widgetDefinitions']['configFunc']) ) {
			$config = TodoyuDiv::callUserFunction($config['widgetDefinitions']['configFunc'], $config);
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

		$definitions['widgetDefinitions'] = self::getWidgetTypeConfig($definitions['widget']);

			// Create id for the widget
		$definitions['widgetID'] = $widgetName . '-' . $numOfWidget;

			// Add filtername to widget
		$definitions['widgetFilterName'] = $widgetName;

			// Add value to definitions
		$definitions['value'] = $value;

			// Add negate value to definitions
		$definitions['negate'] = $negate;

		if( TodoyuDiv::isFunctionReference($definitions['widgetDefinitions']['configFunc']) ) {
			$definitions = TodoyuDiv::callUserFunction($definitions['widgetDefinitions']['configFunc'], $definitions);
		}

		return $definitions;
	}



	/**
	 * Checks if the given widget template exists
	 *
	 * @param	Array	$widgetDefinitions
	 * @return	Mixed	String / Boolean
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
	public static function getAutocompletionResults($type, $sword, $widgetKey) {
		$widgetKeyArray	= explode('-', $widgetKey);

		$widgetName		= $widgetKeyArray[0];
		$numOfWidget	= $widgetKeyArray[1];

		$definitions = self::getFilterWidgetDefinitions($type, $widgetName, $numOfWidget);

		$funcRefString = $definitions['wConf']['FuncRef'];
		$funcRefParams = TodoyuArray::assure($definitions['wConf']['FuncParams']);

		TodoyuDebug::printInFirebug($definitions);

		if( TodoyuDiv::isFunctionReference($funcRefString) ) {
			$data = TodoyuDiv::callUserFunction($funcRefString, $sword, $funcRefParams);
		} else {
			Todoyu::log('Invalid AC-callback function', LOG_LEVEL_ERROR, array('widget'=>$widgetName, 'acFunc'=>$funcRefString));
			$data = array();
		}

		return array(
			'widgetID' => $widgetIDParam,
			'results' => $data
		);
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

		if( TodoyuDiv::isFunctionReference($optionMethod) )	{
			$definitions = TodoyuDiv::callUserFunction($optionMethod, $definitions);
		}

		return $definitions;
	}



	/**
	 * Prepare user role filter widget: get available user roles for selector
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function prepareProjectrole($definitions) {
		$userroles	= TodoyuUserroleManager::getAllUserroles();

			// Add userrole options
		foreach($userroles as $userrole) {
			$definitions['options'][]=	array(
				'label'	=> $userrole['title'],
				'value'	=> $userrole['id'],
			);
		}

			// Prepare seperate values
		$values	= explode(':', $definitions['value']);
		$definitions['valueUser'] 		= intval($values[0]);
		$definitions['valueUserLabel']	= TodoyuUserManager::getLabel($values[0]);
		$definitions['valueUserroles']	= TodoyuArray::intExplode(',', $values[1], true, true);

			// Add JS config
		$definitions['specialConfig'] = json_encode(array('acOptions' => array('afterUpdateElement' => 'Todoyu.Ext.project.Filter.onProjectroleUserAcSelect')));

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
		$optionMethod = $definitions['wConf']['LabelFuncRef'];

		if( $definitions['wConf']['autocomplete'] == true && intval($definitions['value']) > 0 )	{
			if( TodoyuDiv::isFunctionReference($optionMethod) )	{
				$definitions = TodoyuDiv::callUserFunction($optionMethod, $definitions);
				//self::proceedLabelFunc($definitions);
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

		return $GLOBALS['CONFIG']['FILTERS'][strtoupper($filterType)]['widgets'][$widgetName]['wConf']['negation'][$label];
	}



	/**
	 * Proceeds the label function
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	protected static function proceedLabelFunc($definitions)	{
		$methodString = $definitions['wConf']['LabelFuncRef'];

		if( TodoyuDiv::isFunctionReference($methodString) )	{
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

		$definitions	= TodoyuArray::assure($GLOBALS['CONFIG']['FILTERS'][$filterType]['widgets'][$widgetName]);

		if( sizeof($definitions) === 0 ) {
			Todoyu::log('Widget definitions not found', LOG_LEVEL_ERROR, array($filterType,$widgetName));
		}

		return $definitions;
	}

}

?>