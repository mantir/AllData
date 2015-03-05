<?php
/**
 * User model
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://book.cakephp.org/2.0/en/models.html
 * @package       app.Model
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
 
class User extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';


	public function beforeSave($options = array()) {
		if($this->data['User']['password'])
			$this->data['User']['password'] = AuthComponent::password($this->data['User']['password']);
		return true;
	}
	
	public function bindNode($user) {
		//return array('model' => 'group', 'foreign_key' => $user['User']['isAdmin']);
	}
	
	public $validate = array(
		'password' => array(
			'between' => array(
				'rule' => array('between', 6, 30),
				'message' => 'Your password must have at least 6 up to 30 characters',
				'allowEmpty' => false,
				'last' => false, // Stop validation after this rule
			)
		),
		'name' => array(
			'between' => array(
				'rule' => array('between', 4, 30),
				'message' => 'Name must have at least 4 up to 30 characters',
				'allowEmpty' => false,
				'last' => false, // Stop validation after this rule
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'It already exists a user with this name.'
			),
		),
		'email' => array(
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'It already exists a user with this email address.'
			),
			'required' => array(
				'rule' => array('email'),
				'message' => 'You must enter a correct email address.'
			)
		)
		/*'role' => array(
			'valid' => array(
				'rule' => array('inList', array('admin', 'author')),
				'message' => 'Please enter a valid role',
				'allowEmpty' => false
			)
		)*/
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

	
/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(

	);
	
/**
 * hasOne associations
 *
 * @var array
 */
	public $hasOne = array(

	);

/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	/**
	 * hasAndBelongsToMany associations
	 *
	 * @var array
	 */
	public $hasAndBelongsToMany = array(
		'Project' => array(
			'className' => 'Project',
			'joinTable' => 'projects_users',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'project_id',
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
