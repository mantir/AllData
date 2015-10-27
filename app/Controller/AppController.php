<?php
/**
 * Application level Controller.
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here. All other Controllers will inherit from the AppController.
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 * @package       app.Controller
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');
App::uses('console', 'Lib/alldata'); //console special functions
App::uses('s', 'Lib/alldata'); //special String functions
App::uses('f', 'Lib/alldata'); //helper functions

/**
 * Application Controller
 *
 * Very important because all controllers inherit from the AppController
 */
class AppController extends Controller {
	/**
	* @var $p is the merge between query params and posted params, so all params from GET and POST together
	*/
	var $p = array();
	var $error = '', $success = '';
	var $isAjax = false;
	var $beforeRenderExecuted = false;
	var $errorsExist = false;
	var $redirectState = false;
	var $limitReturnList = array(array(), array());
	var $log_types = array(
		'imported_url' => array('source_url', 'target_file', 'related' => 'Input'), //A file is donwloaded from a remote URL and stored on the server
		'uploaded_import' => array(), //An import file is uploaded by the user
		'imported_file' => array('import_timestamp', 'source_file', 'measure_count', 'start_timestamp', 'end_timestamp', 'related' => 'Input'), //A locally stored file is imported into the database
		'auth_link_visitor' => array('ip', 'link'),
		'export' => array('ip', 'link'),
		'method_executed' => array('user_id', 'method_id', 'value_id')
	);
	
	public $components = array(
		'Acl',
		'Session',
		'RequestHandler',
		'Auth' => array(
			'loginRedirect' => array('controller' => 'posts', 'action' => 'index'),
			'logoutRedirect' => array('controller' => 'pages', 'action' => 'display', 'home')
		)
	);
	
	/** 
	* overrides the redirect function from cakephp.
	* if this is an ajax requst don't redirect to another page
	* @param $url target url
	* @param $status Redirect status
	* @param $exit The method will issue an exit() after the redirect unless you set the third parameter to false.
	*/
	public function redirect($url, $status = 0, $exit = true){
		$this->set('redirectState', $this->redirectState = $status);
		$this->set('redirectUrl', $this->redirectUrl = $url);
		if($status == 'real')
			$status = 0;

		if($this->request->redirectTo)
			$url = $this->request->redirectTo;
		if(!$this->isAjax || $this->forbidden) {
			return parent::redirect($url, $status, $exit);
		}
	}
	
	/**
	* Here starts everything. This is executed before every other method in the target controller is executed.
	*/
	public function beforeFilter() {
		$this->response->header('Access-Control-Allow-Origin', '*');
		//The functions that are always allowed for every user without login
		$this->Auth->deny('*');
		$this->Auth->allow('resetPassword', 'activateAccount', 'export', 'register', 'login', 'update_imports', 'update_all_imports'); //Allow these methods without login in every controller
		//$this->Auth->allow();  //No login
		
		$user = $this->Auth->user();
		$this->actor_id = $user['id'];
		$this->authUser = $user;
		$this->p = array_merge($this->request->data, $this->request->query);

		$this->set(array('request' => $this->p));
		$this->isAjax = $this->request->is('ajax') || isset($this->p['isAjax']);
		$this->set('isAjax', $this->isAjax);

		$this->request->here = str_replace('json/', '', $this->request->here);
		$this->request->relative = '/'.str_replace($this->request->base.'/', '', $this->request->here);
		preg_match('#\?.*#', $_SERVER[ 'REQUEST_URI' ], $q);
		$query_string = $q[0];
		$this->request->uri = $this->request->relative.$query_string;
		$this->request->redirectTo = $this->p['redirect'];
		$this->setLanguage('en');
		
		//for local testing
		if(strpos(FULL_BASE_URL, 'localhost') !== false)
			$this->isLocalhost = true;
			

		if($this->json && false)
			$this->layout = 'json';
		else
			$this->layout = 'default';

		if(!$this->Auth->loggedIn()) {
			$allowed = $this->Auth->allowedActions;
			$action = $this->request->params['action'];
			foreach($allowed as $a)
				if($a == $action) {
					$isAllowed = true;
					break;
				}
			if(!$isAllowed) {
				$this->er(__('Please log in.'));
				$this->set('mustLogin', $this->mustLogin = true);
				$this->forbidden = true;
				$this->redirect('/users/login');
			}
		}
	}
	
