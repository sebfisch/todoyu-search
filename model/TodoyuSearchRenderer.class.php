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
 * Search renderer
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchRenderer extends TodoyuRenderer {

	/**
	 * @var	String		Extension key
	 */
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
		return TodoyuPanelWidgetRenderer::renderPanelWidgets(self::EXTKEY);
	}



	/**
	 * Renders the search results.
	 *
	 * - reads the active filter by url-parameter or preset
	 * - reads the active tab (filtertype) by url-parameter or preset
	 * - renders the filtered results by defined render function
	 *
	 * @param	String		$activeTab
	 * @param	Integer		$idFilterset
	 * @param	Boolean		$useConditions
	 * @param	Array		$filterConditions
	 * @param	String		$conjunction			AND / OR
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

		if ( TodoyuFunction::isFunctionReference($renderFunction) ) {
			$content	= TodoyuFunction::callUserFunction($renderFunction, $idFilterset, $useConditions, $filterConditions, $conjunction);
		}

		$tmpl	= 'ext/search/view/search-results.tmpl';
		$data 	= array(
			'activeTab'		=> $activeTab,
			'searchResults'	=> $content
		);

		return render($tmpl, $data);
	}



	/**
	 * Render listing of search results
	 *
	 * @param 	String	$type
	 * @param	Array	$itemIDs
	 * @return	String
	 */
	public static function renderResultsListing($type, array $itemIDs) {
		$renderFunc		= TodoyuFilterManager::getFilterTypeResultsRenderer($type);

		return TodoyuFunction::callUserFunction($renderFunc, $itemIDs);
	}

}
?>