<?php

App::uses('AppController', 'Controller');

class RatecardsController extends AppController{

	public $uses = array('Accounting.Ratecard', 'Accounting.Service', 'Accounting.RatecardService', 'Company.Company', 'Company.User', 'History');
	
	public $paginate = array(
		'conditions' => array(
			'Ratecard.history_status' => 1,
		),
		'joins' => array(
			array(
				'table' => 'company_companies',
				'alias' => 'Company',
				'type' => 'LEFT',
				'conditions' => array('Ratecard.id = Company.ratecard_id','Company.history_status' => 1)
			)
		),
        'order' => array(
            'Ratecard.default' => 'desc'
        ),
		'group' => 'Ratecard.id',
		'limit' => ROWPERPAGE
    );
	
	function beforeRender()
    {
        parent::beforeRender();
        $RetainerType = array(1 => 'Use Flat Retainer Discount Rate', 2 => 'Use Custom Line Item Rate');
        $this->set(compact('RetainerType'));
    }
	
    public function index(){
        $this->Ratecard->recursive = 0;
		$this->Ratecard->virtualFields = array(
			'companies' => 'group_concat(Company.name SEPARATOR \', \')'
		);
		$data = $this->paginate('Ratecard');
        $this->set('data', $data);
    }
	
	public function view($id = null){
		$this->Ratecard->id = $id;
        if (!$this->Ratecard->exists()){
            throw new NotFoundException(__('Invalid Ratecard'));
        }
		$data = $this->RatecardService->find('all', array(
			'conditions' => array('RatecardService.ratecard_id' => $id, 'Service.history_status' => 1),
			'fields' => array('RatecardService.*', 'Service.*'),
			'joins' => array(
                array(
                        'table' => 'accounting_services',
                        'alias' => 'Service',
                        'type' => 'LEFT',
                        'conditions' => array(
							'Service.id = RatecardService.service_id'
                        )
                    )
                )
			));
		$this->set('data', $data);
	}

	public function viewRatecard($id)
	{
		$this->Ratecard->id = $id;
		$data = $this->Ratecard->read();
		//get current company asigned
		$companies = $this->Company->find('list', array(
			'conditions' => array('Company.id' => json_decode($data['Ratecard']['clients'], true), 'Company.history_status' => 1),
			'permissionable' => false,
			'fields' => array('Company.id', 'Company.name')
		));
		$current_clients = array();
		
			foreach($companies as $com_id=>$com_name){
				$current_clients[$com_id]['id'] = $com_id;
				$current_clients[$com_id]['name'] = $com_name;
			}
		
		$this->set('current_clients', array_values($current_clients));
		//get all company in db
		$clients = $this->Company->find('all', array(
			'fields' => array('Company.id', 'Company.name','Ratecard.default'),
			'permissionable' => false,
			'conditions' => array(
				'Company.history_status' => 1
			),
			'joins' => array(
				array(
					'table' => 'accounting_ratecards',
					'alias' => 'Ratecard',
					'type' => 'LEFT',
					'conditions' => array(
						'Ratecard.id = Company.ratecard_id'
					)
				)
			)
		));
		$all_clients = array();
		foreach($clients as $key=>$client){
			$all_clients[$key]['id'] = $client['Company']['id'];
			$all_clients[$key]['name'] = $client['Company']['name'];
			$all_clients[$key]['default'] = $client['Ratecard']['default'];
		}
		$this->set('all_clients', array_values($all_clients));
		$this->request->data = $data;
	}
	