	/**
	* stores a current project to the session to output it on top of the page and to memorize in which project the user is currently navigating
	* @param array $p: project record from database
	* @return void
	*/
	public function project_set_session($p){
		$this->Session->write('Project', array('id' => $p['Project']['id'], 'name' => $p['Project']['name']));
	}
	
	/**
	* Checks whether the user is authorized for actions in the current project and will by default create an error message
	* @param int project_id: ID to check if the user has the necessary state
	* @param int state: the state the user must at least have in this project, 0: Guest, 1: Contributor, 2: Admin
	* @param bool render: whether to render an error message and change the view if no authorization is found
	* @returns boolean
	*/
	public function authorizedProject($project_id, $state, $render = true){
		$authorized = false;
		if($this->Auth->user('id')) {
			if($this->Auth->user('isAdmin')) 
				$authorized = true;
			$this->loadModel('Project');
			$this->Project->Behaviors->attach('Containable');
			$project = $this->Project->find('first', array('fields' => 'id', 'contain' => array('Member' => array('fields' => 'id')), 'conditions' => 'Project.id = "'.$project_id.'"'));
			//debug($project_id);
			if(!$authorized && is_array($project['Member'])) {
				foreach($project['Member'] as $member) {
					if($member['id'] == $this->Auth->user('id')){
						//debug($member['ProjectsUser']['state']);
						$authorized = $member['ProjectsUser']['state'] >= $state;
						break;
					}
				}
			}
		}
		if($render && !$authorized) {
			$this->noPermissionError();
		}
		return $authorized;
	}
	
	/**
	* Creates an error message, that the user has no rights to perform an action and renders an empty page
	* @return void
	*/
	public function noPermissionError(){
		$this->er(__('You have no permission to perform this action in this project.'));
		$this->render('../dummy');
	}
	
	/**
	* Overrides the cake php core render function to determine the layout
	* @param $view View filename
	* @param $layout Layout filename
	*/
	public function render($view = null, $layout = null){
		if($layout) $this->layout = $layout;
		if($this->layout == 'ajax')
			$this->layout = 'json';
		if($this->layout == 'default') {
			$this->set('loggedIn', $this->Auth->loggedIn());
			$user = $this->Auth->user();
			if(is_array($user))
			foreach($user as $k => $v)
				if(array_search($k, array('id', 'name', 'isAdmin', 'isGuest')) === false)
					unset($user[$k]);
			
			$this->set('authUser', $user);
		}
		return parent::render($view, $this->layout);
	}

	
	/**
	* Is executed before the view is rendered.
	*/
	public function beforeRender(){
		$inValidation = array();
		if(is_array($this->{$this->modelClass}->validationErrors))
			foreach($this->{$this->modelClass}->validationErrors as $k => $ver)
				if(count($ver) != 0) {
					$inValidation[$this->modelClass.'.'.$k] = $ver;
					$this->errorsExist = true;
				}

		$errors = array(
			'exist' => $this->errorsExist,
			'inValidation' => $inValidation
		);
		if($this->isAjax)
			unset($this->viewVars['content_for_layout']);
		
		$data = $this->request->data; $query = $this->request->query;
		if(!is_array($data)) $data = array(); if(!is_array($query)) $query = array();
		$this->p = array_merge($data, $query);
		$this->set(array('request' => $this->p));
		
		$m = $this->Session->read('Message.flash.message');
		$this->set('flashMessage', $m);
		
		$currentProject = $this->Session->read('Project');
		$this->set('currentProject', $currentProject);
		
		
		$return = array( //a tiny version of what is possible to send back to the view
			'vars' => $this->viewVars,
			'errors' => $errors,
			//'request' => $this->request,
			//'data' => $this,
			'message' => $m,
/*			'url' => $this->request->url,
			'action' => $this->request->params['action'],
			'controller' => $this->request->params['controller'],
			'model' => $this->modelClass,
			'webroot' => $this->request->webroot,
			'browser' => $this->viewVars['browser'],
			'here' => $this->request->here,
			'hereRel' => str_replace($this->request->base.'/', '', $this->request->here)*/
		);
		if(!$this->isAjax) {
			$return['passedArgs'] = $this->passedArgs;
			$return['base'] = $this->request->base;
		}
		if($this->request->params['controller'] == 'pages'){
			$return['action'] = $return['vars']['action'] = $this->passedArgs[0];
			unset($return['passedArgs'][0]);
		}
		if($words)
			$return['words'] = $words;
			
		if(count($this->limitReturnList[0])){
			$r = array('vars' => array());
			foreach($this->limitReturnList[0] as $i)
				$r['vars'][$i] = $return['vars'][$i];
			if($this->limitReturnList[1] == 'all')
				$return['vars'] = $r['vars'];
			else if(count($this->limitReturnList[1]) == 0)
				$return = $r;
			else {
				foreach($this->limitReturnList[1] as $i)
					$r[$i] = $return[$i];
			}
			$return = $r;
		}
		
		$this->set('return', $return);
	}
	
