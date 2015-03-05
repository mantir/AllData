<?php
/**
 * Unit model
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://book.cakephp.org/2.0/en/models.html
 * @package       app.Model
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
 
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
			'between' => array(
				'rule' => array('between', 2, 40),
				'message' => 'Name must have at least 2 up to 40 characters',
				'allowEmpty' => false,
				'last' => false, // Stop validation after this rule
			),
		),
		'symbol' => array(
			'between' => array(
				'rule' => array('between', 1, 10),
				'message' => 'Symbol must have at least 1 up to 10 characters',
				'allowEmpty' => false,
				'last' => false, // Stop validation after this rule
			),
		),
	);

}
