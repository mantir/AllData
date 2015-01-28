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
		$this->Project->recursive = 1;
		if (!$this->Project->exists()) {
			throw new NotFoundException(__('Invalid project'));
		}
		$this->Project->Behaviors->load('Containable');
		$this->loadModel('Method');
		$p = $this->Project->find('first', array('conditions' => array('Project.id' => $id), 'contain' => array('Value' => array('Input', 'Unit', 'Method'), 'Input')));
		foreach($p['Value'] as $i =>  $v) {
			if($v['method_id'])
				$p['Value'][$i]['execName'] = $this->Method->getExecName($v['Method']['name'], $v['Method']['params'], $v['method_params'], Set::combine($p['Value'], '{n}.id', '{n}'));
		}
		//debug($p);
		$this->set('project', $p);
		$this->project_display_session($p);
	}
	
	/*stores a current project to the session to output it on top of the page and to know in which project the user is currently navigating
	@p: project record from database
	*/
	private function project_display_session($p){
		$this->Session->write('Project', array('id' => $p['Project']['id'], 'name' => $p['Project']['name']));
	}
	
	/*returns a link list with files that could be imported from a source URL for an input
	@input: input_id or input record from database to get array of links from source
	@return array of links
	*/
	public function get_import_list($input){
		if(!is_array($input)) {
			$this->Project->recursive = 0;
			$input = $this->Project->Input->read(null, $input);
		}
		$f = file_get_contents($input['Input']['list_url']);
		$f = console::pre_regex($f);
		//debug($input['Input']['link_regex']);
		preg_match_all('#'.$input['Input']['link_regex'].'#', $f, $m);
		$links = $m[0];
		foreach($links as $i => $l)
			$links[$i] = f::url_to_absolute($input['Input']['list_url'], $l);
		return $links;
	}
	
	/*returns all already imported urls from a source
	@input: input_id or input record from database to get already imported links from logs
	@return array of logs from database
	*/
	public function get_imported_links($input){
		if(is_array($input)) {
			$input = $input['Input']['id'];
		}
		$logs = $this->getLogs('imported_url', array('related_id' => $input), 'data');
		return $logs;
	}
	
	/*imports either all passed links into the system or checks the list-URL for new input if there is something new to import
	@input_id: of the input for which to check for new files from source
	@links: array of links to remote files which should be imported, if not provided, the input source is checked for new links
	@return true or false for import succeeded or not not
	*/
	public function update_imports($input_id = null, $links = false) {
		if(!$input_id) {
			return false;
		}
		if(!$links && $link = $this->request->query['link'])
			$links = array($link);
		if($links && !is_array($links))
			$links = array($links);

		$this->loadModel('Input');
		$this->loadModel('Log');
		$this->Input->id = $input_id;
		$this->Input->recursive = 1;
		if (!$this->Input->exists()) {throw new NotFoundException(__('Invalid Input'));}
		$input = $this->Input->read(null, $input_id);
		if($lurl = $input['Input']['list_url'] || $links) {
			if(!$links) {
				$links = $this->get_import_list($input);
				$logs = $this->get_imported_links($input);
			} else {
				$import_all = true;
			}
			foreach($links as $l) {
				if(!$logs[$l] || $import_all) {
					$filename = $this->store_data($input['Input']['id'], $l, $input['Input']['page_container']);
					//$filename = '/uploads/import_201412150055_548e238b0f7a5.txt';
					$this->import($input_id, $filename);
				}
			}
			//debug($logs);
		}
		$this->set('import_succeeded', true);
		$this->render('../dummy');
		return true;
	}
	
	/*Imports a locally stored file into the database
	 @input_id: of the Input, that maps the data onto the values
	 @filename: relative filepath to the file to be imported
	 @return true or false for import succeeded or not not
	*/
	public function import($input_id = null, $filename = null) {
		if(!$input_id || !$filename) {
			return false;
		}
		$real_filename = $filename;
		$filename = '../webroot'.$filename;
		$this->loadModel('Measure');
		$this->loadModel('Input');
		$this->Input->id = $input_id;
		$this->Input->recursive = 1;
		if (!$this->Input->exists()) {throw new NotFoundException(__('Invalid Input'));}
		$input = $this->Input->read(null, $input_id);
		
		$type = $input['Input']['type'];
		$import_timestamp = time();

		switch($type) {
			case "text": $measures = $this->parseTEXT($filename, $input['Value'], $input['Input']['delimiter'], $input['Input']['data_row'], $input['Input']['timestamp_pos'], $input['Input']['timestamp_format']);
			break;
			case "json": break;
			case "xml": break;
		}
		$earliest = 1000000000000000;
		$latest = -1000000000000000;
		foreach($measures as $i => $m){
			if($m['timestamp'] > $latest)
				$latest = $m['timestamp'];
			if($m['timestamp'] < $earliest)
				$earliest = $m['timestamp'];
			$measures[$i]['import_timestamp'] = $import_timestamp;
			$measures[$i]['original_data'] = $m['data'];
		}
		//debug($measures);
		$this->writeLog('imported_file', array($import_timestamp, $real_filename, count($measures), $earliest, $latest, 'related' => $input_id));
		//debug(time());
		$this->Measure->saveAll($measures);
		//Doppelte Einträge löschen
		$this->deleteDuplicateMeasures();
		return true;
	}
	
	/*Imports a manually uploaded file or makes a guided import from the source URL of an input
	@id: project id for which the upload shell be performed, if not provided the current id is read from the session
	@return void
	*/
	public function upload_import($id = null) {
		if(!$id) {
			$id = $this->Session->read('Project');
			$id = $id['id'];
		}
		$this->Project->id = $id;
		$this->Project->recursive = 0;
		if (!$this->Project->exists()) {throw new NotFoundException(__('Invalid project'));}
		
		if($this->request->data['import_source']){ //import from source
			$input = $this->Project->Input->read(null, $this->request->data['Input']);
			$links = $this->get_import_list($input);
			$imported_links = $this->get_imported_links($input);
			$this->set(compact('links', 'imported_links'));
		} else
		if($this->request->data['links_import']) {
			$input = $this->Project->Input->read(null, $this->request->data['Input']);
			$links_i = $this->request->data['links_import'];
			$links_url = $this->request->data['links_url'];
			$links = array();
			foreach($links_i as $i => $li) {
				if($li) 
					$links[] = $links_url[$i];
			}
			$input = $this->Project->Input->read(null, $this->request->data['Input']);
			$this->update_imports($this->request->data['Input'], $links);
		} else
		if ($this->request->is('post') && $this->request->data['import_file']['name']) { //import from file
			//$filename = $this->store_data($this->request->data['Input']);
			$filename = '/uploads/import_1415156048xA80146_36765237.csv';
			$imported = $this->import($this->request->data['Input'], $filename);
			if($imported) {
				$this->redirect(array('action' => 'data', $id, '?' => array('import_timestamp' => $import_timestamp)));
			}
			
		}
		
		$project = $this->Project->read(null, $id);
		$inputs = $this->Project->Input->find('list');
		$this->set(compact('project', 'inputs', 'input'));
		$this->project_display_session($project);
		$this->render('import');
	}
	
	/*
	delete duplicate measures from database, duplicates have the same timestamp and value_id
	@return void
	*/
	public function deleteDuplicateMeasures(){
		$this->loadModel('Measure');
		//Alle doppelten Einträge entfernen. Doppelte Einträge, haben den selben timestamp und die selbe value_id
		$sel_query = 'SELECT MIN(id) as mid FROM measures GROUP BY timestamp,value_id HAVING COUNT(*) > 1';
		/*$dmeasures = $this->Measure->query('
			SELECT id FROM measures WHERE id NOT in 
				(SELECT MAX(id) as mid FROM measures GROUP BY timestamp,value_id HAVING COUNT(*) > 1)
			AND id NOT IN 
				(SELECT id FROM measures GROUP BY timestamp,value_id HAVING COUNT(*) = 1)');*/
		
		//Flag conflicts with conflict_data
		$this->Measure->query('UPDATE measures m LEFT JOIN measures c ON (m.timestamp = c.timestamp AND m.value_id = c.value_id AND m.data != c.data) SET m.conflict_data = c.data');
		$this->Measure->cacheQueries = false;
		$dmeasures = $this->Measure->query($sel_query); 
		$c = 0;
		while(count($dmeasures)) {
			$c++;
			foreach($dmeasures as $i => $dm)
				$dmeasures[$i] = $dm[0]['mid'];
			//debug('DELETE FROM measures WHERE id IN ('.implode(',',$dmeasures).')');
			
			$this->Measure->query('DELETE FROM measures WHERE id IN ('.implode(',',$dmeasures).')');
			$dmeasures = $this->Measure->query($sel_query);
			if($c >= 1) break;
		}
	}
	
	public function parseXML($filename, $values){
	}
	
	public function parseJSON($filename, $values){
	}
	
	/*Parses the data from a CSV file into an array of Measures to the corresponding values
	@filename: relativ filepath to the input file with the data
	@values:array of fetched relevant values from the database
	@delimiter: for the CSV file
	@data_row: row in input file where data begins
	@$timestamp_pos: column where the timestamp for each row is found
	@timestamp_format: PHP data format to parse the incoming timestamp time format
	@return array of Measure format array('timestamp' =>, 'data' =>, 'value_id'=>) to store to the database 
	*/
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
					$timestamp = trim($cols[$timestamp_pos]);
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
					if(!$timestamp) //Without timestamp, no saving to the database
						continue;
					foreach($values as $v){
						if($v['id'] != -1) { //Don't save the timestamp (marked with -1) extra
							$m = array(
								'timestamp' => $timestamp, 
								'data' => trim($cols[$v['InputsValue']['path']]),  //read column for value from input file row
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
	
	/*Visualizes the data for a project
	@project_id: id of the project to load the data for
	?query params:
		@value_id: id for if only measures for one value should be returned, this only called so that not all measures must be laoded at the start to avoid too big data returns
		@values: id's of the currently selected values
		@start: start date in format d.m.Y
		@end: end date in format d.m.Y
		@start_hour: hour of the day (0-23) for the start date
		@start_minute: minute of start_hour (0-60) for the start date
		@end_hour: hour of the day (0-23) for the end date
		@end_minute: minute of end_hour (0-60) for the end date
		@import_timestamp: timestamp of an import to show data for, if no start and end date is provided
	@return void
	*/
	public function data($project_id = null, $splitted = null){
		$this->loadModel('Measure');
		$this->loadModel('Value');
		$this->loadModel('Method');
		if(!$project_id) {
			$project_id = $this->Session->read('Project');
			$project_id = $project_id['id'];
		}
		$this->Project->id = $project_id;
		$this->Value->Behaviors->load('Containable');
		
		$form_values = $this->request->query['values'];
		if(is_array($form_values)) $form_values = array_flip($form_values); //Exchanges all keys with their associated values in an array
		
		$cond = array(); //array('Measure.value_id' => array_keys($values));
		$conds = array();
		$p = $this->request->query;
		$value_id = $p['value_id'];
		$start = $p['start'];
		$end = $p['end'];
		if($start) {
			$start = strtotime($start.', '.intval($p['start_hour']).':'.intval($p['start_minute']));
			$end = $end ? strtotime($end.', '.intval($p['end_hour']).':'.intval($p['end_minute'])) : strtotime('00:00');
			if($start > $end) {
				$h = $start;
				$start = $end;
				$end = $h;
				$this->request->query['start'] = date('d.m.Y', $start);
				$this->request->query['end'] = date('d.m.Y', $end);
			}
			$conds = array('Measure.timestamp >' => $start, 'Measure.timestamp <' => $end);
		} else {
			$start = strtotime('00:00 -2 weeks');
			$this->request->query['start'] = date('d.m.Y', $start);
			$this->request->query['start_hour'] = '00';
			$this->request->query['start_minute'] = '00';
			$end = strtotime('00:00');
			$this->request->query['end'] = date('d.m.Y', $end);
			$this->request->query['end_hour'] = '00';
			$this->request->query['end_minute'] = '00';
			$conds = array('Measure.timestamp >' => $start, 'Measure.timestamp <' => $end);
		}
		$conds = array_merge($cond, $conds);
		$vconds =  array('Value.project_id' => $project_id);
		$limit = 1;
		if($value_id) {
			$vconds['Value.id'] = $value_id;
			$limit = 0;
		}
		try {
			$values = $this->Value->find('all', array('conditions' => $vconds, 'order' => 'Value.name ASC', 'contain' => array('Unit', 'Measure' => 
				array('fields' => 'Measure.data,Measure.timestamp', 'order' => 'Measure.timestamp ASC', 'limit' => $limit, 'conditions' => $conds)))
			);
		} catch (Exception $e){
			$values = array();
			$this->er($e);
		}
		$values = Set::combine($values, '{n}.Value.id', '{n}'); //set the value id as the key for each value
		foreach($values as $i => $v){
			$v['Measure'] = $this->Measure->short_keys($v['Measure']); //shorter names for less payload when sending data to the browser
			/*foreach($v['Measure'] as $j => $m) {
				$m = array('d' => $m['data'], 't' => $m['timestamp']); //shorter names for less payload when sending data to the browser
				$v['Measure'][$j] = $m;
			}*/
			
			$values[$i] = $v;
		}
		
		//If just requesting one value don't prepare unnecessary variables for the view
		if(!$value_id) { 
			//Get list of import timestamps
			$imports = $this->Measure->find('list', array('fields' => 'Measure.import_timestamp,Measure.import_timestamp', 'order' => 'Measure.import_timestamp DESC', 'conditions' => array('value_id' => array_keys($values)), 'group' => 'Measure.import_timestamp'));
			//Get list of available methods 
			$methods = $this->Method->find('list', array());
			
			foreach($imports as $i => $import)
				$imports[$i] = date('H:i:s, d.m.Y', $i);
			
			$unit_prefixes = console::$unit_prefixes;
			$project = $this->Project->read(null, $project_id);
			$this->project_display_session($project);
			$this->set(compact('project', 'values', 'imports', 'methods', 'unit_prefixes', 'import_timestamp', 'form_values'));
		} else {
			$this->set(compact('values'));
			$this->layout = 'json';
		}
	}
	
	//Stores data from current upload with name import_file into the webroot/uploads folder. 
	//@url: If the param URL is provided it get the data from a remote file. 
	//@page_container: The page_container parameter can be a regex or a jQuery selector to parse the actual content out of a container
	function store_data($input_id, $url = false, $page_container = false) {	
		// Initialize filename-variable
		$filename = null;
		//debug($this->request->data['import_file']);
		$is_upload = !empty($this->request->data['import_file']['tmp_name']) && is_uploaded_file($this->request->data['import_file']['tmp_name']);
		if ($is_upload) {
			$filename = basename($this->request->data['import_file']['name']); 
		} else if($url) {
			$filename = basename($url); 
		}
		
		$filename = 'import_'.date('YmdHis').'_'.$input_id.'.txt';
		$filepath = DS . 'uploads' . DS . $filename;
		$filename = '/' . 'uploads' . '/' . $filename;
		
		if($is_upload) {
			//$c = file_get_contents($this->data['import_file']['tmp_name']);
			move_uploaded_file(
				$this->data['import_file']['tmp_name'],
				WWW_ROOT . $filepath
			);
		} else if($url) {
			$c = file_get_contents($url);
			if($page_container) {
				App::import('Vendor', 'phpQuery');
				pq('load', $c);
				//debug($c);
				if(pq($page_container)->length == 1)
					$c = pq($page_container)->html();
				else {
				}
			}
			$c = utf8_encode(trim($c));
			file_put_contents(WWW_ROOT . $filepath, $c);
			$this->writeLog('imported_url', array($url, $filename, 'related' => $input_id));
		}
		
		// Set the file-name only to save in the database
		$this->data['import_file'] = $filename;
		return $filename;
	}
	
	/*
	@value_id: id of the value
	@start: timestamp
	@end: timestamp
	@params: array or the params for the execution, are fetched from the query string
	*/
	public function calculate_value($value_id, $params = false){
		$this->layout = 'json';
		$this->loadModel('Value');
		if( !$params )
			$params = $this->request->query; 
		$this->Value->id = $value_id;
		if (!$this->Value->exists()) {
			throw new NotFoundException(__('Invalid value'));
		}
		$v = $this->Value->read(null, $value_id);
		if(!$v['Value']['method_id']) {
			$this->er(__('No Method for this Value defined.'));
			$this->render('../dummy');
			return;
		}
		$start = console::makeTimestamp($params['start']);
		$end = console::makeTimestamp($params['end']);
		$project_id = $v['Value']['project_id'];
		$vparams = json_decode($v['Value']['method_params'], true);
		$params = array_merge($params, $vparams);
		$this->loadModel('Method');
		$result = $this->Method->execute($v['Method']['id'], $params, $start, $end, $v['Value']['interval_count'], $v['Value']['interval_type'], true);
		if(is_array($result)){
			$import_timestamp = time();
			foreach($result as $i => $r){
				$result[$i]['import_timestamp'] = $import_timestamp;
				$result[$i]['value_id'] = $value_id;
				$result[$i]['original_data'] = $r['data'];
			}
			//debug($result);
			$this->Value->Measure->saveAll($result);
			$this->deleteDuplicateMeasures();
		} else {
			
		}
		$this->render('../dummy');
	}
	
	/*
	@id: Project id
	@query params:
		@start: Startdate day.month.year,hour:minute:second
		@end: Enddate day.month.year,hour:minute:second
		@format: Possible values: csv, sensorML, xml, json (Optional, standard: csv)
		@fields: Kommagetrennte Liste mit Feldern, die geholt werden sollen (Optional, Liste möglicher Felder in der Dokumentation)
		@exclude: 0 oder 1, gibt an ob Events aus Ergbnisliste entfernt werden sollen, wenn im filter angegebene Bedingungen aus verbundenen Modellen nicht zutreffen (Optional, Standard: 1)
		@debug: bei 1 erfolgt die Ausgabe formatiert
		@dateformat: Wenn gegeben (Gültiges PHP-Date-Format, siehe http://php.net/manual/de/function.date.php), dann wird ein zusätzlicher Wert namens "datetime" zu allen Sendungen ausgegeben: Beispiel: (d.m.Y, H:i) 
	*/
	public function export($id) {
		$this->loadModel('Value');
		$p = $this->p;
		$start = strtotime($p['start']);
		$end = strtotime($p['end']);
		
		//$id noch als parameter
		if(!$end) {
			$end = strtotime('tomorrow 00:00', $start);
		}
		$debug = $p['debug'];
		$format = $p['format'];
		if(!$format) 
			$format = 'csv';
		if(!$debug) {
			if($format == 'json') $hFormat = 'javascript';
			if($format == 'csv') $hFormat = 'javascript';
			$this->response->type('text/'.$hFormat); //Rückgabetyp setzen für das was der Browser bekommt
		} else
			$format = 'json';

		$include = explode(',', $p['inc']); //ist eine kommagetrennte Liste ohne Leerzeichen mit den Dingen, die mitexportiert werden sollen. Möglich sind: links, tags, videos, texts, attributes, images, similar_videos

		$this->Value->Behaviors->load('Containable'); //Containable lässt einfach mehrere Objekte zusammenhängend aus der Datenbank holen mit Logik ohne SQL-Queries zu formulieren


		$conds = array('Measure.timestamp >=' => $start, 'Measure.timestamp <' => $end); //Sendedaten angegeben


		$value_ids = $this->Value->find('list', array('fields' => array('id', 'id'), 'conditions' => array('Value.project_id' => $id))); 
		$options =  array(
			'fields' => $fields, 
			'conditions' => array('Value.id' => $value_ids), 
			'contain' => array(
				'Measure' => array(
					'fields' => 'data,timestamp', 
					'conditions' => $conds, 
					'order' => 'Measure.timestamp ASC'), 
				'Unit'), 
			'order' => '');

		$values = $this->Value->find('all', $options);
		$data = array();
		foreach($values as $i => $v){
			$values[$i]['Measure'] = $v['Measure'] = Set::combine($v['Measure'], '{n}.timestamp', '{n}');
			foreach($v['Measure'] as $j => $m) {
				if(!$data[$j])
					$data[$j] = array();
			}
		}
		foreach($values as $i => $v){
			foreach($data as $j => $d) {
				$data[$j][] = $v['Measure'][$j]['data'];
			}
		}
		//debug($include);
		//debug($filterAfter);

		
		foreach($data as $i => $d) {
			if($this->p['dateformat']) {
				array_unshift($data[$i], date($this->p['dateformat'], $i));
			} else
				array_unshift($data[$i], $i);
		}
		
		$data[0] = array_merge(array('timestamp'), array_map(function($v){
			return $v['Value']['name'].' ('.$v['Value']['prefix'].$v['Unit']['symbol'].')';
		}, $values));
		ksort($data);
		//debug($data);
		/*} catch(Exception $e) {
			echo 'Es ist ein Fehler aufgetreten.';
		}*/
		//debug($this->Event->lastQuery());
		//$this->response->header(array('Content-type: text/javascript; charset=UTF-8'));
		$this->layout = 'template';
		$this->set(compact('data', 'debug', 'format'));
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
