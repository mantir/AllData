<?php
/**
 * Value model
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://book.cakephp.org/2.0/en/models.html
 * @package       app.Model
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
 
App::uses('AppModel', 'Model');
/**
 * Value Model
 *
 * @property Project $Project
 * @property Unit $Unit
 * @property Measure $Measure
 * @property Input $Input
 */
class Value extends AppModel {

	/**
	 * Display field
	 *
	 * @var string
	 */
	public $displayField = 'name';
	
	/**
	* Is executed always before Value->save($data) is executed. It replaces commas by points for numbers to avoid errors in number formatting
	* @param $options Array of options passed with the Value->save function
	*/
	public function beforeSave($options = array()) {
		$this->data['Value']['maximum'] = str_replace(',', '.', $this->data['Value']['maximum']);
		$this->data['Value']['minimum'] = str_replace(',', '.', $this->data['Value']['minimum']);
		$this->data['Value']['max_variation'] = str_replace(',', '.', $this->data['Value']['max_variation']);
		return true;
	}
	
	/**
	* Set all measure states to 0 where the state is -1 or 1 to check them again. The function e.g. executed when a values maximum, minimum... parameters are change which affect the data chacking
	* @param $value_id Value id for which all measures should be unchecked
	* @param bool $all If false only the measures with states -1 and 1 are unchecked, if true all measures are unchecked
	* @param int $start Start timestamp 
	* @param int $end End timestamp
	*/
	public function uncheck_data($value_id, $all = false, $start = 0, $end = 0){
		$timecond = '';
		if($start)
			$timecond = ' AND timestamp >='.$start;
		if($end)
			$timecond .= ' AND timestamp <='.$end;
		$this->query('UPDATE measures SET state=0 WHERE value_id="'.$value_id.'" AND state IN (-1, 1)'.$timecond);
	}
	
	/**
	* Checks a range of data for a value if the data is not over the maximum, under the minimum, an error code or changing to fast.
	* The data is flagged -1 when something is wrong with the data, and flagged 1 when the data is ok.
	*
	* @param $value_id: Value id for which all measures should be checked
	* @param int $start Start timestamp
	* @param int $end End timestamp
	*/
	public function check_data($value_id, $start = 0, $end = 0) {
		$timecond = '';
		if($start)
			$timecond = ' AND timestamp >='.$start;
		if($end)
			$timecond .= ' AND timestamp <='.$end;
		$v = $this->read(null, $value_id);
		$v = $v['Value'];
		$errors = $this->parse_errorcodes($v['error_codes']);

		if(count($errors)) {
			$hasErrorCodes = true;
			$errors = Set::extract($errors, '{n}.code');
			$errors = implode(',', $errors);
		}
		if( isset($v['max_variation']) ){
			$res = $this->query('
				SELECT
					m.id,
					m.data as d,
					m.timestamp as t,
					m.state as s
				FROM
					measures m
				WHERE
					m.value_id="'.$v['id'].' AND m.state >= 0"'.$timecond.'
				ORDER BY m.timestamp
			');
			$timestamp = $res[0]['m']['t'] - 1; //-1 to avoid division by zero in first loop
			$data = $res[0]['m']['d'];
			$incorrects = array();
			$corrects = array();

			foreach($res as $r){
				$m = $r['m'];
				if($m['s'] == 0) { //not checked
					$dif = abs($m['d'] - $data) / (($m['t'] - $timestamp) / 3600);
					//debug($dif);
					if($dif > $v['max_variation']) {
						$m['state'] = -1;
						$incorrects[] = $m['id'];
					} else {
						$m['state'] = 1;
						$corrects[] = $m['id'];
					}
				}
				if($m['s'] > 0 || $m['state'] > 0) { // last data to compare with is only data which has not been flagged incorrect
					$timestamp = $m['t'] - 1;
					$data = $m['d'];
				}
			}
			//debug($corrects);
			//debug($incorrects);
			$this->Measure->updateAll(
				array('Measure.state' => 1),
				array('Measure.id' => $corrects)
			);
			$this->Measure->updateAll(
				array('Measure.state' => -1),
				array('Measure.id' => $incorrects)
			);
			unset($res);
		}
		
		if( isset($v['minimum']) || isset($v['maximum']) || $hasErrorCodes) {
			$where = array();
			if(isset($v['minimum']))
				$where[] =  'data < '.$v['minimum'];
			if(isset($v['maximum']))
				$where[] = 'data > '.$v['maximum'];
			if($hasErrorCodes)
				$where[] = 'data IN ('.$errors.')';
			$query = 'UPDATE measures SET state=-1 WHERE value_id="'.$v['id'].'" AND state IN (0,1) AND ('.implode(' OR ', $where).')'.$timecond;
			$this->query($query);
		}
		$this->query('UPDATE measures SET state=1 WHERE value_id="'.$v['id'].'" AND state=0'.$timecond); //Set all unflagged values to checked
	}
	
	/**
	* Converts the textual representation of error codes from a value into an array
	* Parses error codes from string 'code:description' into array(array('code' => '...', 'description' => '...'),...)
	* @param string $ec Error Codes text
	*/
	public function parse_errorcodes($ec){
		preg_match_all("/([^:]+):([^:]+)/", $ec, $m);
		$param_names = array('code', 'description');
		$params = array();
		foreach($m as $j => $p){
			if($j == 0) continue;
			foreach($p as $i => $v) {
				$params[$i][$param_names[$j - 1]] = trim($v); 
			}
		}
		return $params;
	}
	
	
	public $validate = array(
		'name' => array(
			'between' => array(
				'rule' => array('between', 2, 40),
				'message' => 'Name must have at least 2 up to 40 characters',
				'allowEmpty' => false,
				'last' => false, // Stop validation after this rule
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

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
		),
		'Unit' => array(
			'className' => 'Unit',
			'foreignKey' => 'unit_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Method' => array(
			'className' => 'Method',
			'foreignKey' => 'method_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Measure' => array(
			'className' => 'Measure',
			'foreignKey' => 'value_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);


/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'Input' => array(
			'className' => 'Input',
			'joinTable' => 'inputs_values',
			'foreignKey' => 'value_id',
			'associationForeignKey' => 'input_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);

}
