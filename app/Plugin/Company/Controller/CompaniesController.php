<?php

App::uses('AppController', 'Controller');
App::uses('File', 'Utility');

class CompaniesController extends AppController{
	
	public $uses = array('Company.Company', 'Company.Industry', 'Accounting.Ratecard', 'Currency', 'Country', 'History');
	
	var $paginate = array(
		'joins' => array(
			array('table' => 'company_industries', 'alias' => 'Industry', 'type' => 'LEFT', 'conditions' => array('Industry.id = Company.industry_id')),
			array('table' => 'accounting_ratecards', 'alias' => 'Ratecard', 'type' => 'LEFT', 'conditions' => array('Ratecard.id = Company.ratecard_id')),
			array('table' => 'countries', 'alias' => 'Country', 'type' => 'LEFT', 'conditions' => array('Country.id = Company.country_id'))
		),
		'conditions' => array(
			'Company.history_status' => 1
		),
		'group' => 'Company.id',
        'order' => array(
            'Company.name' => 'asc'
        ),
		'limit' => ROWPERPAGE
    );
	
	function beforeRender()
    {
        parent::beforeRender();
		$industries = $this->Industry->find('list', array('fields' => 'id, name'));
		$industries = array(0 => __('Not Classified')) +  $industries;
		$ratecards = $this->Ratecard->find('list', array('fields' => 'id, name', 'conditions' => array('Ratecard.history_status' => 1), 'order' => array('Ratecard.default DESC')));
		$countries = $this->Country->find('list', array('fields' => 'id, name', 'order' => 'order desc, name asc'));
		$currencies = $this->Currency->find('list', array(
			'fields' => 'id, name',
			'order' => 'order desc, name asc'
		));
        $this->set(compact('countries', 'industries', 'ratecards', 'currencies'));
    }
	
    public function index(){
        $this->Company->recursive = 2;
		$this->Company->virtualFields = array(
			'industry_name' => 'Industry.name',
			'rate_title' => 'Ratecard.name',
			'country' => 'Country.name',
		);
		$data = $this->paginate('Company');
        $this->set('data', $data);
    }

    public function view($id = null){
        $this->Company->id = $id;
        if (!$this->Company->exists()){
            throw new NotFoundException(__('Invalid Company'));
        }
        $this->request->data = $this->Company->read();
    }

    public function add(){
        if ($this->request->is('post') || $this->request->is('put')){
			if(isset($this->request->data['Company']['logo'])){
				$this->request->data['Company']['logo'] = json_encode($this->request->data['Company']['logo']);
			}
			$this->request->data['Company']['created'] = gmdate('Y-m-d h:i:s');
            $this->Company->create();
            if ($this->Company->save($this->request->data)){
            	//save ratecard
            	$ratecard = $this->Ratecard->read(null, $this->request->data['Company']['ratecard_id']);
            	$data = $ratecard;
            	$clients = json_decode($ratecard['Ratecard']['clients'], true);
            	$clients[] = $this->Company->id;
            	$ratecard['Ratecard']['history_link'] = 0;
            	$ratecard['Ratecard']['clients'] = json_encode($clients);
            	if($this->Ratecard->save($ratecard)) {
            		unset($data['Ratecard']['id']);
					$data['Ratecard']['history_status'] = 2;
					$data['Ratecard']['history_link'] = $this->request->data['Company']['ratecard_id'];
					$data['Ratecard']['name'] = $data['Ratecard']['name'] . '-bk' . substr(md5(microtime()),rand(0,26),5);
					$this->Ratecard->create();
					if($this->Ratecard->save($data)) {
						
						$history['History']['plugin'] = 'accounting';
						$history['History']['controller'] = 'Ratecards';
						$history['History']['action'] = 'viewRatecard';
						$history['History']['history_id'] = $this->Ratecard->id;
						$history['History']['action_status'] = __('Add client');
						$history['History']['original_id'] = $this->request->data['Company']['ratecard_id'];
						$history['History']['user_modified'] = $this->Session->read('Auth.User.id');
						$history['History']['date_modified'] = gmdate('Y-m-d H:i:s');
						$this->History->save($history);
					}
					else {
						prd( $this->Ratecard->validationErrors);
					}
            	}
                $this->Session->setFlash(__('The company has been saved'));
				$this->Session->write('company_id', $this->Company->id);
                return $this->redirect(array('plugin' => 'company', 'controller' => 'users', 'action' => 'add'));
            } 
        }
		$this->render('edit');
    }
	
