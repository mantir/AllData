<?php
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
		$this->set('value', $this->Value->read(null, $id));
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
			if ($this->Value->save($this->request->data)) {
				$this->Session->setFlash(__('The value has been saved'));
				$this->redirect(array('controller' => 'projects', 'action' => 'view', $project_id));
			} else {
				$this->Session->setFlash(__('The value could not be saved. Please, try again.'));
			}
		}
		$this->loadModel('Method');
		$units = $this->Value->Unit->find('list');
		$methods = $this->Method->find('list', array('conditions' => 'project_id = 0 OR project_id = '.$project_id));
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
		if ($this->request->is('post') || $this->request->is('put')) {
			if($this->request->data['Value']['method_id']) {
				$this->request->data['Value']['method_params'] = json_encode($this->request->data['params']);
			}
			if ($this->Value->save($this->request->data)) {
				$this->Session->setFlash(__('The value has been saved'));
				$this->redirect(array('controller' => 'projects', 'action' => 'view', $project_id));
			} else {
				$this->Session->setFlash(__('The value could not be saved. Please, try again.'));
			}
		}  else {
			$this->request->data = $this->Value->read(null, $id);
		}
		$this->loadModel('Method');
		$units = $this->Value->Unit->find('list');
		$methods = $this->Method->find('list', array('conditions' => 'project_id = 0 OR project_id = '.$project_id));
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
 * delete method
 *
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Value->id = $id;
		if (!$this->Value->exists()) {
			throw new NotFoundException(__('Invalid value'));
		}
		$v = $this->Value->read(null, $id);
		
		if (!$this->Value->exists()) {
			throw new NotFoundException(__('Invalid value'));
		}
		if ($this->Value->delete()) {
			$this->Session->setFlash(__('Value deleted'));
			$this->redirect(array('controller' => 'projects', 'action' => 'view', $v['Value']['project_id']));
		}
		$this->Session->setFlash(__('Value was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
