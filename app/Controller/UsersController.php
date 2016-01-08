<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class UsersController extends AppController {
	
	public $uses = array('User', 'Group', 'Company.Company', 'SignatureArchive', 'BlockedUser');
	
	var $paginate = array(
		'group' => 'User.id',
        'order' => array(
            'User.name' => 'asc'
        ),
		'limit' => ROWPERPAGE
    );
	function beforeRender()
    {
        parent::beforeRender();
		$stateOptions = $this->User->stateOptions();
		$groups = $this->Group->find('list',array('fields' => 'id,name'));
		$companies = $this->Company->find('list', array('fields' => 'id, name', 'permissionable' => false, 'conditions' => array('Company.history_status' => 1)));
		$this->set(compact('stateOptions','companies', 'groups'));
	}
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('logout');
	}
	function captcha($width = 150, $height = 50){
		$this->autoRender = false;
		$this->layout='ajax';
		if(!isset($this->Captcha)){
			$this->Captcha = $this->Components->load('Captcha', array(
				'width' => $width,
				'height' => $height,
				'theme' => 'random',
			));
		}
		$this->Captcha->create();
	}
    public function index() {
        $this->User->recursive = 1;
   		/*$this->User->virtualFields = array(
			'group' => 'GROUP_CONCAT(Group.name SEPARATOR \', \')'
		);*/
		$this->set('users', $this->paginate('User'));
    }

    public function view($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->set('user', $this->User->read(null, $id));
    }

    public function add() {
        if ($this->request->is('post')) {
			// print_r($this->request->data);die;
            $this->User->create();

			$this->request->data['User']['date_joined'] = gmdate('Y-m-d H:i:s');
			$password = getPassword();
			$this->request->data['User']['password'] = $password;
			$signature = substr(MD5($this->request->data['User']['email'].$this->request->data['User']['date_joined']), 0, 7);
			$this->request->data['User']['signature'] = $signature;
			$error_status = false;
			$error_message = '';
			$groups = array();
            foreach ($this->request->data['Group']['Group'] as $key => $value) {
        		if(!empty($value))
    				$groups[] = $value;
        		else
        			unset($this->request->data['Group']['Group'][$key]);
            }
            if(empty($groups)){
            	$error_status = true;
				$error_message .= __('Group not empty') . '<br />';
            }
			if(in_array(Configure::read('Settings.Company.JobGroupId'), $groups) ) {
				if(empty($this->request->data['User']['candidate_address'])) {
					$error_status = true;
					$error_message .= __('Candidate address not empty') . '<br />';
				}
				if(!empty($this->request->data['User']['candidate_dob'])) {
					$this->request->data['User']['candidate_dob'] = sqlFormatDate($this->request->data['User']['candidate_dob']);
				}
				else {
					$error_status = true;
					$error_message .= __('Candidate date of birth not empty') . '<br />';
				}
				if(empty($this->request->data['User']['candidate_last_drawn_salary'])) {
					$error_status = true;
					$error_message .= __('Candidate last drawn salary not empty') . '<br />';
				}
				if(empty($this->request->data['User']['candidate_expected_salary'])) {
					$error_status = true;
					$error_message .= __('Candidate expected salary not empty') . '<br />';
				}
				if(!empty($this->request->data['User']['candidate_resume'])) {
					$this->request->data['User']['candidate_resume'] = json_encode($this->request->data['User']['candidate_resume']);
				}
				else {
					$error_status = true;
					$error_message .= __('Candidate resume birth not empty') . '<br />';
				}
				if(!empty($this->request->data['User']['candidate_portfolio_files'])) {
					$this->request->data['User']['candidate_portfolio_files'] = json_encode($this->request->data['User']['candidate_portfolio_files']);
				}
			}
			else {
				unset($this->request->data['User']['candidate_dob']);
				unset($this->request->data['User']['candidate_address']);
				unset($this->request->data['User']['candidate_last_drawn_salary']);
				unset($this->request->data['User']['candidate_expected_salary']);
				unset($this->request->data['User']['candidate_resume']);
				unset($this->request->data['User']['candidate_portfolio_link']);
				unset($this->request->data['User']['candidate_portfolio_files']);
			}
			
			if(!$error_status) {
	            if ($this->User->saveAssociated($this->request->data)){
					//save Signature archive
					$id = $this->User->id;
					//save group
					/*$user_group = array();
					foreach ($groups as $key => $value) {
						$user_group[$key]['user_id'] = $id;
						$user_group[$key]['group_id'] = $value;
					}
					if(!empty($user_group))
						$this->UsersGroup->saveMany($user_group);*/
					$arrSignature = array();
					$arrSignature['SignatureArchive']['user_id'] = $id;
					$arrSignature['SignatureArchive']['signature'] = $signature;
					$arrSignature['SignatureArchive']['user_modify'] = $this->Session->read('Auth.User.id');
					$arrSignature['SignatureArchive']['date_from'] = gmdate('Y-m-d h:i:s');
					$arrSignature['SignatureArchive']['date_till'] = gmdate('Y-m-d h:i:s');
					$this->SignatureArchive->save($arrSignature);
					$mail_content = "Account informations: \n\n" .
									"Name: " . $this->request->data['User']['name'] . "\n" .
									"Email login: " . $this->request->data['User']['email'] . "\n" .
									"Password: " . $password . "\n" .
									"Signature: " . $signature . "\n";
					$arr_options = array(
						'to' => array($this->request->data['User']['email']),
						'viewVars' => array('content' => $mail_content)
					);
					$this->_sendEmail($arr_options);
					
	                $this->Session->setFlash(__('The user has been saved'));
	                return $this->redirect(array('action' => 'index'));
	            }
	            else {
	            	if(isset($this->request->data['User']['candidate_dob']))
	            		$this->request->data['User']['candidate_dob'] = formatDate($this->request->data['User']['candidate_dob']);
	            	$this->Session->setFlash(
		                __('The user could not be saved. Please, try again.'),'warning'
		            );
	            }
            }
            else {
            	$this->request->data['User']['candidate_dob'] = formatDate($this->request->data['User']['candidate_dob']);
            	$this->Session->setFlash(
                	$error_message,'warning'
            	);
            }
            // $this->Session->setFlash(
            //     __('The user could not be saved. Please, try again.'),'warning'
            // );
        }
		$this->render('edit');
    }

    public function edit($id = null) {
        $this->User->id = $id;
     //    $group_id = $this->User->Group->find('list', array(
     //    	'fields' => 'id, group_id',
     //    	'conditions' => array('user_id' => $id)
    	// ));
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
			/* if(!$this->request->data['User']['password']){
				unset($this->request->data['User']['password']);
			} */

			$error_status = false;
			$error_message = '';
			$groups = array();
            foreach ($this->request->data['Group']['Group'] as $key => $value) {
        		if(!empty($value))
    				$groups[] = $value;
        		else
        			unset($this->request->data['Group']['Group'][$key]);
            }
            if(empty($groups)){
            	$error_status = true;
				$error_message .= __('Group not empty') . '<br />';
            }
      
			if(in_array(Configure::read('Settings.Company.JobGroupId'), $groups) ) {
				if(empty($this->request->data['User']['candidate_address'])) {
					$error_status = true;
					$error_message .= __('Candidate address not empty') . '<br />';
				}
				if(!empty($this->request->data['User']['candidate_dob'])) {
					$this->request->data['User']['candidate_dob'] = sqlFormatDate($this->request->data['User']['candidate_dob']);
				}
				else {
					$error_status = true;
					$error_message .= __('Candidate date of birth not empty') . '<br />';
				}
				if(empty($this->request->data['User']['candidate_last_drawn_salary'])) {
					$error_status = true;
					$error_message .= __('Candidate last drawn salary not empty') . '<br />';
				}
				if(empty($this->request->data['User']['candidate_expected_salary'])) {
					$error_status = true;
					$error_message .= __('Candidate expected salary not empty') . '<br />';
				}
				if(!empty($this->request->data['User']['candidate_resume'])) {
					$this->request->data['User']['candidate_resume'] = json_encode($this->request->data['User']['candidate_resume']);
				}
				else {
					$error_status = true;
					$error_message .= __('Candidate resume birth not empty') . '<br />';
				}
				if(!empty($this->request->data['User']['candidate_portfolio_files'])) {
					$this->request->data['User']['candidate_portfolio_files'] = json_encode($this->request->data['User']['candidate_portfolio_files']);
				}
			}
			else {
				unset($this->request->data['User']['candidate_dob']);
				unset($this->request->data['User']['candidate_address']);
				unset($this->request->data['User']['candidate_last_drawn_salary']);
				unset($this->request->data['User']['candidate_expected_salary']);
				unset($this->request->data['User']['candidate_resume']);
				unset($this->request->data['User']['candidate_portfolio_link']);
				unset($this->request->data['User']['candidate_portfolio_files']);
			}
			if(!$error_status) {
	            if ($this->User->save($this->request->data)) {
	                $this->Session->setFlash(__('The user has been saved'));
	                return $this->redirect(array('action' => 'index'));
	            }
	            else {
		            $this->Session->setFlash(
		                __('The user could not be saved. Please, try again.'),'warning'
		            );
	        	}
	        }
	        else {
	        	$this->request->data['User']['candidate_dob'] = formatDate($this->request->data['User']['candidate_dob']);
            	$this->Session->setFlash(
                	$error_message,'warning'
            	);
	        }
        } else {
            $this->request->data = $this->User->read(null, $id);
            //$this->request->data['User']['group_id'] = $group_id;
            if(!empty($this->request->data['User']['candidate_dob']))
            	$this->request->data['User']['candidate_dob'] = formatDate($this->request->data['User']['candidate_dob']);
			$this->set('resume',json_decode($this->request->data['User']['candidate_resume'], true));
			$this->set('portfolio_files',json_decode($this->request->data['User']['candidate_portfolio_files'], true));
            unset($this->request->data['User']['password']);
        }
    }

    public function delete($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if($id == SUPER_ADMIN)
        	throw new NotFoundException(__('Can not delete supper admin'));
        	
        if ($this->User->delete()) {
            $this->Session->setFlash(__('User deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('User was not deleted'),'danger');
        return $this->redirect(array('action' => 'index'));
    }
	
	public function login() {
		if ($this->request->is('post')) {
			if ($this->Auth->login()){
				
				if($this->Auth->user('active') == 3){
					
					$this->Session->setFlash(__('Our records show that this account has expired. This may be due to staff turnover or appointment change. Please contact your account manager or administrator for a new account.', 'danger'));
					return $this->redirect($this->Auth->logout());
				}
				else if($this->Auth->user('active') == 0) {
					$this->Session->setFlash(__('User not active!', 'danger'));
					return $this->redirect($this->Auth->logout());
				}
				else if($this->Auth->user('active') == 2) {
					$this->Session->setFlash(__('User is blocked!', 'danger'));
					return $this->redirect($this->Auth->logout());
				}
				$this->User->updateAll(array("last_login" => "'" . gmdate('Y-m-d H:i:s') . "'"), array("email" => $this->request->data['User']['email']));
				return $this->redirect($this->Auth->redirect());
			}
			$this->Session->setFlash(__('Invalid username or password, try again'),'warning');
		}
		$this->layout = 'login';
	}

	public function logout() {
		return $this->redirect($this->Auth->logout());
	}
	public function profiles(){
		$user_id = $this->Session->read('Auth.User.id');
		if($user_id){
			$user = $this->User->find('first', array(
				'conditions' => array('User.id' => $user_id),
				'permissionable' => false
			));
			
			if ($this->request->is('post') || $this->request->is('put')){
				$this->request->data['User']['id'] = $user_id;
				if ($this->User->save($this->request->data)){
					$this->Session->setFlash(__('Your profiles has been saved'));
					return $this->redirect(array('action' => 'profiles'));
				}
				$this->Session->setFlash(
					__('The user could not be saved. Please, try again.'),'warning'
				);
			} else {
				$this->request->data = $user;
				unset($this->request->data['User']['password']);
			}
			$this->render('edit');
		}else{
			return $this->redirect($this->Auth->logout());
		}
	}
	public function forgot(){
		
		if ($this->request->is('post') || $this->request->is('put')){
			$this->Captcha = $this->Components->load('Captcha');
			$this->User->setCaptcha($this->Captcha->getVerCode());
			$this->User->set($this->request->data);
			if($this->User->validates()){
				$email = $this->request->data['User']['your_email'];
				$user = $this->User->find('first', array('permissionable' => false, 'conditions' => array('User.email' => $email)));
				if (empty($user)){
					$this->Session->setFlash(
						__('Could not find your email in system. Please fill an other email or create new account.'),
						'warning'
					);
					return $this->redirect(array('action' => 'forgot'));
				}else{
					
					$random_pass = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') , 0 , 10 );
					$passwordHasher = new SimplePasswordHasher();
					$newpass = $passwordHasher->hash($random_pass);
					
					$this->User->updateAll(array('User.password' => "'$newpass'"), array('User.email' => $email));
					
					$content = "New password: $random_pass";
					$email_options = array(
						'to' => array($email),
						'subject' => __('Password change'),
						'viewVars' => array('content' => $content)
					);
					$this->_sendEmail($email_options);
					$this->Session->setFlash(__('Password changed. Please check your email to get new password.'));
					return $this->redirect(array('action' => 'profiles'));
					
				}
			}else{
				$this->Common->flashErrorDisplay($this->User->invalidFields());
			}
		}
		$this->layout = 'login';
	}
	public function changepass(){
		$user = $this->Session->read('Auth.User');
		if(empty($user)){
			return $this->redirect($this->Auth->logout());
		}
		$this->User->id = $user['id'];
		$user = $this->User->read(null, $user['id']);
		
		if ($this->request->is('post') || $this->request->is('put')){
			$this->Captcha = $this->Components->load('Captcha');
			$this->User->setCaptcha($this->Captcha->getVerCode());
			$data = $this->request->data['User'];
			
			$this->User->set($this->request->data);
			if($this->User->validates()){
				$passwordHasher = new SimplePasswordHasher();
				$curpass = $passwordHasher->hash($data['current_password']);
				
				if($curpass != $user['User']['password']){
					$this->Session->setFlash(
						__('Current password not correct! Please try again.'),
						'warning'
					);
					return $this->redirect(array('action' => 'changepass'));
				}elseif($data['new_password'] != $data['confirm_password']){
					$this->Session->setFlash(
						__('Confirm password not equalation! Please try again.'),
						'warning'
					);
					return $this->redirect(array('action' => 'changepass'));
				}elseif(strlen($data['new_password']) < 6){
					$this->Session->setFlash(
						__('Min lenght of pass word is 6 char! Please try again.'),
						'warning'
					);
					return $this->redirect(array('action' => 'changepass'));
				}else{
					$newpass = $data['new_password'];
					$this->User->saveField('password',"$newpass");
					$this->Session->setFlash(__('Change pass successfull!'));
					return $this->redirect(array('action' => 'profiles'));
				}
			}else{
				$this->Common->flashErrorDisplay($this->User->invalidFields());
			}
		}
	}
	/**
	 * @Description : sales staff reset password
	 *
	 * @return 	: html
	 * @Author 	: tungpa - tungbk29@gmail.com
	 */
	public function resetPassword($id)
	{
		$this->autoRender=false;
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid User'));
		}
		$data = $this->User->read(null, $id);
		$password = getPassword();
		$data['User']['password'] = $password;
		if ($this->User->save($data)){
			$mail_content = "Account informations: \n\n" .
							"New password: " . $password . "\n";
			$arr_options = array(
				'to' => array($data['User']['email']),
				'viewVars' => array('content' => $mail_content),
				'subject' => __('Your password had been reset')
			);
			$this->_sendEmail($arr_options);
			
			$this->Session->setFlash(__('The user has been reset password'));
			return $this->redirect(array('action' => 'index'));
		}
	}
	
	/**
	 * @Description : sales staff reset signature
	 *
	 * @return 	: html
	 * @Author 	: tungpa - tungbk29@gmail.com
	 */
	public function resetSignature($id)
	{
		$this->autoRender=false;
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid User'));
		}
		$data = $this->User->read(null, $id);
		$signature = substr(MD5($data['User']['email'] . time()), 0, 7);
		$data['User']['signature'] = $signature;
		if ($this->User->save($data)){
			//save Signature archive
			$lastSignature = $this->SignatureArchive->find('first', array(
				'conditions' => array(
					'user_id' => $id
				),
				'order' => 'date_till DESC'
			));
			
			$arrSignature = array();
			$arrSignature['SignatureArchive']['user_id'] = $id;
			$arrSignature['SignatureArchive']['signature'] = $signature;
			$arrSignature['SignatureArchive']['user_modify'] = $this->Session->read('Auth.User.id');
			$arrSignature['SignatureArchive']['date_from'] = (!empty($lastSignature)) ? $lastSignature['SignatureArchive']['date_from'] : gmdate('Y-m-d h:i:s');
			$arrSignature['SignatureArchive']['date_till'] = gmdate('Y-m-d h:i:s');
			$this->SignatureArchive->save($arrSignature);
			$mail_content = "Account informations: \n\n" .
							"Name: " . $data['User']['name'] . "\n" .
							"Email login: " . $data['User']['email'] . "\n" .
							"New signature: " . $signature . "\n";
			$arr_options = array(
				'to' => array($data['User']['email']),
				'viewVars' => array('content' => $mail_content),
				'subject' => __('Your signature had been reset')
			);
			$this->_sendEmail($arr_options);
			
			$this->Session->setFlash(__('The user has been reset signature'));
			return $this->redirect(array('action' => 'index'));
		}
	}
	//block user
	public function block($id)
	{
		if ($this->request->is('post') || $this->request->is('put')){
			$data = $this->User->read(null, $id);
			$data['User']['active'] = 2;
			$this->request->data['BlockedUser']['user_id'] = $id;
			$this->request->data['BlockedUser']['block_date'] = gmdate('Y-m-d h:i:s');
			$this->request->data['BlockedUser']['block_by'] = $this->Session->read('Auth.User.id');
			if($this->User->save($data)){
				$this->BlockedUser->save($this->request->data);
				$mail_content = 'The account is blocked by '. $this->Session->read('Auth.User.name') . ' with reason: ' .$this->request->data['BlockedUser']['block_reason'];
				$arr_options = array(
					'to' => array($data['User']['email']),
					'viewVars' => array('content' => $mail_content),
					'subject' => __('This account has been blocked')
				);
				$this->_sendEmail($arr_options);
				$this->Session->setFlash(__('The user is blocked'));
				return $this->redirect(array('action' => 'index'));
			}
		}
	}
	
	function _sendEmail($arr = array()){
		$sender = Configure::read('Settings.Accounting.accounting_email');
		$email_options = array(
			'sender' => array($sender => PAGE_NAME),
			'from' => array($sender => PAGE_NAME),
			'to' => array($sender),
			'subject' => __('New account has been created on '. PAGE_NAME),
			'viewVars' => array('content' => ''),
			'template' => 'default',
			'layout' => 'default'
		);
		
		$options = array_merge($email_options, $arr);
		$email = new CakeEmail($options);
		$email->emailFormat('html');
		$email->send();
	}
	
	public function dashboard(){
	
	}
	public function userNoActive()
	{
		$this->autoRender=false;
		$users = $this->User->find('all', array(
			'fields' => 'User.*, FLOOR((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(User.last_login))/86400) as User_TotalDays',
			'conditions' => array(
				'User.active >' => '0',
				
			),
			'permissionable' => false,
			'group' => 'User.id HAVING User_TotalDays >= 90'
		));
		// print_r($users);
		if(!empty($users)) {
			foreach($users as $k => $user) {
				$content = 'User has not logged in for more than 90 days.';
				$arr_options = array(
					'to' => $user['User']['email'],
					'subject' => __('User has not logged in for more than 90 days'),
					'viewVars' => array('content' => $content)
				);
				if($this->User->sendEmail($arr_options)) {
					$user['User']['active'] = 0;
					$this->User->save($user);
				}
			}
		}
	}
}
