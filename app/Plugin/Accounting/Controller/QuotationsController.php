<?php

App::uses('AppController', 'Controller');

class QuotationsController extends AppController{
	
	public $uses = array('Accounting.Ratecard', 'Accounting.Service', 'Accounting.RatecardService', 'Company.Company', 'Company.User', 'Currency', 'Country', 'Accounting.Quotation', 'Accounting.QuotationDetail', 'Accounting.QuotationNotify', 'History');
	public $paginate = array(
		'joins' => array(
			array(
				'table' => 'company_companies',
				'alias' => 'Company',
				'type' => 'LEFT',
				'conditions' => array('Quotation.client_id = Company.id')
			),
			array(
				'table' => 'accounting_ratecards',
				'alias' => 'Ratecard',
				'type' => 'LEFT',
				'conditions' => array('Quotation.ratecard_id = Ratecard.id')
			)
		),
        'order' => array(
            'Quotation.id' => 'desc'
        ),
		'conditions' => array(
			'Quotation.history_status' => 1
		),
		'limit' => ROWPERPAGE
    );
	function beforeRender()
    {
        parent::beforeRender();
		$ratecards = $this->Ratecard->find('list', array('fields' => 'id, name', 'conditions' => array('Ratecard.history_status' => 1), 'order' => array('Ratecard.default DESC')));
		$countries = $this->Country->find('list', array('fields' => 'id, name', 'order' => 'order desc, name asc'));
		$currencies = $this->Currency->find('list', array('fields' => 'id, name', 'order' => 'order desc, name asc'));
		$companies = $this->Company->find('list', array('fields' => 'id, name', 'conditions' => array('Company.history_status' => 1), 'permissionable' => false, 'order' => array('Company.name ASC')));
		$arr_key = array_keys($ratecards);
		$default_ratecard = Configure::read('Settings.Company.DefaultRatecard');
		$product_service = $this->Service->find('list', array(
			'fields' => 'id, name',
			'conditions' => array('Service.history_status' => 1),
			'order' => array('Service.name ASC')
		));
        $this->set(compact('ratecards', 'countries', 'currencies', 'companies', 'default_ratecard', 'product_service'));
    }
	
	public function index()
	{
		if($this->Session->read('Auth.User.group_id') == (int) Configure::read('Settings.Company.DefaultGroupId')) {
			$this->paginate['conditions']['Quotation.client_id'] = $this->Session->read('Auth.User.company_id');
			$this->paginate['conditions']['Quotation.status <'] = 6;
		}
		$this->Quotation->recursive = 0;
		$this->Quotation->virtualFields = array(
			'company_name' => 'Company.name',
			'ratecard_name' => 'Ratecard.name',
		);
		$data = $this->paginate('Quotation');
        $this->set('data', $data);
	}
	
	public function view($id)
	{
		$this->Quotation->id = $id;
        if (!$this->Quotation->exists()){
            throw new NotFoundException(__('Invalid quotation'));
        }
        $list_invoices = $this->Quotation->find('all',array(
    		'fields' => 'Quotation.id, Quotation.estimate_number',
    		'conditions' => array(
    			'Quotation.invoice_status' => 1,
    			'Quotation.history_status' => 1
			)
		));
    	$this->set('list_invoices', $list_invoices);

		$quotation_detail = $this->QuotationDetail->find('all', array(
			'fields' => 'QuotationDetail.*, Service.name',
			'conditions' => array(
				'QuotationDetail.quotation_id' => $id
			),
			'joins' => array(
				array(
					'table' => 'accounting_services',
					'alias' => 'Service',
					'type' => 'LEFT',
					'conditions' => 'QuotationDetail.product_service_id = Service.id'
				)
			)
		));
		$quotation = $this->Quotation->read(null, $id);
        $group = $this->Session->read('Auth.Group');
        $arrGroup = array();
        if(!empty($group)) {
        	foreach ($group as $key => $value) {
        		$arrGroup[] = $value['id'];
        	}
        }
        $this->set('group', $arrGroup);
        $this->set('data', $quotation);
		
	}
	public function approve($id)
	{
		if($this->request->is('post') || $this->request->is('put')) {
			if(isset($this->request->data['Quotation']['term']) && ($this->Session->read('Auth.User.signature') == trim($this->request->data['Quotation']['approval_signature'])) ) {
				$data = $this->Quotation->read(null, $id);
				$data['Quotation']['status'] = 5;
				//send notify to sales staff
				$saleStaff = $this->User->find('list', array(
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
						'UsersGroup.group_id' => Configure::read('Settings.Company.SalesStaffGroupId')
					)
				));
				if(!empty($saleStaff)) {
					$content = 'Cost estimate <a href="'. Router::url(array('plugin' => 'accounting', 'controller' => 'quotations', 'action' => 'view', $id), true) .'">'. $data['Quotation']['estimate_number'] .'</a> has approved by ' . $this->Session->read('Auth.User.name');
					$arr_options = array(
						'to' => $saleStaff,
						'subject' => __('On Approval by client'),
						'viewVars' => array('content' => $content)
					);
					if($this->Quotation->sendEmail($arr_options)) {
						if($this->Quotation->save($data))
						$this->Session->setFlash(__('The quotation has been approved'));
					}
					else {
						$this->Session->setFlash(__('The quotation not approved'));
					}
				}
				return $this->redirect(array('action' => 'index'));
			
			}
			else {
				$this->Session->setFlash(__('Signature incorrect'));
				return $this->redirect(array('action' => 'approve', $id));
			}
		}

