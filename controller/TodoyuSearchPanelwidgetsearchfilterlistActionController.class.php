<?php

class TodoyuSearchPanelwidgetSearchfilterlistActionController extends TodoyuActionController {
	
	public function updateAction(array $params) {
		$panelWidget = TodoyuPanelWidgetManager::getPanelWidget('SearchFilterList');

		return $panelWidget->renderContent();
	}	
	
}

?>