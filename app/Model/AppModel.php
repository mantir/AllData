<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://book.cakephp.org/2.0/en/models.html
 * @package       app.Model
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
	/**
	*
	*/
	function lastQuery(){
		$dbo = $this->getDatasource();
		$logs = $dbo->getLog();
		// return the first element of the last array (i.e. the last query)
		return $logs['log'];
	}
	
	/**
	* Prints the database log for the current model
	*/
	function debugDB(){
		$dbo = $this->getDatasource();
		$logs = $dbo->getLog();
		debug($logs);
	}
	
	/**
	* Generates a unique id for the model
	*/
	function generateID(){
		$gen_id = rand(1000, 9999).rand(1000, 9999);
		while($this->find('count', array('conditions' => $this->name.'.id='.$gen_id)) > 0){
			$gen_id = rand(1000, 9999).rand(1000, 9999);
		}
		return $gen_id;
	}
	
	function checkUnique($data, $fields) {
		// check if the param contains multiple columns or a single one
		if (!is_array($fields)){
			$fields = array($fields);
		}
		 
		// go trough all columns and get their values from the parameters
		foreach($fields as $key) {
			$unique[$key] = $this->data[$this->name][$key];
		}
		 
		// primary key value must be different from the posted value
		if (isset($this->data[$this->name][$this->primaryKey]))
		{
			$unique[$this->primaryKey] = "<>" . $this->data[$this->name][$this->primaryKey];
		}
		 
		// use the model's isUnique function to check the unique rule
		return $this->isUnique($unique, false);
	}
	
}