		$this->Quotation->id = $id;
		$data = $this->Quotation->read();
		if($this->Session->read('Auth.User.company_id') != $data['Quotation']['client_id']) {
			$this->Session->setFlash(__('You do not have permission to approve'), 'warning');
			$this->redirect(array('action' => 'index'));
		}
		$this->set('data', $data);
		
	}
	public function history($key)
	{
		$histories = $this->Quotation->find('all', array(
			'fields' => 'Quotation.*, Company.name, Ratecard.name',
			'conditions' => array(
				'Quotation.random_key' => $key,
				'Quotation.history_status' => 2,
			),
			'joins' => array(
				array(
					'table' => 'company_companies',
					'alias' => 'Company',
					'type' => 'LEFT',
					'conditions' => array('Quotation.client_id = Company.id')
				),
				array(
					'table' => 'accounting_ratecards',
					'alias' => 'Ratecard',
					'type' => 'LEFT',
					'conditions' => array('Quotation.ratecard_id = Ratecard.id')
				)
			),
			'order' => array(
				'Quotation.id' => 'desc'
			)
		));
		$this->set('histories', $histories);
	}
	
	public function add()
	{
		//add helper
		$this->helpers[] = 'Accounting.Quotation';
		//add default  ratecard
		$ratecard_standard = $this->Ratecard->find('list', array('fields' => 'id, name', 'order' => array('Ratecard.default DESC'),  'limit' => '1'));
		$this->set('ratecard_standard', $ratecard_standard);
		
		//check post
		if($this->request->is('post') || $this->request->is('put')){
			// print_r($this->request->data);die;
			if(isset($this->request->data['Quotation']['total']) && (int)($this->request->data['Quotation']['total'] == 0)) {
				$this->Session->setFlash(__('Total not null'));
				return $this->redirect(array('action' => 'add'));
			}
			$this->Quotation->create();
			$this->request->data['Quotation']['date'] = sqlFormatDate($this->request->data['Quotation']['date']);
			//check discount percentage
			if($this->request->data['Quotation']['discount'] == '')
				$this->request->data['Quotation']['discount'] = 0;
			elseif($this->request->data['Quotation']['discount'] > 100 && $this->request->data['Quotation']['discount_type'] == 'percentage' ){
				$this->Session->setFlash(__('Max discount is 100%!'));
				return $this->redirect(array('action' => 'add', $this->request->data));
			}
			//build random key
			$this->request->data['Quotation']['random_key'] = substr(md5(microtime()),rand(0,26),9);
			//build next follow up day
			$this->request->data['Quotation']['next_follow_up'] = date('Y-m-d', (int) Configure::read('Settings.Accounting.follow_up_days') * 86400 + time() );
			$this->request->data['Quotation']['follow_up_days'] = Configure::read('Settings.Accounting.follow_up_days');
			//save data
            if ($this->Quotation->saveAll($this->request->data)) {
                $this->Session->setFlash(__('The quotation has been saved'));
				$id = $this->Quotation->id; 
				/*if(!empty($this->request->data['Quotation']['row'])) {
					$quotation_detail = array();
					foreach($this->request->data['Quotation']['row'] as $k => $item) {
						if((int)$item['product_service'] > 0) {
							$quotation_detail[$k]['product_service_id'] = $item['product_service'];
							$quotation_detail[$k]['product_description'] = $item['description'];
							$quotation_detail[$k]['quantity'] = $item['qty'];
							$quotation_detail[$k]['price'] = $item['price'];
							$quotation_detail[$k]['waiver_status'] = $item['waiver_status'];
							$quotation_detail[$k]['quotation_id'] = $id;
						}
					}
					if(!empty($quotation_detail))
						$this->QuotationDetail->saveMany($quotation_detail);
				}*/
				if($this->request->data['notify_status'] == 0)
					return $this->redirect(array('action' => 'view', $id));
				else 
					return $this->redirect(array('action' => 'notify', $id));
			}
		}
		// $this->render('edit');
	}
	public function edit($id)
	{
		$this->Quotation->id = $id;
		if (!$this->Quotation->exists()) {
			throw new NotFoundException(__('Invalid Quotation'));
		}
		$data = $this->Quotation->read();
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['Quotation']['id'] = $id;
			$this->request->data['Quotation']['date'] = sqlFormatDate($this->request->data['Quotation']['date']);
			//check discount percentage
			if($this->request->data['Quotation']['discount'] == '')
				$this->request->data['Quotation']['discount'] = 0;
			elseif($this->request->data['Quotation']['discount'] > 100 && $this->request->data['Quotation']['discount_type'] == 'percentage' ){
				$this->Session->setFlash(__('Max discount is 100%!'));
				return $this->redirect(array('action' => 'edit', $id));
			}
			if($this->request->data['Quotation']['estimate_number'] != $data['Quotation']['estimate_number'])
				$this->request->data['Quotation']['history_link'] = 0;
			//save all history version
			/*$this->request->data['Quotation']['random_key'] = $data[0]['Quotation']['random_key'];
			$list_history = $this->Quotation->find('all', array(
				'fields' => 'Quotation.id',
				'conditions' => array(
					'Quotation.random_key' => $data[0]['Quotation']['random_key']
				)
			));
			if(!empty($list_history)) {
				$changeHistory = array();
				foreach($list_history as $k => $item) {
					$changeHistory[$k]['id'] = $item['Quotation']['id'];
					$changeHistory[$k]['history_status'] = 2;
				}
				$this->Quotation->saveMany($changeHistory);
			}*/
			
			/*$this->request->data['Quotation']['next_follow_up'] = date('Y-m-d', (int) Configure::read('Settings.Accounting.follow_up_days') * 86400 + time() );
			$this->request->data['Quotation']['follow_up_days'] = Configure::read('Settings.Accounting.follow_up_days');*/
			//save data

			if ($this->Quotation->saveAll($this->request->data)) {
				
                $this->Session->setFlash(__('The quotation has been saved'));
				$this->Quotation->create();
				//unset id
				foreach ($data['QuotationDetail'] as $key => $value) {
					unset($data['QuotationDetail'][$key]['id']);
					unset($data['QuotationDetail'][$key]['quotation_id']);
				}
				unset($data['Quotation']['id']);
				//$data['Quotation']['estimate_number'] = $data['Quotation']['estimate_number'] . ' (bk' . substr(md5(microtime()),rand(0,26),5) . ')';
				$data['Quotation']['history_status'] = 2;
				$data['Quotation']['history_link'] = $id;
				if($this->Quotation->saveAll($data)) {
					$history['History']['plugin'] = 'accounting';
					$history['History']['controller'] = 'Quotations';
					$history['History']['action'] = 'view';
					$history['History']['history_id'] = $this->Quotation->id;
					$history['History']['original_id'] = $id;
					$history['History']['action_status'] = __('Edit');
					$history['History']['user_modified'] = $this->Session->read('Auth.User.id');
					$history['History']['date_modified'] = gmdate('Y-m-d H:i:s');
					$this->History->save($history);
				}
				if($this->request->data['notify_status'] == 0)
					return $this->redirect(array('action' => 'view', $id));
				else 
					return $this->redirect(array('action' => 'notify', $id));
			}
		}
		else {
			$quotation_detail = $this->QuotationDetail->find('all', array(
				'fields' => 'QuotationDetail.*, Service.name',
				'conditions' => array(
					'QuotationDetail.quotation_id' => $id
				),
				'joins' => array(
					array(
						'table' => 'accounting_services',
						'alias' => 'Service',
						'type' => 'LEFT',
						'conditions' => 'QuotationDetail.product_service_id = Service.id'
					)
				)
			));

			$n = preg_replace("/[^0-9]/","",$data['Quotation']['estimate_number']);
			$n++;
			$data['Quotation']['estimate_number'] = preg_replace('/\d/', '', $data['Quotation']['estimate_number'] ) . $n;
			//$this->set('quotation_detail', $quotation_detail);
			$data['Quotation']['date'] = formatDate($data['Quotation']['date']);
			$this->request->data = $data;
		}
	}
	public function generateInvoice($id) {
		$this->Quotation->id = $id;
		if(!$this->Quotation->exists()) {
			throw new NotFoundException(__('Invalid Quotation'));
		}
		if ($this->request->is('get')) {
			throw new MethodNotAllowedException();
		}
		if($this->request->is(array('post', 'put'))) {
			$data = $this->Quotation->read(null, $id);
			$data['Quotation']['invoice_status'] = 1;
			
			$clientUser = $this->User->find('list', array(
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
					'User.company_id' => $data['Quotation']['client_id'],
					'UsersGroup.group_id' => Configure::read('Settings.Company.DefaultGroupId'),
					'User.active' => 1
				)
			));
			if(!empty($clientUser))	{
				$content = '<a href="'. Router::url(array('plugin' => 'accounting', 'controller' => 'Quotations', 'action' => 'view', $id), true) .'">'. sprintf(__('Quotation %s'), $data['Quotation']['estimate_number']) .'</a>';
				
				$arr_options = array(
					'to' => $clientUser,
					'viewVars' => array('content' => $content),
					'subject' => __('Invoice generate')
				);
				if($this->Quotation->sendemail($arr_options)) {
					$this->Quotation->save($data);
					$this->Session->setFlash(__('Generate Invoice successfully'));
					$this->redirect(array( 'action' => 'index'));
				}
			}
			$this->Session->setFlash(__('Generate Invoice not successfully'));
			$this->redirect(array( 'action' => 'index'));
		}
	}
	public function followUp($id)
	{
		$this->Quotation->id = $id;
		if (!$this->Quotation->exists()) {
			throw new NotFoundException(__('Invalid Quotation'));
			
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if(!isset($this->request->data['Quotation']['choose_follow'])) {
				$this->Session->setFlash(__('Please select type follow up'));
				return $this->redirect(array('action' => 'followUp', $id));
			}
			// if(
			if( $this->request->data['Quotation']['choose_follow'] == 0) {
				if( (strtotime(sqlFormatDate($this->request->data['Quotation']['next_follow_up'])) - time()) < 0) {
					$this->Session->setFlash(__('Min date is today'));
					return $this->redirect(array('action' => 'followUp', $id));
				}
				$this->request->data['Quotation']['next_follow_up'] = sqlFormatDate($this->request->data['Quotation']['next_follow_up']);
				unset($this->request->data['Quotation']['follow_up_days']);
			}
			else 
				unset($this->request->data['Quotation']['next_follow_up']);
			if ($this->Quotation->save($this->request->data)) {
				$this->Session->setFlash(__('Follow up setting has been saved'));
				return $this->redirect(array('action' => 'index'));
			}
		}
		else {
			
			$this->set('data', $this->Quotation->read(null, $id));
		}
	}
	public function delete($id)
	{
		$this->Quotation->id = $id;
		$data = $this->Quotation->read();
		if($data['Quotation']['status'] !='6') {
			$this->Session->setFlash(__('Only deleted expired cost estimate'));
			return $this->redirect(array('action' => 'index'));
		}
		else {
			if ($this->Quotation->delete($id)){
				unset($data['Quotation']['id']);
				$data['Quotation']['history_status'] = 2;
				$data['Quotation']['estimate_number'] = $data['Quotation']['estimate_number'] . '-bk' . substr(md5(microtime()),rand(0,26),5);
				$this->Quotation->create();
				if($this->Quotation->saveAll($data)) {
					$history['History']['plugin'] = 'accounting';
					$history['History']['controller'] = 'Quotations';
					$history['History']['action'] = 'view';
					$history['History']['history_id'] = $this->Quotation->id;
					$history['History']['action_status'] = __('Delete');
					$history['History']['user_modified'] = $this->Session->read('Auth.User.id');
					$history['History']['date_modified'] = gmdate('Y-m-d H:i:s');
					$this->History->save($history);
				}
				else {
					prd( $this->Quotation->validationErrors);
				}
				$this->Session->setFlash(__('Quotation deleted'));
				return $this->redirect(array('action' => 'index'));
			}
		}
	}
	public function notify($id)
	{
		if($this->request->is('post') || $this->request->is('put')) {
			if(isset($this->request->data['Ratecard']['client'])) {
				$data = $this->Quotation->read(null, $id);
				$clients_email = $this->User->find('list', array(
					'fields' => 'User.id, User.email',
					'conditions' => array( 'User.id' => array_keys($this->request->data['Ratecard']['client']))
				));
				$clients_notify = $this->User->find('all', array(
					'fields' => 'User.*',
					'conditions' => array( 'User.id' => array_keys($this->request->data['Ratecard']['client']))
				));
				if(!empty($clients_notify)) {
					$quotation_notify = array();
					foreach( $clients_notify as $k => $item) {
						$quotation_notify[$k]['quotation_id'] = $id;
						$quotation_notify[$k]['user_id'] = $item['User']['id'];						
						$quotation_notify[$k]['user_contact'] = $item['User']['contact'];					
						$quotation_notify[$k]['user_email'] = $item['User']['email'];				
						$quotation_notify[$k]['user_name'] = $item['User']['name'];
						$quotation_notify[$k]['datetime_notify'] = gmdate('Y-m-d H:i:s');
					}
					if(!empty($quotation_notify))
						$this->QuotationNotify->saveMany($quotation_notify);
				}
				$content = '<a href="'. Router::url(array('plugin' => 'accounting', 'controller' => 'quotations', 'action' => 'view', $id), true) .'">'. sprintf(__('Quotation %s'), $data['Quotation']['estimate_number']) .'</a><br />';
				$content .= $this->request->data['Ratecard']['content_notify'];
				$arr_options = array(
					'to' => $clients_email,
					'viewVars' => array('content' => $content),
					'subject' => __('Cost estimate')
				);
				if($this->Quotation->sendemail($arr_options)) {
					$data['Quotation']['status'] = 3;
					$this->Quotation->save($data);
				}
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
					'table' => 'accounting_quotations',
					'alias' => 'Quotation',
					'type' => 'INNER',
					'conditions' => array('Ratecard.id = Quotation.ratecard_id')
				),
				array(
					'table' => 'users_groups',
					'alias' => 'UsersGroup',
					'type' => 'LEFT',
					'conditions' => array('User.id = UsersGroup.user_id')
				)
			),
			'conditions' => array(
				'Quotation.id' => $id,
				'User.active' => 1,
				'UsersGroup.group_id' => (int) Configure::read('Settings.Company.DefaultGroupId')
			),
			'group' => 'User.id',
			'order' => array(
				'User.name' => 'asc'
			)
		));
		if(empty($clients)) {
			$this->Session->setFlash('Not found clients');
			return $this->redirect(array('action' => 'index'));
		}
		else {
			$this->set('clients', $clients);
		}
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
		$this->Quotation->id = $id;
        if (!$this->Quotation->exists()){
            throw new NotFoundException(__('Invalid quotation'));
        }
		$quotation_detail = $this->QuotationDetail->find('all', array(
			'fields' => 'QuotationDetail.*, Service.name',
			'conditions' => array(
				'QuotationDetail.quotation_id' => $id
			),
			'joins' => array(
				array(
					'table' => 'accounting_services',
					'alias' => 'Service',
					'type' => 'LEFT',
					'conditions' => 'QuotationDetail.product_service_id = Service.id'
				)
			)
		));
        $this->set('quotation_detail', $quotation_detail);
        $this->set('data', $this->Quotation->read(null, $id));

        $this->layout = 'pdf/default'; //this will use the pdf.ctp layout 
        $this->render(); 
	}
	//List function ajax
	public function bindRatecard(){
		if($this->request->is('post')){
			$this->autoRender=false;
			$default_ratecard = $this->Company->find('first', array(
				'conditions' => array(
					'Company.id' => $this->request->data['Quotation']['client_id']
				),
				'permissionable' => false
			));
			$default_ratecard = $default_ratecard['Company']['ratecard_id'];
			$ratecards = $this->Ratecard->find('list', array(
				'fields' => 'id, name', 
				'conditions' => array(
					'AND' => array(
						'OR' => array(
							'Ratecard.id' => Configure::read('Settings.Company.DefaultRatecard'),
							'Ratecard.id' => $default_ratecard
						),
						'Ratecard.history_status' => 1
					)
				),
				'order' => array('Ratecard.default DESC')
			));
			$html = '';//'<option value="">Select one</option>';
			
			foreach ($ratecards as $key => $value) { 
				if($key == $default_ratecard)
					$html .= '<option selected="selected" value="'. $key .'">'. $value .'</option>';
				else
					$html .= '<option value="'. $key .'">'. $value .'</option>';
			}
			echo $html; die;
		}
	}
	public function bindProductService()
	{
		if($this->request->is('post')){
			$this->autoRender=false;
			$product_service = $this->Service->find('all', array(
				'fields' => array('Service.*','RatecardService.amount'),
				'joins' => array(
					array(
						'table' => 'accounting_ratecard_services',
						'alias' => 'RatecardService',
						'type' => 'inner',
						'conditions' => array('Service.id = RatecardService.service_id')
					)
				),
				'conditions' => array(
					'Service.id' => $this->request->data['product_service_id'],
					'RatecardService.ratecard_id' => $this->request->data['ratecard_id']
				)
			));
			$html = '';
			if(!empty($product_service)) {
				foreach($product_service[0]['Service'] as $k => $item) {
					if($k != 'id' && $k != 'locked' && $k != 'category_id' && $k != 'history_status' && $k != 'history_link') {
						$html .= ucfirst($k) . ': ' . strip_tags($item) . "\n";
					}
				}
				$product_service[0]['Service']['html'] = $html;
				$product_service[0]['Service']['price'] = $product_service[0]['RatecardService']['amount'];
				echo json_encode($product_service[0]['Service']); die;
			}
			else 
				die('nodata');
		}
	}
	/**
	 * @Description : get ajax view detail quotation
	 *
	 * @return 	: html
	 * @Author 	: tungpa - tungbk29@gmail.com
	 */
	public function bindDetailHistory()
	{
		if($this->request->is('post')){
			$this->autoRender=false;
			$quotation_detail = $this->QuotationDetail->find('all', array(
				'fields' => 'QuotationDetail.*, Service.name',
				'conditions' => array(
					'QuotationDetail.quotation_id' =>  $this->request->data['quotation_id']
				),
				'joins' => array(
					array(
						'table' => 'accounting_services',
						'alias' => 'Service',
						'type' => 'LEFT',
						'conditions' => 'QuotationDetail.product_service_id = Service.id'
					)
				)
			));
			$html = '';
			if(!empty($quotation_detail)) {
				$html .= '<h2>'. __('Detail') .'</h2>';
				$html .= '<table class="table table-striped">';
				$html .= '<thead><tr><th>No.</th> <th>Services</th> <th>Description</th> <th>Quantity</th> <th>Price</th> <th>Waiver</th></tr></thead>';
				$key = 0;
				foreach($quotation_detail as $k => $item) {
					$key++;
					$html .= '<tr><td>'. $key .'</td>';
					$html .= '<td>'. $item['Service']['name'] .'</td>';
					$html .= '<td>'. nl2br($item['QuotationDetail']['product_description']) .'</td>';
					$html .= '<td>'. $item['QuotationDetail']['quantity'] .'</td>';
					$html .= '<td>'. $item['QuotationDetail']['price'] .'</td>';
					$html .= '<td>'. (($item['QuotationDetail']['waiver_status'] == '1') ? 'No' : 'Yes') .'</td></tr>';
				}
				$html .= '</table>';
			}
			else {
				$html .= __('No data');
			}
			echo $html; die;
		}
	}
	/**
	 * @Description : ajax compare client's signature 
	 *
	 * @return 	: html
	 * @Author 	: tungpa - tungbk29@gmail.com
	 */
	public function compareSignature()
	{
		if($this->request->is('post')){
			$this->autoRender=false;
			if($this->Session->read('Auth.User.signature') == trim($this->request->data['signature'])) {
				echo 'pass';
			}
			else {
				echo 'Signature incorrect';
			}
			die;
		}
	}
	/**
	 * @Description : cronjobs Follow-up System
	 *
	 * @return 	: html
	 * @Author 	: tungpa - tungbk29@gmail.com
	 */
	public function sendFollowUp()
	{
		$this->autoRender=false;
		$quotations = $this->Quotation->find('all', array(
			'fields' => 'Quotation.*, Company.name, QuotationDetail.*,Service.name',
			'conditions' => array(
				'next_follow_up' => date('Y-m-d'),
				'Quotation.history_status' => 1,
				'send_second_expiry' => 0,
				'status <' => 2,
			),
			'joins' => array(
				array(
					'table' => 'company_companies',
					'alias' => 'Company',
					'type' => 'LEFT',
					'conditions' => array('Quotation.client_id = Company.id')
				),
				array(
					'table' => 'accounting_quotation_details',
					'alias' => 'QuotationDetail',
					'type' => 'LEFT',
					'conditions' => array('Quotation.id = QuotationDetail.quotation_id')
				),
				array(
					'table' => 'accounting_services',
					'alias' => 'Service',
					'type' => 'LEFT',
					'conditions' => array('Service.id = QuotationDetail.product_service_id')
				)
			),
			'group' => 'QuotationDetail.id',
			'order' => 'Quotation.id asc'
		));
		$saleStaff = $this->User->find('list', array(
			'fields' => 'User.id, User.email',
			'conditions' => array(
				'User.group_id' => 8
			),
			'permissionable' => false
		));
		// CakeLog::write('error', print_r($saleStaff));
		// print_r($saleStaff);die;
		$arrQuotation = array();
		if(!empty($quotations)) {
			$id = 0;
			$i = 0;
			foreach($quotations as $k => $item) {
				if($item['Quotation']['id'] == $id) {
					$arrQuotation[$i]['child'][] = $item['QuotationDetail'];
					$arrQuotation[$i]['child'][$k]['service_name'] = $item['Service']['name'];
				}
				else {
					$i++;
					$id = $item['Quotation']['id'];
					$arrQuotation[$i] = $item['Quotation'];
					$arrQuotation[$i]['company_name'] = $item['Company']['name'];
					$arrQuotation[$i]['child'][$k] = $item['QuotationDetail'];
					$arrQuotation[$i]['child'][$k]['service_name'] = $item['Service']['name'];
					
				}
			}
		}

		if(!empty($saleStaff) && !empty($arrQuotation)) {
			foreach($arrQuotation as $k => $item) {
				$day = floor((time() - strtotime($item['date']))/86400);
				$content = '<p><a href="'. Router::url(array('plugin' => 'accounting', 'controller' => 'quotations', 'action' => 'view', $id), true) .'">Estimate '. $item['estimate_number'] .'</a></p>';
				$content .= '<p>Date of Issue: '. $day .'</p>';
				$content .= '<p>'. (((int) $item['validity'] - $day) > 0) ? ((int) $item['validity'] - $day). ' days till' : 'No expiry' .'</p>';
				$content .= '<p>Client: <a href="'. Router::url(array('plugin' => 'accounting', 'controller' => 'quotations', 'action' => 'viewClient', $item['client_id']), true) .'">'. $item['company_name'] .'</a></p>';
				$content .= '<p>Subject: '. $item['subject'] .'</p>';
				$content .= '<table><tr><td>No.</td><td>Product/Service</td><td>Qty</td><td>Price</td></tr>';
				$m = 0;
				foreach($item['child'] as $n => $vl) {
					$content .= '<tr><td>'. $m .'</td>';
					$content .= '<td>'. $vl['service_name'] .'</td>';
					$content .= '<td>'. $vl['quantity'] .'</td>';
					$content .= '<td>'. $vl['price'] .'</td></tr>';
					$m++;
				}
				$content .= '</table>';
				$arr_options = array(
					'to' => $saleStaff,
					'subject' => sprintf(__('Cost Estimate %s Follow-up for %s'),$item['estimate_number'], $item['company_name']),
					'viewVars' => array('content' => $content)
				);
				
				if($this->Quotation->sendEmail($arr_options)) {
					
					$next_day = date( 'Y-m-d',strtotime($item['next_follow_up'] ." + ". $item['follow_up_days'] ." days"));
					$data['Quotation']['id'] = $item['id'];
					$data['Quotation']['next_follow_up'] = $next_day;
					$this->Quotation->save($data);
				}
			}
		}
	}
	/**
	 * @Description : cronjobs Send first cost estimate expiry notification email to client
	 *
	 * @return 	: html
	 * @Author 	: tungpa - tungbk29@gmail.com
	 */
	 
	public function sendFirstExpiry()
	{
		$this->autoRender=false;
		$this->Quotation->virtualFields['TotalDays'] = 'validity - FLOOR((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(Quotation.date))/86400)';
		$dayNotice = Configure::read('Settings.Accounting.cost_estimate_expiry_notice');
		$quotations = $this->Quotation->find('all', array(
			'fields' => 'Quotation.*, ( validity - FLOOR((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(Quotation.date))/86400) ) as Quotation_TotalDays',
			'conditions' => array(
				'Quotation.history_status' => 1,
				'send_first_expiry' => 0,
				'status <' => 2,
			),
			'group' => 'Quotation.id HAVING Quotation_TotalDays = ' . $dayNotice
		));
		
		if(!empty($quotations)) {
			foreach($quotations as $k => $item) {
				$clientUser = $this->User->find('list', array(
					'fields' => 'User.id, User.email',
					'conditions' => array(
						'User.company_id' => $item['Quotation']['client_id'],
						'User.group_id' => Configure::read('Settings.Company.DefaultGroupId'),
						'User.active' => 1
					)
				));
				if(!empty($clientUser)) {
					$content = $dayNotice .' days';
					$arr_options = array(
						'to' => $clientUser,
						'subject' => sprintf(__('Send first cost estimate expiry notification email to client %s days before validity reaches expiry'), $dayNotice),
						'viewVars' => array('content' => $content)
					);
					if($this->_sendemail($arr_options)) {
						$data['Quotation']['id'] = $item['Quotation']['id'];
						$data['Quotation']['send_first_expiry'] = 1;
						$this->Quotation->save($data);
					}
				}
			}
		}
	}
	/**
	 * @Description : cronjobs Send second cost estimate expiry notification email to client
	 *
	 * @return 	: html
	 * @Author 	: tungpa - tungbk29@gmail.com
	 */
	 
	public function sendSecondExpiry()
	{
		$this->autoRender=false;
		$this->Quotation->virtualFields['TotalDays'] = 'validity - FLOOR((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(Quotation.date))/86400)';
		$dayNotice = 1;
		$quotations = $this->Quotation->find('all', array(
			'fields' => 'Quotation.*, ( validity - FLOOR((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(Quotation.date))/86400) ) as Quotation_TotalDays',
			'conditions' => array(
				'history_status' => 1,
				'send_second_expiry' => 0,
				'status <' => 2,
			),
			'group' => 'Quotation.id HAVING Quotation_TotalDays = ' . $dayNotice
		));
		if(!empty($quotations)) {
			foreach($quotations as $k => $item) {
				$clientUser = $this->User->find('list', array(
					'fields' => 'User.id, User.email',
					'conditions' => array(
						'User.company_id' => $item['Quotation']['client_id'],
						'User.group_id' => Configure::read('Settings.Company.DefaultGroupId'),
						'User.active' => 1
					)
				));
				if(!empty($clientUser)) {
					$content = $dayNotice .' days';
					$arr_options = array(
						'to' => $clientUser,
						'subject' => __('Send second expiry notification email to client 1 day before validity reaches expiry'),
						'viewVars' => array('content' => $content)
					);
					if($this->_sendemail($arr_options)) {
						$data['Quotation']['id'] = $item['Quotation']['id'];
						$data['Quotation']['send_second_expiry'] = 1;
						$this->Quotation->save($data);
					}
				}
			}
		}
	}
	/**
	 * @Description : cronjobs Send Expired notification email to client on expiry
	 *
	 * @return 	: html
	 * @Author 	: tungpa - tungbk29@gmail.com
	 */
	 
	public function sendOnExpiry()
	{
		$this->autoRender=false;
		$this->Quotation->virtualFields['TotalDays'] = 'validity - FLOOR((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(Quotation.date))/86400)';
		$dayNotice = 0;
		$quotations = $this->Quotation->find('all', array(
			'fields' => 'Quotation.*, ( validity - FLOOR((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(Quotation.date))/86400) ) as Quotation_TotalDays',
			'conditions' => array(
				'history_status' => 1,
				'status <' => 2
			),
			'group' => 'Quotation.id HAVING Quotation_TotalDays <= ' . $dayNotice
		));
		if(!empty($quotations)) {
			foreach($quotations as $k => $item) {
				$clientUser = $this->User->find('list', array(
					'fields' => 'User.id, User.email',
					'conditions' => array(
						'User.company_id' => $item['Quotation']['client_id'],
						'User.group_id' => Configure::read('Settings.Company.DefaultGroupId'),
						'User.active' => 1
					)
				));
				if(!empty($clientUser)) {
					$content = 'on expiry';
					$arr_options = array(
						'to' => $clientUser,
						'subject' => __('Send Expired notification email to client on expiry'),
						'viewVars' => array('content' => $content)
					);
					if($this->Quotation->sendEmail($arr_options)) {
						$item['Quotation']['status'] = 6;
						$this->Quotation->save($item);
					}
				}
			}
		}
	}
	
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
	//list function cron job
}