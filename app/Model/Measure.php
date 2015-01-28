<?php
App::uses('AppModel', 'Model');
/**
 * Measure Model
 *
 * @property Value $Value
 */
class Measure extends AppModel {


	/*
	@long_results: array of Measures array('data' => '...', 'timestamp' => 123, ...) where the long array keys are replace with shorter terms for less payload when sending data
	*/
	public function short_keys($long_results){
		$results = array();
		$names = array('data' => 'd', 'timestamp' => 't');
		foreach($long_results as $r) {
			foreach($names as $j => $n) {
				if(isset($r[$j])) {
					$r[$n] = $r[$j];
					unset($r[$j]); 
				}
			}
			unset($r['value_id']);
			$results[] = $r;
		}
		return $results;
	}
	
//The Associations below have been created with all possible keys, those that are not needed can be removed
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Value' => array(
			'className' => 'Value',
			'foreignKey' => 'value_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