    public function edit_price($id = null) {
        $this->Ratecard->id = $id;
        if (!$this->Ratecard->exists()){
            throw new NotFoundException(__('Invalid Ratecard'));
        }
		
        $data = $this->Ratecard->read(null, $id);
		
		if($data['Ratecard']['type'] == 1){
			$this->Session->setFlash(__('Could not edit price for flat retainer discount ratecard type.'));
			return $this->redirect(array('action' => 'index'));
		}
		$serviceData = $this->RatecardService->find('all', array(
			'conditions' => array('RatecardService.ratecard_id' => $id, 'Service.history_status' => 1),
			'fields' => array('RatecardService.*', 'Service.*'),
			'joins' => array(
           		array(	
                    'table' => 'accounting_services',
                    'alias' => 'Service',
                    'type' => 'LEFT',
                    'conditions' => array(
						'Service.id = RatecardService.service_id'
                    )
                )
            ),
            'order' => 'Service.id asc'
		));
		//prd($serviceData);
		if($this->request->is('post') || $this->request->is('put')){
			$arrService = array();
			foreach ($serviceData as $key => $value) {
				$arrService[$key]['ratecard_id'] = $value['RatecardService']['ratecard_id'];
				$arrService[$key]['service_id'] = $value['RatecardService']['service_id'];
				$arrService[$key]['amount'] = $value['RatecardService']['amount'];
			}
			//check change
			$flagChange = false;
			$mapdata = $this->request->data['mapdata'];
			foreach ($arrService as $key => $value) {
				$diff = array_diff($mapdata[$key], $arrService[$key]);
				if(!empty($diff))
					$flagChange = true;
			}
			//save history ratecard
			if($flagChange) {
				$ratecard =  $this->Ratecard->read(null, $id);
				unset($ratecard['Ratecard']['id']);
				$ratecard['Ratecard']['history_status'] = 2;
				$ratecard['Ratecard']['history_link'] = $id;
				$this->Ratecard->create();
				if($this->Ratecard->save($ratecard)) {
					$this->RatecardService->updateAll(array('ratecard_id' => $this->Ratecard->id), array('ratecard_id' => $id));
					$history['History']['plugin'] = 'accounting';
					$history['History']['controller'] = 'Ratecards';
					$history['History']['action'] = 'view';
					$history['History']['history_id'] = $this->Ratecard->id;
					$history['History']['action_status'] = __('Edit price');
					$history['History']['original_id'] = $id;
					$history['History']['user_modified'] = $this->Session->read('Auth.User.id');
					$history['History']['date_modified'] = gmdate('Y-m-d H:i:s');
					$this->History->save($history);
				}
				$this->RatecardService->saveMany($mapdata);
			}	
			//delete all before save
			//$this->RatecardService->deleteAll(array('RatecardService.ratecard_id' => $id));
			//save
			
			$this->Session->setFlash(__('The custom amounts have been saved successfull!.'));
			if(isset($this->request->data['Ratecard']['notify']))
				return $this->redirect(array('action' => 'notify', $id));
			else 
				return $this->redirect(array('action' => 'index'));
		}
		$this->set('services', $serviceData);
		$this->set('ratecard', $data);
    }

