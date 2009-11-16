<?php

class TodoyuSearchFiltercontrollerActionController extends TodoyuActionController {

	public function autocompletionAction(array $params) {
		$widgetKey	= $params['completionID'];
		$filterType	= $params['filtertype'];
		$searchWord	= $params['sword'];

		$data = TodoyuFilterWidgetManager::getAutocompletionResults($filterType, $searchWord, $widgetKey);

		return TodoyuFilterWidgetRenderer::renderAutocompletion($data);
	}

}

?>