	/**
	 * Refreshes the Auth session
	 * @param string $field
	 * @param string $value
	 * @return void 
	 */
	function _refreshAuth($field = '', $value = '') {
		if (!empty($field) && !empty($value)) { 
			$this->Session->write('Auth.User.'. $field, $value);
		} else {
			if (isset($this->User)) {
				$user = $this->User->read(false, $this->Auth->user('id'));
				$user = array_merge($user['User']);
				$this->Auth->login($user);
			} else {
				//$this->Auth->login(ClassRegistry::init('User')->findById($this->Auth->user('id')));
			}
		}
	}
	
	/**
	* overrides the standard afterFilter Method from cakephp to give the possibility to redirect 
	* if redirectTo is passed as url param.
	*/
	public function afterFilter(){
		if($this->p['redirectTo'])
			$this->redirect($this->p['redirectTo']);
	}
	
	/**
	* Writes a log to the database
	* @param $type Can be one of the log types defined in Model/Log.php
	* @param $data numeric array of data that shall be logged into the database. Max. 6 elements. The structure for a type is also defined by the log types in Model/Log.php
	* @param mixed $dump a variable to dump in the database (Not implemented)
	*/
	public function writeLog($type, $data, $dump = false){
		$this->loadModel('Log');
		$l = array('type' => $type, 'time' => time(), 'data' => $data[0], 'data_2' => $data[1],'data_3' => $data[2], 'data_4' => $data[3], 'data_5' => $data[4], 'data_6' => $data[5], 'related_id' => $data['related']);
		if(strpos($type, '_error') !== false) {
			$l['error'] = 1;
			$lt = $this->Log->find('first', array('fields' => 'Log.time', 'conditions' => 'type LIKE "%_error"', 'order' => 'Log.time DESC'));
			if(time() - $lt['Log']['time'] > strtotime('+12 hours', 0)){
				mail(console::$serviceEmail, console::$systemName.' Error: '.$data, $info);
			}
		}
		$this->Log->create();
		$this->Log->save($l);
	}
	
	/**
	* Return a list of logs of a specified type
	* @param $type can be one of the log types defined in Model/Log.php
	* @param array $params CakePHP conditions for a find query to filter results
	* @param string $combine Log column which should be the key for the returned array
	* @param string $order SQL order to order results 
	*/
	public function getLogs($type, $params = array(), $combine = 'id', $order = 'time DESC'){
		$this->loadModel('Log');
		$params['type'] = $type;
		$logs = $this->Log->find('all', array('conditions' => $params));
		if($logs && $combine)
			$logs = Set::combine($logs, '{n}.Log.'.$combine, '{n}.Log');
		return $logs;
	}

	
	
