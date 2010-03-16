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
 * Filter area renderer
 *
 * @package		Todoyu
 * @subpackage	Search
 */

class TodoyuFilterAreaRenderer {

	/**
	 * Render whole filter area: contains tabs, control, widget area and search results
	 *
	 * @param	String		$activeTab		Active tab/filter type
	 * @param	Integer		$idFilterset	Active filterset
	 * @param	Array		$conditions		Custom conditions instead of a stored filterset
	 * @param	Boolean		$init			Add init script at the bottom of loaded with ajax
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
			$filterset	= TodoyuFiltersetManager::getFiltersetRecord($idFilterset);
			$conjunction= $filterset['conjunction'];
			$conditions	= TodoyuFilterConditionManager::getFiltersetConditions($idFilterset);
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
			'searchResults'	=> $results
		);

			// If init necessary (ajax), add it to the response
		if( $init ) {
			$data['init'] = 'Todoyu.Ext.search.Filter.init(\'' . $activeTab . '\', \'' . $idFilterset . '\', ' . json_encode($conditions) . ')';
		}

		return render($tmpl, $data);
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
//
//		$tabs[0]['position'] = 'first';
//		$tabs[sizeof($tabs)-1]['position'] = 'last';

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
	public static function renderControls($tab, $idFilterset = 0)	{
		$idFilterset = intval($idFilterset);

			// Find filterset ID if not given
		if( $idFilterset === 0 ) {
			$idFilterset = TodoyuSearchPreferences::getActiveFilterset($tab);
		}

			// Get conjunction from filterset if available
		if( $idFilterset !== 0 ) {
			$filterset	= TodoyuFiltersetManager::getFiltersetRecord($idFilterset);
			$conjunction= $filterset['conjunction'];
		} else {
			$conjunction= 'AND';
		}

			// Get grouped type conditions
		$groupedConditions	= TodoyuFilterConditionManager::getGroupedTypeConditions($tab);

		$tmpl	= 'ext/search/view/filter-action-controls.tmpl';
		$data 	= array(
			'type'				=> $tab,
			'groupedConditions'	=> $groupedConditions,
			'conjunctions'		=> array(
				array(
					'key'	=> 'AND',
					'label'	=> 'search.and'
				),
				array(
					'key'	=> 'OR',
					'label'	=> 'search.or'
				)
			),
			'activeConjunction'	=> $conjunction
		);

		return render($tmpl, $data);
	}



	/**
	 * Render widget
	 *
	 * @param	Integer		$idFilterset
	 * @return	String
	 */
	public static function renderWidgetArea($idFilterset) {
		$idFilterset= intval($idFilterset);
		$filterset	= TodoyuFiltersetManager::getFiltersetRecord($idFilterset);
		$conditions	= TodoyuFilterConditionManager::getFiltersetConditions($idFilterset);

		$content	= '';

		foreach($conditions as $condition) {
			$content .= TodoyuFilterWidgetRenderer::renderWidget($filterset['type'], $condition['filter'], $condition['id'], $condition['value'], $condition['negate']==1);
		}

		return $content;
	}



	/**
	 * Render
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

			// If filterset is given, use its conditions
		if( $idFilterset !== 0 ) {
			$conditions = TodoyuFilterConditionManager::getFilterSetConditions($idFilterset);
		} else {
			$conditions = TodoyuFilterConditionManager::buildFilterConditionArray($conditions);
		}

			// Build filter
		$typeClass	= TodoyuFilterManager::getFilterTypeClass($type);
		$typeFilter	= new $typeClass($conditions, $conjunction);

		$itemIDs	= $typeFilter->getItemIDs();

		TodoyuDebug::printLastQueryInFirebug();

		return TodoyuSearchRenderer::renderResultsListing($type, $itemIDs);
	}

}


?>