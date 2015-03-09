<?php
/**
 * Exports Controller
 *
 * This file is the exports controller.
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://book.cakephp.org/2.0/en/controllers.html
 * @package       app.Controller
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppController', 'Controller');
/**
 * Exports Controller
 *
 * @property Export $Export
 */
class ExportsController extends AppController {


	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id Export ID
	 * @return void
	 */
	public function view($id = null) {
		$this->Export->id = $id;
		if (!$this->Export->exists()) {
			throw new NotFoundException(__('Invalid export'));
		}
		$export = $this->Export->read(null, $id);
		if(!$this->authorizedProject($export['Export']['project_id'], console::$contributorState, false)) {
			$this->er(__('The provided api key is not correct or you have not enough rights.'));
			return;
		}
		$interval_type = $export['Export']['interval_type'];
		$start = strtotime('-'.$export['Export']['interval_count'].' '.console::$intervalTypes[$interval_type]);
		$end = time();
		$start_date = date('d.m.Y,H:i', $start);
		$end_date = date('d.m.Y,H:i');
		$params = array('start' => $start_date, 'end' => $end_date, 'values' => $export['Export']['value_ids'], 'dateformat' => $export['Export']['dateformat'], 'format' => $export['Export']['format'], 'states' => 0, 'deleted' => 0, 'api_key' => $export['Export']['auth']);
		$this->request->data['Export']['start'] = date('d.m.Y', $start);
		$this->request->data['Export']['end'] = date('d.m.Y', $end);
		$url = array('controller' => 'projects', 'action' => 'export', 'full_base' => true, $export['Export']['project_id'], '?' => $params);
		$this->set(compact('params', 'url', 'export'));
	}

	/**
	 * add method
	 * @param string $project_id Project ID of project to which the export shall be added
	 * @return void
	 */
	public function add($project_id) {
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->Export->create();
			$this->request->data['Export']['value_ids'] = implode(',', $this->request->data['Export']['value_ids']);
			$this->request->data['Export']['project_id'] = $project_id;
			$this->request->data['Export']['auth'] = uniqid().uniqid();
			$this->request->data['Export']['interval_count'] = abs($this->request->data['Export']['interval_count']); //Only positive values possible
			if ($this->Export->save($this->request->data)) {
				$this->writeLog('created', array($this->Auth->user('id'), 'exports', 'related' => $this->Export->getLastInsertId()));
				$this->Session->setFlash(__('The export has been saved'));
				$this->redirect(array('controller' => 'projects', 'action' => 'view', $project_id));
			} else {
				$this->Session->setFlash(__('The export could not be saved. Please, try again.'));
			}
			$this->request->data['Export']['value_ids'] = explode(',', $this->request->data['Export']['value_ids']);
		} else {
			$this->request->data['Export']['interval_count'] = 1;
			$this->request->data['Export']['interval_type'] = 4; //default 1 week 
		}
		$value_ids = $this->Export->Project->Value->find('list', array('order' => 'name ASC', 'conditions' => 'Value.project_id="'.$project_id.'"'));
		$selected_values = $this->request->data['Export']['value_ids'] ? $this->request->data['Export']['value_ids'] : array_keys($value_ids);
		
		$this->set(compact('value_ids', 'selected_values'));
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id Export ID
	 * @return void
	 */
	public function edit($id = null) {
		$this->Export->id = $id;
		$export = $this->Export->read(null, $id);
		$project_id = $export['Export']['project_id'];
		if (!$this->Export->exists()) {
			throw new NotFoundException(__('Invalid export'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['Export']['interval_count'] = abs($this->request->data['Export']['interval_count']); //Only positive values possible
			$this->request->data['Export']['value_ids'] = implode(',', $this->request->data['Export']['value_ids']);
			if ($this->Export->save($this->request->data)) {
				$this->writeLog('edited', array($this->Auth->user('id'), 'exports', 'related' => $id));
				$this->Session->setFlash(__('The export has been saved'));
				$this->redirect(array('controller' => 'projects', 'action' => 'view', $project_id));
			} else {
				$this->Session->setFlash(__('The export could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Export->read(null, $id);
		}
		$this->request->data['Export']['value_ids'] = explode(',', $this->request->data['Export']['value_ids']);
		$project_id = $export['Export']['project_id'];
		$value_ids = $this->Export->Project->Value->find('list', array('order' => 'name ASC', 'conditions' => 'Value.project_id="'.$project_id.'"'));
		$selected_values = $this->request->data['Export']['value_ids'] ? $this->request->data['Export']['value_ids'] : array_keys($value_ids);
		$this->set(compact('value_ids', 'selected_values'));
		$this->render('add');
	}

	/**
	 * delete method
	 *
	 * @throws MethodNotAllowedException
	 * @throws NotFoundException
	 * @param string $id Export ID
	 * @return void
	 */
	public function delete($id = null) {
		$this->delete_object('Export', $id);
	}
}
