<?php

class TodoyuFilterManager {

	public static function getFilterConfig($type, $name) {
		$base	=& $GLOBALS['CONFIG']['FILTERS'][$type];
		$config	= false;

		if( is_array($base['filters'][$name]) ) {
			$config	= $base['filters'][$name];
		} elseif( is_array($base['widgets'][$name]) ) {
			$config	= $base['widgets'][$name];
		}

		return $config;
	}


	public static function getFilterTypeConfig($type, $key = null) {
		TodoyuExtensions::loadAllFilters();

		$base =& $GLOBALS['CONFIG']['FILTERS'][strtoupper($type)]['config'];

		TodoyuDebug::printInFirebug($base);

		return is_null($key) ? $base : $base[$key];
	}


	public static function getFilterTypes($sort = false) {
		TodoyuExtensions::loadAllFilters();

		$types	= array_keys($GLOBALS['CONFIG']['FILTERS']);

		if( $sort ) {
			$sorting = array();

			foreach($types as $type) {
				$sorting[$GLOBALS['CONFIG']['FILTERS'][$type]['config']['position']] = $type;
			}

			ksort($sorting);

			$types = $sorting;
		}

		return $types;
	}



	/**
	 * Get filter class for a type
	 *
	 * @param	String		$type
	 * @return	String
	 */
	public static function getFilterTypeClass($type) {
		return $GLOBALS['CONFIG']['FILTERS'][strtoupper($type)]['config']['class'];
	}


	public static function getFilterTypeLabel($type) {
		return TodoyuLanguage::getLabel($GLOBALS['CONFIG']['FILTERS'][strtoupper($type)]['config']['label']);
	}

}

?>