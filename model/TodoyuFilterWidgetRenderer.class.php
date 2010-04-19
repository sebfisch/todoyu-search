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
 * Filter widget renderer
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuFilterWidgetRenderer {

	/**
	 * Render a filter widget
	 *
	 * @param	String		$type
	 * @param	String		$widgetKey
	 * @param	String		$widgetName
	 * @param	Mixed		$value
	 * @param	Boolean		$negate
	 * @return	String
	 */
	public static function renderWidget($type, $widgetKey, $widgetName = 'new1', $value = '', $negate = false) {
		$config	= TodoyuFilterWidgetManager::getExtendedWidgetConfig($type, $widgetKey, $widgetName, $value, $negate);

		$tmpl	= $config['widgetDefinitions']['tmpl'];
		$data	= array(
			'definitions' => $config
		);

		if( is_null($tmpl) ) {
			Todoyu::log('Missing widget template (' . $type . '/' . $widgetKey . ')');
			return '';
		}

		return render($tmpl, $data);
	}



	/**
	 * Render suggestions of the autocompleter
	 *
	 * @param	Array	$results
	 * @return	String
	 */
	public static function renderAutocompletion($results)	{
		$tmpl = 'ext/search/view/filterwidget-suggest.tmpl';

		$empty = count($results) > 0 ? false:true;
		$data = array('results'		=> $results['results'],
					  'widgetID'	=> $results['widgetID'],
					  'empty'		=> $empty);

		return render($tmpl, $data);
	}

}

?>