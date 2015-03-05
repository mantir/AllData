<?php
/**
 * Input model
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://book.cakephp.org/2.0/en/models.html
 * @package       app.Model
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
 
App::uses('AppModel', 'Model');
/**
 * Input Model
 *
 * @property Project $Project
 * @property Value $Value
 */
class Input extends AppModel {
	
	public $validate = array(
		'name' => array(
			'between' => array(
				'rule' => array('between', 2, 20),
				'message' => 'Name must have at least 2 up to 20 characters',
				'allowEmpty' => false,
				'last' => false, // Stop validation after this rule
			),
		),
	);

	/**
	 * Display field
	 *
	 * @var string
	 */
	public $displayField = 'name';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	/**
	 * hasAndBelongsToMany associations
	 *
	 * @var array
	 */
	public $hasAndBelongsToMany = array(
		'Value' => array(
			'className' => 'Value',
			'joinTable' => 'inputs_values',
			'foreignKey' => 'input_id',
			'associationForeignKey' => 'value_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);

}
