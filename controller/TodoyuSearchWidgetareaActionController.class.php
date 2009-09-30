<?php

class TodoyuSearchWidgetareaActionController extends TodoyuActionController {
	
	public function addAction(array $params) {
		$widgetName	= $params['name'];
		$condition	= $params['condition'];
		$type		= $params['type'];
		$value		= $params['value'];
		$negate		= intval($params['negate']) === 1;

		echo TodoyuFilterWidgetRenderer::renderWidget($type, $condition, $widgetName, $value, $negate);
	}
	
	
	public function loadAction(array $params) {
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
	
	
}

?>