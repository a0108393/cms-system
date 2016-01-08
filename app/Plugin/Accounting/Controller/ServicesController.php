<?php

App::uses('AppController', 'Controller');

class ServicesController extends AppController{

	public $uses = array('Accounting.Service', 'Accounting.RatecardService', 'Accounting.Ratecard', 'Company.Company', 'Company.User', 'Accounting.Category', 'History');
	
	public $paginate = array(
		'joins' => array(
			array(
				'table' => 'accounting_categories',
				'alias' => 'Category',
				'type' => 'LEFT',
				'conditions' => array('Service.category_id = Category.id')
			)
		),
		'conditions' => array(
			'Service.history_status' => 1
		),
        'order' => array(
            'Service.name' => 'asc'
        ),
		'limit' => ROWPERPAGE
    );
	
	function beforeRender()
    {
        parent::beforeRender();
		$categories = $this->Category->find('list', array('fields' => 'id, name', 'conditions' => array('Category.history_status' => 1), 'order' => 'name ASC'));
		$this->set(compact('categories'));
    }
	
    public function index(){
        $this->Service->recursive = 0;
		$this->Service->virtualFields = array(
			'category_name' => 'Category.name'
		);
		$data = $this->paginate('Service');
        $this->set('data', $data);
    }

    public function add(){
        if ($this->request->is('post')){
            $this->Service->create();
            if ($this->Service->save($this->request->data)){ 
				$id = $this->Service->id;
				$ratecards = $this->Ratecard->find('all');
				$newdata = array();
				foreach($ratecards as $key=>$item){
					$newdata[$key]['service_id'] = $id;
					$newdata[$key]['ratecard_id'] = $item['Ratecard']['id'];
					if($item['Ratecard']['type'] == 1){
						$newdata[$key]['amount'] = (int)$this->request->data['Service']['price'] * (100 - (int)$item['Ratecard']['discount']) / 100;
					}else{
						$newdata[$key]['amount'] = $this->request->data['Service']['price'];
					}
				}
				if(!empty($newdata)){
					$this->RatecardService->saveMany($newdata);
				}
				
                $this->Session->setFlash(__('The product/service has been saved'));
                return $this->redirect(array('action' => 'index'));
            } 
        }
		$this->render('edit');
    }

    public function edit($id = null) {

        $this->Service->id = $id;
        if (!$this->Service->exists()) {
            throw new NotFoundException(__('Invalid product/service'));
        }
		
		$ratecards = $this->Ratecard->find('all');
		$data = $this->Service->read();
        if ($this->request->is('post') || $this->request->is('put')) {
        	$this->request->data['Service']['price'] =  (float)$this->request->data['Service']['price'];
        	$data['Service']['price'] =  (float)$data['Service']['price'];
        	$diff = array_diff($this->request->data['Service'], $data['Service']);
        	if(!empty($diff))
        		$this->request->data['Service']['history_link'] = 0;
            if ($this->Service->save($this->request->data)) {

            	// check edit price and save history
				if(!empty($diff)) {
					$status = '';
					if(in_array('name', array_keys($diff)))
						$status .= (empty($status)) ? __('Change name') : ', ' . __('Change name');
					if(in_array('description', array_keys($diff)))
						$status .= (empty($status)) ? __('Change description') : ', ' . __('Change description');
					if(in_array('unit', array_keys($diff)))
						$status .= (empty($status)) ? __('Change unit') : ', ' . __('Change unit');
					if(in_array('price', array_keys($diff)))
						$status .= (empty($status)) ? __('Change price') : ', ' . __('Change price');
					if(in_array('deliverables', array_keys($diff)))
						$status .= (empty($status)) ? __('Change deliverables') : ', ' . __('Change deliverables');
					if(in_array('locked', array_keys($diff)))
						$status .= (empty($status)) ? __('Change locked') : ', ' . __('Change locked');
					if(in_array('category_id', array_keys($diff)))
						$status .= (empty($status)) ? __('Change category') : ', ' . __('Change category');
					unset($data['Service']['id']);
					$data['Service']['history_status'] = 2;
					$data['Service']['history_link'] = $id;
					$data['Service']['name'] = $data['Service']['name'] . '-bk' . substr(md5(microtime()),rand(0,26),5);
					$this->Service->create();

					if($this->Service->save($data)) {
						
						$history['History']['plugin'] = 'accounting';
						$history['History']['controller'] = 'Services';
						$history['History']['action'] = 'view';
						$history['History']['history_id'] = $this->Service->id;
						$history['History']['action_status'] = $status;
						$history['History']['original_id'] = $id;
						$history['History']['user_modified'] = $this->Session->read('Auth.User.id');
						$history['History']['date_modified'] = gmdate('Y-m-d H:i:s');
						$this->History->save($history);
					}
					else {
						prd( $this->Service->validationErrors);
					}
				}
				//delete all service_id
				$this->RatecardService->deleteAll(array('RatecardService.service_id' => $id));
				
				//save new rows
				$newdata = array();
				foreach($ratecards as $key=>$item){
						$newdata[$key]['ratecard_id'] = $item['Ratecard']['id'];
						$newdata[$key]['service_id'] = $id;
					if($item['Ratecard']['type'] == 1){
						if(!$this->request->data['Service']['locked']){
							$newdata[$key]['amount'] = (int)$this->request->data['Service']['price'] * (100 - (int)$item['Ratecard']['discount']) / 100;
						}else{
							$newdata[$key]['amount'] = (int)$this->request->data['Service']['price'];
						}
					}else{
						$newdata[$key]['amount'] = (int)$this->request->data['Service']['price'];
					}
				}
				if(!empty($newdata)){
					$this->RatecardService->saveMany($newdata);
				}
				
                $this->Session->setFlash(__('The product/service has been saved'));
                return $this->redirect(array('action' => 'notify', $id));
            }
            $this->Session->setFlash(
                __('The product/service could not be saved. Please, try again.')
            );
        } else {
            $this->request->data = $data;
        }
    }

