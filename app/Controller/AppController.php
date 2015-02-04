<?php
/**
 * Application level Controller
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
App::uses('console', 'Lib'); //console special functions
App::uses('s', 'Lib'); //special String functions
App::uses('f', 'Lib'); //helper functions
App::uses('Browser', 'Vendor'); //to get browser Information


class AppController extends Controller {
	/**
	*	p is the merge between query params and posted params, so all params from GET and POST together
	*/
	var $p = array();
	var $error = '', $success = '';
	var $isAjax = false;
	var $beforeRenderExecuted = false;
	var $newView = false, $newViewJs = false;
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
	
	/* overrides the redirect function from cakephp.
	 * if this is an ajax requst don't redirect to another page
	 */
	public function redirect($url, $status = 0, $exit = true){
		$this->set('redirectState', $this->redirectState = $status);
		$this->set('redirectUrl', $this->redirectUrl = $url);
		if($status == 'real')
			$status = 0;
		if(!$this->isAjax) {
			return parent::redirect($url, $status, $exit);
		}
	}
	
	/*
	 * this is executed before every other functions in the controller
	 */
	public function beforeFilter() {
		$this->response->header('Access-Control-Allow-Origin', '*');
		//The functions that are always allowed for every user
		$this->Auth->allow('index', 'view', 'display', 'autocomplete', 'resetPassword', 'activateAccount', 'enterDeveloperMode', 'exitDeveloperMode', 'search', 'export', 'register', 'login', 'images', 'modeltest'); 
		$this->Auth->allow();  //No login
		$this->action = $this->request->params['action'];
		$user = $this->Auth->user();
		$this->actor_id = $user['id'];
		$this->authUser = $user;
		$this->p = array_merge($this->request->data, $this->request->query);
		$this->set(array('request' => $this->p));
		//echo 'AJAX: '.intval($this->RequestHandler->isAjax());
		$this->isAjax = $this->request->is('ajax') || isset($this->p['isAjax']);
		//$this->json = isset($this->p['json']) || isset($this->request->params['json']);
		$this->set('isAjax', $this->isAjax);
		$this->DeveloperMode = $this->Session->read('DeveloperMode');
		$this->request->here = str_replace('json/', '', $this->request->here);
		$this->request->relative = '/'.str_replace($this->request->base.'/', '', $this->request->here);
		$this->request->controller = $this->request->params['controller'];
		$this->setLanguage('en');
		
		/*$this->set('webroot', $this->request->webroot);
		$this->set('domain', $this->request->host());
		$this->set('action', $this->request->params['action']);
		$this->set('model', $this->modelClass);
		$this->set('requestUrl', $this->request->url);*/
		
		$browser = new Browser();
		/*$this->set('browser', array('name' => $browser->Name));*/
		
		//for local testing
		if(strpos(FULL_BASE_URL, 'localhost') !== false)
			$this->isLocalhost = true;
			
/*		$methods = array('get', 'post', 'put', 'delete');
		foreach($methods as $m) {
			if($this->request->is($m)){
				$this->set('requestMethod', $m);
				break;
			}
		}*/
		//debug($user);
		/*if($this->request->params['admin']) {
			debug($user);
			if(!$user['isAdmin'])
				$this->redirect(str_replace('admin/', '/', $this->request->url));
			else
				$this->DeveloperMode = true;
		}*/
		if($this->json && false)
			$this->layout = 'json';
		else
			$this->layout = 'default';
		/*if($this->p['asTemplate'])
			$this->layout = 'template';*/
		return false;
		if(!$this->Auth->loggedIn()) {
			$allowed = $this->Auth->allowedActions;
			//Debugger::dump($allowed);
			$action = $this->request->params['action'];
			//echo $action;
			foreach($allowed as $a)
				if($a == $action) {
					$isAllowed = true;
					break;
				}
			if(!$isAllowed) {
				//@mkdir(WWW_ROOT.'TEST_'.$action);
				$this->er(__('Please log in.'));
				$this->set('mustLogin', $this->mustLogin = true);
				//$this->redirect('/users/login', 403, false);
			}
		}
	}
	
	/**
	* Checks whether the user is authorized for actions in the current project
	* @param int project_id: ID to check if the user has the necessary state
	* @param int state: the state the user must at least have in this project, 0: Guest, 1: Contributor, 2: Admin
	* @param bool render: whether to render an error message and change the view if no authorization is found
	* @returns boolean
	*/
	public function authorizedProject($project_id, $state, $render = true){
		if(!$this->Auth->user('id'))
			return false;
		if($this->Auth->user('isAdmin'))
			return true;
		//debug($this->Auth->user('id'));
		$this->loadModel('Project');
		$this->Project->Behaviors->attach('Containable');
		$project = $this->Project->find('first', array('fields' => 'id', 'contain' => array('Member' => array('fields' => 'id')), 'conditions' => 'Project.id = "'.$project_id.'"'));
		//debug($project);
		if(is_array($project['Member']))
			foreach($project['Member'] as $member) {
				if($member['id'] == $this->Auth->user('id')){
					return $member['ProjectsUser']['state'] >= $state;
				}
			}
		if($render) {
			$this->noPermissionError();
		}
		return false;
	}
	
	/**
	* Creates an error message, that the user has no rights to perform an action and renders an empty page
	* @return void
	*/
	public function noPermissionError(){
		$this->er(__('You have no permission to perform this action in this project.'));
		$this->render('../dummy');
	}
	
	public function isAuthorized() {
        $result = $this->__permitted($this->name,$this->action);
        return $result;
    }
	
	public function getViewFilename($type = false){
		$lim = '.';
		//debug($this->newView);
		
		if($this->newViewJs && $type == 'js') {
			if(strpos($this->newViewJs, '.') === false)
				return $this->modelClass.$lim.$this->newViewJs;
			return $this->newViewJs;
		}
		if(!$this->newView || $type == 'js')
			return $this->modelClass.$lim.$this->request->params['action'];
		return $this->modelClass.$lim.$this->newView;
	}
	
	/*
	*/
	public function render($view = null, $layout = null){
		if($view != '../dummy')
			$this->newView = $view; //Sets the name of the .html viewfile
		
		/*if(!$this->DeveloperMode && $this->layout != 'template' && $layout != 'template'){
			if(!$this->isAjax) {
				//$view = '../dummy';
				$layout = 'default';
			}
		}*/
		
		if($layout) $this->layout = $layout;
		if($this->layout == 'ajax')
			$this->layout = 'json';
		if($this->layout == 'default') {
			$this->set('loggedIn', $this->Auth->loggedIn());
			$this->set('authUser', $this->Auth->user());
		}
		return parent::render($view, $this->layout);
	}
	
	/*
	*Enter developer mode
	*/
	public function enterDeveloperMode(){
		$this->Session->write('DeveloperMode', 1);
		$this->DeveloperMode = 1;
		$this->redirect('/');
	}
	
	/*
	*Exit developer mode
	*/
	public function exitDeveloperMode(){
		$this->Session->write('DeveloperMode', 0);
		$this->DeveloperMode = 0;
		$this->redirect('/');
	}
	
	public function beforeRender(){
		//debug($this);
		$inValidation = array();
		//debug($this->{$this->modelClass}->validationErrors);
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
		
		if(!$this->isAjax || ($this->isAjax && $this->p['loadTemplate'])) {
			/*$this->set('loadTemplate', true);
			$file = WWW_ROOT.'js'.DS.'view'.DS.$this->getViewFilename().'.html';
			if(file_exists($file))
				$template = file_get_contents($file);*/
		}
		//$this->set('loadTemplate', true); //Da der View manchmal vom Server und manchmal vom CLient gerendert wird immer true.
		$data = $this->request->data; $query = $this->request->query;
		if(!is_array($data)) $data = array(); if(!is_array($query)) $query = array();
		$this->p = array_merge($data, $query);
		$this->set(array('request' => $this->p));
		
		$m = $this->Session->read('Message.flash.message');
		$this->set('flashMessage', $m);
		if(!$this->isAjax)
			$this->set('authUser', $this->authUser);
		
		$currentProject = $this->Session->read('Project');
		$this->set('currentProject', $currentProject);
		
		//$this->set('redirectTo', $this->redirectTo);
		//$this->viewVars['request'] = array_merge($this->request->data, $this->request->query);
		//debug($request);
		
		$return = array(
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
		/*if($this->redirectState || $this->redirectUrl) 
			$return['vars'] = array('redirectState' => $this->redirectState, 'redirectUrl' => $this->redirectUrl);*/
		
		/*if($template)
			$return['template'] = $template;*/
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
	
	/*
	  @inc array of names in $return['vars'] to be included in the result
	  @incD array of names in $return directly to be included in the result. if has value 'all', everything from $return directly is included in the result
	*/
	function limitReturn($inc, $incD = array()){
		$this->limitReturnList = array($inc, $incD);
	}
	
	
	function getTemplate($name){
		$n = explode('.', $name);
		$name = Inflector::pluralize($n[0]).'/'.implode('/', array_slice($n, 1));
		$this->render('/'.$name, 'template');
	}
	
	/*
	
	*/
	function search(){
		
	}
		
	/*
	 *
	 */
	public function getList(){
		//debug($this);
		$model = $this->modelClass;
		$this->loadModel($model);
		/*
		* params for array p: sort (order by), conditions: array or string to filter results, find: all, list, threaded etc.
		*/
		$this->p['find'] = $this->p['find'] ? $this->p['find'] : 'all';
		$list = $this->{$model}->find($this->p['find'], $this->p);
		$this->set('list', $list);
		$this->render('../dummy');
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
				$user = array_merge($user['User'], array('Setting' => $user['Setting'], 'Image' => $user['Image']));
				$this->Auth->login($user);
			} else {
				//$this->Auth->login(ClassRegistry::init('User')->findById($this->Auth->user('id')));
			}
		}
		$this->set('authUser', $this->Auth->user());
	}
	

	
	/*
	 * generates a globally unique ID
	*/
	function generateID(){
		$gen_id = rand(1000, 9999).rand(1000, 9999);
		$this->loadModel('User');
		$this->loadModel('Event');
		while($this->User->find('count', array('conditions' => 'User.id='.$gen_id)) > 0 ||
		$this->Event->find('count', array('conditions' => 'Event.id='.$gen_id)) > 0) {
				$gen_id = rand(1000, 9999).rand(1000, 9999);
		}
		return $gen_id;
	}
	
	/*
	 * overrides the standard afterFilter Method from cakephp to give the possibility to redirect 
	 * if redirectTo is passed as param.
	*/
	public function afterFilter(){
		if($this->p['redirectTo'])
			$this->redirect($this->p['redirectTo']);
		
	}
	
	/* 
	*/
	public function writeLog($type, $data, $dump = false){
		$this->loadModel('Log');
		$l = array('type' => $type, 'time' => time(), 'data' => $data[0], 'data_2' => $data[1],'data_3' => $data[2], 'data_4' => $data[3], 'data_5' => $data[4], 'related_id' => $data['related']);
		if(strpos($type, '_error') !== false) {
			$l['error'] = 1;
			$lt = $this->Log->find('first', array('fields' => 'Log.time', 'conditions' => 'type LIKE "%_error"', 'order' => 'Log.time DESC'));
			if(time() - $lt['Log']['time'] > strtotime('+12 hours', 0)){
				mail(console::$serviceEmail, 'Alldata Error: '.$data, $info);
			}
		}
		if($dump) {
			$l['dump'] = f::debug($dump);
			$l['error'] = true;
		}
		$this->Log->create();
		$this->Log->save($l);
	}
	
	public function getLogs($type, $params = array(), $combine = 'id', $order = 'time DESC'){
		$this->loadModel('Log');
		$params['type'] = $type;
		$logs = $this->Log->find('all', array('conditions' => $params));
		if($logs && $combine)
			$logs = Set::combine($logs, '{n}.Log.'.$combine, '{n}.Log');
		return $logs;
	}

	
	
    // This is a (MySQL specific) check to see if a 
    // constraint was violated as the last error. If it was,
    // the VALUE of the field which failed is returned.
    // this is not ideal, but will do for most situations.
    // The logic to work out the specific field which failed
    // requires more MySQL specific SQL (such as 'show keys from...'
    // so I shall leave it out. Most tables only have one 
    // unique constraint anyway, although our example above
    // has 2.
    function checkFailedConstraint() {
        $db =& ConnectionManager::getDataSource($this->useDbConfig); 
        $lastError = $db->lastError();

        // this is MYSQL SPECIFIC
        if(preg_match('/^\d+: Duplicate entry \'(.*)\' for key \d+$/i', $lastError, $matches)) {
            return $matches[1];
        }

        return false;
    } 
	
	function __permitted($controllerName,$actionName) {
        //Ensure checks are all made lower case
        $controllerName = low($controllerName);
        $actionName = low($actionName);
       
       
        //If permissions have not been cached to session...
        if(!$this->Session->check('Permissions')){
            //...then build permissions array and cache it
            $permissions = array();

            //everyone gets permission to logout
            $permissions[]='users:logout';

            //Import the User Model so we can build up the permission cache
            App::import('Model', 'User');
            $thisUser = new User;
            $thisUser->Behaviors->attach('Containable');
           
            //Now bring in the current users full record along with groups
            $thisUser->contain('Group');
            $thisGroups = $thisUser->find('first', array(
                'conditions'=>array('User.id'=>$this->Auth->user('id'))
            ));

            foreach($thisGroups['Group'] as $thisGroup) {
                $thisUser->contain('Permission');
                $thisPermissions = $thisUser->Group->find('first', array(
                    'conditions'=>array('Group.id'=>$thisGroup['id'])
                ));
               
                foreach($thisPermissions['Permission'] as $thisPermission) {
                    $permissions[]=$thisPermission['name'];
                }

            }
                //debug($permissions);
                //write the permissions array to session
                $permissions = array_unique($permissions);
                $this->Session->write('Permissions',$permissions);
               
            //}
        } else {
            //...they have been cached already, so retrieve them
            $permissions = $this->Session->read('Permissions');
        }
   
        //Now iterate through permissions for a positive match
        foreach($permissions as $permission) {
            if($permission == '*') {
                Configure::write('debug',2);
                return true; //Super Admin Bypass Found
            }
            if($permission == $controllerName.':*') {
                return true; //Controller Wide Bypass Found
            }
            if($permission == $controllerName.':'.$actionName) {
                return true; //Specific permission found
            }
        }
        return false;
    }
	
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
	
	/*
	* @e errorstring, if empty function returns bool if there already was an error. It sets errorsExist for 
	* the view to true and adds the errormessage to the Session->setFlash message.
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
	
	/*
	* @s success string. 
	* Gives a success message to the view
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
