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



	/**
	 * Rename a filterset
	 *
	 * @param	Array		$params
	 */
	public function renameFiltersetAction(array $params) {
		$idFilterset= intval($params['item']);
		$title		= trim($params['value']);

		TodoyuFiltersetManager::renameFilterset($idFilterset, $title);
	}



	/**
	 * Update the visibility of a filterset
	 *
	 * @param	Array		$params
	 */
	public function toggleFiltersetVisibilityAction(array $params) {
		$idFilterset= intval($params['item']);
		$visible	= intval($params['value']) === 1;

		TodoyuFiltersetManager::updateFiltersetVisibility($idFilterset, $visible);
	}



	/**
	 * Delete a filterset with its condition
	 *
	 * @param	Array		$params
	 */
	public function deleteFiltersetAction(array $params) {
		$idFilterset	= intval($params['item']);

		TodoyuFiltersetManager::deleteFilterset($idFilterset, true);
	}



	/**
	 * Update order of the filtersets
	 *
	 * @param	Array		$params
	 */
	public function filtersetOrderAction(array $params)	{
		$orderData	= json_decode($params['value'], true);

		TodoyuFiltersetManager::updateOrder($orderData['items']);
	}



	/**
	 *	General panelWidget action, saves collapse status
	 *
	 *	@param	Array	$params
	 */
	public function pwidgetAction(array $params) {
		$idWidget	= $params['item'];
		$value		= $params['value'];

		TodoyuPanelWidgetManager::saveCollapsedStatus(EXTID_SEARCH, $idWidget, $value);
	}
}



?>