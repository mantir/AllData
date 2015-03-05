<?php
/**
 * Log model
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://book.cakephp.org/2.0/en/models.html
 * @package       app.Model
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('AppModel', 'Model');
/**
 * Log Model
 *
 * @property Event $Event
 */
class Log extends AppModel {

	/**
	 * Display field
	 *
	 * @var string
	 */
	public $displayField = 'data';
	
	/**
	 * Log types and their fields
	 * @var array
	 */
	public $logTypes = array(
		'imported_file' => array('import_timestamp', 'filename', 'measure_count', 'start', 'end', 'lines', 'related' => 'Input'),
		'imported_url' => array('url', 'filename', 'hash', 'related' => 'Input'),
		'user_edited' => array('user', 'model', 'name', 'project_id', 'related' => 'Model'),
		//'user_invited' => array('project_id', 'inviter', 'related' => 'User'),
		'exported' => array('user', 'start', 'end', 'measure_count', 'lines', 'related' => 'Export')
	);
	
	/**
	 * Data field names in the database
	 * @var array
	 */
	public $data_fields = array('data', 'data_2', 'data_3', 'data_4', 'data_5', 'data_6');
	
	/**
	 * Returns the name of the database column for a given logtype and field name
	 * @param $type a log type contained in 
	 * @param $name
	 * @return string
	 */
	public function dataField($type, $name) {
		$field = $this->data_fields[array_search($name, $this->logTypes[$type])];
		return $field;
	}
	
	/**
	 * Get field list for given log type
	 * @param $type
	 * @return string
	 */
	public function getFieldList($type){
		return 'time,related_id,'.implode(',', array_slice($this->data_fields, 0, count($this->logTypes[$type]) - 1));
	}
	
