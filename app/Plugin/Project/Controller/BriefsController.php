<?php

App::uses('AppController', 'Controller');

class BriefsController extends AppController{
	public $uses = array('User', 'Project.Brief', 'Company.Company');

	public $paginate = array(
		'joins' => array(
			array(
				'table' => 'company_companies',
				'alias' => 'Company',
				'type' => 'LEFT',
				'conditions' => array('Brief.company_id = Company.id')
			),
			array(
				'table' => 'users',
				'alias' => 'User',
				'type' => 'LEFT',
				'conditions' => array('Brief.user_created = User.id')
			)
		),
		'conditions' => array(
			'Brief.history_status' => 1
		),
		'order' => array(
            'Brief.id' => 'desc'
        ),
		'limit' => ROWPERPAGE

	);

	public function beforeFilter()
	{
		parent::beforeFilter();
		$staffs = $this->User->find('list', array(
			'fields' => 'User.id, User.name',
			'joins' => array(
				array(
					'table' => 'users_groups',
					'alias' => 'UsersGroup',
					'type' => 'LEFT',
					'conditions' => array('User.id = UsersGroup.user_id')
				)
			),
			'conditions' => array(
				'UsersGroup.group_id' => Configure::read('Settings.Company.SalesStaffGroupId')
			),
			'permissionable' => false
		));
		$companies = $this->Company->find('list', array(
			'fields' => 'id, name',
			'permissionable' => false,
			'conditions' => array('history_status' => 1),
			'order' => 'name asc'
		));
		$arrGroup = $this->Session->read('Auth.Group');
        $group = array();
        if(!empty($arrGroup)) {
        	foreach ($arrGroup as $key => $value) {
        		$group[] = $value['id'];
        	}
        }
		$this->set(compact('staffs', 'companies', 'group'));
	}
	//index
	public function index()
	{
		$this->Brief->recursive = 0;
		$this->Brief->virtualFields = array(
			'company_name' => 'Company.name',
			'user_name' => 'User.name'
		);
		$data = $this->paginate('Brief');
        $this->set('data', $data);
	}

	//add new
	public function add()
	{
		
		if($this->request->is(array('post', 'put'))) {
			$arrGroup = $this->Session->read('Auth.Group');
	        $group = array();
	        if(!empty($arrGroup)) {
	        	foreach ($arrGroup as $key => $value) {
	        		$group[] = $value['id'];
	        	}
	        }
			if(in_array(Configure::read('Settings.Company.DefaultGroupId'), $group) && !in_array(Configure::read('Settings.Company.SalesStaffGroupId'), $group)) {
	        	$this->request->data['Brief']['company_id'] = $this->Session->read('Auth.User.company_id');
	        	//if send and save then status is approved
	    		if(isset($this->request->data['Brief']['saveAndSend']))
	    			$this->request->data['Brief']['status'] = 2;
	        }
	        if(isset($this->request->data['Brief']['date']))
				$this->request->data['Brief']['date'] = sqlFormatDate($this->request->data['Brief']['date']);
			if(!empty($this->request->data['Brief']['attached_files'])) {
				$this->request->data['Brief']['attached_files'] = json_encode($this->request->data['Brief']['attached_files']);
			}

			$this->request->data['Brief']['created'] = gmdate('Y-m-d h:i:s');
			$this->request->data['Brief']['user_created'] = $this->Session->read('Auth.User.id');
			$this->request->data['Brief']['random_key'] = substr(md5(microtime()),rand(0,26),9);
			$approval_key = substr(md5(microtime()),rand(0,26),9);
			if(isset($this->request->data['Brief']['saveAndSend']))
				$this->request->data['Brief']['approval_key'] = $approval_key;
			$this->Brief->create();
			if ($this->Brief->save($this->request->data)) {
				$id = $this->Brief->id;
				$this->Session->setFlash(__('The brief has been saved'));
				//send notification to all staff 
				if(isset($this->request->data['Brief']['saveAndSend'])) {
					$staffs = $this->User->find('list', array(
						'fields' => 'User.id, User.email',
						'joins' => array(
							array(
								'table' => 'users_groups',
								'alias' => 'UsersGroup',
								'type' => 'LEFT',
								'conditions' => array('User.id = UsersGroup.user_id')
							)
						),
						'conditions' => array(
							'UsersGroup.group_id' => Configure::read('Settings.Company.SalesStaffGroupId'),
							
						),
						'permissionable' => false
					));
					$content = '<a href="'. Router::url(array('plugin' => false, 'controller' => 'briefs', 'action' => 'view', $id), true) .'">'. $this->request->data['Brief']['project_title'] .'</a>';
					$content .= '<p>Approval key: ' . $approval_key;
					$arr_options = array(
						'to' => $staffs,
						'subject' => __('Client create brief'),
						'viewVars' => array('content' => $content)
					);
					$this->_sendemail($arr_options);
				}
				return $this->redirect(array('action' => 'view', $id));
			}
		}
		$this->render('edit');
	}

