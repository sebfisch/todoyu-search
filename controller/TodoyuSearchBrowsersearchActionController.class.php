<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions GmbH, Switzerland
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
class TodoyuSearchBrowsersearchActionController extends TodoyuActionController {

	public function xmlAction(array $params) {
		$tmpl	= 'ext/search/view/opensearch.tmpl';
		$data	= array(
			'iconUrl'	=> TODOYU_URL . '/favicon.ico',
			'searchUrl'	=> TODOYU_URL . '/index.php?ext=search&amp;controller=browsersearch&amp;action=search&amp;q={searchTerms}',
			'suggestUrl'=> TODOYU_URL . '/index.php?ext=search&amp;controller=browsersearch&amp;action=suggest&amp;q={searchTerms}'
		);

		TodoyuHeader::sendHeaderXML();

		return render($tmpl, $data);
	}

	public function searchAction(array $params) {

		return 'test 123';
	}

	public function suggestAction(array $params) {
		$q			= trim($params['q']);
		$keywords	= TodoyuArray::trimExplode(' ', $q, true);

		$results	= TodoyuTaskSearch::getSuggestions($keywords);
		$suggestions= array();

		foreach($results as $result) {
			$suggestions[] = 'Task: ' . $result['labelTitle'];
		}

		$response	= array(
			$q,
			$suggestions
		);

		TodoyuHeader::sendHeaderJSON();

		return json_encode($response);
	}
}

?>