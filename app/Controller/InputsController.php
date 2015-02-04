<?php
App::uses('AppController', 'Controller');
/**
 * Inputs Controller
 *
 * @property Input $Input
 */
class InputsController extends AppController {
	var $marker = '_###_';
	var $limiter = '_---_';
	var $title_limiter = '_____';
	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		$this->Input->recursive = 0;
		$this->set('inputs', $this->paginate());
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		$this->Input->id = $id;
		if (!$this->Input->exists()) {
			throw new NotFoundException(__('Invalid input'));
		}
		$this->set('input', $this->Input->read(null, $id));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add($project_id) {
		if ($this->request->is('post')) {
			$r = $this->request->data;
			$name = $r['Input']['name'];
			$filename = $this->upload();
			if(!trim($name) || !$filename) {
				$this->render('add');
				$this->er(__('The name must not be empty and it must be uploaded a valid input template.'));
				return;
			}
			$r['Input']['project_id'] = $project_id;
			$r['Input']['template_path'] = $filename;
			$r['Input']['type'] = $this->getTemplateType($filename);
			$this->Input->create();
			if ($this->Input->save($r)) {
				$this->Session->setFlash(__('The input has been saved'));
				$id = $this->Input->getInsertID();
				$this->redirect(array('action' => 'edit', $id));
			} else {
				$this->Session->setFlash(__('The input could not be saved. Please, try again.'));
			}
		}
		$values = $this->Input->Value->find('list', array('conditions' => 'Value.project_id = '.$project_id));
		$this->set(compact('name'));
	}
	
