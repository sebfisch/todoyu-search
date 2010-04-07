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

class TodoyuHeadletQuickSearch extends TodoyuHeadletTypeOverlay {

	/**
	 * Initialize quick search headlet (set template, set initial data)
	 */
	protected function init() {
		$this->setJsHeadlet('Todoyu.Ext.search.Headlet.QuickSearch');

		$this->setVisibleStatus();
	}



	/**
	 * Set visible status for headlet
	 *
	 */
	private function setVisibleStatus() {
		$open	= TodoyuSearchPreferences::getPref('headletOpen');

		if( intval($open) === 1 ) {
			$this->setVisible();
		}
	}



	/**
	 * Render headlet: searchword input and list of search engines
	 *
	 * @return	String
	 */
	protected function renderOverlayContent() {
		$tmpl	= 'ext/search/view/headlet-quicksearch.tmpl';
		$data	= array(
			'id'			=> $this->getID(),
			'searchModes'	=> TodoyuSearchManager::getEngines(),
			'query'			=> $this->params['query']
		);

		return render($tmpl, $data);
	}



	/**
	 * Get headlet label
	 *
	 * @return	String
	 */
	public function getLabel() {
		return Label('search.headlet.label');
	}

}

?>