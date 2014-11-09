<?php
App::uses('AppController', 'Controller');
/**
 * Values Controller
 *
 * @property Value $Value
 */
class ValuesController extends AppController {
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Value->recursive = 0;
		$this->set('values', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Value->id = $id;
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
		$units = $this->Value->Unit->find('list');
		$this->set(compact('project_id', 'units', 'inputs'));
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
		$v = $this->Value->read(null, $id);
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Value->save($this->request->data)) {
				$project_id = $v['Value']['project_id'];
				$this->Session->setFlash(__('The value has been saved'));
				$this->redirect(array('controller' => 'projects', 'action' => 'view', $project_id));
			} else {
				$this->Session->setFlash(__('The value could not be saved. Please, try again.'));
			}
		}  else {
			$this->request->data = $this->Value->read(null, $id);
		}
		$units = $this->Value->Unit->find('list');
		$this->set(compact('units', 'inputs'));
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
		if ($this->Value->delete()) {
			$this->Session->setFlash(__('Value deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Value was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
