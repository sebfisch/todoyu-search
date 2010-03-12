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

class TodoyuHeadletQuickSearch extends TodoyuHeadletTypeOverlay {

	/**
	 * Initialize quick search headlet (set template, set initial data)
	 */
	protected function init() {
		$this->setJsHeadlet('Todoyu.Ext.search.Headlet.QuickSearch');

		TodoyuPage::addExtAssets('search', 'headlet-quicksearch');
	}


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
		return 'Todoyu durchsuchen';
	}

}

?>