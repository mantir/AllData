<?php
/**
 * Users Controller
 *
 * Provides all user related functions.
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://headkino.de
 * @package       app.Controller
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('CakeEmail', 'Network/Email');
/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {
	public $components = array(
		'Auth' => array(
			'authenticate' => array(
				'Form' => array(
					'fields' => array('username' => 'email'),
					'scope' => array('activated' => 1, 'isGuest' => 0)
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
		$this->Acl->deny(0, 'admin');
	
		//we add an exit to avoid an ugly "missing views" error message
		echo "all done";
		exit;
	}*/
	
	public function index(){
		$this->login();
		$this->render('login');
	}
	
	public function admin_index(){
		$this->set('session', $this->Auth->user());
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}
	
	public function register() {
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->User->create();
			$this->request->data['User']['id'] = $this->generateID();
			$this->request->data['User']['activated'] = 0;
			$this->request->data['isAdmin'] = $this->User->find('count') == 0; //If it is the first user, it will be the admin
			$user = $this->request->data['User'];
			$this->request->data['User']['register_time'] = time();
			$this->request->data['User']['email'] = strtolower($this->request->data['User']['email']);
			$code = $this->request->data['User']['activation_code'] = uniqid();
			if (($user['password'] == $user['passwordRepeat']) && $this->User->save($this->request->data)) {
					$this->su(__('The registration was successfull. An email with an activation link was sent to your inbox.'));
					//cant send mails from localhost...
					if(!$this->isLocalhost) {
						$email = new CakeEmail();
						$sent = $email
							->from(console::$noreplyEmail)->to($this->request->data['User']['email'])
							->template('registered')
							->emailFormat('both')
							->subject(__('Activate your '.console::$systemName.' account'))
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
			if ($this->Auth->login()) {
				$this->_refreshAuth();
				$this->set('loggedIn', true);
				$this->su(__('You are now logged in.'));
				if($this->request->data['User']['goto'])
					$this->redirect($this->Auth->redirect(), 'real');
				else
					$this->redirect($this->Auth->redirect('/', 'real'));
			} else {
				$this->er(__('Invalid email or password or account was not activated.'));
			}
		}
	}
	
	public function logout() {
		$this->redirect($this->Auth->logout());
		$this->render('../dummy');
	}
	
	public function activateAccount(){
		$q = $this->request->query;
		$this->loadModel('Activation');
		if($q['activation']){
			$u = $this->User->find('first', array('conditions' => 'activation_code="'.$q['activation'].'"'));
			if(!empty($u)){
				$this->User->id = $u['User']['id'];
				if($this->User->exists()) {
					$this->User->save(array('User' => array('activated' => 1)));
					$user = $this->User->read();
					$this->request->data = $user;
					$this->Auth->login($user);
					$this->_refreshAuth();
					$this->su(__('Your account was successfully verified. Welcome to '.console::$systemName.'!'));
					$this->redirect('/');
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
			$a = $this->User->find('first', array('conditions' => 'activation_code="'.$q['activation'].'"'));
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
				$u = $this->User->find('first', array('conditions' => array('User.email' => strtolower($d['User']['email']), 'User.activated' => 1)));
				if(empty($u)){
					$this->er(__('The user does not exist.'));
				} else {
					$code = uniqid();
					$this->User->id = $u['User']['id'];
					$this->User->save(array('activation_code' => $code));
					$email = new CakeEmail();
					$email->from(console::$noreplyEmail);
					$email->to($u['User']['email']);
					$email->template('beforeResetPassword');
					$email->emailFormat('both');
					$email->subject(__('Reset your password'));
					$email->viewVars(array('user' => $u['User'], 'resetLink' => Router::url(array('controller' => 'users', 'action' => 'resetPassword', '?' => array('activation' => $code)), true)));
					$email->send();
					$this->su(__('An email with a reset link was sent to your inbox.'));
				}
			} else {
				$u = $a;
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
							$this->User->validates();
						} else
							$this->er(__('Something went wrong, please try again.'));
						
					}
				}
			}
		} 
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
			$url = $user['id'];
			//debug($user);
		} 
		$user = $this->User->find('first', array('conditions' => 'User.id = "'.$url.'"'));
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
		$this->set(compact('user', 'ownProfile', 'isRecommended'));
	}


/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function settings() {
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
			if ($this->User->save($this->request->data)) {
				$u = $this->User->read(); //read user to get the setting id
				//debug($this->User->lastQuery());
				$this->_refreshAuth(); //refresh the authUser, if his url was change
				//$this->_refreshAuth('name', $this->data['User']['name']); //refresh the authUser
				//$this->_refreshAuth('Setting', $u['Setting']); //refresh the authUser
				$this->Session->setFlash(__('Changes have been saved'));
				//$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('Something went wrong. Please, try again.'));
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
			unset($this->request->data['User']['password']);
		}
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
			$password_correct = $this->User->find('count', array('conditions' => array(
				'email' => strtolower($user['email']), 
				'password' => $this->Auth->password($this->request->data['User']['old_password'])))
			);
			$r = $this->request->data;
			if ($password_correct && ($r['User']['password'] == $r['User']['password_repeat']) && $this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The new password has been saved'));
				$this->redirect(array('action' => 'settings'));
			} else {
				if($r['User']['password'] != $r['User']['password_repeat']) {
					$this->er(__('The new passwords were not the same.'));
					$this->User->set($this->request->data);
					$this->User->validates();
				} else if(!$password_correct) {
					$this->er(__('The old password is not correct.'));
				} else
					$this->er(__('Something went wrong, please try again.'));
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
		}
	}


/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
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
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
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
	 * admin_delete method
	 *
	 * @throws MethodNotAllowedException
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_delete($id = null) {
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