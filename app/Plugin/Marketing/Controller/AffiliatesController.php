<?php
class AffiliatesController extends AppController {
	
	var $name = 'Affiliates';
	public $uses = array('Marketing.AdvertisingLink', 'Marketing.Channel', 'Marketing.Affiliate', 'User', 'SignatureArchive');
	public $paginate = array(
        'joins' => array(
            array(
                'table' => 'users_groups',
                'alias' => 'UsersGroup',
                'type' => 'LEFT',
                'conditions' => array('UsersGroup.user_id = User.id')
            )
        ),
        
        'order' => array(
            'User.name' => 'asc'
        ),
		'limit' => ROWPERPAGE
    );
	function beforeRender()
    {
        parent::beforeRender();
		$stateOptions = $this->Affiliate->stateOptions();
		$this->set(compact('stateOptions'));
	
	}
	public function index() {
        $affiliate = Configure::read('Settings.Company.AffiliateGroupId');
        $affiliate = (!empty($affiliate)) ? $affiliate : 0;
		$this->User->recursive = 0;
        $this->set('data', $this->paginate('User', array('UsersGroup.group_id' => $affiliate)));
	}
	public function add()
	{
		if ($this->request->is('post')){
            $this->User->create();
			$this->request->data['Group']['Group'][] = Configure::read('Settings.Company.AffiliateGroupId');
			$this->request->data['User']['date_joined'] = gmdate('Y-m-d H:i:s');
			$password = getPassword();
            $signature = substr(MD5($this->request->data['User']['email'].$this->request->data['User']['date_joined']), 0, 7);
			$this->request->data['User']['password'] = $password;
            if ($this->User->save($this->request->data)){
                $id = $this->User->id;
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
                $this->Session->setFlash(__('The affiliate has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
        }
		$this->render('edit');
	}
	public function edit($id = null){		
		$this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid Affiliate'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
			
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The affiliate has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(
                __('The affiliate could not be saved. Please, try again.')
            );
        } else {
            $this->request->data = $this->User->read(null, $id);
			 unset($this->request->data['User']['password']);
        }
    }
	public function delete($id = null){
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid affiliate'));
        }
        if ($this->User->delete()){
            $this->Session->setFlash(__('Affiliate deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Affiliate was not deleted'));
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
        $email->emailFormat('html');
        $email->send();
    }
}