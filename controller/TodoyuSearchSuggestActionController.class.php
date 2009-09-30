<?php

class TodoyuSearchSuggestActionController extends TodoyuActionController {

	public function getSuggestionsAction(array $params) {
		$query	= $params['query'];
		$mode	= $params['mode'];

		$suggestions	= TodoyuSearch::getSuggestions($query, $mode);

			// Display output
		return TodoyuSearchRenderer::renderSuggestions($suggestions);
	}


}

?>