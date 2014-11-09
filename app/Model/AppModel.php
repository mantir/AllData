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
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
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
	function lastQuery(){
		$dbo = $this->getDatasource();
		$logs = $dbo->getLog();
		// return the first element of the last array (i.e. the last query)
		return $logs['log'];
	}
	
	function debugDB(){
		$dbo = $this->getDatasource();
		$logs = $dbo->getLog();
		debug($logs);
	}
	
	
	function lang($lang){
		//debug($this);
		if($lang != 'de' && $this->displayFieldEN){
			$displayField = $this->displayFieldEN;
			$this->virtualFields[$this->displayField] = $displayField;
		}
	}
	
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
	
	/*
		to parameters to be unequal when using in validate array in model (e.g. Recommendation)
	*/
	function checkUnequal($data, $fields) {
		// check if the param contains multiple columns
		if (!is_array($fields)) return false;
		 
		return $this->data[$this->name][$fields[0]] != $this->data[$this->name][$fields[1]];
	}
	
	
	/**
	 * Unbinds validation rules and optionally sets the remaining rules to required.
	 * 
	 * @param string $type 'Remove' = removes $fields from $this->validate
	 *                       'Keep' = removes everything EXCEPT $fields from $this->validate
	 * @param array $fields
	 * @param bool $require Whether to set 'required'=>true on remaining fields after unbind
	 * @return null
	 * @access public
	 */
	function unbindValidation($type, $fields, $require=false)
	{
		if ($type === 'remove')
		{
			$this->validate = array_diff_key($this->validate, array_flip($fields));
		}
		else
		if ($type === 'keep')
		{
			$this->validate = array_intersect_key($this->validate, array_flip($fields));
		}
		
		if ($require === true)
		{
			foreach ($this->validate as $field=>$rules)
			{
				if (is_array($rules))
				{
					$rule = key($rules);
					
					$this->validate[$field][$rule]['required'] = true;
				}
				else
				{
					$ruleName = (ctype_alpha($rules)) ? $rules : 'required';
					
					$this->validate[$field] = array($ruleName=>array('rule'=>$rules,'required'=>true));
				}
			}
		}
	} 
	
	public function bindValidation($rules) {
		$fields = is_array($rules) ? $rules : array($rules);
		$this->validate = array_merge($this->validate, $fields);
	}
	
	function filterBindings($bindings = null, $exclude = false) {
        if (empty($bindings) && !is_array($bindings)) {
            return false;
        }
        $relations = array('hasOne', 'hasMany', 'belongsTo', 'hasAndBelongsToMany');
        $unbind = array();
		if($exclude) {
			foreach ($bindings as $binding) {
				foreach ($relations as $relation) {
					if (isset($this->$relation)) {
						$currentRelation = $this->$relation;
						if (isset($currentRelation) && isset($currentRelation[$binding])) {
							$unbind[$relation][] = $binding;
						}
					}
				}
			}
		} else {
			foreach ($relations as $relation) {
				if (isset($this->$relation)) {
					$entities = $this->$relation;
					foreach($entities as $k => $e) {
						if(array_search($k, $bindings) === false)
							$unbind[$relation][] = $k;
					}
				}
			}
		}

        if (!empty($unbind)) {
            $this->unbindModel($unbind);
        }
    } 
	
}
