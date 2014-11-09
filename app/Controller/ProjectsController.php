<?php
App::uses('AppController', 'Controller');
/**
 * Projects Controller
 *
 * @property Project $Project
 */
class ProjectsController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Session->write('Project', false);
		$this->Project->recursive = 0;
		$this->set('projects', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Project->id = $id;
		$this->Project->recursive = 2;
		if (!$this->Project->exists()) {
			throw new NotFoundException(__('Invalid project'));
		}
		$p = $this->Project->read(null, $id);
		$this->set('project', $p);
		$this->Session->write('Project', array('id' => $p['Project']['id'], 'name' => $p['Project']['name']));
	}
	

	public function import($id = null) {
		if(!$id) {
			$id = $this->Session->read('Project');
			$id = $id['id'];
		}
		$this->Project->id = $id;
		$this->loadModel('Measure');
		$this->loadModel('Input');
		$this->Project->recursive = 2;
		if (!$this->Project->exists()) {throw new NotFoundException(__('Invalid project'));}
		
		if (true || $this->request->is('post')) {
			//$filename = $this->upload();
			$filename = '../webroot/uploads/import_1415156048xA80146_36765237.csv';
			$input = $this->Input->read(null, $this->request->data['Input']);
			if($input) {
				$type = $input['Input']['type'];
				$import_timestamp = time();
				$measures = $this->parseTEXT($filename, $input['Value'], $input['Input']['delimiter'], $input['Input']['data_row'], $input['Input']['timestamp_pos'], $input['Input']['timestamp_format']);
				foreach($measures as $i => $m){
					$measures[$i]['import_timestamp'] = $import_timestamp;
				}
				//debug($measures);
				$this->Measure->saveAll($measures);
				//Doppelte Einträge löschen
				$this->deleteDuplicateMeasures();
				$this->redirect(array('action' => 'data', $id, '?' => array('import_timestamp' => $import_timestamp)));
			}
			
		}
		
		$project = $this->Project->read(null, $id);
		$inputs = $this->Project->Input->find('list');
		$this->set(compact('project', 'inputs'));
	}
	
	private function deleteDuplicateMeasures(){
		$this->loadModel('Measure');
		$dmeasures = $this->Measure->query('
			SELECT id FROM measures WHERE id NOT in 
				(SELECT MAX(id) as mid FROM measures GROUP BY timestamp,value_id HAVING COUNT(*) > 1)
			AND id NOT IN 
				(SELECT id FROM measures GROUP BY timestamp,value_id HAVING COUNT(*) = 1)');
		foreach($dmeasures as $i => $dm)
			$dmeasures[$i] = $dm['measures']['id'];
		//debug($dmeasures);
		if(count($dmeasures))
			$this->Measure->query('DELETE FROM measures WHERE id IN ('.implode(',',$dmeasures).')');
	}
	
	public function parseXML(){
	}
	
	public function parseJSON(){
	}
	
	public function parseTEXT($filename, $values, $delimiter, $data_row, $timestamp_pos, $timestamp_format){
		$d = file($filename);
		$data_row--;
		$measures = array();
		foreach($d as $i => $row) {
			if($i >= $data_row) {
				if($i >= $data_row){
					$limiter = $delimiter;
					$cols = explode($limiter[0], $row);
					$limiter = substr($limiter, 1);
					for ($j = 0; $j < strlen($limiter); $i++) {
						foreach($cols as $col) {
							
						}
					}
					$newRow = array();
					$timestamp = $cols[$timestamp_pos];
					$useStrTime = $timestamp_format ? false : true;
					if($timestamp_format) {
						$date_obj = DateTime::createFromFormat($timestamp_format, $timestamp);
						if(!$date_obj)
							$useStrTime = true;
						else
							$timestamp = date_timestamp_get($date_obj);
					}
					if($useStrTime)	
						$timestamp = strtotime($timestamp);
					if(!$timestamp)
						continue;
					foreach($values as $v){
						if($v['id'] != -1) { //Den Timestamp nicht extra speichern
							$m = array(
								'timestamp' => $timestamp, 
								'data' => $cols[$v['InputsValue']['path']], 
								'value_id' => $v['id']
							);
							$measures[] = $m;
						}
					}
				} else {
					$rows[$i] = trim($rows[$i]);
				}
			}
		}
		return $measures;
	}
	
	public function data($project_id){
		$this->loadModel('Measure');
		$this->loadModel('Value');
		if(!$project_id) {
			$project_id = $this->Session->read('Project');
			$project_id = $project_id['id'];
		}
		$this->Project->id = $project_id;
		$this->Value->Behaviors->load('Containable');
		
		$cond = array(); //array('Measure.value_id' => array_keys($values));
		$conds = array();
		$p = $this->request->query;
		if($p['import_timestamp']) {
			$conds = array('Measure.import_timestamp' => $p['import_timestamp']);
		}
		$conds = array_merge($cond, $conds);
		//$measures = $this->Measure->find('all', array('conditions' => $conds,));
		$values = $this->Value->find('all', array('conditions' => 'project_id = '.$project_id, 'contain' => array('Unit', 'Measure' => 
			array('order' => 'Measure.timestamp ASC', 'conditions' => $conds)))
		);
		$values = Set::combine($values, '{n}.Value.id', '{n}');
		$project = $this->Project->read(null, $project_id);
		$this->set(compact('project', 'values'));
	}
	
	function upload() {	
		// Initialize filename-variable
		$filename = null;
		//debug($this->request->data['import_file']);
		if (!empty($this->request->data['import_file']['tmp_name']) && is_uploaded_file($this->request->data['import_file']['tmp_name'])) {
			$filename = basename($this->request->data['import_file']['name']); 
			$filename = 'import_'.time().$filename;
			$filepath = DS . 'uploads' . DS .$filename;
			$filename = '/' . 'uploads' . '/' . $filename;
			move_uploaded_file(
				$this->data['import_file']['tmp_name'],
				WWW_ROOT . $filepath
			);
		}
		// Set the file-name only to save in the database
		$this->data['import_file'] = $filename;
		return $filename;
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Project->create();
			if ($this->Project->save($this->request->data)) {
				$this->Session->setFlash(__('The project has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The project could not be saved. Please, try again.'));
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
		$this->Project->id = $id;
		if (!$this->Project->exists()) {
			throw new NotFoundException(__('Invalid project'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Project->save($this->request->data)) {
				$this->Session->setFlash(__('The project has been saved'));
				$this->redirect(array('action' => 'view', $id));
			} else {
				$this->Session->setFlash(__('The project could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Project->read(null, $id);
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
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Project->id = $id;
		if (!$this->Project->exists()) {
			throw new NotFoundException(__('Invalid project'));
		}
		if ($this->Project->delete()) {
			$this->Session->setFlash(__('Project deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Project was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
