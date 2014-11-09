<?php
App::uses('AppController', 'Controller');
/**
 * Logs Controller
 *
 * @property Log $Log
 */
class LogsController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Log->recursive = 0;
		$this->loadModel('Station'); 
		$this->Station->recursive = 0;
		$stations = $this->Station->find('list', array('order' => 'Station.name'));
		if(!$this->p['station_id']){
			$this->request->query['station_id'] = $this->p['station_id'] = 28007;
		}
		$date_query = '';
		if($this->p['import_date']) {
			$date = strtotime($this->p['import_date'].' midnight');
			$end = strtotime('midnight +1 day', $date);
			$date_query = 'AND Log.time >= '.$date.' AND Log.time < '.$end;
		} else {
			if($this->p['events_date']) {
				$date_query = 'AND Log.info LIKE "%'.$this->p['events_date'].'%"';
			} else {
				$this->request->query['events_date'] = date('d.m.Y');
				$date_query = 'AND Log.info LIKE "%'.$this->p['events_date'].'%"';
			}
		}
		$station_id = $this->p['station_id'];
		$type = $this->p['type'];
		$station = $this->Station->find('first', array('conditions' => 'id="'.$station_id.'"'));
		$station_name = $station['Station']['name'].'';
		if($type == '_error') {
			$logs = $this->Log->find('all', array('recursive' => 1, 'order' => 'Log.time DESC', 'conditions' => 'Log.type LIKE "%'.$type.'"'));
		} else
			$logs = $this->Log->find('all', array('recursive' => 1, 'order' => 'Log.time', 'conditions' => 'Log.type LIKE "%'.$type.'" AND Log.info LIKE "%'.$station_name.'%" '.$date_query));
		$logTypes = console::$logTypes;
		$chartData = array(); $ykeys = array();
		if($type != '_error')
		foreach($logs as $i => $log) {
			$l = $log['Log'];
			$la = explode("\n", $l['info']);
			foreach($la as $li) {
				$chartData[$i]['x'] = date('Y-m-d H:i:s', $l['time']);
				if(strpos($li, ':')) {
					$lo = explode(":", $li);
					$chartData[$i][trim($lo[0])] = trim($lo[1]);
					$ykeys[trim($lo[0])] = 1;
				}
			}
		}
		//debug($chartData);
		$ykeys = array_keys($ykeys);
		$this->set(compact('logs', 'stations', 'logTypes', 'chartData', 'ykeys'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Log->id = $id;
		if (!$this->Log->exists()) {
			throw new NotFoundException(__('Invalid log'));
		}
		$this->set('log', $this->Log->read(null, $id));
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
