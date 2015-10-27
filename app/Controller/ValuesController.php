<?php
/**
 * Values Controller
 *
 * Provides all value related functions.
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://headkino.de
 * @package       app.Controller
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
 
App::uses('AppController', 'Controller');
/**
 * Values Controller
 *
 * @property Value $Value
 */
class ValuesController extends AppController {

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		$this->Value->id = $id;
		
		$this->Value->recursive = 0;
		if (!$this->Value->exists()) {
			throw new NotFoundException(__('Invalid value'));
		}
		$v = $this->Value->read(null, $id);
		if(!$this->authorizedProject($v['Value']['project_id'], console::$contributorState)) {
			return;
		}
		$this->set('value', $v);
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add($project_id) {
		if ($this->request->is('post')) {
			$this->Value->create();
			$this->request->data['Value']['project_id'] = $project_id;
			if(!$this->authorizedProject($project_id, console::$contributorState)) {
				return;
			}
			if($this->request->data['Value']['method_id']) {
				$this->request->data['Value']['method_params'] = json_encode($this->request->data['params']);
			}
			if ($this->Value->save($this->request->data)) {
				$this->writeLog('created', array($this->Auth->user('id'), 'values', 'related' => $this->Value->getLastInsertId()));
				$this->Session->setFlash(__('The value has been saved'));
				$this->redirect(array('controller' => 'projects', 'action' => 'view', $project_id));
			} else {
				$this->Session->setFlash(__('The value could not be saved. Please, try again.'));
			}
		}
		$this->loadModel('Method');
		$units = $this->Value->Unit->find('list', array('order' => 'name ASC', 'conditions' => 'ISNULL(project_id) OR project_id = '.$project_id));
		$methods = $this->Method->find('list', array('order' => 'name ASC', 'conditions' => 'ISNULL(project_id) OR project_id=0 OR project_id = '.$project_id));
		$this->set(compact('project_id', 'units', 'inputs', 'methods'));
	}
	
	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {
		$this->Value->id = $id;
		if (!$this->Value->exists()) {
			throw new NotFoundException(__('Invalid value'));
		}
		$this->Value->recursive = 0;
		$v = $this->Value->read(null, $id);
		$project_id = $v['Value']['project_id'];
		if(!$this->authorizedProject($project_id, console::$contributorState)) {
			return;
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if($this->request->data['Value']['method_id']) {
				$this->request->data['Value']['method_params'] = json_encode($this->request->data['params']);
			}
			if ($this->Value->save($this->request->data)) {
				$newMax = $v['Value']['maximum'] != $this->request->data['Value']['maximum'];
				$newMin = $v['Value']['minimum'] != $this->request->data['Value']['minimum'];
				$newVariation = $v['Value']['max_variation'] != $this->request->data['Value']['max_variation'];
				$newErrorCodes = $v['Value']['error_codes'] != $this->request->data['Value']['error_codes'];
				if( $newMax || $newMin || $newVariation || $newErrorCodes ) {
					$news = $newMax ? 'max:'.$newMax.',' : '';
					$news .= $newMin ? 'min:'.$newMax.',' : '';
					$news .= $newVariation ? 'var:'.$newVariation : '';
					$news .= $newErrorCodes ? 'er:'.$newErrorCodes : '';
					$news = trim($news, ',');
					$this->Value->uncheck_data($id);
					$this->writeLog('value_changed', array($this->Auth->user('id'), $news, 'related' => $id));
				}
				$this->Session->setFlash(__('The value has been saved'));
				$this->writeLog('edited', array($this->Auth->user('id'), 'values', $v['Value']['name'], 'related' => $id));
				$this->redirect(array('controller' => 'projects', 'action' => 'view', $project_id));
			} else {
				$this->Session->setFlash(__('The value could not be saved. Please, try again.'));
			}
		}  else {
			$this->Value->recursive = 0;
			$this->request->data = $this->Value->read(null, $id);
		}
		//debug($this->request);
		$this->loadModel('Method');
		$units = $this->Value->Unit->find('list', array('order' => 'name ASC', 'conditions' => 'ISNULL(project_id) OR project_id=0 OR project_id = '.$project_id));
		$methods = $this->Method->find('list', array('order' => 'name ASC', 'conditions' => 'ISNULL(project_id) OR project_id=0 OR project_id = '.$project_id));
		$values = $this->Value->find('list', array('order' => 'Value.name ASC', 'conditions' => 'Value.id != "'.$id.'" AND project_id = '.$project_id));
		
		$this->loadModel('Method');
		if($v['Method']['id']) {
			$params = $this->Method->parse_params($v['Method']['params']);
			$this->request->data['params'] = json_decode($this->request->data['Value']['method_params'], true);
		}
		//debug($params);
		$this->set(compact('units', 'inputs', 'methods', 'params', 'values'));
		$this->render('add');
	}
	
