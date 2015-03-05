<?php
/**
 * Projects Controller
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
 * Projects Controller
 *
 * @property Project $Project
 */
class ProjectsController extends AppController {

	/**
	 * Shows a list of all for the users visible projects.
	 * @return void
	 */
	public function index() {
		$this->Session->write('Project', false);
		if($this->Auth->user('isAdmin')) {
			$this->Project->recursive = 0;
			$projects = $this->Project->find('all');
			$projects = Set::extract($projects, '{n}.Project');
		} else {
			$this->loadModel('User');
			$this->User->recursive = 1;
			$user = $this->User->read(null, $this->Auth->user('id'));
			$projects = $user['Project'];
		}
		$this->set('projects', $projects);
	}

	/**
	 * Shows a glance of a project with all Values, Inputs, Project Members, Methods, Units
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
		$p = $this->Project->find('first', array('conditions' => array('Project.id' => $id), 'contain' => array('Value' => array('order' => 'Value.name ASC', 'Input', 'Unit', 'Method'), 'Member', 'Input', 'Export', 'Method', 'Unit')));
		foreach($p['Value'] as $i =>  $v) {
			if($v['method_id'])
				$p['Value'][$i]['execName'] = $this->Method->getExecName($v['Method']['name'], $v['Method']['params'], $v['method_params'], Set::combine($p['Value'], '{n}.id', '{n}'));
		}
		//debug($p);
		$this->set('project', $p);
		$this->project_set_session($p);
	}
	
	
	/**
	* returns a link list with files that could be imported from a source URL for an input
	* @param mixed $input input_id or input record from database to get array of links from source
	* @return array of links
	*/
	public function get_import_list($input){
		if(!is_array($input)) {
			$this->Project->recursive = 0;
			$input = $this->Project->Input->read(null, $input);
		}
		if(!trim($input['Input']['list_url'])){
			$this->er(__('There is no source list URL provided in the input. Please edit the input and add the needed parameters to fetch the data.'));
			return array();
		}
		$f = @file_get_contents($input['Input']['list_url']);
		if(!$f) {
			$this->er(__('The provided source list URL returns en empty result or cannot be found.'));
			return array();
		}
		$f = console::pre_regex($f);
		//debug($input['Input']['link_regex']);
		preg_match_all('#'.$input['Input']['link_regex'].'#', $f, $m);
		$links = $m[0];
		foreach($links as $i => $l)
			$links[$i] = f::url_to_absolute($input['Input']['list_url'], $l);
		return $links;
	}
	
	/**
	* returns all already imported urls from a source
	* @param mixed $input input_id or input record from database to get already imported links from logs
	* @return array of logs from database
	*/
	public function get_imported_links($input){
		if(is_array($input)) {
			$input = $input['Input']['id'];
		}
		$from_url = $this->getLogs('imported_url', array('related_id' => $input), 'data_2'); //data_2 is the filename of the raw data file in the log table
		$from_file = $this->getLogs('imported_file', array('related_id' => $input), 'data_2'); //data_2 is the filename of the raw data file in the log table
		$logs = array();
		foreach($from_file as $file => $ff){
			if(!is_array($from_url[$file])) {
				$from_url[$file] = array();
				continue;
			}
			$logs[$from_url[$file]['data']] = array_merge($from_url[$file], $ff);
		}
		ksort($logs);
		return $logs;
	}
	
	/**
	* Updates all imports by fetching the data from all project related input sources
	* @param project_id Project id of the project for which the imports should be updated
	*/
	public function update_all_imports($project_id) {
		$this->loadModel('Input');
		$inputs = $this->Input->find('list', array('conditions' => array('Input.project_id' => $project_id)));
		if(is_array($inputs))
			$inputs = array_keys($inputs);
		else
			$inputs = array();
		foreach($inputs as $i){
			$this->update_imports($i);
		}
		$this->render('../dummy');
	}
	
