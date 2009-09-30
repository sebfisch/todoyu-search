<?php

class TodoyuSearchFilteractioncontrollActionController extends TodoyuActionController {
	
	public function loadAction(array $params) {
		$tab		= $params['tab'];
		$idFilterset= 0; // Where does it come from?

		return TodoyuFilterAreaRenderer::renderControls($tab, $idFilterset);
	}
}

?>