	/**
	* @param $value_id: Value id for which all measures should be checked
	* @param $start 
	* @param $end
	*/
	public function check_data($value_id, $start = 0, $end = 0) {
		$this->Value->check_data($value_id, $start, $end);
		$timecond = '';
		if($start)
			$timecond = ' AND timestamp >='.$start;
		if($end)
			$timecond .= ' AND timestamp <='.$end;
		$flagged = $this->Value->Measure->find('count', array('conditions' => 'Measure.value_id="'.$value_id.'" AND Measure.state = -1'.$timecond));
		$this->set(compact('flagged'));
		$this->render('../dummy');
	}
	
	/**
	* Reset the states which are deleted or correct
	* @param $value_id: Value id for which all measures should be checked
	* @param $flag: '!' to flag as error or 'correct' to set as correct
	* @param $start 
	* @param $end
	*/
	public function reset_data($value_id, $flag, $start, $end) {
		$v = $this->Value->read(null, $value_id);
		if(!$this->authorizedProject($v['Value']['project_id'], console::$contributorState)) {
			return;
		}
		$flag = $flag == 'deleted' ? -2 : ($flag == 'correct' ? 2 : false);
		if($flag) {
			$timecond = ' AND timestamp >='.$start.' AND timestamp <='.$end;
			$query = 'UPDATE measures SET state=0 WHERE state="'.$flag.'" AND value_id="'.$value_id.'"'.$timecond;
			//debug($query);
			$this->Value->query($query);
		} else {
			$this->er(__('No flag was removed'));
		}
		$this->render('../dummy');
	}
	
	/**
	* The user can flag data in a time interval with flags to delete them later
	* @param $value_id: Value id for which all measures should be checked
	* @param $flag: '!' to flag as error or 'correct' to set as correct
	* @param $start 
	* @param $end
	*/
	public function flag_data($value_id, $flag, $start, $end) {
		$v = $this->Value->read(null, $value_id);
		if(!$this->authorizedProject($v['Value']['project_id'], console::$contributorState)) {
			return;
		}
		$flag = $flag == '!' ? -1 : ($flag == 'correct' ? 1 : false);
		if($flag) {
			$timecond = ' AND timestamp >='.$start.' AND timestamp <='.$end;
			$query = 'UPDATE measures SET state='.$flag.' WHERE value_id="'.$value_id.'"'.$timecond;
			//debug($query);
			$this->Value->query($query);
		} else {
			$this->er(__('No flag was set'));
		}
		$this->render('../dummy');
	}
	
	/**
	* Delete or unflag flagged data
	* @param $value_id: Value id for which the measure states should be changed
	* @param $start 
	* @param $end
	* @param string $method: delete (set the data points flagged -1, to -2) or unflag (set the data points flagged -1, to 2)  
	* @param string $flagType: max (over value maximum), min (under value minimum), ! (over value max_variation), error (error code)
	*/
	public function manipulate_data($value_id, $start, $end, $method, $flagType) {
		$timecond = ' AND timestamp >='.$start.' AND timestamp <='.$end;
		$state = $method == 'delete' ? -2 : 2;
		$v = $this->Value->read(null, $value_id);
		if(!$this->authorizedProject($v['Value']['project_id'], console::$contributorState)) {
			return;
		}
		$v = $v['Value'];
		$errors = $this->Value->parse_errorcodes($v['error_codes']);
		if(count($errors)) {
			$hasErrorCodes = true;
			$errors = Set::extract($errors, '{n}.code');
			$errors = implode(',', $errors);
		}
		switch($flagType){
			case 'max': 
				$cond = 'data > "'.$v['maximum'].'"';
			break;
			case 'min':
				$cond = 'data < "'.$v['minimum'].'"';
			break;
			case '!':
				$cond = array();
				if($v['maximum'])
					$cond[] =  'data > "'.$v['maximum'].'"';
				if($v['minimum'])
					$cond[] = 'data < "'.$v['minimum'].'"';
				if($hasErrorCodes)
					$cond[] = 'data IN ('.$errors.')';
				if(count($cond))
					$cond = 'NOT('.implode(' OR ', $cond).')';
				else
					$cond = '';
			break;
			case 'error':
				if($hasErrorCodes)
					$cond = 'data IN ('.$errors.')'; 
			break;
		}
		if($flagType == 'error' && !$hasErrorCodes) {
			$this->er(__('There are no error codes defined.'));
		} else {
			if($cond)
				$cond = ' AND '.$cond;
			$query = 'UPDATE measures SET state='.$state.' WHERE state=-1 AND value_id="'.$value_id.'"'.$cond.$timecond;
			//debug($query);
			$this->Value->query($query);
		}
		$this->render('../dummy');
	}

	/**
	 * delete method
	 *
	 * @throws MethodNotAllowedException
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {
		$this->delete_object('Value', $id);
	}
}