	/**
	* imports either all passed links into the system or checks the list-URL of an input for new data if there is something new to import
	* @param $input_id of the input for which to check for new files from source
	* @param $links array of links to remote files which should be imported, if not provided, the input source is checked for new links
	* @param $_GET[link]
	* @return true or false for import succeeded or not
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
		if($lurl = $input['Input']['list_url'] || $links || $this->request->query['auto']) {
			if(!$links) { //No link list given
				$links = $this->get_import_list($input); //Get links to fetch
				$logs = $this->get_imported_links($input); //Get already imported links
				set_time_limit(600);
			} else {
				$import_all = true;
			}
			foreach($links as $l) { //Import all links
				if(!$logs[$l] || $import_all) {
					$filename = $this->store_data($input['Input']['id'], $l, $input['Input']['page_container']); //store raw data file
					$imported = $this->import($input_id, $filename);
					if($imported['imported']) {
						$this->check_data($input['Input']['project_id'], $imported['earliest'], $imported['latest']);
						$this->calculate_values($input['Input']['project_id'], array('start' => $imported['earliest'], 'end' => $imported['latest'])); //Calculate values for time range
					}
				}
			}
		}
		$this->set('import_succeeded', true);
		$this->set('imported', $imported);
		$this->render('../dummy');
		return true;
	}
	
	/**
	* Imports a manually uploaded file or makes a guided import from the source URL of an input
	* @param id project id for which the upload shell be performed, if not provided the current id is read from the session
	* @return void
	*/
	public function upload_import($id = null) {
		if(!$id) {
			$id = $this->Session->read('Project');
			$id = $id['id'];
		}
		$this->Project->id = $id;
		$this->Project->recursive = 0;
		if (!$this->Project->exists()) {throw new NotFoundException(__('Invalid project'));}
		
		//import from source, show list of importable links to be imported with update_imports via AJAX
		if($this->request->data['import_source']){ 
			$input = $this->Project->Input->read(null, $this->request->data['Input']);
			$links = $this->get_import_list($input);
			$imported_links = $this->get_imported_links($input);
			if(!count($input['Input']['list_url'])){
				$this->er(__('There was no input source url found. Please define a List URL by editing the input %s', $input['Input']['name']));
			}
			$this->set(compact('links', 'input', 'imported_links'));
		} else
		
		//import from uploaded file manually
		if ($this->request->is('post') && $this->request->data['import_file']['name']) { 
			$filename = $this->store_data($this->request->data['Input']);
			$imported = $this->import($this->request->data['Input'], $filename);
			if($imported['imported']) {
				$this->check_data($id, $imported['earliest'], $imported['latest']);
				$this->calculate_values($id, array('start' => $imported['earliest'], 'end' => $imported['latest'])); //Calculate values for time range
				$this->redirect(array('action' => 'data', $id, '?' => array('start' => date('d.m.Y',$imported['earliest']), 'end' => date('d.m.Y',$imported['latest']))));
			}
			
		}
		
		$project = $this->Project->read(null, $id);
		$project_id = $id;
		$inputs = $this->Project->Input->find('list', array('conditions' => 'project_id="'.$id.'"'));
		$this->set(compact('project', 'inputs', 'input', 'project_id'));
		$this->project_set_session($project);
		$this->render('import');
	}
	
