<?php
/**
 * Inputs Controller
 *
 * This file is the inputs controller.
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://book.cakephp.org/2.0/en/controllers.html
 * @package       app.Controller
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
 
App::uses('AppController', 'Controller');
/**
 * Inputs Controller
 *
 * @property Input $Input
 */
class InputsController extends AppController {
	var $marker = '_###_'; //marker to start and end a select in the view before replacing those markers with selects 
	var $limiter = '_---_'; //Limiter inside a marker to delimit title and path
	var $title_limiter = '_____'; //To delimit the column headlines in a CSV file


	/**
	 * add method
	 * @param $project_id Project ID to which the Input shall be added
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
				$this->writeLog('created', array($this->Auth->user('id'), 'inputs', 'related' => $this->Input->getLastInsertId()));
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
	 * Edit method, Displays the template file and let's the user set some parameters for the input format. E.g. in which row starts the data, timestamp format, column delimiter (in CSV)
	 * The function which replaces the marks with HTML-Selects is found in the view file View/Input/edit. It maybe has to be adapted for other formats than CSV.
	 * The user can setup a source list URL to automatically import data from a source.
	 * @throws NotFoundException
	 * @param string $id Input ID
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
		else if($input['Input']['type'])
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
				$this->writeLog('edited', array($this->Auth->user('id'), 'inputs', 'related' => $id));
				$this->Session->setFlash(__('The input has been saved'));
				//debug($r);
				if(is_array($r['Value']['Value']))
					foreach($r['Value']['Value'] as $i => $v) {
						$path = $r['Value']['path'][$i];
						$this->Input->query('UPDATE inputs_values SET path="'.$path.'" WHERE input_id='.$id.' AND value_id='.intval($v));
					}
				if(!$filename && !$this->request->data['refresh'])
					$this->redirect(array('action' => 'view', 'controller' => 'projects', $project_id));
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
		$skeleton = htmlspecialchars($skeleton); // Prepare for display
		$values = $this->Input->Value->find('list', array('conditions' => 'Value.project_id = '.$project_id));
		$this->set(compact('marker', 'limiter', 'title_limiter', 'name', 'values', 'skeleton', 'saved_paths', 'input'));
		$this->render('add');
	}
	
	/**
	* Converts a XML file structure into an array (Needs to be adapted to real formats if there is a use case)
	* @param $file Path to file to parse
	* @return array
	*/
	public function parseXML($file){
		$t = file_get_contents($file);
		$d = f::xml_to_json('<root>'.$t.'</root>');
		$d = $this->parseArray($d);
		$d = f::arr_to_xml($d, $xml);
		return ($d); 
	}
	
	/**
	* Converts a JSON file structure into an array 
	* @todo Needs probably to be adapted to real foramts
	* @param $file Path to file to parse
	* @return array
	*/
	public function parseJSON($file){
		$t = file_get_contents($file);
		$d = json_decode($t, true);
		$d = $this->parseArray($d);
		$d = json_encode($d, JSON_PRETTY_PRINT); //Good readable format
		return $d;
	}
	
	/** 
	* Parses a textfile and sets marker to put in the view the HTML-selects for the values in the correct position with the correct title
	* @param string $file: file path to a template file
	* @param string $delimiter: one or more characters which delimit the columns in the CSV
	* @param int $data_row: number of the row where the actual data begins in the text file
	* @param int $head_row: number of the row where headline information is found for the columns
	*/
	public function parseTEXT($file, $delimiter = ',', &$data_row = -1, &$head_row = -1){
		//$file = '../webroot/uploads/input_545950577d717xA80146_36765237.txt';
		//$file = '../Test/test.txt';  = '../Test/test1.json'
		if(!file_exists($file)) {
			$this->er('The input template file cannot be found. Please upload a new one.');
			$rows = array();
		} else {
			$rows = file($file);
		}
		//debug($file);
		$paths = array();
		$data_row--;
		$head_row--;
		$cols_count = 0;
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
					$cols_count = count($cols);
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

		if($cols_count > 0) //If the data row was not set up and no columns were found
			$rows = implode("\n", $rows);
		else
			$rows = implode("", $rows);
		return $rows;
	}
	
	/**
	* parses an array structure and returns a textual representation with markers to replace them with HTML-selects in the view. The path is unique for each select.
	* @param array|string $d: Data structure as array (tree) or as a string (leave of the tree)
	* @param string $path: the current path for $d
	* @return array of markers if $d is an array or one marker if $d is a string
	*/
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
	
	/**
	* returns the file format of a template file.
	* @param string $file: Input file path relative to the webroot folder
	* @return string: text, json or xml
	*/
	public function getTemplateType($file){
		$path = '../webroot'.$file;
		$t = @file_get_contents($path);
		$t = trim($t);
		if(!$t)
			return 'text';
		$c = $t[0];
		if($c == '{' || $c == '[')
			return 'json';
		if($c == '<')
			return 'xml';
		return 'text';
	}
	
	/**
	* Handles the upload of an input template file from input->add or input->edit action.
	* @return string, filename of the template file
	*/
	function upload() {	
		// Initialize filename-variable
		$filename = null;
		if (!empty($this->request->data['Input']['template_file']['tmp_name']) && is_uploaded_file($this->request->data['Input']['template_file']['tmp_name'])) {
			$filename = basename($this->request->data['Input']['template_file']['name']); 
			$filename = 'input_'.uniqid().$filename;
			$filepath = 'uploads' . DS .$filename;
			$filename = '/' . 'uploads' . '/' . $filename;
			move_uploaded_file(
				$this->data['Input']['template_file']['tmp_name'],
				WWW_ROOT . $filepath
			);
			/*
			//debug(WWW_ROOT . $filepath);
			$encoding = mb_detect_encoding(WWW_ROOT . $filepath);
			//debug($encoding);
			$file = file_get_contents(WWW_ROOT . $filepath);
			
			$file = iconv($encoding, 'UTF-8', $file);
			file_put_contents(WWW_ROOT . $filepath, $file);
			//mb_convert_encoding(WWW_ROOT . $filepath, 'UTF-8');
			*/
			$content = file_get_contents(WWW_ROOT . $filepath);
			$encoding = mb_detect_encoding($content);
			if(!$encoding) {
				$content = mb_convert_encoding($content, 'UTF-8');
				file_put_contents(WWW_ROOT . $filepath, $content);
			}
			if(!$content) {
				$this->er(__('The provided template file is empty. If the original file is not empty it can be a problem with the encoding of the uploaded file. Please verify that the file is encoded with UTF-8 if it contains UTF-8 characters, for example.'));
			}
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
		$this->delete_object('Input', $id);
	}
}