    public function edit($id = null) {
        $this->Company->id = $id;
        if (!$this->Company->exists()) {
            throw new NotFoundException(__('Invalid company'));
        }
        $data = $this->Company->read();
        if ($this->request->is('post') || $this->request->is('put')) {
			$diff = array_diff($this->request->data['Company'], $data['Company']);
			if(!empty($diff)) {
				$this->request->data['Company']['history_link'] = 0;
			}

			// prd($diff);
			if(isset($this->request->data['Company']['logo'])){
				if(isset($this->request->data['full_file_name'])){
					$file = new File(UPLOAD_PATH . DS . $this->request->data['full_file_name']);
					if($file->exists()){
						$file->delete();
					}
				}
				$this->request->data['Company']['logo'] = json_encode($this->request->data['Company']['logo']);
			}
			if(isset($this->request->data['delete_file']) && !isset($this->request->data['Company']['logo'])){
				
				$file = new File(UPLOAD_PATH . DS . $this->request->data['full_file_name']);
				if($file->exists()){
					$file->delete();
				}
				
				$this->request->data['Company']['logo'] = '[]';
			}
			
            if ($this->Company->save($this->request->data)) {
            	//save history
            	if(!empty($diff)) {
            		//update ratecard
            		$status = '';
            		if(in_array('ratecard_id', array_keys($diff))) {
            			$status .= __('Change ratecard');
            			//save old ratecard
            			$old_ratecard = $this->Ratecard->read(null, $data['Company']['ratecard_id']);
            			$old_ratecard_client = json_decode($old_ratecard['Ratecard']['clients'], true);
            			if(!empty($old_ratecard_client)) {
	            			if(isset($old_ratecard_client[array_search($id, $old_ratecard_client)])) {
	            				unset($old_ratecard_client[array_search($id, $old_ratecard_client)]);
	            			}
	            		}
            			$old_ratecard['Ratecard']['clients'] = json_encode(array_values($old_ratecard_client));
            			$this->Ratecard->save($old_ratecard);
            			//save new ratecard
            			$new_ratecard = $this->Ratecard->read(null, $this->request->data['Company']['ratecard_id']);
            			$new_ratecard_client = json_decode($new_ratecard['Ratecard']['clients'], true);
            			$new_ratecard_client[] = $id;
            			$new_ratecard['Ratecard']['clients'] = json_encode($new_ratecard_client);
            			$this->Ratecard->save($new_ratecard);
            		}
            		if(in_array('contact_number', array_keys($diff)))
            			$status .= (empty($status)) ? __('Change contact no') : ', ' . __('Change contact no');
            		if(in_array('website', array_keys($diff)))
            			$status .= (empty($status)) ? __('Change website') : ', ' . __('Change website');
            		if(empty($status))
            			$status = __('Change info');
					unset($data['Company']['id']);
					$data['Company']['history_status'] = 2;
					$data['Company']['history_link'] = $id;
					$data['Company']['name'] = $data['Company']['name'] . '-bk' . substr(md5(microtime()),rand(0,26),5);
					$this->Company->create();
					$this->Company->disablePermissionable('Company');
					if($this->Company->save($data)) {
						
						$history['History']['plugin'] = 'company';
						$history['History']['controller'] = 'Companies';
						$history['History']['action'] = 'view';
						$history['History']['history_id'] = $this->Company->id;
						$history['History']['action_status'] = $status;
						$history['History']['original_id'] = $id;
						$history['History']['user_modified'] = $this->Session->read('Auth.User.id');
						$history['History']['date_modified'] = gmdate('Y-m-d H:i:s');
						$this->History->save($history);
					}
					else {
						prd( $this->Company->validationErrors);
					}
				}
                $this->Session->setFlash(__('The company has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(
                __('The company could not be saved. Please, try again.')
            );
        } else {
            $this->request->data = $data;
        }
    }

    public function history($id)
    {
    	$this->paginate = array(
    		'joins' => array(
				array('table' => 'company_industries', 'alias' => 'Industry', 'type' => 'LEFT', 'conditions' => array('Industry.id = Company.industry_id')),
				array('table' => 'accounting_ratecards', 'alias' => 'Ratecard', 'type' => 'LEFT', 'conditions' => array('Ratecard.id = Company.ratecard_id')),
				array('table' => 'countries', 'alias' => 'Country', 'type' => 'LEFT', 'conditions' => array('Country.id = Company.country_id'))
			),
			'conditions' => array(
				'Company.history_link' => $id
			),
			'permissionable' => false,
			'order' => 'Company.id ASC',
		   'limit' => ROWPERPAGE
		);
		$this->Company->virtualFields = array(
			'industry_name' => 'Industry.name',
			'rate_title' => 'Ratecard.name',
			'country' => 'Country.name',
		);
		$this->set('data', $this->paginate('Company'));
    }

    public function delete($id = null){
        $this->Company->id = $id;
        if (!$this->Company->exists()) {
            throw new NotFoundException(__('Invalid company'));
        }
		$data = $this->Company->findById($id);
		if($data['Company']['logo']){
			$file = new File(WWW_ROOT . '/' . $data['Company']['logo']);
			$file->delete();
		}
        if ($this->Company->delete()){
            $this->Session->setFlash(__('Company deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Company was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
}