	/**
	* Imports a locally stored file into the database
	* @param $input_id of the Input, that maps the data onto the values
	* @param $filename relative filepath to the file to be imported
	* @return array [
	* 	imported: true or false for import succeeded or not not, 
	* 	earliest: earliest timestamp imported, 
	* 	latest: latest timestamp imported
	* ]
	*/
	public function import($input_id = null, $filename = null) {
		if(!$filename) {
			$filename = $this->request->query['filename'];
		}
		if(!$input_id || !$filename) {
			return false;
		}
		$real_filename = $filename;
		$filename = '../webroot'.$filename;
		$this->loadModel('Measure');
		$this->loadModel('Value');
		$this->loadModel('Input');
		$this->Input->id = $input_id;
		$this->Input->recursive = 1;
		if (!$this->Input->exists()) {throw new NotFoundException(__('Invalid Input'));}
		$input = $this->Input->read(null, $input_id);
		
		$type = $input['Input']['type'];
		$import_timestamp = time();

		switch($type) {
			case "text": $measures = $this->parseTEXT(
					$filename, 
					$input['Value'], 
					$input['Input']['delimiter'], 
					$input['Input']['data_row'], 
					$input['Input']['timestamp_pos'], 
					$input['Input']['timestamp_format']
				);
			break;
			case "json": break;
			case "xml": break;
		}
		$earliest = console::$maxTime;
		$latest = console::$minTime;
		$values = array();
		$lines = array();
		foreach($measures as $i => $m){
			if($m['timestamp'] > $latest)
				$latest = $m['timestamp'];
			if($m['timestamp'] < $earliest)
				$earliest = $m['timestamp'];
			$lines[$m['timestamp']] = true;
			$measures[$i]['import_timestamp'] = $import_timestamp;
			$measures[$i]['original_data'] = $m['data'];
			$values[$m['value_id']] = true;
		}
		foreach(array_keys($values) as $v) //Check the data for the timespan
			$this->Value->check_data($v, $earliest, $latest);
		//debug($measures);
		$this->writeLog('imported_file', array($import_timestamp, $real_filename, count($measures), $earliest, $latest, count($lines), 'related' => $input_id));
		//debug(time());
		if(!$this->Measure->saveAll($measures)){
			
		}
		//Doppelte Einträge löschen
		$this->deleteDuplicateMeasures();
		return array('imported' => true, 'earliest' => $earliest, 'latest' => $latest);
	}
	
	
	/**
	* Checks the data for all values in a project
	* @param $project_id Project id for which all measures should be checked
	* @param int $start Start timestamp for checking data
	* @param int $end End timestamp for checking data
	*/
	public function check_data($project_id, $start = 0, $end = 0){
		$this->loadModel('Value');
		$values = $this->Value->find('list', array('recursive' => 0, 'conditions' => 'Value.project_id="'.$project_id.'"'));
		
		foreach($values as $id => $v){
			$this->Value->check_data($id, $start, $end);
		}
		$this->render('../dummy');
	}
	
	/**
	* delete duplicate measures from database, duplicates have the same timestamp and value_id
	* @return void
	*/
	public function deleteDuplicateMeasures(){
		$this->loadModel('Measure');
		//Build select query for the earliest imported duplicate
		$sel_query = 'SELECT MIN(id) as mid FROM measures GROUP BY timestamp,value_id HAVING COUNT(*) > 1';
		/*$dmeasures = $this->Measure->query('
			SELECT id FROM measures WHERE id NOT in 
				(SELECT MAX(id) as mid FROM measures GROUP BY timestamp,value_id HAVING COUNT(*) > 1)
			AND id NOT IN 
				(SELECT id FROM measures GROUP BY timestamp,value_id HAVING COUNT(*) = 1)');*/
		
		//Flag conflicts with conflict_data
		$this->Measure->query('UPDATE measures m LEFT JOIN measures c ON (m.timestamp = c.timestamp AND m.value_id = c.value_id AND m.data != c.data) SET m.conflict_data = c.data, m.state=0');
		$this->Measure->cacheQueries = false;
		$dmeasures = $this->Measure->query($sel_query); //Execute query for earliest duplicates 
		$c = 0;
		while(count($dmeasures)) { //Remove earliest duplicates until there are no duplicates
			$c++;
			foreach($dmeasures as $i => $dm)
				$dmeasures[$i] = $dm[0]['mid'];
			
			$this->Measure->query('DELETE FROM measures WHERE id IN ('.implode(',',$dmeasures).')');
			$dmeasures = $this->Measure->query($sel_query);
			if($c >= 1) break;
		}
	}
	
	/**
	* TODO
	* Parses the data from an XML file into an array of Measures to the corresponding values
	*/
	public function parseXML($filename, $values){
		//TODO
	}
	
	/**
	* TODO
	* Parses the data from a JSON file into an array of Measures to the corresponding values
	*/
	public function parseJSON($filename, $values){
		//TODO
	}
	