	public function edit($id)
	{
		$this->Brief->id = $id;
		$data = $this->Brief->read();
        if (!$this->Brief->exists()) {
            throw new NotFoundException(__('Invalid brief'));
        }
		if($this->request->is(array('post', 'put'))) {
			$arrGroup = $this->Session->read('Auth.Group');
	        $group = array();
	        if(!empty($arrGroup)) {
	        	foreach ($arrGroup as $key => $value) {
	        		$group[] = $value['id'];
	        	}
	        }
	        //if onlysave then remove validate
	        if($this->Session->read('Auth.User.id') == $data['Brief']['user_created']) {
	        	$this->Brief->validate['job_number'] = array();
	        	$this->Brief->validate['date'] = array();
	        	//prd($this->Brief->validate);
	        }
	        if(isset($this->request->data['Brief']['saveAndSend'])) {
				$version = $data['Brief']['version'];
				$version++;
				$this->request->data['Brief']['version'] = $version;
				if($this->Session->read('Auth.User.id') == $data['Brief']['user_created'])
					$this->request->data['Brief']['status'] = 2;
				else 
					$this->request->data['Brief']['status'] = 4;
			}
			if(empty($this->request->data['Brief']['user_modified']) && $this->Session->read('Auth.User.id') != $data['Brief']['user_created'])
				$this->request->data['Brief']['user_modified'] = $this->Session->read('Auth.User.id');
			if(isset($this->request->data['Brief']['date']))
				$this->request->data['Brief']['date'] = sqlFormatDate($this->request->data['Brief']['date']);
			if(!empty($this->request->data['Brief']['attached_files'])) {
				$this->request->data['Brief']['attached_files'] = json_encode($this->request->data['Brief']['attached_files']);
			}
			$approval_key = substr(md5(microtime()),rand(0,26),9);
			if(isset($this->request->data['Brief']['saveAndSend']))
				$this->request->data['Brief']['approval_key'] = $approval_key;
			// prd($this->request->data['Brief']);
			if($this->Brief->save($this->request->data)) {
				if(isset($this->request->data['Brief']['saveAndSend'])) {
					$this->Brief->create();
					unset($data['Brief']['id']);
					$data['Brief']['project_title'] = $data['Brief']['project_title'] . '-bk' . substr(md5(microtime()),rand(0,26),5);
					$data['Brief']['history_status'] = 2;
					$this->Brief->save($data);
					if(isset($this->request->data['Brief']['saveAndSend'])) {
						if(empty($data['Brief']['user_modified'])) {
							$staffs = $this->User->find('list', array(
								'fields' => 'User.id, User.email',
								'joins' => array(
									array(
										'table' => 'users_groups',
										'alias' => 'UsersGroup',
										'type' => 'LEFT',
										'conditions' => array('User.id = UsersGroup.user_id')
									)
								),
								'conditions' => array(
									'UsersGroup.group_id' => Configure::read('Settings.Company.SalesStaffGroupId'),
									
								),
								'permissionable' => false
							));
						}
						else 
							if($this->request->data['Brief']['status'] == 4) {
								$staffs = $this->User->find('list', array(
									'fields' => 'User.id, User.email',
									'conditions' => array('User.id' => $data['Brief']['user_created']),
									'permissionable' => false
								));
							}
							else {
								$staffs = $this->User->find('list', array(
									'fields' => 'User.id, User.email',
									'conditions' => array('User.id' => $data['Brief']['user_modified']),
									'permissionable' => false
								));
							}
						$content = '<a href="'. Router::url(array('plugin' => false, 'controller' => 'briefs', 'action' => 'view', $id), true) .'">'. $this->request->data['Brief']['project_title'] .'</a>';
						$content .= '<p>Approval key: ' . $approval_key;
						$arr_options = array(
							'to' => $staffs,
							'subject' => __('Client create brief'),
							'viewVars' => array('content' => $content)
						);
						$this->_sendemail($arr_options);
					}
				}
				//$id = $this->Brief->id;
				$this->Session->setFlash(__('The brief has been saved'));
				
				return $this->redirect(array('action' => 'index'));
			}
			else {
				prd($this->Brief->validationErrors);
			}
		}
		else {
			$this->request->data = $data;
			/*if($this->Session->read('Auth.User.id') != $this->request->data['Brief']['user_created']) {
				$this->Session->setFlash(__('Only user created edit'));
				$this->redirect(array('action' => 'index'));
			}*/
            if(!empty($this->request->data['Brief']['date']))
            	$this->request->data['Brief']['date'] = formatDate($this->request->data['Brief']['date']);
			$this->set('attached_files',json_decode($this->request->data['Brief']['attached_files'], true));
		}
	}
	public function view($id)
	{
		$data = $this->Brief->read(null, $id);
		$created_by = $this->User->read(null, $data['Brief']['user_created']);
		$this->set('created_by', $created_by);
		if(!empty($data['Brief']['attached_files'])) {
			$attached_files = json_decode($data['Brief']['attached_files'], true);
			$this->set('attached_files', $attached_files);
		}
		$allVersions = $this->Brief->find('all', array(
			'conditions' => array(
				'random_key' => $data['Brief']['random_key']
			),
			'order' => 'version desc'
		));
		if($this->request->is(array('post', 'put'))) {
			if(trim($this->request->data['Brief']['text_approved']) != $data['Brief']['approval_key']) {
				$this->Session->setFlash(__('Approval key incorrect'), 'danger');
				$this->redirect(array('action' => 'view', $id));
			}
			if($this->Session->read('Auth.User.id') == $data['Brief']['user_created']) {
				$data['Brief']['status'] = 1;
			}
			else {
				$data['Brief']['status'] = 3;
			}
			if($this->Brief->save($data)) {
				$this->Session->setFlash(__('Approved brief'));
				$this->redirect(array('action' => 'edit', $id));
			}
			else 
				prd($this->Brief->validationErrors);
		}
		$this->set('allVersions', $allVersions);
		$this->set('data', $data);
	}

