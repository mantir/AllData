<?php
/**
 * Method model
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://book.cakephp.org/2.0/en/models.html
 * @package       app.Model
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
 
App::uses('AppModel', 'Model');
/**
 * Method Model
 *
 */
class Method extends AppModel {
	/**
	* Validation rules
	*/
	public $validate = array(
		'name' => array(
			 'nameRule' => array(
				'rule' => 'alphaNumeric',
				'message' => 'Only alphabets and numbers allowed',
			 ),
			'between' => array(
				'rule' => array('between', 2, 20),
				'message' => 'Name must have at least 2 up to 20 characters',
				'allowEmpty' => false,
				'last' => false, // Stop validation after this rule
			),
		),
		'description' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Must not be empty',
				'allowEmpty' => false,
			),
		),
		'code' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Code must not be empty',
				'allowEmpty' => false,
			),
		),
		'params' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Parameters must not be empty',
				'allowEmpty' => false,
			),
		),
	);
	
	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	/**
	 * Display field
	 *
	 * @var string
	 */
	public $displayField = 'name';
	private $param_names = array('name', 'type', 'description');
	private $param_types = array('val', 'num');
	private $execName = '';
	
	/**
	* @return string the last execution name
	*/
	public function getLastExecutionName(){
		return $this->execName;
	}
	
	/**
	* Returns the execution name for a method execution. E.g. method_name(Air temperatur, 0)
	* @param string $name Method name
	* @param array $m_params array of the method defined parameters: array(array('name' => ..., 'type' => ..., 'descripton => ...))
	* @param array $params array of the passed parameters: array(name => value)
	* @param array $values array of values with their ID as key array(value_id => array(), value_id2 => array())
	* @return string
	*/
	public function getExecName($name, $m_params, $params, $values){
		if(gettype($m_params == 'string'))
			$m_params = $this->parse_params($m_params);
		if(gettype($params == 'string'))
			$params = json_decode($params, true);
		
		$execName = array();
		foreach($m_params as $i => $m_param){
			if($m_param['type'] == 'val') {
				$execName[$i] = $values[$params[$m_param['name']]]['name'];
			} else {
				$execName[$i] = $params[$m_param['name']];
			}
		}
		return $name.'('.implode(', ', $execName).')';
	}
	
	/**
	* Executes function code with it's parameters. The code is executed in a SandboxClass provided in app/Lib/phpSandbox
	* Documentation: http://fieryprophet.com/phpsandbox-docs/classes/PHPSandbox.PHPSandbox.html
	*
	* @param $method_id ID of the method to be executed
	* @param array $params parameters for the method to be executed. Values are passed by their ID. E.g. array('value_parameter_name' => 18);
	* @param int $start timestamp for startdate of data for execution
	* @param int $end timestamp for enddate of data for execution
	* @param int $interval_count number of $interval_type, e.g. number of days if $interval_type is days
	* @param int $interval_type 0 : 'input timestamps', 1: 'minutes', 2: 'hours', 3: 'days', 4: 'weeks', 5: 'months', 6: 'years';
	* @param bool $adjustTime if true the start and end timestamp will be adjusted to the beginning of the interval type. E.g. if interval type is days the start time will be at 00:00:00 of that day
	* @return array $result
	*/
	public function execute($method_id, $params, $start, $end, $interval_count, $interval_type, $adjustTime = false){
		require_once(APPLIBS.'PHP-Parser/lib/bootstrap.php');
		require_once(APPLIBS.'phpSandbox/PHPSandbox.php');
		require_once(APPLIBS.'phpSandbox/ValidatorVisitor.php');
		require_once(APPLIBS.'phpSandbox/Error.php');
		require_once(APPLIBS.'phpSandbox/functions.php');
		
		if(!$start || !$end) {
			return __('There must be set a start and end date.');
		}
		
		//$intervalTypes 0 : 'input timestamps', 1: 'minutes', 2: 'hours', 3: 'days', 4: 'weeks', 5: 'months', 6: 'years';
		if($adjustTime)
		switch($interval_type){
			case 1 : //minutes
				$start = strtotime('-'.date('s', $start).' seconds', $start); 
				$end = strtotime('+1 minute -'.(date('s', $end)).' seconds', $end); 
			break;
			case 2 : //hours
				$start = strtotime('-'.date('i', $start).' minutes', $start);
				$end = strtotime('+1 hour -'.(date('i', $end)).' minutes', $end); 
			break;
			case 3 : //days 
				$start = strtotime('-'.date('H', $start).' hours', $start);
				$end = strtotime('+1 day -'.(date('H', $end)).' hours', $end); 
			break;
			case 4 : //weeks
				$start = strtotime('-'.(date('N', $start) - 1).' days', $start); 
				$end = strtotime('+1 week -'.(date('N', $end)).' days', $end);
			break;
			case 5 : //months
				$start = strtotime(date('1.m.Y', $start), $start);
				$end = strtotime(date('1.m.Y', $end).' +1 month -1 day'); 
			break;
			case 6 : //years
				$start = strtotime(date('1.1.Y', $start)); 
				$end = strtotime(date('31.12.Y', $end)); 
			break;
		};
		
		set_time_limit(600);
		$m = $this->read(null, $method_id);
		$m_params = $this->parse_params($m['Method']['params']); //get method parameters as array
		
		if(!$interval_type || $interval_type == 'input timestamps') { //default interval type is every timestamp in this function encoded as 'all'
			$interval_type = 'all';
			$timestamps = array();
		}
		if(is_numeric( $interval_type ) )
			 $interval_type = console::$intervalTypes[ $interval_type ];
		if(!$interval_count)
			$interval_count = 1;
		
		$interval = $interval_count.' '.$interval_type;

		App::import('Model','Value');
		$this->Value = new Value();
		$this->Value->Behaviors->load('Containable');

		$m = $this->read(null, $method_id);
		$vals = array();
		$values = array();
		
		//Check for missing params and for values in params
		foreach($m_params as $i => $m_param){
			if($m_param['type'] == 'val') {
				if(!$params[$m_param['name']]) return __('Missing parameter "%s" for method: %s', $m_param['name'], $m['Method']['name']);
				$vals[$m_param['name']] = $params[$m_param['name']];
			}
		}
		
		//Get all needed values
		foreach($vals as $name => $val){
			$v = $this->Value->find('first', array('conditions' => 'Value.id = '.$val, 'contain' => array('Unit', 'Measure' => 
				array('order' => 'Measure.timestamp ASC', 'conditions' => 'Measure.timestamp >= '.$start.' AND Measure.timestamp < '.$end.' AND Measure.state > -2')))
			);
			$values[$name] = Set::combine($v['Measure'], '{n}.timestamp', '{n}'); //All data for the value
			$value = $v['Value'];
			$value['unit'] = $v['Unit'];
			$params[$name] = $value;
			$params[$name]['past'] = array();
			if($interval_type == 'all') {
				foreach($values[$name] as $t => $d)
					$timestamps[$t] = true;
			}
		}
		//Build the execution name e.g. method_name(param1, param2)
		$this->execName = array(); 
		foreach($m_params as $i => $m_param){
			if($m_param['type'] == 'val') {
				$this->execName[$i] = $params[$m_param['name']]['name'];
			} else {
				$this->execName[$i] = $params[$m_param['name']];
			}
		}
		$this->execName = $m['Method']['name'].'('.implode(', ', $this->execName).')';
		
		$params['start'] = $start;
		$params['end'] = $end;
		$params['results'] = array();
		$sandbox = new PHPSandbox\PHPSandbox(array(), array(), $params);
		$sandbox->blacklist_func(console::$disabled_functions);
		
		$code = $m['Method']['code'];
		$counter = 0;
		
		if($interval_type == 'all') { //prepare execution for every timestamp
			$timestamps = array_keys($timestamps);
			sort($timestamps);
			$timestamp_count = count($timestamps);
			$timestamp = $timestamps[0];
			$timestamp_end = $timestamps[1];
			if(!$timestamp_end) $timestamp_end = $timestamp + 1;
		} else {
			$timestamp = $start; //prepare execution for other interval types
			$timestamp_end = strtotime($interval, $timestamp);
		}
		$result = array();
		//debug($interval_type);
		//debug(date('d.m.Y. H:i:', $end));
		
		//*** Run the execution over whole interval ***
		while($timestamp < $end) { 
			//debug(date('d.m.Y. H:i:', $timestamp));
			foreach($values as $name => $v){ //Extract value data for current timestamp interval
				$params[$name]['data'] = array();
				foreach($v as $time => $point) {
					//debug($time. ' '.$timestamp.':'.($time < $timestamp));
					if($time < $timestamp) continue;
					if($time >= $timestamp && $time < $timestamp_end) { 
						$params[$name]['data'][] = $point;
					} else
						break;
				}
			}
			//debug($params);
			$sandbox->define_vars($params);
			try { //execute method and store in array
				$data_result = $sandbox->execute($code);
				//debug($data_result);
				if($data_result !== null ) {
					$r = array('data' => $data_result , 'timestamp' => $timestamp); 
					//debug($r);
					$result[] = $r;
				}
				$params['results'] = $result; //set results to be accessable from inside the method
			} catch(Exception $e) {
				return $e->getMessage();
			}
			$counter++;
			if($counter > 50000) break;
			foreach($values as $name => $v){ //Extract value data for current timestamp interval
				$params[$name]['past'] = array_merge($params[$name]['past'], $params[$name]['data']); 
			}
			if($interval_type == 'all') {
				$timestamp = $timestamp_end;
				if($counter > $timestamp_count - 1) 
					break;
				$timestamp_end = $timestamps[$counter + 1];
				if(!$timestamp) break;
				if(!$timestamp_end) $timestamp_end = $timestamp + 1;
				//debug($timestamp);
				//debug($timestamp_end);
			} else {
				$timestamp = $timestamp_end;
				$timestamp_end = strtotime($interval, $timestamp); //Timestamp for next round
			}
		}
		return $result;
	}
	
	
	/**
	* Checks if the parameter definition for a method is correct, if yes returns false else an error message
	* @param string $params A list of 3 columns each row seperated by :, param_name:type:description (e.g. param_name:val:description for value{newline}param_name2:num:description for number)
	*
	*/
	public function check_params($params){
		$params = $this->parse_params($params);
		foreach($params as $p) {
			if(preg_match('/[^a-zA-z][^a-zA-Z0-9]*/', $p['name']) || !trim($p['name'])) {
				return __('A variable name must have at least one character a-z or A-Z followed by characters or numbers.');
			}
			if(array_search($p['type'], $this->param_types) === false) {
				return __('A variable type must be one of these: '.implode(', ', $this->param_types));
			}
			if(!trim($p['description'])) {
				return __('A variable description must not be empty.');
			}
		}
		return false;
	}
	
	/**
	* Checks if the code runs without errors (Has to be implemented)
	* @todo
	* @param $code
	*/
	public function check_code($code){

	}

	
	/**
	* Parses params from string 'name:type:description' into array(array('name' => '...', 'type' => '...', 'description' => '...'),...)
	* @param string $string String to parse
	*/
	public function parse_params($string){
		preg_match_all("/([^:]+):([^:]+):(.*)/", $string, $m);
		$params = array();
		foreach($m as $j => $p){
			if($j == 0) continue;
			foreach($p as $i => $v) {
				$params[$i][$this->param_names[$j - 1]] = trim($v); 
			}
		}
		return $params;
	}

}
