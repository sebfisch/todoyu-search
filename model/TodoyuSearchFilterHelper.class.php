<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2012, snowflake productions GmbH, Switzerland
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
 * Various filter helper functions
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchFilterHelper {

	/**
	 * Get query parts for date field filtering
	 *
	 * @param	String		$tables
	 * @param	String		$field
	 * @param	Integer		$time
	 * @param	Boolean		$negate
	 * @return	Array|Boolean			Query parts array / false if no date timestamp given (or 1.1.1970 00:00)
	 */
	public static function getDateFilterQueryparts($tables, $field, $time, $negate = false) {
		$queryParts	= false;

		if( $time !== 0 ) {
			$info	= self::getTimeAndLogicForDate($time, $negate);

			$queryParts = array(
				'tables'=> $tables,
				'where'	=> $field . ' ' . $info['logic'] . ' ' . $info['timestamp']
			);
		}

		return $queryParts;
	}



	/**
	 * Return timestamp and conjunction logic for date-input queries
	 *
	 * @param	Integer		$timestamp
	 * @param	Boolean		$negate
	 * @return	Array		[timestamp,logic]
	 */
	public static function getTimeAndLogicForDate($timestamp, $negate = false) {
		$timestamp	= intval($timestamp);

		if( $negate ) {
			$info	= array(
				'timestamp'	=> TodoyuTime::getDayStart($timestamp),
				'logic'		=> '>='
			);
		} else {
			$info	= array(
				'timestamp'	=> TodoyuTime::getDayEnd($timestamp),
				'logic'		=> '<='
			);
		}

		return $info;
	}

}

?>