	public function delete($id)
	{
		
	}
	/**
	 * @Description : view pdf
	 *
	 * @return 	: pdf file
	 * @Author 	: tungpa - tungbk29@gmail.com
	 */
	public function viewPdf($id)
	{
		if (!$id) 
        { 
            $this->Session->setFlash('Sorry, there was no property ID submitted.'); 
            $this->redirect(array('action'=>'index'), null, true); 
        }
		$this->Brief->id = $id;
        if (!$this->Brief->exists()){
            throw new NotFoundException(__('Invalid quotation'));
        }
        $data = $this->Brief->read(null, $id);
        $created_by = $this->User->read(null, $data['Brief']['user_created']);
        $this->set('created_by', $created_by);
        $this->set('data', $data);

      	$this->layout = 'pdf/brief'; //this will use the pdf.ctp layout 
        $this->render(); 
	}
	/**
	 * @Description : public
	 *
	 * @return 	: form add
	 * @Author 	: tungpa - tungbk29@gmail.com
	 */
	public function visitor()
	{
		if($this->request->is(array('post', 'put'))) {
			$this->Captcha = $this->Components->load('Captcha');
			$this->Brief->setCaptcha($this->Captcha->getVerCode());
			$this->Brief->create();
			$this->request->data['Brief']['date'] = sqlFormatDate($this->request->data['Brief']['date']);
			if(!empty($this->request->data['Brief']['attached_files'])) {
				$this->request->data['Brief']['attached_files'] = json_encode($this->request->data['Brief']['attached_files']);
			}
			$this->request->data['Brief']['created'] = gmdate('Y-m-d h:i:s');
			$this->request->data['Brief']['user_created'] = 0;
			$this->request->data['Brief']['random_key'] = substr(md5(microtime()),rand(0,26),9);
            if ($this->Brief->save($this->request->data)){
            	//send notification to all staff
				$staffs = $this->User->find('list', array(
					'fields' => 'User.id, User.email',
					'joins' => array(
						array(
							'table' => 'users_groups',
							'alias' => 'UsersGroup',
							'type' => 'LEFT',
							'conditions' => array('User.id = UsersGroup.user_id')
						)
					),
					'conditions' => array(
						'UsersGroup.group_id' => Configure::read('Settings.Company.SalesStaffGroupId'),
						
					),
					'permissionable' => false
				));
				$content = '<a href="'. Router::url(array('plugin' => false, 'controller' => 'briefs', 'action' => 'view', $id), true) .'">'. $this->request->data['Brief']['project_title'] .'</a>';
				$arr_options = array(
					'to' => $staffs,
					'subject' => __('Client create brief'),
					'viewVars' => array('content' => $content)
				);
				$this->_sendemail($arr_options);
                $this->Session->setFlash(__('Thank you!'));
                return $this->redirect(array('action' => 'visitor'));
            }
		}
	}
	//send email
	private function _sendemail($arr = array()){
		$sender = Configure::read('Settings.Accounting.accounting_email');
		$email_options = array(
			'sender' => array($sender => PAGE_NAME),
			'from' => array($sender => PAGE_NAME),
			'to' => array($sender),
			'subject' => __('Ratecard change information'),
			'viewVars' => array('content' => ''),
			'template' => 'default',
			'layout' => 'default'
		);
		
		$options = array_merge($email_options, $arr);
		$email = new CakeEmail($options);
		$email->emailFormat('html');
		return $email->send();
	}
}