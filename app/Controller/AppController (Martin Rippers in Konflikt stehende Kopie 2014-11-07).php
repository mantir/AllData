<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');
App::uses('console', 'Lib'); //console special functions
App::uses('s', 'Lib'); //special String functions
App::uses('f', 'Lib'); //helper functions
App::uses('Browser', 'Vendor'); //to get browser Information

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	/*
		p is the merge between query params and posted params, so all params from GET and POST together
	*/
	var $p = array();
	var $error = '', $success = '';
	var $isAjax = false;
	var $beforeRenderExecuted = false;
	var $newView = false, $newViewJs = false;
	var $errorsExist = false;
	var $redirectState = false;
	var $limitReturnList = array(array(), array());
	
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
		$this->set('loggedIn', true);// $this->Auth->loggedIn());
		$this->action = $this->request->params['action'];
		$user = $this->Auth->user();
		$this->actor_id = $user['id'];
		$this->authUser = $user;
		$this->p = array_merge($this->request->data, $this->request->query);
		$this->set(array('request' => $this->p));
		//echo 'AJAX: '.intval($this->RequestHandler->isAjax());
		$this->isAjax = $this->request->is('ajax') || isset($this->p['isAjax']);
		$this->set('isAjax', $this->isAjax);
		$this->DeveloperMode = $this->Session->read('DeveloperMode');
		$this->request->here = str_replace('json/', '', $this->request->here);
		$this->request->relative = '/'.str_replace($this->request->base.'/', '', $this->request->here);
		$this->request->controller = $this->request->params['controller'];
		
		$this->set('webroot', $this->request->webroot);
		$this->set('domain', $this->request->host());
		$this->set('action', $this->request->params['action']);
		$this->set('model', $this->modelClass);
		$this->set('requestUrl', $this->request->url);
		$this->set('errors', $errors);
		
		$browser = new Browser();
		$this->set('browser', array('name' => $browser->Name));
		
		//for local testing
		if(strpos(FULL_BASE_URL, 'localhost') !== false)
			$this->isLocalhost = true;
			
		$methods = array('get', 'post', 'put', 'delete');
		foreach($methods as $m) {
			if($this->request->is($m)){
				$this->set('requestMethod', $m);
				break;
			}
		}
		
		if($this->request->params['addy']) {
			//debug($user);
			if(!$user['isAdmin'])
				$this->redirect(str_replace('addy/', '/', $this->request->url));
			else
				$this->DeveloperMode = true;
		}
		
		$this->layout = 'ajax';
		if($this->p['asTemplate'])
			$this->layout = 'template';
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
		//debug($this->DeveloperMode);
		if($view != '../dummy')
			$this->newView = $view; //Sets the name of the .html viewfile
		
		if($layout == 'js') {
			//Sets the name of the .js viewfile
			$this->newViewJs = $view;
			return;
		}
		if(!$this->DeveloperMode && $this->layout != 'template' && $layout != 'template'){
			if(!$this->isAjax) {
				//$view = '../dummy';
				$layout = 'default';
			}
		}
		//debug($view);
		if($layout) $this->layout = $layout;
		//debug($this->layout);
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
			$this->set('loadTemplate', true);
			/*$file = WWW_ROOT.'js'.DS.'view'.DS.$this->getViewFilename().'.html';
			if(file_exists($file))
				$template = file_get_contents($file);*/
		}
		$this->set('loadTemplate', true); //Da der View manchmal vom Server und manchmal vom CLient gerendert wird immer true.
		$data = $this->request->data; $query = $this->request->query;
		if(!is_array($data)) $data = array(); if(!is_array($query)) $query = array();
		$this->p = array_merge($data, $query);
		$this->set(array('request' => $this->p));
		
		$m = $this->Session->read('Message.flash.message');
		$this->set('flashMessage', $m);
		$this->set('authUser', $this->authUser);
		//$this->set('redirectTo', $this->redirectTo);
		//$this->viewVars['request'] = array_merge($this->request->data, $this->request->query);
		//debug($request);
		
		$return = array(
			'passedArgs' => $this->passedArgs,
			'vars' => $this->viewVars,
			'viewJs' => $this->getViewFilename('js'),
			'errors' => $errors,
			//'request' => $this->request,
			//'data' => $this,
			'message' => $m,
			'url' => $this->request->url,
			'action' => $this->request->params['action'],
			'controller' => $this->request->params['controller'],
			'model' => $this->modelClass,
			'base' => $this->request->base,
			'webroot' => $this->request->webroot,
			'browser' => $this->viewVars['browser'],
			'here' => $this->request->here,
			'hereRel' => str_replace($this->request->base.'/', '', $this->request->here)
		);
		if($this->request->params['controller'] == 'pages'){
			$return['action'] = $return['vars']['action'] = $this->passedArgs[0];
			unset($return['passedArgs'][0]);
		}
		if($this->redirectState || $this->redirectUrl) 
			$return['vars'] = array('redirectState' => $this->redirectState, 'redirectUrl' => $this->redirectUrl);
		
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
	
	/*
	 *
	 */
	public function autocomplete($model = false){
		//debug($this->p);
		//activate more autocompletes with adding e.g. /-/tags/genres/etc../
		if($model && $model != '-') {
			$more = true;
			$model = Inflector::camelize(Inflector::singularize($model));
		} else
			$model = $this->modelClass;
		$this->loadModel($model);
		$s = trim($this->p['query']);
		$v = $this->p['property'] ? $model.'.'.$this->p['property'] : $model.'.name';
		$s2 = substr($s, 1);
		$distance = isset($this->p['distance']) ? $this->p['distance'] : 2;
		if(strlen($s) > 4 && $distance > 2) $distance = 2;
		if(strlen($s) > 5 && $distance > 1) $distance = 1;
		if(strlen($s) > 9) $distance = 0;
		//elseif(strlen($s) < 7) $distance = 2;
		//elseif(strlen($s) < 15) $distance = 3;
		if($s2)
			$regex = '( |^)'.$s[0].'('.muzup::wordRegex($s2, $distance).').*'; //find all words beginning with the first letter and rest of the word edit distance 2 and autcompleted
		else
			$regex = '^'.$s[0].'.*';
		//debug($regex);
		$cond = array($v.' REGEXP' => $regex);
		$conditions = $this->p['conditions'];

		if(is_array($conditions))
			$conditions = array_merge($conditions, $cond);
		else
			$conditions = $cond;
		$results = $this->{$model}->find('all', array('conditions' => $conditions, 'group' => $v, 'order' => 'CHAR_LENGTH('.$v.') ASC', 'recursive' => -1));
		//debug($this->{$model}->lastQuery));
		//$this->set('results', Set::combine($results, '{n}.'.$model.'.id', '{n}.'.$model));
		$sug = Set::classicExtract($results, '{n}.'.$v);
		if(!is_array($sug)) $sug = array();
		$data = Set::classicExtract($results, '{n}.'.$model);
		if(!is_array($data)) $data = array();
		
		//if was added to the url with /-/
		if($more) {
			return array('data' => $data, 'sug' => $sug);
		}
		//if some more args were passed with /-/ collect the other results and merge
		if(count($this->passedArgs))
			foreach($this->passedArgs as $i => $a){
				if($a == '-') continue;
				$more = $this->autocomplete($a);
				$data = array_merge($data, $more['data']);
				$sug = array_merge($sug, $more['sug']);
			}
		$this->set('suggestions', $sug);
		$this->set('data', $data);
		$this->set('query', $this->p['query']);
		$this->limitReturn(array('suggestions', 'data', 'query')); //to speed things up only include this into the results
		$this->render('../autocomplete');
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
	public function writeLog($type, $title, $info, $link = '', $dump = false){
		$this->loadModel('Log');
		$l = array('type' => $type, 'time' => time(), 'title' => $title, 'info' => $info, 'link' => $link);
		if(strpos($type, '_error') !== false) {
			$lt = $this->Log->find('first', array('fields' => 'Log.time', 'conditions' => 'type LIKE "%_error"', 'order' => 'Log.time DESC'));
			debug($lt);
			if(time() - $lt['Log']['time'] > strtotime('+12 hours', 0)){
				mail(console::$serviceEmail, 'Houston Error: '.$title, $info);
			}
		}
		if($dump) {
			$l['dump'] = f::debug($dump);
			$l['error'] = true;
		}
		$this->Log->create();
		$this->Log->save($l);
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
        }else{
            //...they have been cached already, so retrieve them
            $permissions = $this->Session->read('Permissions');
        }
   
        //Now iterate through permissions for a positive match
        foreach($permissions as $permission) {
            if($permission == '*') {
                Configure::write('debug',2);
                return true;//Super Admin Bypass Found
            }
            if($permission == $controllerName.':*') {
                return true;//Controller Wide Bypass Found
            }
            if($permission == $controllerName.':'.$actionName) {
                return true;//Specific permission found
            }
        }
        return false;
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
	
	function test(){ 
		//$d = simplexml_load_string('');
		$date = '14.09.2013';
		$datetime1 = new DateTime($date);
		$interval = $datetime1->diff(new DateTime(date('d.m.Y')));
		debug($interval);
		$this->render('../dummy');
		
		$this->loadModel('Station');
		$ev = $this->Station->find('all', array('recursive' => 0, 'order' => 'rank ASC'));
		echo json_encode($ev);
		$this->render('../dummy');
	}
	
	function modeltest($id = false){
		$this->loadModel('Event');
		$this->Event->Behaviors->load('Containable');
		$contain = array('Video.Similar');
		debug($contain);
		$e = $this->Event->find('all', array('conditions' => 'Event.id=150953 AND Event.station_id=28229', 'limit' => 200, 'contain' => $contain));
		debug($e);
		
	}

}
