<?php
/**
 * User Model
 *
 * @property Image $Image
 * @property Message $Message
 * @property Group $Group
 * @property Language $Language
 * @property Recommendation $Recommendation
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
	public $hasAndBelongsToMany = array(
	
	);

}
