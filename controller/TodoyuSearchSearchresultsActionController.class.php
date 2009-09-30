<?php

class TodoyuSearchSearchresultsActionController extends TodoyuActionController {
	
	public function updateAction(array $params) {
		$tab			= $params['tab'];
		$idFilterset	= intval($params['filterset']);
		$conditions		= $params['conditions'];
		$conditions		= empty($conditions) ? array() : json_decode($conditions, true) ;
		$conjunction	= $params['conjunction'];

		if( $idFilterset > 0 || sizeof($conditions) > 0)	{
			return TodoyuFilterAreaRenderer::renderResults($tab, $idFilterset, $conditions, $conjunction);
		}
	}
	
	
}

?>