	/**
	 * edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function edit($id = null) {
		$this->Input->id = $id;
		if (!$this->Input->exists()) {
			throw new NotFoundException(__('Invalid input'));
		}
		$r = $this->request->data;
		$input = $this->Input->read(null, $id);
		$filename = $input['Input']['template_path'];
		$filename = '../webroot'.$filename;
	
		if($input['Input']['type'] == 'text')
			$skeleton = $this->parseText($filename, $input['Input']['delimiter'], $input['Input']['data_row'], $input['Input']['head_row']);
		else
			$skeleton = $this->{'parse'.strtoupper($input['Input']['type'])}($filename);
		
		$project_id = $input['Input']['project_id'];
		if ($this->request->is('post') || $this->request->is('put')) {
			$r['Input']['template_path'] = ($filename = $this->upload()) ? $filename : $input['Input']['template_path'];
			if($filename)
				$r['Input']['type'] = $this->getTemplateType($filename);
			if(is_array($r['Value']['Value']))
					foreach($r['Value']['Value'] as $i => $v) {
						if($v == -1) {
							$r['Input']['timestamp_pos'] = $r['Value']['path'][$i];
						}
					}
			if ($this->Input->save($r)) {
				$this->Session->setFlash(__('The input has been saved'));
				debug($r);
				if(is_array($r['Value']['Value']))
					foreach($r['Value']['Value'] as $i => $v) {
						$path = $r['Value']['path'][$i];
						$this->Input->query('UPDATE inputs_values SET path="'.$path.'" WHERE input_id='.$id.' AND value_id='.intval($v));
					}
				if(!$filename && !$this->request->data['refresh'])
					$this->redirect(array('action' => 'view', 'controller' => 'projects', $project_id));
				else
					$this->redirect(array('action' => 'edit', $id));
			} else {
				$this->Session->setFlash(__('The input could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $input;
			$saved_paths = array();
			foreach($input['Value'] as $v) {
				$saved_paths[$v['InputsValue']['path']] = $v['InputsValue']['value_id'];
			}
		}
		$marker = $this->marker;
		$limiter = $this->limiter;
		$title_limiter = $this->title_limiter;
		$skeleton = htmlspecialchars($skeleton); // Für Darstellung vorbereiten
		$values = $this->Input->Value->find('list', array('conditions' => 'Value.project_id = '.$project_id));
		$this->set(compact('marker', 'limiter', 'title_limiter', 'name', 'values', 'skeleton', 'saved_paths', 'input'));
		$this->render('add');
	}
	
	public function parseXML($file = '../Test/test1.xml'){
		$t = file_get_contents($file);
		$d = f::xml_to_json('<root>'.$t.'</root>');
		$d = $this->parseArray($d);
		$d = f::arr_to_xml($d, $xml);
		return ($d); 
	}
	
	public function parseJSON($file = '../Test/test1.json'){
		$t = file_get_contents($file);
		$d = json_decode($t, true);
		$d = $this->parseArray($d);
		$d = json_encode($d, JSON_PRETTY_PRINT); //Schön formatieren
		return $d;
	}
	
	//parses textfile and sets marker to later in the view put the HTML-selects for the values in the correct position with the correct title
	public function parseTEXT($file = '../Test/test1.json', $delimiter = ',', &$data_row = -1, &$head_row = -1){
		//$file = '../webroot/uploads/input_545950577d717xA80146_36765237.txt';
		//$file = '../Test/test.txt';
		if(!file_exists($file)) {
			$this->er('The input template file cannot be found. Please upload a new one.');
			$rows = array();
		} else
			$rows = file($file);
		$paths = array();
		$data_row--;
		$head_row--;
		$header = array();
		if(!$delimiter) $delimiter = ',';
		foreach($rows as $i => $r) {
			if($head_row == $i) {
				$limiter = $delimiter;
				$cols = explode($limiter[0], $r);
				$limiter = substr($limiter, 1);
				foreach($cols as $j => $col) {
					$col = trim(str_replace("\n", '', $col));
					$header[$j] = $col.$this->title_limiter;
				}
				//debug($header);
			}
			if($data_row >= 0) {
				if($i >= $data_row){
					$limiter = $delimiter;
					$cols = explode($limiter[0], $r);
					$limiter = substr($limiter, 1);
					$newRow = array();
					foreach($cols as $j => $col) {
						if(($col = trim(str_replace(',', '.', str_replace("'", '', str_replace('"', '', str_replace("\n", '', $col)))))) !== '') {
							$newRow[] = $this->marker.$header[$j].$col.$this->limiter.$j.$this->marker;
						}
					}
					//debug($newRow);
					$rows[$i] = implode($delimiter[0], $newRow);
				} else {
					$rows[$i] = trim($rows[$i]);
				}
			} else {
				
			}
		}
		$data_row++;
		$head_row++;
		//debug($rows);
		return implode("\n", $rows);
	}
	
	public function parseArray($d, $path = '') {
		if(is_array($d)) {
			foreach($d as $k => $t) {
				if(is_numeric($k)) $add = ''; else $add = '/'.$k;
				$d[$k] = $this->parseArray($t, $path.$add);
			}
			return $d;
		} else {
			return $this->marker.$d.$this->limiter.$path.$this->marker;
		}
	}
	
	public function getTemplateType($file){
		$path = '../webroot'.$file;
		$t = @file_get_contents($path);
		$t = trim($t);
		if(!$t)
			return '';
		$c = $t[0];
		if($c == '{' || $c == '[')
			return 'json';
		if($c == '<')
			return 'xml';
		return 'text';
	}
	
	function upload() {	
		// Initialize filename-variable
		$filename = null;
		if (!empty($this->request->data['Input']['template_file']['tmp_name']) && is_uploaded_file($this->request->data['Input']['template_file']['tmp_name'])) {
			$filename = basename($this->request->data['Input']['template_file']['name']); 
			$filename = 'input_'.uniqid().$filename;
			$filepath = DS . 'uploads' . DS .$filename;
			$filename = '/' . 'uploads' . '/' . $filename;
			move_uploaded_file(
				$this->data['Input']['template_file']['tmp_name'],
				WWW_ROOT . $filepath
			);
		}
		
		// Set the file-name only to save in the database
		$this->data['Input']['template_file'] = $filename;
		return $filename;
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
		$this->Input->id = $id;
		if (!$this->Input->exists()) {
			throw new NotFoundException(__('Invalid input'));
		}
		$i = $this->Input->read(null, $id);
		$pid = $i['Input']['project_id'];
		if ($this->Input->delete()) {
			$this->Session->setFlash(__('Input deleted'));
			$this->redirect(array('action' => 'view', 'controller' => 'projects', $pid));
		}
		$this->Session->setFlash(__('Input was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
