<?php

class TodoyuSearchPreferenceActionController extends TodoyuActionController {

	public function saveCurrentFilterSetAction(array $params) {
		$idFilterset	= intval($params['value']);

		TodoyuSearchPreferences::saveCurrentFilter($idFilterset);
	}


	public function removeCurrentFilterSetAction(array $params) {
		$idFilterset= intval($params['filterset']);
		$tab		= $params['tab'];
		$content	= '';

		if( $idFilterset === 0 ) {
			$idFilterset = TodoyuSearchPreferences::getActiveFilterset($tab);
		}

		if( $idFilterset !== 0 ) {
			$conditions	= TodoyuFiltersetManager::getFiltersetConditions($idFilterset);

				// Send widgets
			$content	= TodoyuFilterAreaRenderer::renderWidgetArea($idFilterset);
				// Add js init for loaded widgets
			$content 	.= TodoyuDiv::wrapScript('Todoyu.Ext.search.Filter.initConditions(\'' . $tab . '\', ' . json_encode($conditions) . ');');
		} else {
			$content	= 'No widgets';
		}

		return $content;
	}


	public function saveActiveTabAction(array $params) {
		$tab	= $params['value'];

		TodoyuSearchPreferences::saveActiveTab($tab);
	}


	public function activeFiltersetAction(array $params) {
		$idFilterset= intval($params['item']);
 		$tab		= $params['value'];

 		TodoyuSearchPreferences::saveActiveFilterset($tab, $idFilterset);
	}



	public function filtersetOrderAction(array $params)	{
		$values	= json_decode($params['value']);

		TodoyuFiltersetManager::updateOrder($values->items);
	}

}



?>