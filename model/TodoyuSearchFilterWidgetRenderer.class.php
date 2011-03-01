<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2011, snowflake productions GmbH, Switzerland
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
class TodoyuSearchFilterWidgetRenderer {

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
		$config	= TodoyuSearchFilterWidgetManager::getExtendedWidgetConfig($type, $widgetKey, $widgetName, $value, $negate);

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

}

?>