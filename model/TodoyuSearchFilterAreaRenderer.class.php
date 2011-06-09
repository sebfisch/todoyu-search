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
 * Filter area renderer
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchFilterAreaRenderer {

	/**
	 * Render whole filter area: contains tabs, control, widget area and search results
	 *
	 * @param	String		$activeTab		Active tab/filter type
	 * @param	Integer		$idFilterset	Active filterset
	 * @param	Array		$conditions		Custom conditions instead of a stored filterset
	 * @param	Boolean		$init			Add init script at the bottom of loaded with AJAX
	 * @return	String
	 */
	public static function renderFilterArea($activeTab, $idFilterset = 0, array $conditions = array(), $init = true) {
		$idFilterset= intval($idFilterset);

			// If no filterset and conditions set, check for preset filterset
		if( $idFilterset === 0 && sizeof($conditions) === 0 ) {
			$idFilterset = TodoyuSearchPreferences::getActiveFilterset($activeTab);
		}

			// If filterset is set, get filterset conjunction
		if( $idFilterset !== 0 ) {
			$filterset	= TodoyuSearchFiltersetManager::getFiltersetRecord($idFilterset);
			$conjunction= $filterset['conjunction'];
			$conditions	= TodoyuSearchFilterConditionManager::getFiltersetConditions($idFilterset);
		} else {
			$conjunction= 'AND';
		}

			// Render controls
		$controls	= self::renderControls($activeTab, $idFilterset);

			// Render filterset widgets
		if( $idFilterset !== 0 ) {
			$widgetArea	= self::renderWidgetArea($idFilterset);
		} elseif( sizeof($conditions) > 0 ) {
			# render conditions here
		}

			// If filterset or conditions are defined, render search results
		if( $idFilterset !== 0 || sizeof($conditions) > 0 ) {
			$results	= self::renderResults($activeTab, $idFilterset, $conditions, $conjunction);
		}

		$tmpl	= 'ext/search/view/filter-area.tmpl';
		$data	= array(
			'controls'		=> $controls,
			'activeWidgets'	=> $widgetArea,
			'actionpanel'	=> TodoyuSearchActionPanelManager::renderActionPanel($activeTab),
			'searchResults'	=> $results
		);

			// If init necessary (AJAX), add it to the response
		if( $init ) {
			$data['init'] = 'Todoyu.Ext.search.Filter.init(\'' . $activeTab . '\', \'' . $idFilterset . '\', ' . json_encode($conditions) . ')';
		}

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render type tabs. Each filter type has its own tab
	 *
	 * @param	String		$activeTab
	 * @return	String
	 */
	public static function renderTypeTabs($activeTab = null) {
		$tabs 		= array();

			// If no tab forced, get preferenced tab
		if( is_null($activeTab) ) {
			$activeTab = TodoyuSearchPreferences::getActiveTab();
		}

		$name		= 'search';
		$jsHandler	= 'Todoyu.Ext.search.Filter.onTabClick.bind(Todoyu.Ext.search.Filter)';

		$filterConf	= TodoyuSearchManager::getFilterConfigs();
		$filterConf	= TodoyuArray::sortByLabel($filterConf, 'position');

		foreach($filterConf as $config) {
			$type = strtolower($config['__key']);
			$tabs[] = array(
				'id'		=> $type,
				'label'		=> $config['label']
			);
		}

		$tabs	= TodoyuArray::sortByLabel($tabs, 'position');

		return TodoyuTabheadRenderer::renderTabs($name, $tabs, $jsHandler, $activeTab);
	}



	/**
	 * Render filter condition controls for a type/tab
	 *
	 * @param	String		$tab
	 * @param	Integer		$idFilterset
	 * @return	String
	 */
	public static function renderControls($tab, $idFilterset = 0) {
		$idFilterset = intval($idFilterset);

			// Find filterset ID if not given
		if( $idFilterset === 0 ) {
			$idFilterset = TodoyuSearchPreferences::getActiveFilterset($tab);
		}

			// Get conjunction from filterset if available
		if( $idFilterset !== 0 ) {
			$filterset	= TodoyuSearchFiltersetManager::getFiltersetRecord($idFilterset);
			$conjunction= $filterset['conjunction'];
		} else {
			$conjunction= 'AND';
		}

			// Get grouped type conditions
		$groupedConditions	= TodoyuSearchFilterConditionManager::getGroupedTypeConditions($tab);

		$tmpl	= 'ext/search/view/filter-action-controls.tmpl';
		$data 	= array(
			'type'				=> $tab,
			'groupedConditions'	=> $groupedConditions,
			'conjunctions'		=> array(
				array(
					'key'	=> 'AND',
					'label'	=> 'search.ext.and'
				),
				array(
					'key'	=> 'OR',
					'label'	=> 'search.ext.or'
				)
			),
			'activeConjunction'	=> $conjunction
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render widget
	 *
	 * @param	Integer		$idFilterset
	 * @return	String
	 */
	public static function renderWidgetArea($idFilterset) {
		$idFilterset= intval($idFilterset);
		$filterset	= TodoyuSearchFiltersetManager::getFiltersetRecord($idFilterset);
		$conditions	= TodoyuSearchFilterConditionManager::getFiltersetConditions($idFilterset);

		$content	= '';

		foreach($conditions as $condition) {
			$content .= TodoyuSearchFilterWidgetRenderer::renderWidget($filterset['type'], $condition['filter'], $condition['id'], $condition['value'], $condition['is_negated']==1);
		}

		return $content;
	}



	/**
	 * Render search results for search area
	 *
	 * @param	String		$type
	 * @param	Integer		$idFilterset
	 * @param	Array		$conditions
	 * @param	String		$conjunction
	 * @return	String
	 */
	public static function renderResults($type = 'TASK', $idFilterset = 0, array $conditions = array(), $conjunction = 'AND') {
		$idFilterset	= intval($idFilterset);
		$conjunction	= strtoupper($conjunction) === 'OR' ? 'OR' : 'AND';
		$hardLimit		= 200;

			// If filterset is given, use its conditions
		if( $idFilterset !== 0 ) {
			$conditions = TodoyuSearchFilterConditionManager::getFilterSetConditions($idFilterset);
		} else {
			$conditions = TodoyuSearchFilterConditionManager::buildFilterConditionArray($conditions);
		}

			// Build filter
		$typeClass	= TodoyuSearchFiltersetManager::getFiltersetTypeClass($type);

		/**
		 * @var	TodoyuProjectTaskFilter	$typeFilter
		 */
		$typeFilter	= new $typeClass($conditions, $conjunction);
		$sorting	= TodoyuSearchFilterManager::getFilterDefaultSorting($type);
		$itemIDs	= array();

		if( $typeFilter->hasActiveFilters() ) {
			$itemIDs	= $typeFilter->getItemIDs($sorting, $hardLimit);
		}

			// Prepare variables
		$numItems	= sizeof($itemIDs);
		$totalItems	= $typeFilter->getTotalItems();
		$resultLabel= self::renderResultInfoText($type, $numItems, $totalItems, $hardLimit);

		$tmpl	= 'ext/search/view/search-results.tmpl';
		$data	= array(
			'resultLabel'	=> $resultLabel,
			'itemsList'		=> TodoyuSearchRenderer::renderResultsListing($type, $itemIDs)
		);

		return Todoyu::render($tmpl, $data);
	}


	/**
	 * Render info text about the number of matching elements in the result
	 *
	 * @param	String		$type
	 * @param	Integer		$numItems
	 * @param	Integer		$totalItems
	 * @param	Integer		$hardLimit
	 * @return	String
	 */
	private static function renderResultInfoText($type, $numItems, $totalItems, $hardLimit) {
		$type		= trim(strtoupper($type));
		$numItems	= intval($numItems);
		$totalItems	= intval($totalItems);
		$hardLimit	= intval($hardLimit);

					// Prepare variables
		$typeLabel	= Todoyu::Label(Todoyu::$CONFIG['FILTERS'][$type]['config']['label']);

			// Get text pattern
		if( $numItems > 0 && $totalItems > $hardLimit ) {
			$infoTextPattern	= Todoyu::Label('search.ext.areMatching.overLimit');
		} else {
			$infoTextPattern	= Todoyu::Label('search.ext.areMatching');
		}

			// Compile string from string and variables
		return sprintf($infoTextPattern, $numItems, $typeLabel);
	}

}

?>