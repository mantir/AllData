<?php
/**
 * Export model
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://book.cakephp.org/2.0/en/models.html
 * @package       app.Model
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
 
App::uses('AppModel', 'Model');
/**
 * Export Model
 *
 * @property Project $Project
 */
class Export extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';
	
	public $validate = array(
		'name' => array(
			'between' => array(
				'rule' => array('between', 4, 30),
				'message' => 'Name must have at least 4 up to 30 characters',
				'allowEmpty' => false,
				'last' => false, // Stop validation after this rule
			),
		)
	);


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
}
