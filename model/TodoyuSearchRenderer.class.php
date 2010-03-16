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

/**
 * Search renderer
 *
 * @package		Todoyu
 * @subpackage	Search
 */

class TodoyuSearchRenderer extends TodoyuRenderer {

	const EXTKEY = 'search';

	/**
	 * Render search suggestion list
	 *
	 * @param	Array		$suggestions
	 * @return	String
	 */
	public static function renderSuggestions(array $suggestions) {
		$tmpl	= 'ext/search/view/suggest.tmpl';

		return render($tmpl, $suggestions);
	}


	/**
	 * Render headlet searchbox in the toppanel
	 *
	 * @return	String
	 */
	public static function renderHeadlet() {
		$tmpl	= 'ext/search/view/headlet.tmpl';
		$data	= array(
			'query'			=> TodoyuRequest::getParam('query'),
			'searchModes'	=> TodoyuSearch::getSearchModes()
		);

		return render($tmpl, $data);
	}



	/**
	 * Render tab head of search area
	 *
	 * @param	String		$activeTab
	 * @return	String
	 */
	public static function renderInlineTabHead($activeTab = null)	{
		$tabs 		= array();

			// If no tab forced, get preferenced tab
		if( is_null($activeTab) ) {
			$activeTab = TodoyuSearchPreferences::getActiveTab();
		}

		$name		= 'search';
		$jsHandler	= 'Todoyu.Ext.search.Filter.onTabClick.bind(Todoyu.Ext.search.Filter)';

		$tabsArr	= TodoyuSearchManager::getInlineTabHeads();

		foreach($tabsArr as $key => $tab) {
			$tabs[] = array(
				'id'		=> $key,
				'label'		=> $tab['config']['label']
			);
		}

//		$tabs[0]['position'] = 'first';
//		$tabs[sizeof($tabs)-1]['position'] = 'last';

		return TodoyuTabheadRenderer::renderTabs($name, $tabs, $jsHandler, $activeTab);
	}



	/**
	 * Render panel widgets
	 *
	 * @return	String
	 */

	public static function renderPanelWidgets() {
		$params	= array();

		return TodoyuPanelWidgetRenderer::renderPanelWidgets('search', $params);

	}



	/**
	 * Renders the search results.
	 *
	 * - reads the active filter by url-parameter or preset
	 * - reads the active tab (filtertype) by url-parameter or preset
	 * - renders the filtered results by defined render function
	 *
	 * @return	String
	 */
	public static function renderSearchResults($activeTab = null, $idFilterset = 0, $useConditions = true, array $filterConditions = array(), $conjunction = 'AND')	{
		$idFilterset	= intval($idFilterset);
		$conjunction	= strtoupper($conjunction) === 'OR' ? 'OR' : 'AND';

			// Find current tab if not given as parameter
		if( is_null($activeTab) ) {
			$activeTab = TodoyuSearchPreferences::getActiveTab();
		}
			// Get active filter if not given as parameter
		if( $idFilterset === 0 ) {
			$idFilterset = TodoyuSearchPreferences::getActiveFilterset($activeTab);
		}

			// Get render function
		$renderFunction	= TodoyuFilterBase::getFilterRenderFunction($activeTab);

		if ( TodoyuDiv::isFunctionReference($renderFunction) ) {
			$content	= TodoyuDiv::callUserFunction($renderFunction, $idFilterset, $useConditions, $filterConditions, $conjunction);
		}

		$tmpl	= 'ext/search/view/search-results.tmpl';
		$data 	= array(
			'activeTab'		=> $activeTab,
			'searchResults'	=> $content
		);

		return render($tmpl, $data);
	}


	public static function renderResultsListing($type, array $itemIDs) {
		$renderFunc		= TodoyuFilterManager::getFilterTypeResultsRenderer($type);

		return TodoyuDiv::callUserFunction($renderFunc, $itemIDs);
	}

}
?>