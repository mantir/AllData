<?php

App::uses('CakeEmail', 'Network/Email');

class UsersController extends AppController {
	public $components = array(
		'Auth' => array(
			'authenticate' => array(
				'Form' => array(
					'fields' => array('username' => 'email')
				)
			)
		)
	);
	
	public function beforeFilter() {
		$this->Auth->fields = array(
			'username' => 'name OR email',
			'password' => 'password'
		);
		parent::beforeFilter();
		$this->Auth->allow('register'); // Letting users register themselves
	}
	
	/*public function initDB() {
		$this->Acl->deny(0, 'addy');
	
		//we add an exit to avoid an ugly "missing views" error message
		echo "all done";
		exit;
	}*/
	
	public function index(){
		$this->login();
		$this->render('login');
	}
	
	public function addy_index(){
		$this->set('session', $this->Auth->user());
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}
	
	public function register() {
		$this->loadModel('Aro');
		//debug($this->request->data);
		$this->set("groups", $this->Aro->find('list', array('fields' => array('Aro.alias'))));
		if ($this->request->is('post')) {
			$this->User->create();
			$this->request->data['User']['url'] = $this->generateUrl();
			$this->request->data['User']['id'] = $this->generateID();
			$this->request->data['User']['activated'] = 0;
			$this->request->data['Setting']['object_id'] = $this->request->data['User']['id'];
			$user = $this->request->data['User'];
			if (($user['password'] == $user['passwordRepeat']) && $this->User->save($this->request->data)) {
					$this->loadModel('Activation');
					$code = $this->Activation->generateCode('ac', $this->request->data['User']['id'], $this->request->clientIp());
					$this->su(__('The registration was successfull. An email with an activation link was sent to your inbox.'));
					//cant send mails from localhost...
					if(!$this->isLocalhost) {
						$email = new CakeEmail();
						$email
							->from(muzup::$noreplyEmail)->to($this->request->data['User']['email'])
							->template('registered')
							->emailFormat('both')
							->subject(__('Activate your Muzup account'))
							->viewVars(array('user' => $this->request->data['User'], 'activateLink' => Router::url(array('controller' => 'users', 'action' => 'activateAccount', '?' => array('activation' => $code)), true)))
							->send();
					}
					$this->redirect(array('action' => 'login'));
				} else {
					if($user['password'] != $user['passwordRepeat'])
						$this->er(__('The passwords were not the same.')); 
					else if(!$user['password'])
						$this->er(__('Please type in a password'));
					else
						$this->er(__('Something went wrong, please try again.'));
			}
		}
	}
	
	public function firstWelcome(){
		$user = $this->Auth->user();
		$this->edit($id);
	}
	
	function generateUrl(){
		$gen_id = rand(1000, 9999).rand(1000, 9999);
		while($this->User->find('count', array('conditions' => 'Url="'.$gen_id.'"')) > 0){
			$gen_id = rand(1000, 9999).rand(1000, 9999);
		}
		return $gen_id;
	}
	
	function generateID(){
		$gen_id = rand(1000, 9999).rand(1000, 9999);
		while($this->User->find('count', array('conditions' => 'User.id='.$gen_id))) {
			$gen_id = rand(1000, 9999).rand(1000, 9999);
		}
		return $gen_id;
	}
	
	public function login() {
		$user = $this->Auth->user();
		//debug($this->Auth->user());
		//debug($this->request->data);
		if($this->Session->read('Auth.redirect')){
			$this->set('goto', $this->Session->read('Auth.redirect'));
		}
		if($this->Auth->loggedIn()) {
			$this->request->query['target'] = '_none';
			$this->redirect('/');
		}
		if ($this->request->is('post')) {
			$this->Auth->userScope = array('User.activated'=>1);
			if ($this->Auth->login()) {
				$this->_refreshAuth();
				$this->set('loggedIn', true);
				$this->su(__('Sie sind nun eingeloggt.'));
				if($this->request->data['User']['goto'])
					$this->redirect($this->Auth->redirect(), 'real');
				else
					$this->redirect($this->Auth->redirect('/', 'real'));
			} else {
				$this->er(__('Invalid email or password, try again'));
			}
		}
	}
	
	public function logout() {
		$this->redirect($this->Auth->logout(), 'real');
		$this->render('../dummy');
	}
	
	public function activateAccount(){
		$q = $this->request->query;
		$this->loadModel('Activation');
		if($q['activation']){
			$a = $this->Activation->find('first', array('conditions' => 'id="'.$q['activation'].'"'));
			if(!empty($a)){
				$this->User->id = $a['Activation']['object_id'];
				if($this->User->exists()) {
					$this->User->save(array('User' => array('activated' => 1)));
					$user = $this->User->read();
					$this->request->data = $user;
					$this->Auth->login($user);
					$this->_refreshAuth();
					$this->su(__('Your account was successfully verified. Welcome to Muzup!'));
					$this->redirect(array('controller' => 'users', 'action' => 'view', $this->User->id));
				} else {
					$this->er(__('Invalid link.'));
				}
			} else {
				$this->er(__('Invalid link.'));
			}
		}
		$this->render('../dummy');
	}
	
