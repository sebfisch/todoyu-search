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
 * Panel widget: Search filter list
 *
 * @package		Todoyu
 * @subpackage	Search
 */

class TodoyuPanelWidgetSearchFilterList extends TodoyuPanelWidget implements TodoyuPanelWidgetIf {


	/**
	 * Constructor of the class
	 *
	 * - intitializes the filters
	 * - modifies the filters
	 */
	public function __construct(array $config, array $params = array(), $idArea = 0)	{

		parent::__construct(
			'search',
			'searchfilterlist',
			'LLL:panelwidget-searchfilterlist.title',
			$config,
			$params,
			$idArea
		);

		$this->addHasIconClass();
	}



	/**
	 * Render panel widget content
	 *
	 * @return	String
	 */
	public function renderContent() {
		$filters			= TodoyuFiltersetManager::getFiltersets();
		$groupedFiltersets	= $this->groupFiltersets($filters);
		$toggleStatus		= TodoyuSearchPreferences::getFiltersetListToggle();

		$filtersetTypes		= TodoyuFiltersetManager::getFiltersetTypes();
		$activeFiltersets	= array();

		foreach($filtersetTypes as $filtersetType) {
			if($filtersetType == TodoyuSearchPreferences::getActiveTab())	{
				$activeFiltersets[] = TodoyuSearchPreferences::getActiveFilterset($filtersetType);
			}
		}

		$tmpl = 'ext/search/view/panelwidget-searchfilterlist.tmpl';
		$data = array(
			'id'				=> $this->getID(),
			'groupedFiltersets'	=> $groupedFiltersets,
			'activeFiltersets'	=> $activeFiltersets,
			'toggleStatus' 		=> $toggleStatus
		);

		$content = render($tmpl, $data);

		$this->setContent($content);

		return $content;
	}



	/**
	 * Renders the Panel Widget
	 *
	 * @return	String
	 */
	public function render()	{
		$this->renderContent();

		TodoyuPage::addExtAssets('search', 'panelwidget-searchfilterlist');

		TodoyuPage::addJsOnloadedFunction('Todoyu.Ext.search.PanelWidget.SearchFilterList.init.bind(Todoyu.Ext.search.PanelWidget.SearchFilterList)');

		return parent::render();
	}



	/**
	 * Group filtersets by their type attribute
	 *
	 * @param	Array		$filtersets
	 * @return	Array
	 */
	private static function groupFiltersets(array $filtersets) {
		$groups = array();

		foreach($filtersets as $filterset) {
			$groups[ $filterset['type'] ]['label']	= TodoyuDiv::getLabel($GLOBALS['CONFIG']['FILTERS'][strtoupper($filterset['type'])]['config']['label']);
			$groups[ $filterset['type'] ]['filtersets'][]					= $filterset;

		}

		return $groups;
	}

}


?>