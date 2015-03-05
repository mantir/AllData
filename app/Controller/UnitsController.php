<?php
/**
 * Units Controller
 *
 * Provides all project related functions.
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://headkino.de
 * @package       app.Controller
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('AppController', 'Controller');
/**
 * Units Controller
 *
 * @property Unit $Unit
 */
class UnitsController extends AppController {
	public $paginate = array(
        'limit' => 1000000,
		'maxLimit' => 1000000
    );
	
	/**
	 * index method
	 *
	 * @return void
	 */
	public function admin_index() {
		$this->Unit->recursive = 0;
		$this->set('units', $this->paginate());
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		$this->Unit->id = $id;
		if (!$this->Unit->exists()) {
			throw new NotFoundException(__('Invalid unit'));
		}
		$this->set('unit', $this->Unit->read(null, $id));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add($project_id = null) {
		if(!$this->authorizedProject($project_id, console::$contributorState)) {
			return;
		}
		if(!$project_id && !$this->Auth->user('isAdmin')) {
			$this->noPermissionError();
			return;
		}
		$this->request->data['Unit']['project_id'] = $project_id;
		if ($this->request->is('post')) {
			$this->Unit->create();
			if ($this->Unit->save($this->request->data)) {
				$this->writeLog('created', array($this->Auth->user('id'), 'units', 'related' => $this->Unit->getLastInsertId()));
				$this->Session->setFlash(__('The unit has been saved'));
				if($project_id)
					$this->redirect(array('action' => 'view', 'controller' => 'projects', $project_id));
				else
					$this->redirect(array('action' => 'admin_index', 'controller' => 'units', 'admin' => 1));
			} else {
				$this->Session->setFlash(__('The unit could not be saved. Please, try again.'));
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
		$this->Unit->id = $id;
		if (!$this->Unit->exists()) {
			throw new NotFoundException(__('Invalid unit'));
		}
		$u = $this->Unit->read(null, $id);
		$project_id = $u['Unit']['project_id'];
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Unit->save($this->request->data)) {
				$this->writeLog('edited', array($this->Auth->user('id'), 'units', 'related' => $id));
				$this->Session->setFlash(__('The unit has been saved'));
				if($project_id)
					$this->redirect(array('action' => 'view', 'controller' => 'projects', $project_id));
				else
					$this->redirect(array('action' => 'admin_index', 'controller' => 'units', 'admin' => 1));
			} else {
				$this->er(__('The unit could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Unit->read(null, $id);
		}
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
		$this->delete_object('Unit', $id);
	}
}