	public function resetPassword(){
		$q = $this->request->query;
		$this->loadModel('Activation');
		if($q['activation']){
			$a = $this->Activation->find('first', array('conditions' => 'id="'.$q['activation'].'"'));
			if(!empty($a)){
				$this->set('allowReset', true);
			} else {
				$this->er(__('Invalid link.'));
				return;
			}
		}
		if($this->request->is('post')){
			$d = $this->request->data;
			if($d['User']['email']) {
				$u = $this->User->find('first', array('conditions' => array('User.email' => $d['User']['email'])));
				if(empty($u)){
					$this->er(__('It doesn\'t exist a user with this name or email address.'));
				} else {
					debug($u);
					$code = $this->Activation->generateCode('pw', $u['User']['id'], $this->request->clientIp());
					$email = new CakeEmail();
					$email->from(muzup::$noreplyEmail);
					$email->to($u['User']['email']);
					$email->template('beforeResetPassword');
					$email->emailFormat('both');
					$email->subject(__('Reset your password'));
					$email->viewVars(array('user' => $u['User'], 'resetLink' => Router::url(array('controller' => 'users', 'action' => 'resetPassword', '?' => array('activation' => $code)), true)));
					$email->send();
					$this->su(__('An email with a reset link was sent to your inbox.'));
				}
			} else {
				$u = $this->User->find('first', array('conditions' => array('User.id' => $a['Activation']['object_id'])));
				if(empty($u)){
					$this->er(__('The user does not exist.'));
				} else if($d['User']['password']) {
					$user = $this->request->data['User'];
					$this->User->unbindValidation('keep', array('password'), true);
					$this->User->id = $u['User']['id'];
					if (($user['password'] == $user['passwordRepeat']) && $this->User->save($this->request->data)) {
						$this->Activation->delete($a['Activation']['id']);
						$this->su(__('The password was changed, you can now login.'));
						$this->redirect('login');
					} else {
						if($user['password'] != $user['passwordRepeat']) {
							$this->er(__('The passwords were not the same.'));
							$this->User->set($this->request->data);
							//debug($this);
							$this->User->validates();
						} else
							$this->er(__('Something went wrong, please try again.'));
						
					}
				}
			}
		} 
	}
	
	public function addy_login(){
		$this->render('login');
		$this->login();
	}
	
	public function addy_loginAs($id = null){
		if($this->Auth->user('isAdmin')){
			$user = $this->User->read(null, $id);
			$this->Auth->login($user);
			$this->_refreshAuth();
			$this->_refreshAuth('isAdmin', true);
		}
		$this->redirect($this->referer());
	}
	
/**
 * view method Profile
 *
 * @param string $url
 * @return void
 */
	public function view($url = null) {
		//$this->er('URL: '.$url);
		if(!$url) {
			$user = $this->Auth->user();
			$url = $user['url'];
			//debug($user);
		} 
		$user = $this->User->find('first', array('conditions' => 'User.url = "'.$url.'"'));
		//debug($user);
		$this->User->id = $user['User']['id'];
		$recursive = 1;
		if (!$this->User->exists()) {
			$user = $this->User->find('first', array('recursive' => $recursive, 'conditions' => 'User.id = "'.$url.'"'));
			$this->User->id = $user['User']['id'];
			if (!$this->User->exists()) {
				$this->er("The user doesn't exist.");
			} else $exists = true;
		} else $exists = true;
		if($exists){
			$ownProfile = false;
			$isRecommended = false;
			if($this->Auth->loggedIn()) {
				if($this->User->id == $this->Auth->user('id')) 
					$ownProfile = true; 
				else {
					$self = $this->User->find('first', array('recursive' => $recursive, 'conditions' => 'User.id='.$this->Auth->user('id')));
					if($self['RecommendedUser'])
						foreach($self['RecommendedUser'] as $ru) {
							if($ru['id'] == $this->User->id) {
								$isRecommended = true;
								break;
							}
						}
				}
			}
		}
		$this->set(compact('user', 'ownProfile', 'isRecommended'));
	}
	
/**
 * view method Profile
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	
	public function dashboard(){
		
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
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
		$user = $this->Auth->user();
		$id = $user['id'];
		$this->User->id = $id;
		if (!$this->User->exists()) {
			$this->redirect('/');
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$user = $this->request->data['User'];
			$data = $this->request->data;
			//Create New Genres for User
			$data['Genre']['Genre'] = $this->addGenres($data['New']['Genre'], $data['Genre']['Genre']);
			//Create New Tags for User
			$data['Tag']['Tag'] = $this->addTags($data['New']['Tag'], $data['Tag']['Tag']);
			if ($this->User->save($data)) {
				$u = $this->User->read(); //read user to get the setting id
				$this->User->Setting->id = $u['Setting']['id']; //set the setting id for saving
				$this->User->Setting->save($this->request->data);  //save the settings model
				//debug($this->User->lastQuery());
				$this->_refreshAuth(); //refresh the authUser, if his url was change
				//$this->_refreshAuth('name', $this->data['User']['name']); //refresh the authUser
				//$this->_refreshAuth('Setting', $u['Setting']); //refresh the authUser
				$this->Session->setFlash(__('Changes has been saved'));
				//$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('Something went wrong. Please, try again.'));
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
			$this->request->data['New']['Genre'] = $this->request->data['Genre'];
			$tags = $this->request->data['Tag'];
			if(count($tags))
				$this->request->data['New']['Tag'] = implode(',', Set::classicExtract($tags, '{n}.name'));
		}
		$user = $this->User->read(null, $id);
		$tags = $this->User->Tag->find('list');
		$topgenres = $this->User->Genre->mainGenres('list');
		$this->set(compact('tags', 'topgenres', 'user'));
		//debug($this->Session->read('Auth.User.url'));
	}
	
/**
 * changePassword method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function changePassword($id = null) {
		$user = $this->Auth->user();
		
		$this->User->id = $user['id'];
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
		}
	}


/**
 * addy_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function addy_view($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

/**
 * addy_add method
 *
 * @return void
 */
	public function addy_add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		}
	}

/**
 * addy_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function addy_edit($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
		}
	}

	/**
	 * addy_delete method
	 *
	 * @throws MethodNotAllowedException
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function addy_delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->User->delete()) {
			$this->Session->setFlash(__('User deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('User was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
	

}