    public function add(){
        if ($this->request->is('post')){
			if(isset($this->request->data['Ratecard']['discount']) && ($this->request->data['Ratecard']['discount'] > 100)){
				$this->Session->setFlash(__('Max discount is 100%!'));
				return $this->redirect(array('action' => 'add'));
			}
		
			$services = $this->Service->find('all');
            $this->Ratecard->create();
            $clients = $this->request->data['Ratecard']['clients'];
            $this->request->data['Ratecard']['clients'] = json_encode(explode(',', $this->request->data['Ratecard']['clients']));
            if ($this->Ratecard->save($this->request->data)){
                $this->Session->setFlash(__('The ratecard has been saved'));
				$id = $this->Ratecard->id;
				
				//update new clients
				$new_com_ids = array();
				if(!empty($clients)) {
					$new_com_ids = explode(',', $clients);
					unset($this->request->data['Ratecard']['clients']);
				}
				$update_coms = array();
				if(!empty($new_com_ids)){
					foreach($new_com_ids as $key=>$company_id){
						$update_coms[$key]['id'] = $company_id;
						$update_coms[$key]['ratecard_id'] = $id;
					}
					$this->Company->saveMany($update_coms);
				}
				
				$mapdata = array();
				if($this->request->data['Ratecard']['type'] == 2){
					foreach($services as $key=>$item){
						$mapdata[$key]['ratecard_id'] = $id;
						$mapdata[$key]['service_id'] = $item['Service']['id'];
						$mapdata[$key]['amount'] = $item['Service']['price'];
					}
					$this->RatecardService->saveMany($mapdata);
					$this->Session->setFlash(__('Please set custom amount for each services.'));
					return $this->redirect(array('action' => 'edit_price', $id));
				
				}else{
				
					foreach($services as $key=>$item){
						$mapdata[$key]['ratecard_id'] = $id;
						$mapdata[$key]['service_id'] = $item['Service']['id'];
						if(!$item['Service']['locked']){
							$mapdata[$key]['amount'] = (int)$item['Service']['price'] * (100 - (int)$this->request->data['Ratecard']['discount']) / 100;
						}else{
							$mapdata[$key]['amount'] = $item['Service']['price'];
						}
					}
					$this->RatecardService->saveMany($mapdata);
					if(isset($this->request->data['Ratecard']['notify'])) {
						$this->Session->write('Sendmail_title', __('Create ratecard'));
						return $this->redirect(array('action' => 'notify', $id));
					}
					else 
						return $this->redirect(array('action' => 'view', $id));
				}
				
            } 
        }
		//get all company in db
			$clients = $this->Company->find('all', array(
				'fields' => array('Company.id', 'Company.name','Ratecard.default'),
				'permissionable' => false,
				'conditions' => array(
					'Company.history_status' => 1
				),
				'joins' => array(
						array(
							'table' => 'accounting_ratecards',
							'alias' => 'Ratecard',
							'type' => 'LEFT',
							'conditions' => array(
								'Ratecard.id = Company.ratecard_id'
							)
						)
					)
				));
			
			$all_clients = array();
			foreach($clients as $key=>$client){
				$all_clients[$key]['id'] = $client['Company']['id'];
				$all_clients[$key]['name'] = $client['Company']['name'];
				$all_clients[$key]['default'] = $client['Ratecard']['default'];
			}
			$this->set('all_clients', array_values($all_clients));
			
		$this->render('edit');
    }