	/**
	 * Get a list of logs for a given log type
	 * @param $project_id
	 * @param $type
	 * @param $offset
	 * @param $limit
	 * @return array
	 */
	public function getLogs($project_id, $type, $offset = 0, $limit = 100){
		$limit = $offset.','.$limit;
		if($type == 'import') {
			$imported_file = $this->query('SELECT '.$this->getFieldList('imported_file').' FROM logs WHERE type="imported_file" AND related_id IN (SELECT id FROM inputs WHERE project_id="'.$project_id.'") ORDER BY time DESC LIMIT '.$limit);
			$field = $this->dataField('imported_file', 'filename');
			$file_list = Set::extract($imported_file, '{n}.logs.'.$field);
			$files = Set::combine($imported_file, '{n}.logs.'.$field, '{n}.logs');
			if(!$file_list)
				$file_list = array();
			$field = $this->dataField('imported_url', 'filename');
			//debug($field);
			$imported_url = $this->query('SELECT '.$this->getFieldList('imported_url').' FROM logs WHERE type IN ("imported_url","uploaded_file") AND '.$field.' IN ("'.implode('","', $file_list).'")');
			$imported_url = Set::combine($imported_url, '{n}.logs.'.$field, '{n}.logs');
			$logs = array();
			foreach($files as $t => $f){
				$l = array('type' => 'import', 'time' => $f['time']);
				foreach($this->data_fields as $j => $df) {
					if($f[$df])
						$l[$this->logTypes['imported_file'][$j]] = $f[$df];
					if($imported_url[$t] && $imported_url[$t][$df]) {
						$l[$this->logTypes['imported_url'][$j]] = $imported_url[$t][$df];
					}
				}
				$l[$this->logTypes['imported_file']['related']] = $f['related_id'];
				$logs[$f['time']] = $l;
			}
		} else 
		
		if($type == 'user') { //returns all user actions
			$models = array('values', 'inputs', 'exports', 'methods', 'units', 'users');
			$res = array();
			$users = array();
			$names = array();
			foreach($models as $model) {
				if($model != 'users') {
					$res[$model] = $this->query('SELECT type,'.$this->getFieldList('user_edited').' FROM logs WHERE type IN ("edited","created") AND '.$this->dataField('user_edited', 'model').'="'.$model.'" AND related_id IN (SELECT id FROM `'.$model.'` WHERE project_id="'.$project_id.'") ORDER BY time DESC LIMIT '.$limit);
					$res[$model] = array_merge($res[$model], $this->query('SELECT type,'.$this->getFieldList('user_edited').' FROM logs WHERE type="deleted" AND '.$this->dataField('user_edited', 'project_id').'="'.$project_id.'" ORDER BY time DESC LIMIT '.$limit));
				} else {
					$res[$model] = $this->query('SELECT type,'.$this->getFieldList('user_edited').' FROM logs WHERE type="invited" AND '.$this->dataField('user_edited', 'project_id').'="'.$project_id.'" ORDER BY time DESC LIMIT '.$limit);
				}
				if(!count($res[$model]))
					continue;
				$list =  Set::extract($res[$model], '{n}.logs.related_id');
				$list = implode(',', $list);
				$names[$model] = $this->query('SELECT id,name FROM `'.$model.'` WHERE id IN ('.$list.')');
				$names[$model] = Set::combine($names[$model], '{n}.'.$model.'.id', '{n}.'.$model.'.name');
				
				$list =  Set::extract($res[$model], '{n}.logs.'.$this->dataField('user_edited', 'user'));
				$list = implode(',', $list);
				$users[$model] = $this->query('SELECT id,name FROM `users` WHERE id IN ('.$list.')');
				$users[$model] = Set::combine($users[$model], '{n}.users.id', '{n}.users.name');
				
				$res[$model] = Set::extract($res[$model], '{n}.logs');
				//debug($users);
				//debug($res);
			}
			
			$logs = array();
			foreach($res as $model => $r){
				foreach($r as $e) {
					$l = array('type' => 'user', 'time' => $e['time']);
					foreach($this->data_fields as $j => $df) {
						if($e[$df]) {
							$l[$this->logTypes['user_edited'][$j]] = $e[$df];
						}
					}
					$l['action'] = $e['type'];
					if($l['action'] != 'deleted') {
						$l['related_id'] = $e['related_id'];
						$l['name'] = $names[$model][$e['related_id']];
					}
					$l['username'] = $users[$model][$l['user']];
					$logs[$e['time']] = $l;
				}
			}
			
		} else 
		
		if($type == 'data') {
			$imported_file = $this->query('SELECT '.$this->getFieldList('imported_file').' FROM logs WHERE type="imported_file" AND related_id IN (SELECT id FROM inputs WHERE project_id="'.$project_id.'") ORDER BY time DESC LIMIT '.$limit);
			$field = $this->dataField('imported_file', 'filename');
			$file_list = Set::extract($imported_file, '{n}.logs.'.$field);
			$files = Set::combine($imported_file, '{n}.logs.'.$field, '{n}.logs');
			
			$field = $this->dataField('imported_url', 'filename');
			//debug($field);
			$imported_url = $this->query('SELECT '.$this->getFieldList('imported_url').' FROM logs WHERE type="imported_url" AND '.$field.' IN ("'.implode('","', $file_list).'")');
			$imported_url = Set::combine($imported_url, '{n}.logs.'.$field, '{n}.logs');
			$logs = array();
			foreach($files as $t => $f){
				$l = array('type' => 'import', 'time' => $f['time']);
				foreach($this->data_fields as $j => $df) {
					if($f[$df])
						$l[$this->logTypes['imported_file'][$j]] = $f[$df];
					if($imported_url[$t] && $imported_url[$t][$df]) {
						$l[$this->logTypes['imported_url'][$j]] = $imported_url[$t][$df];
					}
				}
				$l[$this->logTypes['imported_file']['related']] = $f['related_id'];
				$logs[$f['time']] = $l;
			}
		}
		krsort($logs);
		return $logs;
	}
}