    public function view($value='')
    {
    	$this->request->data = $this->Service->read(null, $value);

    }

    public function delete($id = null){
        $this->Service->id = $id;
        if (!$this->Service->exists()) {
            throw new NotFoundException(__('Invalid product/service'));
        }
        $data = $this->Service->read(null, $id);
        if ($this->Service->delete()){
			$this->RatecardService->deleteAll(array('RatecardService.service_id' => $id));
			unset($data['Service']['id']);
			$data['Service']['history_status'] = 2;
			$data['Service']['name'] = $data['Service']['name'] . ' (bk' . substr(md5(microtime()),rand(0,26),5) . ')';
			//print_r($data);die('aa');
			$this->Service->create();

			if($this->Service->save($data)) {
				
				$history['History']['plugin'] = 'accounting';
				$history['History']['controller'] = 'Services';
				$history['History']['action'] = 'view';
				$history['History']['history_id'] = $this->Service->id;
				$history['History']['action_status'] = __('Delete');
				$history['History']['user_modified'] = $this->Session->read('Auth.User.id');
				$history['History']['date_modified'] = gmdate('Y-m-d H:i:s');
				$this->History->save($history);
			}
			else {
				prd( $this->Service->validationErrors);
			}
            $this->Session->setFlash(__('Product/Service deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Product/Service was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
	public function notify($id) 
	{
		if($this->request->is('post') || $this->request->is('put')) {
			if(isset($this->request->data['Ratecard']['client'])) {
				// $clients = implode(',' , array_keys($this->request->data['Ratecard']['client']));
				$clients_email = $this->User->find('list', array(
					'fields' => 'User.id, User.email',
					'conditions' => array( 'User.id' => array_keys($this->request->data['Ratecard']['client']))
				));
				$arr_options = array(
					'to' => $clients_email,
					'viewVars' => array('content' => $this->request->data['Ratecard']['content_notify'])
				);
				$this->_sendemail($arr_options);
			}
			
			return $this->redirect(array('action' => 'index'));
			// print_r($this->request->data);die;
		}

		$clients = $this->User->find('list', array(
			'fields' => 'User.id, User.name',
			'joins' => array(
				array(
					'table' => 'company_companies',
					'alias' => 'Company',
					'type' => 'INNER',
					'conditions' => array('Company.id = User.company_id')
				),
				array(
					'table' => 'accounting_ratecards',
					'alias' => 'Ratecard',
					'type' => 'INNER',
					'conditions' => array('Ratecard.id = Company.ratecard_id')
				),
				array(
					'table' => 'accounting_ratecard_services',
					'alias' => 'Ratecard_Service',
					'type' => 'INNER',
					'conditions' => array('Ratecard.id = Ratecard_Service.ratecard_id')
				),
				array(
					'table' => 'users_groups',
					'alias' => 'UsersGroup',
					'type' => 'LEFT',
					'conditions' => array('UsersGroup.user_id = User.id')
				)
			),
			'conditions' => array(
				'Ratecard_Service.service_id' => $id,
				'User.active' => 1,
				'UsersGroup.group_id' => (int) Configure::read('Settings.Company.DefaultGroupId')
			),
			'group' => 'User.id',
			'order' => array(
				'User.name' => 'asc'
			)
		));
		if(empty($clients)) {
			return $this->redirect(array('action' => 'index'));
		}
		else {
			$this->set('clients', $clients);
			
		}
	}

	public function history($id)
	{
		$this->paginate = array(
			'joins' => array(
				array(
					'table' => 'accounting_categories',
					'alias' => 'Category',
					'type' => 'LEFT',
					'conditions' => array('Service.category_id = Category.id')
				)
			),
			'conditions' => array(
				'Service.history_link' => $id
			),
	        'order' => array(
	            'Service.name' => 'asc'
	        )
	    );
	    $this->Service->recursive = 0;
		$this->Service->virtualFields = array(
			'category_name' => 'Category.name'
		);
		$data = $this->paginate('Service');
		// prd($arrData);
        $this->set('data', $data);
	}

	function _sendemail($arr = array()){
		$sender = Configure::read('Settings.Accounting.accounting_email');
		$email_options = array(
			'sender' => array($sender => PAGE_NAME),
			'from' => array($sender => PAGE_NAME),
			'to' => array($sender),
			'subject' => __('Service change information'),
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