	/** Parses the data from a CSV file into an array of Measures to the corresponding values
	* @param $filename relativ filepath to the input file with the data
	* @param $valuesarray of fetched relevant values from the database
	* @param $delimiter for the CSV file
	* @param $data_row row in input file where data begins
	* @param $timestamp_pos column where the timestamp for each row is found
	* @param $timestamp_format PHP data format to parse the incoming timestamp time format
	* @return array of Measure format array('timestamp' =>, 'data' =>, 'value_id'=>) to store to the database 
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
	
	/**
	* Visualizes the data of a project. When the whole page is loading then the measures for all values are only fetched grouped by their states to show the correct buttons in the view for the data visualization and manipulation. All data for one value is loaded when the users wants to see the data by selecting a value.
	*
	* The following url parameters can be provided:
	*
	** __start__ - start date in format d.m.Y (Default: Last 2 weeks with data)
	** __end__ - end date in format d.m.Y (Last date with data)
	** __value_id__ - id for if only measures for one value should be returned, this is only called so that not all measures must be loaded at the start to avoid too big data returns
	** __values__ - id's of the currently selected values in the view
	** __start_hour__ - hour of the day (0-23) for the start date (Default: 0)
	** __start_minute__ - minute of start_hour (0-60) for the start date (Default: 0)
	** __end_hour__ - hour of the day (0-23) for the end date (Default: 0)
	** __end_minute__ - minute of end_hour (0-60) for the end date (Default: 0)
	
	* @param $project_id id of the project to load the data for
	* @return void
	*/
	public function data($project_id = null){
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
			$value_ids = $this->Project->Value->find('list', array('conditions' => array('project_id' => $project_id)));
			$latest = $this->Project->Value->Measure->find('first', array('recursive' => 0, 'conditions' => array('value_id' => array_keys($value_ids)), 'order' => 'Measure.timestamp DESC'));
			$latest = $latest['Measure']['timestamp'];
			$start = strtotime('00:00 -2 weeks', $latest);
			$this->request->query['start'] = date('d.m.Y', $start);
			$this->request->query['start_hour'] = '00';
			$this->request->query['start_minute'] = '00';
			$end = strtotime('00:00', $latest);
			$this->request->query['end'] = date('d.m.Y', $end);
			$this->request->query['end_hour'] = '00';
			$this->request->query['end_minute'] = '00';
			$conds = array('Measure.timestamp >' => $start, 'Measure.timestamp <' => $end);
		}
		$conds = array_merge($cond, $conds);
		$vconds =  array('Value.project_id' => $project_id);
		$limit = 0;
		$group = 'Measure.state';
		if($value_id) {
			$vconds['Value.id'] = $value_id;
			$limit = 0;
			$group = null;
		}
		try {
			$values = $this->Value->find('all', array('conditions' => $vconds, 'order' => 'Value.name ASC', 'contain' => array('Unit')));
			
			foreach($values as $i => $v){
				$measures = $this->Value->Measure->find('all', array(
					'fields' => 'Measure.data,Measure.timestamp,Measure.state', 
					'recursive' => 0, 
					'order' => 'Measure.timestamp ASC', 
					'limit' => $limit, 
					'group' => $group,
					'conditions' => array_merge($conds, array('Measure.value_id' => $v['Value']['id'])))
				);
				$measures = Set::extract($measures, '{n}.Measure');
				//debug($measures);
				$values[$i]['Measure'] = $measures;
			}
		} catch (Exception $e){
			$values = array();
			$this->er($e);
		}
		$values = Set::combine($values, '{n}.Value.id', '{n}'); //set the value id as the key for each value
		foreach($values as $i => $v){
			$v['Measure'] = $this->Measure->short_keys($v['Measure']); //shorter names for less payload when sending data to the browser
			
			$values[$i] = $v;
		}
		
