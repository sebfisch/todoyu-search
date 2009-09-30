<?php

class TodoyuSearchFiltersetActionController extends TodoyuActionController {
	
	public function saveAsNewAction(array $params) {
		$type		= $params['type'];
		$conditions	= $params['conditions'];
		$conditions = empty($conditions) ? array() : json_decode($conditions, true);
		$title		= $params['title'];
		$conjunction= $params['conjunction'];

		$data = array(
			'filterset'	=> 0,
			'type'		=> $type,
			'title'		=> $title,
			'conjunction'=> $conjunction,
			'conditions'=> $conditions
		);

		$idFilterset = TodoyuFiltersetManager::saveFilterset($data);

		TodoyuSearchPreferences::saveActiveFilterset($type, $idFilterset);

		return $idFilterset;
	}
	
	public function saveAction(array $params) {
		$idFilterset= intval($params['filterset']);
		$conditions	= $params['conditions'];
		$conditions = empty($conditions) ? array() : json_decode($conditions, true);
		$conjunction= $params['conjunction'];
		$tab		= $params['tab'];

		$data = array(
			'conjunction'=> $conjunction,
		);
		TodoyuFiltersetManager::updateFilterset($idFilterset, $data);
		TodoyuFilterConditionManager::saveFilterConditions($idFilterset, $conditions);

		TodoyuSearchPreferences::saveActiveFilterset($tab, $idFilterset);

		return $idFilterset;
	}
	
	
}

?>