    /** This is a (MySQL specific) check to see if a 
    * constraint was violated as the last error. If it was,
    * the VALUE of the field which failed is returned.
    * this is not ideal, but will do for most situations.
    * The logic to work out the specific field which failed
    * requires more MySQL specific SQL (such as 'show keys from...'
    * so I shall leave it out. Most tables only have one 
    * unique constraint anyway, although our example above
    * has 2.
	*/
    function checkFailedConstraint() {
        $db =& ConnectionManager::getDataSource($this->useDbConfig); 
        $lastError = $db->lastError();

        // this is MYSQL SPECIFIC
        if(preg_match('/^\d+: Duplicate entry \'(.*)\' for key \d+$/i', $lastError, $matches)) {
            return $matches[1];
        }

        return false;
    } 
	
	/**
	* For changing the language (Default English) (Not really implemented, maybe in future versions with multi language support)
	*/
	public function setLanguage($lang = null, $redirect = true){
		if(($lang == 'de' || $lang == 'en') && $lang != $this->Session->read('Config.language')) {
			$this->Session->write('Config.language', $lang);
			$standard_lang = 'en';
			if(!$lang) $lang = $standard_lang;
			$this->lang = $lang;
			//$this->set('lang', $lang);
			$valid = false;
			
			/*@include_once("../Lib/languages/".$standard_lang.".php");
			if($valid) {
				$lang_words = $this->language_words;
				if($lang != $standard_lang) {
					@include_once("../Lib/languages/".$lang.".php");
					$this->language_words = array_merge($lang_words, $this->language_words);
				}
			}*/
		}
		//clearCache();
		//Cache::clear();
		/*if($redirect) {
			$ref = preg_replace('#/pages/([a-z]{2}/)?(.*)#', '/pages/'.$lang.'/$2', $this->referer());
			$this->redirect($ref);
		}*/
	}
	
	/**
	* Delete a project related object like value, input, export, method, unit
	* @param string $model Can be Value, Input, Export, Method, Unit
	* @param $id ID of the object to be deleted
	*/
	public function delete_object($model, $id){
		$this->loadModel($model);
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->{$model}->id = $id;
		if (!$this->{$model}->exists()) {
			throw new NotFoundException(__('Invalid export'));
		}
		$m = $this->{$model}->read(null, $id);
		$project_id = $m[$model]['project_id'];
		if($project_id) {
			$redirect = array('controller' => 'projects', 'action' => 'view', $project_id);
		} else
			$redirect = array('controller' => 'projects', 'action' => 'index');
		if(!$this->authorizedProject($project_id, console::$contributorState)) {
			return;
		}
		if ($this->{$model}->delete()) {
			$this->Session->setFlash(__($model.' deleted'));
			$this->writeLog('deleted', array($this->Auth->user('id'), strtolower(Inflector::pluralize($model)), $m[$model]['name'], $project_id, 'related' => $id));
			$this->redirect($redirect);
		}
		$this->Session->setFlash(__($model.' was not deleted'));
		$this->redirect($redirect);
	}
	
	/**
	* Passes an error message to the view
	* @param $e errorstring, if empty function returns bool if there already was an error. It sets errorsExist for the view to true and adds the errormessage to the Session->setFlash message.
	*/
	function er($e = ""){
		if(!$e) return !empty($this->error);
		if(!$this->isAjax) {
			$this->error .= $e."<br />";
			$this->set(array('error' => true));
		} else
			$this->error .= $e."\n";
		$this->Session->setFlash($this->error);
		$this->set('errorsExist', true);
		$this->errorsExist = true;
	}
	
	/**
	* Passes a success message to the view
	* @param $s success string. 
	*/
	function su($s){
		switch($s){
			
		}
		if(!$this->isAjax) {
			$this->success .= $s."<br />";
		} else
			$this->success .= $s."\n";
		$this->Session->setFlash($this->error.$this->success); 
	}
	

}
