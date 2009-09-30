<?php

class TodoyuSearchFiltercontrollerActionController extends TodoyuActionController {
	
	public function autocompletionAction(array $params) {
		$data = TodoyuFilterWidgetManager::handleAutocompletion();
		
		return TodoyuFilterWidgetRenderer::renderAutocompletion($data);
	}
		
}

?>