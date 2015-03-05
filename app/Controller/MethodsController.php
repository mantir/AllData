<?php
/**
 * Methods Controller
 *
 * This file is the method controller.
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://book.cakephp.org/2.0/en/controllers.html
 * @package       app.Controller
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
 
App::uses('AppController', 'Controller');
/**
 * Methods Controller
 *
 * @property Method $Method
 */
class MethodsController extends AppController {
	public $paginate = array(
        'limit' => 1000000,
		'maxLimit' => 1000000
    );
	
	/**
	 * index method for the admin
	 * Lists all methods in the system for the Super Admin
	 * @return void
	 */
	public function admin_index() {
		$this->Method->recursive = 1;
		$this->set('methods', $this->paginate());
	}
	
	/**
	* @param int $id ID of the method to get the parameter definitions for
	* @param int $project_id ID of the project for which its values can be set for value parameters
	* @return void
	*/
	public function get_params($id, $project_id = null){
		if(!$project_id) {
			$project = $this->Session->read('Project');
			$project_id = $project['id'];
		}
		if(!$project_id) {
			$this->er(__('The project ID does not exist'));
		}
		if(!$this->authorizedProject($project_id, console::$contributorState)) {
			return;
		}
		$this->Method->id = $id;
		$method = $this->Method->read(null, $id);
		if(!$method) {
			$this->er(__('The method ID does not exist'));
		}
		$params = $this->Method->parse_params($method['Method']['params']);
		$this->loadModel('Value');
		$values = $this->Value->find('list', array('fields' => 'Value.id, Value.name', 'order' => 'Value.name ASC', 'conditions' => 'Value.project_id="'.$project_id.'"'));
		$this->set(compact('params', 'values', 'method'));
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param int $id ID of the method to be shown
	 * @param int $project_id ID of the project in which the method should be executed after configuration
	 * @return void
	 */
	public function view($id = null, $project_id = null) {
		if(!$project_id) {
			$this->er('You must be in a project to execute a methode.');
			$this->render('../dummy');
		}
		if(!$this->authorizedProject($project_id, console::$contributorState)) {
			return;
		}
		$this->Method->id = $id;
		if (!$this->Method->exists()) {
			throw new NotFoundException(__('Invalid method'));
		}
		$method = $this->Method->read(null, $id);
		$params = $this->Method->parse_params($method['Method']['params']);
		$this->loadModel('Value');
		if($this->request->query['values'][0] < 0)
			array_shift($this->request->query['values']);
		//
		foreach($params as $p) {
			if($p['type'] == 'val') {
				if(!$this->request->query[$p['name']] && is_array($this->request->query['values']))
					$this->request->query[$p['name']] = array_shift($this->request->query['values']);
			}
		}
		if(!isset($this->request->query['interval_type'])) $this->request->query['interval_type'] = 3;
		//debug($this->request->query);
		$values = $this->Value->find('list', array('fields' => 'Value.id, Value.name', 'order' => 'Value.name ASC', 'conditions' => 'Value.project_id="'.$project_id.'"'));
		$intervalTypes = console::$intervalTypes;
		$this->set(compact('method', 'params', 'values', 'intervalTypes'));
	}

	/**
	 * add method
	 * @param int $project_id: ID of the project the method is created for. If a super admin creates a global function the ID can be empty
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
		$this->request->data['Method']['project_id'] = $project_id;
		if ($this->request->is('post')) {
			$this->Method->create();
			if ($this->Method->save($this->request->data)) {
				$this->writeLog('created', array($this->Auth->user('id'), 'methods', 'related' => $this->Method->getLastInsertId()));
				$this->Session->setFlash(__('The method has been saved'));
				$this->redirect(array('action' => 'edit', $this->Method->getLastInsertId()));
			} else {
				$this->Session->setFlash(__('The method could not be saved. Please, try again.'));
			}
		}
	}
	
	/**
	* Executes a method with given URL parameters and a start and an end time and returns the result in JSON to the Browser
	*
	* @param int $method_id ID of the Method to be executed
	* @param $_GET['start']: date in the form day.month.year
	* @param $_GET['end']: date in the form day.month.year 
	* @param $_GET['start_hour']: (optional) Start hour 0 - 23
	* @param $_GET['start_minute']: (optional) Start minute 0-59
	* @param $_GET['end_hour']: (optional) End hour 0 - 23
	* @param $_GET['end_minute']: (optional) End minute 0-59
	* @return void
	*/
	public function execute($method_id = false){
		$this->layout = 'json';
		if(!$method_id) {
			$this->er(__('No method was found for the given method ID'));
			$this->render('../dummy');
			return;
		}
		$method = $this->Method->read(null, $method_id);
		$this->loadModel('Measure');
		$p = $this->request->query;
		$start = $p['start'];
		$end = $p['end'];
		$start = strtotime($start.', '.$p['start_hour'].':'.$p['start_minute']);
		$end = $end ? strtotime($end.', '.$p['end_hour'].':'.$p['end_minute']) : time();
		
		$result = $this->Method->execute($method_id, $p, $start, $end, $p['interval_count'], console::$intervalTypes[$p['interval_type']]);
		$result = $this->Measure->short_keys($result); //shorter names for less payload when sending data to the browser
		
		$execName = $this->Method->getLastExecutionName();
		$this->set(compact('debug', 'result', 'method', 'execName')); 
		
		$this->render('../dummy');
	}

	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {
		$this->Method->id = $id;
		if (!$this->Method->exists()) {
			throw new NotFoundException(__('Invalid method'));
		}
		$m = $this->Method->read(null, $id);
		if(!$this->authorizedProject($m['Method']['project_id'], console::$contributorState)) {
			return;
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$paramCheck = $this->Method->check_params($this->request->data['Method']['params']);
			$codeCheck = $this->Method->check_code($this->request->data['Method']['code']);
			if (!$paramCheck && !$codeCheck && $this->Method->save($this->request->data)) {
				$this->Session->setFlash(__('The method has been saved'));
				$this->writeLog('edited', array($this->Auth->user('id'), 'methods', 'related' => $id));
				$this->redirect(array('action' => 'edit', $id));
			} else {
				$this->er(__('The method could not be saved. ').$paramCheck.' '.$codeCheck);
			}
		} else {
			$this->request->data = $this->Method->read(null, $id);
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
		$this->delete_object('Method', $id);
	}
}