		//If just requesting one value don't prepare unnecessary variables for the view
		if(!$value_id) { 
			//Get list of available methods 
			$methods = $this->Method->find('list', array('order' => 'name', 'conditions' => 'ISNULL(project_id) OR project_id = 0 OR project_id='.$project_id));
			
			$unit_prefixes = console::$unit_prefixes;
			$project = $this->Project->read(null, $project_id);
			$this->project_set_session($project);
			$manipulates = array('-1' => __('that are flagged'), 0 => __('under'), 1 => __("over"));
			$this->set(compact('project', 'project_id', 'values', 'imports', 'methods', 'unit_prefixes', 'import_timestamp', 'form_values', 'start', 'end', 'manipulates'));
		} else {
			$this->set(compact('values'));
			$this->layout = 'json';
		}
	}
	
	/**
	* Stores data from current upload with name import_file into the webroot/uploads folder. 
	* @param $url If the param URL is provided it get the data from a remote file. 
	* @param $page_container The page_container parameter can be a regex or a jQuery selector to parse the actual content out of a container
	* @return string $filename of the stored file, set $this->data['import_file'] = $filename
	*/
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
			$hash = hash_file('md5', WWW_ROOT . $filepath);
			$this->writeLog('uploaded_file', array(false, $filename, $hash, 'related' => $input_id));
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
			$hash = md5($c);
			$this->writeLog('imported_url', array($url, $filename, $hash, 'related' => $input_id));
		}
		
		// Set the file-name only to save in the database
		$this->data['import_file'] = $filename;
		return $filename;
	}
	
	/**
	* Calculates Measures for all Values by its predefined methods in a project
	* @param $project_id id of the project
	* @param array $params[start->int, end->int] must contain 'start' and 'end' timestamp or must be provided in the $_GET parameters
	*/
	public function calculate_values($project_id, $params = false){
		$values = $this->Project->Value->find('list', array('conditions' => 'project_id="'.$project_id.'"'));
		foreach($values as $value_id => $name) {
			$this->calculate_value($value_id, $params);
		}
	}
	
	/**
	* Calculates Measures for a Value by its predefined method
	* The params must contain:
	*
	** __start__ - Start timestamp
	** __end__ - End timestamp
	*
	* @param value_id id of the value
	* @param $params array or the params for the execution, are fetched from the query string or the value
	* @return void
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
	
	/**
	* Exports data in a given format. The function is implemented to export the data in CSV format and must be changed to enable other formats
	*
	* The following url parameters can or must be provided:
	*
	** __start__ - Startdate day.month.year,hour:minute:second
	** __end__ - Enddate day.month.year,hour:minute:second
	** __api_key__ - Export api key to get access to the api, it can be one of the project related keys generated for an export
	** __format__ - Possible values: csv, sensorML, xml, json (Optional, default: csv) //Until now only csv is supported (Remove this notice if format supports were added)
	** __values__ - Comma seperated list of value ids to be exported (Optional, default is empty and returns all values in project)
	** __debug__ - 1 returns data in user readable format (Optional)
	** __dateformat__ - One or more PHP date formats seperated by comma (Optional)
	** __download__ - 1 creates a file with the export name which is directly sent to the browser for download (Optional, Default: 0)
	** __states__ - 1 exports the state for every measure (Optional, Default: 0)
	** __deleted__ - 1 exports the state for every measure (Optional, Default: 0)
	
	@param int $id Project ID
	*/
	public function export($id) {
		$project_id = $id;
		$this->loadModel('Value');
		$p = $this->p;
		$start = strtotime($p['start']);
		$end = strtotime($p['end']);
		$this->layout = 'template';
		$this->loadModel('Export');
		$export = $this->Export->find('first', array('conditions' => array('project_id' => $id, 'auth' => $p['api_key']))); //Find an export to verify rights to export
		if(!$export && !$this->authorizedProject($project_id, console::$contributorState, false)) {
			$this->er(__('The provided api key is not correct or you have not enough rights.'));
			return;
		}
		if(!$start) {
			$this->er(__('A starttime must be provided.'));
			return;
		}
		//$id noch als parameter
		if(!$end) {
			$end = strtotime('tomorrow 00:00', $start);
		}
		$debug = $p['debug'];
		$format = $p['format'];
		if(!$format) 
			$format = 'csv';
		if(!$debug) {
			if($format == 'json') $hFormat = 'text/javascript';
			if($format == 'csv') $hFormat = $p['download'] ? 'application/vnd.ms-excel' : 'text/javascript';
			if($p['download']) { //If download, create filename and set content types
				$filename = $export['Export']['name'].'_'.date('Y-m-d', $start).'_'.date('Y-m-d', $end);
				$this->response->type($hFormat); //Response type for browser
				header("Cache-Control: must-revalidate");
				header("Pragma: must-revalidate");
				header("Content-type: ".$hFormat.'; charset=UTF-8');
				header("Content-disposition: attachment; filename=".$filename.'.'.$format);
			} else {
				$this->response->type($hFormat); //Response type for browser
			}
		} else
			$format = 'json';


		$this->Value->Behaviors->load('Containable'); //Containable makes it possible to fetch multiple related objects from the Db without forumlating SQL Queries

		$conds = array('Measure.timestamp >=' => $start, 'Measure.timestamp <' => $end); //Conditions for Measures, only export from given time range and only verified measures
		if(!$p['deleted']) //If deleted data should not be included filter by states
			 $conds['Measure.state >='] = 0;
		$vconds = array('Value.project_id' => $id); //Conditions for Values
		if($p['values']) {
			$values = explode(',', $p['values']);
			$vconds['Value.id'] = $values;
		} else if($export) {
			$values = explode(',', $export['Export']['value_ids']);
			$vconds['Value.id'] = $values;
		} 
		$value_ids = $this->Value->find('list', array('fields' => array('id', 'id'), 'conditions' => $vconds)); 
		$options = array(
			'conditions' => array('Value.id' => $value_ids), 
			'contain' => array(
				'Measure' => array(
					'fields' => 'data,timestamp'.($p['states'] ? ',state' : ''), 
					'conditions' => $conds, 
					'order' => 'Measure.timestamp ASC'), 
				'Unit'), 
			'order' => ''
		);

		$values = $this->Value->find('all', $options);
		
		//Build rows for CSV (one row for each timestamp)
		$data = array();
		foreach($values as $i => $v){ //Find all timestamps
			$values[$i]['Measure'] = $v['Measure'] = Set::combine($v['Measure'], '{n}.timestamp', '{n}');
			foreach($v['Measure'] as $timestamp => $m) {
				if(!$data[$timestamp])
					$data[$timestamp] = array(); //Create empty row for timestamp
			}
		}
		foreach($values as $i => $v){ 
			foreach($data as $timestamp => $d) {
				$data[$timestamp][] = $v['Measure'][$timestamp]['data']; //Fill the rows with the data (If there is no data for the timestamp for one value it will be empty)
				if($p['states']) {
					//debug($v['Measure'][$timestamp]);
					$data[$timestamp][] = $v['Measure'][$timestamp]['state'];
				}
			}
		}
		
		//Parse the date formats
		$dateformats = array_reverse(explode(',', $this->p['dateformat']));
		$hasDateformats = count($dateformats);
		$timestampNames = array('timestamp');
		if($hasDateformats) {
			foreach($dateformats as $i => $df) {
				if($i == 0) continue;
				$timestampNames[] = 'time'.($i + 1); //Set the timestamp names for the header
			}
		}
		//add the timestamps
		foreach($data as $i => $d) { 
			if($hasDateformats) { //Multiple timestamp formats
				foreach($dateformats as $dateformat)
					array_unshift($data[$i], date($dateformat, $i)); 
			} else //One timestamp formats
				array_unshift($data[$i], $i);
		}
		
		$header = array();
		array_map(function($v, $s) use (&$header, $p){ //Set the header in the first row
			$header[] = $v['Value']['name'].' ('.$v['Value']['prefix'].$v['Unit']['symbol'].')'; //Set the header names for each value
			if($p['states'])
				$header[] = 'State '.$v['Value']['name'];
		}, $values, $values);
		$data[0] = array_merge($timestampNames, $header);
		ksort($data);
		
		$this->writeLog('exported', array($project_id, $this->Auth->user('id'), 'related' =>  $export_id)); 
		$this->set(compact('data', 'debug', 'format'));
	}
	
	/**
	 * invite method
	 * @param project_id Project ID of project to which a user is invited
	 * @return void
	 */
	public function invite($project_id) {
		if(!$this->authorizedProject($project_id, console::$contributorState)) {
			return;
		}
		$this->loadModel('User');
		$this->Project->Behaviors->load('Containable');
		$this->request->data['Project']['id'] = $project_id;
		$project = $this->Project->find('first', array('conditions' => 'id="'.$project_id.'"', 'contain' => array('Member' => array('fields' => 'id,id'))));
		$members = Set::extract($project['Member'], '{n}.id');
		$this->set(compact('project'));
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->Project->id = $project_id;
			$member = $this->request->data['Member']['Member'][0];
			$user = $this->User->find('first', array('conditions' => array('LOWER(email)' => strtolower($member['email']))));
			if($user) {
				$user_id = $user['User']['id'];
			} else {
				$code = uniqid();
				$pw = uniqid();
				$user = array('email' => strtolower($member['email']), 'password' => $pw, 'activation_code' => $code, 'name' => strtolower($member['email']));
				$this->User->create();
				if($this->User->save($user)) {
					$user_id = $this->User->getLastInsertId();
					if(!$this->isLocalhost) {
						$this->er('SENDING');
						App::uses('CakeEmail', 'Network/Email');
						$email = new CakeEmail();
						$sent = $email
							->from(console::$noreplyEmail)->to($member['email'])
							->template('invited')
							->emailFormat('html')
							->subject(__('You have been invited to '.console::$systemName))
							->viewVars(array(
								'user' => $user, 
								'email' => $member['email'], 
								'project' => $project,
								'password' => $pw,
								'authUser' => $this->Auth->user(),
								'loginLink' => Router::url(array('controller' => 'users', 'action' => 'login'), true), 
								'activateLink' => Router::url(array('controller' => 'users', 'action' => 'activateAccount', '?' => array('activation' => $code)), true)))
							->send();
						$this->er('SENT:'.$sent);
					}
				} else { //User could not be invited
					$this->er(__('The user could not be invited.'));
					return;
				}
			}
			if(array_search($user_id, $members) !== false) { //User already invited
				$this->er(__('The user has already been invited. If you want to change his rights you have to uninvite and reinvite him.'));
				$this->redirect(array('action' => 'view', $project_id));
				return;
			}
			$members[] = $user_id;
			$this->request->data['Member']['Member'] = $members;
			if ($this->Project->saveAll($this->request->data)) {
				$this->Project->query('UPDATE projects_users SET state='.$member['state'].' WHERE user_id="'.$user_id.'" AND project_id="'.$project_id.'"');
				$this->writeLog('invited', array($project_id, $this->Auth->user('id'), 'related' => $user_id));
				$this->Session->setFlash(__('The user has been invited'));
				$this->redirect(array('action' => 'view', $project_id));
			} else {
				$this->Session->setFlash(__('The user could not be invited. Please, try again.'));
			}
		}
	}
	
	/**
	 * uninvite a user from a project
	 * @param $project_id Project ID
	 * @param $user_id User ID of the user to be uninvited
	 * @return void
	 */
	public function uninvite($project_id, $user_id) {
		if(!$this->authorizedProject($project_id, console::$contributorState)) {
			return;
		}
		$this->loadModel('User');
		$this->Project->Behaviors->load('Containable');
		$project = $this->Project->find('first', array('conditions' => 'id="'.$project_id.'"', 'contain' => array('Member' => array('fields' => 'id,id'))));
		if($project['Project']['user_id'] == $user_id) {
			$this->er(__('The project admin cannot be uninvited.'));
			return;
		}
		$members = Set::extract($project['Member'], '{n}.id');
		$this->set(compact('project'));
		if ($this->request->is('post') || $this->request->is('put')) {
			$user = $this->User->find('first', array('conditions' => array('LOWER(email)' => strtolower($member['email']))));
			$pos = array_search($user_id, $members);
			if($pos === false) { //User already invited
				$this->er(__('The user is not invited and cannot be uninvited.'));
				return;
			}
			unset($members[$pos]);
			$this->request->data['Project']['id'] = $project_id;
			$this->request->data['Member']['Member'] = $members;
			if ($this->Project->saveAll($this->request->data)) {
				$this->Session->setFlash(__('The user has been uninvited'));
				$this->redirect(array('action' => 'view', $project_id));
			} else {
				$this->Session->setFlash(__('The user could not be invited. Please, try again.'));
			}
		}
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Project->create();
			$this->request->data['Member']['Member'][0] = $this->Auth->user('id');
			$this->request->data['Project']['user_id'] = $this->Auth->user('id');
			if ($this->Project->saveAll($this->request->data)) {
				$id = $this->Project->getLastInsertId();
				$this->Project->query('UPDATE projects_users SET state='.console::$adminState.' WHERE user_id="'.$this->Auth->user('id').'" AND project_id="'.$id.'"');
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
	 * @param string $id ID of the project to be edited
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
	 * @param string $id Project ID
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
		$this->Project->recursive = 0;
		$user_id = $this->Project->read('user_id', $id);
		$user_id = $user_id['Project']['user_id'];
		if($this->Auth->user('id') != $user_id && !$this->Auth->user('isAdmin')) {
			$this->er(__('Only the creator of a project can delete it.'));
			$this->redirect(array('action' => 'index'));
			return;
		}
		if ($this->Project->delete()) {
			$this->Session->setFlash(__('Project deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Project was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
