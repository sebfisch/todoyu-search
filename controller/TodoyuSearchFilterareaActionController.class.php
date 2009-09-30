<?php

class TodoyuSearchFilterareaActionController extends TodoyuActionController {
	
	public function loadAction(array $params) {
		$tab		= $params['tab'];
		$idFilterset= intval($params['filterset']);
		$conditions	= array();
		$init		= true;

		return TodoyuFilterAreaRenderer::renderFilterArea($tab, $idFilterset, $conditions, $init);
	}
}

?>