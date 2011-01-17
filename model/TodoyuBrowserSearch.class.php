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
 * [Enter Class Description]
 *
 * @package		Todoyu
 * @subpackage	[Subpackage]
 */
class TodoyuBrowserSearch {

	public static function addPageLinkTag() {
		$title		= 'todoyu';
		$xmlPath	= 'index.php?ext=search&controller=browsersearch&action=xml';
		$linkTag	= '<link rel="search" type="application/opensearchdescription+xml" title="' . $title . '" href="' . $xmlPath . '" />';

		TodoyuPage::addAdditionalHeaderData($linkTag);
	}


	/**
	 * Hook
	 * If not logged in and request is a browser search, send back an empty json array to prevent errors in the browser
	 *
	 * @param	Array		$requestVars
	 * @param	Array		$originalRequestVars
	 * @return	Array
	 */
	public static function hookNotLoggedIn(array $requestVars, array $originalRequestVars) {
		if( ! TodoyuAuth::isLoggedIn() && $requestVars['ext'] === 'search' && $requestVars['ctrl'] === 'browsersearch' ) {
			TodoyuHeader::sendTypeJSON();
			echo json_encode(array());
			exit();
		}

		return $requestVars;
	}

}

?>