    public function edit($id = null) {
		
		$this->Ratecard->id = $id;
		if (!$this->Ratecard->exists()) {
            throw new NotFoundException(__('Invalid ratecard'));
        }
		
		$default_card = $this->Ratecard->find('first', array('conditions' => array('Ratecard.id' => Configure::read('Settings.Company.DefaultRatecard'))));
		$data = $this->Ratecard->read(null, $id);
		$serviceData = $this->RatecardService->find('all', array(
			'conditions' => array('RatecardService.ratecard_id' => $id),
			'fields' => array('RatecardService.*', 'Service.*'),
			'joins' => array(
                array(
                    'table' => 'accounting_services',
                    'alias' => 'Service',
                    'type' => 'LEFT',
                    'conditions' => array(
						'Service.id = RatecardService.service_id'
                    )
                )		
            )
        ));
		
		//get current company asigned
		$companies = $this->Company->find('list', array(
			'conditions' => array('Company.ratecard_id' => $id, 'Company.history_status' => 1),
			'permissionable' => false,
			'fields' => array('Company.id', 'Company.name')
		));
		$current_clients = array();
		
			foreach($companies as $com_id=>$com_name){
				$current_clients[$com_id]['id'] = $com_id;
				$current_clients[$com_id]['name'] = $com_name;
				if($id == Configure::read('Settings.Company.DefaultRatecard'))
					$current_clients[$com_id]['readonly'] = true;
			}
		
		$this->set('current_clients', array_values($current_clients));
		
		//get all company in db
		$clients = $this->Company->find('all', array(
			'fields' => array('Company.id', 'Company.name','Ratecard.default'),
			'permissionable' => false,
			'joins' => array(
				array(
					'table' => 'accounting_ratecards',
					'alias' => 'Ratecard',
					'type' => 'LEFT',
					'conditions' => array(
						'Ratecard.id = Company.ratecard_id'
					)
				)
			),
			'conditions' => array(
				'Company.history_status' => 1,
				'Ratecard.history_status' => 1
			)
		));
		
		$all_clients = array();
		foreach($clients as $key=>$client){
			$all_clients[$key]['id'] = $client['Company']['id'];
			$all_clients[$key]['name'] = $client['Company']['name'];
			$all_clients[$key]['default'] = $client['Ratecard']['default'];
		}
		$this->set('all_clients', array_values($all_clients));
		
		
		if ($this->request->is('post') || $this->request->is('put')) {
			// prd($this->request->data);
			if(isset($this->request->data['Ratecard']['discount']) && $this->request->data['Ratecard']['discount'] > 100){
				$this->Session->setFlash(__('Max discount is 100%!'));
				return $this->redirect(array('action' => 'edit', $id));
			}
			$request_clients = $this->request->data['Ratecard']['clients'];
			
			if(!empty($this->request->data['Ratecard']['clients'])) {
				$req_clients = explode(',', $this->request->data['Ratecard']['clients']);
				sort($req_clients);
				$this->request->data['Ratecard']['clients'] = json_encode($req_clients);
			}
			else {
				$this->request->data['Ratecard']['clients'] = null;
			}
			if(isset($this->request->data['Ratecard']['discount']))
				$this->request->data['Ratecard']['discount'] = number_format($this->request->data['Ratecard']['discount'], 2);
			$diff = array_diff_assoc($this->request->data['Ratecard'], $data['Ratecard']);
			// print_r($this->request->data['Ratecard']);
			// print_r($data['Ratecard']);
			// prd($diff);
			if(!empty($diff))
				$this->request->data['Ratecard']['history_link'] = 0;
			if ($this->Ratecard->save($this->request->data)) {
				if(!empty($diff)) {
					$status = '';
					if(in_array('name', array_keys($diff)))
						$status .= (empty($status)) ? __('Change name') : ', ' . __('Change name');
					if(in_array('discount', array_keys($diff)))
						$status .= (empty($status)) ? __('Change discount') : ', ' . __('Change discount');
					if(in_array('description', array_keys($diff)))
						$status .= (empty($status)) ? __('Change description') : ', ' . __('Change description');
					if(in_array('clients', array_keys($diff)))
						$status .= (empty($status)) ? __('Change client') : ', '. __('Change client');


					unset($data['Ratecard']['id']);
					$data['Ratecard']['history_status'] = 2;
					$data['Ratecard']['history_link'] = $id;
					$data['Ratecard']['name'] = $data['Ratecard']['name'] . '-bk' . substr(md5(microtime()),rand(0,26),5);
					$this->Ratecard->create();
					//$this->Ratecard->disablePermissionable('Ratecard');
					if($this->Ratecard->save($data)) {
						
						$history['History']['plugin'] = 'accounting';
						$history['History']['controller'] = 'Ratecards';
						$history['History']['action'] = 'viewRatecard';
						$history['History']['history_id'] = $this->Ratecard->id;
						$history['History']['action_status'] = $status;
						$history['History']['original_id'] = $id;
						$history['History']['user_modified'] = $this->Session->read('Auth.User.id');
						$history['History']['date_modified'] = gmdate('Y-m-d H:i:s');
						$this->History->save($history);
					}
					else {
						prd( $this->Ratecard->validationErrors);
					}
				}
				//update new clients
				$new_com_ids = array();
				if(!empty($request_clients)) {
					$new_com_ids = explode(',', $request_clients);
					//unset($this->request->data['Ratecard']['clients']);
				}

				$update_coms = array();
				$data_client = json_decode($data['Ratecard']['clients'], true);
				if(!is_array($data_client))
					$data_client = array();
				$diff_com = array_diff($data_client, $new_com_ids);
				if(!empty($new_com_ids) || !empty($diff_com)) {
					foreach($new_com_ids as $key=>$company_id){
						$update_coms[$key]['id'] = $company_id;
						$update_coms[$key]['ratecard_id'] = $id;
					}
					$n = count($update_coms);
					if(!empty($diff_com)) {
						foreach ($diff_com as $key => $value) {
							$update_coms[$n]['id'] = $value;
							//default ratecard
							$update_coms[$n]['ratecard_id'] = Configure::read('Settings.Company.DefaultRatecard');
							$n++;
						}
					}
					$this->Company->disablePermissionable('Company');
					$this->Company->saveMany($update_coms);
				}
				
				$this->Session->setFlash(__('The ratecard has been saved'));
				$mapdata = array();
				if($this->request->data['Ratecard']['type'] == 2){
					$this->Session->setFlash(__('Please set custom amount for each services.'));
					return $this->redirect(array('action' => 'edit_price', $id));
				}else{
					//delete data and recalculated new amount
					$this->RatecardService->deleteAll(array('RatecardService.ratecard_id' => $id));
					$newdata = array();
					if(empty($serviceData)){
						$serviceData = $this->Service->find('all');
					}
					foreach($serviceData as $key=>$item){
						$newdata[$key]['ratecard_id'] = $id;
						$newdata[$key]['service_id'] = $item['Service']['id'];
						if(!$item['Service']['locked']){
							$newdata[$key]['amount'] = (int)$item['Service']['price'] * (100 - (int)$this->request->data['Ratecard']['discount']) / 100;
						}else{
							$newdata[$key]['amount'] = $item['Service']['price'];
						}
					}
					
					$this->RatecardService->saveMany($newdata);
					if(isset($this->request->data['Ratecard']['notify']))
						return $this->redirect(array('action' => 'notify', $id));
					else 
						return $this->redirect(array('action' => 'view', $id));
				}
				
			}
			$this->Session->setFlash(
				__('The ratecard could not be saved. Please, try again.')
			);
		} else {
			$this->request->data = $data;
		}
		
    }

