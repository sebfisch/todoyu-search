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
		$base =& $GLOBALS['CONFIG']['FILTERS'][strtoupper($type)]['config'];

		TodoyuDebug::printInFirebug($base);

		return is_null($key) ? $base : $base[$key];
	}

}

?>