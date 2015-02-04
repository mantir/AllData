<?php
App::uses('AppModel', 'Model');
/**
 * Unit Model
 *
 */
class Unit extends AppModel {

	/**
	 * Display field
	 *
	 * @var string
	 */
	public $displayField = 'name';
	
	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'message' => 'Must not be empty',
				'allowEmpty' => false,
				'last' => false,
			),
			'between' => array(
				'rule' => array('between', 2, 40),
				'message' => 'Name must have at least 2 up to 40 characters',
				'allowEmpty' => false,
				'last' => false, // Stop validation after this rule
			),
		),
		'symbol' => array(
			'notEmpty' => array(
				'message' => 'Must not be empty',
				'allowEmpty' => false,
				'last' => false,
			),
			'between' => array(
				'rule' => array('between', 1, 10),
				'message' => 'Symbol must have at least 1 up to 10 characters',
				'allowEmpty' => false,
				'last' => false, // Stop validation after this rule
			),
		),
	);

}
