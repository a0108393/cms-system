<?php

App::uses('AppController', 'Controller');

class UsersController extends AppController{
	
	public $uses = array('Company.User', 'Group', 'Company.Company','SignatureArchive');
	
	var $paginate = array(
		'joins' => array(
				array(
					'table' => 'users_groups', 
					'alias' => 'UsersGroup', 
					'type' => 'LEFT', 
					'conditions' => array('User.id = UsersGroup.user_id')
				),
				array(
					'table' => 'company_companies', 
					'alias' => 'Company', 
					'type' => 'LEFT', 
					'conditions' => array('Company.id = User.company_id')
				)
			),
		'group' => 'User.id',
        'order' => array(
            'User.name' => 'asc'
        ),
		'limit' => ROWPERPAGE
    );
	function beforeRender()
    {
        parent::beforeRender();
		//gender company select list
		$companies = $this->Company->find('list', array('fields' => 'id, name', 'permissionable' => false, 'conditions' => array('Company.history_status' => 1)));
		$stateOptions = $this->User->stateOptions();
		$this->set(compact('stateOptions','companies'));
	
	}
    public function index(){
        $this->User->recursive = 1;
		$this->User->virtualFields = array(
			'company' => 'Company.name'
		);
		$data = $this->paginate('User', array('UsersGroup.group_id' => (int) Configure::read('Settings.Company.DefaultGroupId'))); 
        $this->set('data', $data);
		
		
    }

    public function view($id = null){
        $this->User->id = $id;
        if (!$this->User->exists()){
            throw new NotFoundException(__('Invalid user'));
        }
        $this->request->data = $this->User->read();
        unset($this->request->data['User']['password']);
    }

    public function add(){
		
        if ($this->request->is('post')){
            $this->User->create();
			$this->request->data['Group']['Group'][] = (int) Configure::read('Settings.Company.DefaultGroupId');
			$this->request->data['User']['date_joined'] = gmdate('Y-m-d H:i:s');
			$password = getPassword();
			$this->request->data['User']['password'] = $password;
			
			$signature = substr(MD5($this->request->data['User']['email'].$this->request->data['User']['date_joined']), 0, 7);
			$this->request->data['User']['signature'] = $signature;
            if ($this->User->save($this->request->data)){
				//save Signature archive
				$arrSignature = array();
				$arrSignature['SignatureArchive']['user_id'] = $this->User->id;
				$arrSignature['SignatureArchive']['signature'] = $signature;
				$arrSignature['SignatureArchive']['user_modify'] = $this->Session->read('Auth.User.id');
				$arrSignature['SignatureArchive']['date_from'] = gmdate('Y-m-d H:i:s');
				$arrSignature['SignatureArchive']['date_till'] = gmdate('Y-m-d H:i:s');
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
        }else{
			$companyid = $this->Session->read('company_id');
			if($companyid){
				$this->request->data['User']['company_id'] = $companyid;
			}
		}
		$this->render('edit');
    }

    public function edit($id = null){		
		$this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $data = $this->User->read();
        if ($this->request->is('post') || $this->request->is('put')) {
			$diff = array_diff($this->request->data['User'], $data['User']);
			
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(
                __('The user could not be saved. Please, try again.')
            );
        } else {
            $this->request->data = $data;
			 unset($this->request->data['User']['password']);
        }
    }

    public function delete($id = null){
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->User->delete()){
            $this->Session->setFlash(__('User deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('User was not deleted'));
        return $this->redirect(array('action' => 'index'));
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
		$email->send();
	}
}