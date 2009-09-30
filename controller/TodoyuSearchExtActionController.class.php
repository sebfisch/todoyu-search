<?php

class TodoyuSearchExtActionController extends TodoyuActionController {
	
	public function defaultAction(array $params) {
		TodoyuFrontend::setActiveTab('search');

		TodoyuPage::init('ext/search/view/ext.tmpl');
		TodoyuPage::setTitle('LLL:search.page.title');
		
		$activeTab	= $params['tab'];
		
		
		if( ! empty($activeTab) ) { 	// If tab is set manualy
			$filters 	= json_decode($params['filters'], true);
		
			$idFilterset= 0;
			$conditions = TodoyuSearchManager::convertSimpleToFilterConditionArray($filters);
		} else {	// Normal preferences rendering
			$activeTab	= TodoyuSearchPreferences::getActiveTab();
			$idFilterset= TodoyuSearchPreferences::getActiveFilterset($activeTab);
		
			if( $idFilterset !== 0 ) {
				$conditions	= TodoyuFilterConditionManager::getFiltersetConditions($idFilterset);
			} else {
				$conditions	= array();
			}
		}
		
			// panel widgets
		$panelWidgets = TodoyuSearchRenderer::renderPanelWidgets();
		TodoyuPage::set('panelWidgets', $panelWidgets);
		
			// Filter area
		$filterArea = TodoyuFilterAreaRenderer::renderFilterArea($activeTab, $idFilterset, $conditions, false);
		TodoyuPage::set('filterArea', $filterArea);
		
			// Add js init command
		TodoyuPage::addJsOnloadedFunction('Todoyu.Ext.search.Filter.init.bind(Todoyu.Ext.search.Filter, \'' . $activeTab . '\', \'' . $idFilterset . '\', ' . json_encode($conditions) . ')');
		
		return TodoyuPage::render();		
	}
}

?>