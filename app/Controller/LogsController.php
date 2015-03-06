<?php
/**
 * Logs Controller
 *
 * This file is the export controller.
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://book.cakephp.org/2.0/en/controllers.html
 * @package       app.Controller
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('AppController', 'Controller');
/**
 * Logs Controller
 *
 * @property Log $Log
 */
class LogsController extends AppController {


	/**
	 * Returns a list of Logs for a project and a type: 
	 * 
	 * @param string $project_id Project ID
	 * @param string $type Log type
	 * @return void
	 */
	public function view($project_id = null, $type = null) {
		$logs = $this->Log->getLogs($project_id, $type);
		$this->loadModel('Input');
		$inputs = $this->Input->find('all', array('recursive' => 0, array('conditions' => 'project_id="'.$project_id.'"')));
		$inputs = Set::combine($inputs, '{n}.Input.id', '{n}.Input');
		$this->set(compact('logs', 'project_id', 'inputs'));
		$this->layout = 'template';
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Log->create();
			if ($this->Log->save($this->request->data)) {
				$this->Session->setFlash(__('The log has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The log could not be saved. Please, try again.'));
			}
		}
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {
		$this->Log->id = $id;
		if (!$this->Log->exists()) {
			throw new NotFoundException(__('Invalid log'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Log->save($this->request->data)) {
				$this->Session->setFlash(__('The log has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The log could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Log->read(null, $id);
		}
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
		$this->Log->id = $id;
		if (!$this->Log->exists()) {
			throw new NotFoundException(__('Invalid log'));
		}
		if ($this->Log->delete()) {
			$this->su(__('Log deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->er(__('Log was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