    public function history($id)
    {
    	$this->paginate = array(
			'conditions' => array(
				'Ratecard.history_link' => $id
			),
	        'order' => array(
	            'Ratecard.default' => 'desc',
	            'Ratecard.id' => 'asc'
	        ),
			//'group' => 'Ratecard.id',
			//'limit' => ROWPERPAGE
	    );
		$this->Ratecard->recursive = 0;
		$this->Ratecard->virtualFields = array(
			//'companies' => 'group_concat(Company.name SEPARATOR \', \')'
		);
		$data = $this->paginate('Ratecard');
		// prd($arrData);
        $this->set('data', $data);
    }

    public function delete($id = null){
        $this->Ratecard->id = $id;
		if (!$this->Ratecard->exists()) {
            throw new NotFoundException(__('Invalid ratecard'));
        }
		
		$data = $this->Ratecard->read(null, $id);
		
		if ($this->request->is('post') || $this->request->is('put')){
			
			if($data['Ratecard']['id'] == Configure::read('Settings.Company.DefaultRatecard')){
				$this->Session->setFlash(__('Could not delete default ratecard.'));
				return $this->redirect(array('action' => 'index'));
			}
			
			$datapost = $this->request->data['Ratecard'];
			
			
			if(!isset($this->request->data['Ratecard']['notify'])) {
				if ($this->Ratecard->delete()){
					unset($data['Ratecard']['id']);
					$data['Ratecard']['history_status'] = 2;
					$data['Ratecard']['name'] = $data['Ratecard']['name'] . ' (bk' . substr(md5(microtime()),rand(0,26),5) . ')';
					//print_r($data);die('aa');
					$this->Ratecard->create();

					if($this->Ratecard->save($data)) {
						
						$history['History']['plugin'] = 'accounting';
						$history['History']['controller'] = 'Ratecards';
						$history['History']['action'] = 'view';
						$history['History']['history_id'] = $this->Ratecard->id;
						$history['History']['action_status'] = __('Delete');
						$history['History']['user_modified'] = $this->Session->read('Auth.User.id');
						$history['History']['date_modified'] = gmdate('Y-m-d H:i:s');
						$this->History->save($history);
					}
					else {
						prd( $this->Ratecard->validationErrors);
					}
					$this->RatecardService->deleteAll(array('RatecardService.ratecard_id' => $id));
					
					//update all company to default ratecard.
					$default_card = $this->Ratecard->find('first', array('conditions' => array('Ratecard.id' => Configure::read('Settings.Company.DefaultRatecard'))));
					
					$this->Company->updateAll(array('Company.ratecard_id' => Configure::read('Settings.Company.DefaultRatecard')), array('Company.ratecard_id' => $id));
					
					$this->Session->setFlash(__('Ratecard deleted'));
					return $this->redirect(array('action' => 'index'));
				}
				
			}
			else {
				$this->Session->write('id_ratecard_delete', $id);
				return $this->redirect(array('action' => 'notify', $id));
			}
			$this->Session->setFlash(__('Ratecard was not deleted'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->request->data = $data;
		$this->render('edit');
    }
	public function notify($id) 
	{
		if($this->request->is('post') || $this->request->is('put')) {
			if(isset($this->request->data['Ratecard']['client'])) {
				$subject = $this->Session->read('Sendmail_title');
				$this->Session->delete('Sendmail_title');
				if(empty($subject))
					$subject = 'Update ratecard';
				if((int) $this->Session->read('id_ratecard_delete') > 0) {
					$id = $this->Session->read('id_ratecard_delete');
					$this->Ratecard->id = $id;
					$data = $this->Ratecard->read(null, $id);
					//delete session
					$this->Session->delete('id_ratecard_delete');
					if ($this->Ratecard->delete()){
						unset($data['Ratecard']['id']);
						$data['Ratecard']['history_status'] = 2;
						$data['Ratecard']['name'] = $data['Ratecard']['name'] . ' (bk' . substr(md5(microtime()),rand(0,26),5) . ')';
						//print_r($data);die('aa');
						$this->Ratecard->create();

						if($this->Ratecard->save($data)) {
							
							$history['History']['plugin'] = 'accounting';
							$history['History']['controller'] = 'Ratecards';
							$history['History']['action'] = 'view';
							$history['History']['history_id'] = $this->Ratecard->id;
							$history['History']['action_status'] = __('Delete');
							$history['History']['user_modified'] = $this->Session->read('Auth.User.id');
							$history['History']['date_modified'] = gmdate('Y-m-d H:i:s');
							$this->History->save($history);
						}
						else {
							prd( $this->Ratecard->validationErrors);
						}
						$this->RatecardService->deleteAll(array('RatecardService.ratecard_id' => $id));
						$default_card = $this->Ratecard->find('first', array('conditions' => array('Ratecard.id' => Configure::read('Settings.Company.DefaultRatecard'))));
						$this->Company->updateAll(array('Company.ratecard_id' => Configure::read('Settings.Company.DefaultRatecard')), array('Company.ratecard_id' => $id));
						$this->Session->setFlash(__('Ratecard deleted'));
						$subject = 'Ratecard deleted';
					}
				}
				$clients_email = $this->User->find('list', array(
					'fields' => 'User.id, User.email',
					'conditions' => array( 'User.id' => array_keys($this->request->data['Ratecard']['client']))
				));
				$arr_options = array(
					'to' => $clients_email,
					'subject' => $subject,
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
					'type' => 'LEFT',
					'conditions' => array('Company.id = User.company_id')
				),
				array(
					'table' => 'accounting_ratecards',
					'alias' => 'Ratecard',
					'type' => 'LEFT',
					'conditions' => array('Ratecard.id = Company.ratecard_id')
				),
				array(
					'table' => 'accounting_ratecard_services',
					'alias' => 'Ratecard_Service',
					'type' => 'LEFT',
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
				'Ratecard_Service.ratecard_id' => $id,
				'User.active' => 1,
				'UsersGroup.group_id' => (int) Configure::read('Settings.Company.DefaultGroupId')
			),
			'group' => 'User.id',
			'order' => array(
				'User.name' => 'asc'
			)
		));
		if(empty($clients)) {
			$this->Session->setFlash(__('Not Found Client'), 'warning');
			return $this->redirect(array('action' => 'index'));
		}
		else {
			$this->set('clients', $clients);
			
		}
	}
	function _sendemail($arr = array()){
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
		$email->send();
	}
}