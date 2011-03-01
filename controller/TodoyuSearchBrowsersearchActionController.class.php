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
 * Search browsersearch action controller
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchBrowsersearchActionController extends TodoyuActionController {

	/**
	 * @param	Array	$params
	 * @return	String
	 */
	public function xmlAction(array $params) {
		$tmpl	= 'ext/search/view/opensearch.tmpl';
		$data	= array(
			'iconUrl'	=> TODOYU_URL . '/favicon.ico',
			'searchUrl'	=> TODOYU_URL . '/index.php?ext=search&amp;controller=browsersearch&amp;action=search&amp;q={searchTerms}',
			'suggestUrl'=> TODOYU_URL . '/index.php?ext=search&amp;controller=browsersearch&amp;action=suggest&amp;q={searchTerms}'
		);

		TodoyuHeader::sendTypeXML();

		return render($tmpl, $data);
	}



	/**
	 * @todo	implement or remove
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function searchAction(array $params) {
		return 'test 123';
	}



	/**
	 * @todo	comment
	 *
	 * @param	Array	$params
	 * @return	String				JSON encoded suggestions to query
	 */
	public function suggestAction(array $params) {
		$q			= trim($params['q']);
		$keywords	= TodoyuArray::trimExplode(' ', $q, true);

		$results	= TodoyuProjectTaskSearch::getSuggestions($keywords);
		$suggestions= array();

		foreach($results as $result) {
			$suggestions[] = 'Task: ' . $result['labelTitle'];
		}

		$response	= array(
			$q,
			$suggestions
		);

		TodoyuHeader::sendTypeJSON();

		return json_encode($response);
	}
}

?>