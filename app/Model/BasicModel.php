<?php

App::uses('AppModel', 'Model');


class BasicModel extends AppModel {

	var $deleted = 0;
	var $wasDeleted = true;

	public function beforeFind($data){
		$model = $this->alias;
		$condition = $model.'.deleted='.$this->deleted;
		if(empty($data['conditions']))
			$data['conditions'] = $condition;
		elseif(is_array($data['conditions'])) {
			$data['conditions'] = array(
				'AND' => array(
					$data['conditions'],
					$condition
				)
			);
		} else {
			if(strpos($data['conditions'], $model.'.deleted') === false)
				$data['conditions'] = '('.$data['conditions'].') AND '.$condition;
		}
		return $data;
	}
	
	function beforeDelete() {
		//debug($this->id);
		$id = $this->id; 
		$this->create();
		$this->id = $id;
		$this->save(array($this->alias => array('deleted' => 1)), false);
		return true;
	}
	
	public function delete($id = null, $cascade = true){
		$this->beforeDelete();
		if(!$this->wasDeleted)
			return parent::delete($id, $cascade);
		return true;
	}
	
}
