	<?php
	App::uses('AppController', 'Controller');

	class DashboardsController extends AppController {

		public $uses = array('Project.Deliverable', 'Project', 'Project.ChangeRequest','User', 'Project.Brief', 'Company.Company', 'Project.NoteDetail', 'Group');
		public function beforeFilter()
	 {
	  parent::beforeFilter();
	  $project_ids = $this->Project->find('list',array(
	   'fields' => 'name, id',
	   'permissionable' => false,
	   'conditions' => array(
		
	   ),
	  ));
	  $project_names = $this->Project->find('list',array(
	   'fields' => 'id, name',
	   'permissionable' => false,
	   'conditions' => array(
		
	   ),
	  ));
	  
	  $client_users = $this->User->find('list', array(
	  'fields' =>'User.name',
	  'joins' => array(
					array(
						'table' => 'users_groups',
						'alias' => 'UsersGroup',
						'type' => 'LEFT',
						'conditions' => array('User.id = UsersGroup.user_id')
					)
				),
				'conditions' => array(
					'UsersGroup.group_id = 5'
				),
	  
	  ));

	 
		
	  $change_requests = $this->ChangeRequest->find('list',array(
	   'fields' => 'deliverable_id',
	   'permissionable' => false,
	   'conditions' => array(  
	   ),
	  ));
	  
	  
	  /*
	  $change_status = $this->ChangeRequest->find('list',array(
	   'fields' => 'status',
	   'permissionable' => false,
	   'conditions' => array(
		
	   ),
	  ));
	  */

	  
	  //$this->User->find('all', array('conditions'=>array('Author.id'=>'2')));
	  $clients = $this->Company->find('list',array(
	   'fields' => 'id, name',
	   'permissionable' => false,
	   'conditions' => array(
		'Company.history_status' => 1
	   ),
	   'order' => 'Company.name ASC'
	  ));
	  $this->request->data['ChangeRequest']['deliverable_id'] = $change_requests;
	  $this->set(compact('clients', 'project_ids', 'project_names','change_requests' ,'client_users'));
	 }
	 
		public function index() {
			$this->Project->recursive = 0;
			$data = $this->paginate('Project');
			$this->set('data', $data);
		} 

		public function deliverabledashboard() {
			$this->Deliverable->recursive = 2;
			$data = $this->paginate('Deliverable');
			$this->set('data', $data);
		}

		public function getPayRequest() {
			$this->Deliverable->recursive = 2;
			$data = $this->paginate('Deliverable');
			$this->set('data', $data);
		}

		public function add(){
			
			//if project files is not empty -- to be removed
			if(!empty($data['Project']['project_file'])) {
			$attached_files = json_decode($data['Project']['project_file'], true);
			$this->set('attached_files', $attached_files);

			}
			
			if($this->request->is(array('post', 'put'))) {
			
			if(!empty($this->request->data['Project']['project_file'])) {
				$this->request->data['Project']['project_file'] = json_encode($this->request->data['Project']['project_file']);
				}
			// store project owner name instead of id if selected in form			
			if(!empty($this->request->data['Project']['owner'])){
				$this->User->id = $this->request->data['Project']['owner'];
				
				$this->request->data['Project']['owner']=$this->User->field('name');
			}
			
			// set project start date and store in sql format
			if(!empty($this->request->data['Project']['project_start'])){
				$this->request->data['Project']['project_start'] = sqlFormatDate($this->request->data['Project']['project_start']);	
			}else{
				$this->request->data['Project']['project_start'] = gmdate('Y-m-d');
			}
			// set project deadline
			if(!empty($this->request->data['Project']['deadline'])){
				$this->request->data['Project']['deadline'] = sqlFormatDate($this->request->data['Project']['deadline']);	
			}
			
			/*
				if(empty($this->request->data['Deliverable']['project_id'])){
					echo 'no project selected';
					$this->Session->setFlash(__('No project selected!'));
					return false;
				}
				*/
				if( $this->Project->saveAll($this->request->data)){
						$this->Session->setFlash('Project Created!');
						$this->redirect(array('action' => 'index'));
				}
				
			}
	  
		}
	 
		//add a new project
		public function addproject() {
			
			
		}
		
		
		public function addDeliverable($id) {
			$this->Project->id = $id;
			$data = $this->Project->read();
			///////////////////////////////
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
			
			///////////////////////////////
			if (!$this->Project->exists()) {
				throw new NotFoundException(__('Invalid Project'));
			}
			
			if($this->request->is(array('post', 'put'))) {
				$this->request->data['Deliverable']['id'] = $id;
				
				if(!empty($this->request->data['Deliverable']['deliverable_file'])) {
				$this->request->data['Deliverable']['deliverable_file'] = json_encode($this->request->data['Deliverable']['deliverable_file']);
				}
				if($this->Deliverable->saveAll($this->request->data)) {
					$this->Session->setFlash(__('The Deliverable has been saved'));
					$this->redirect(array('action' => 'index'));
				}
			}
			else {
				$this->request->data=$data;
				
				//$this->request->data = $this->Deliverable->read(null, $id);
				
				$this->request->data['Deliverable']['date'] = formatDate($this->request->data['Deliverable']['date']);
				$this->set('attached_files',json_decode($this->request->data['Brief']['attached_files'], true));
			}
		}

		public function view($id) {
			$data = $this->Deliverable->read(null,$id);
			
			//no deliverables found
			if(empty($data)){
			$data = $this->Project->read(null,$id);
			$this->set('data', $data);
			return;
			}
			
			
			//deliverable upload attached files
			if($this->request->is(array('post', 'put'))) {
				$this->request->data['Deliverable']['id'] = $id;
				
				if(!empty($this->request->data['Deliverable']['deliverable_file'])) {
				$this->request->data['Deliverable']['deliverable_file'] = json_encode($this->request->data['Deliverable']['deliverable_file']);
				}
				if($this->Deliverable->saveAll($this->request->data)) {
					$this->Session->setFlash(__('The Deliverable has been saved'));
					$this->redirect(array('action' => 'view', $id));
				}
			}
			
			if(!empty($data['Deliverable']['deliverable_file'])) {
			$attached_files = json_decode($data['Deliverable']['deliverable_file'], true);
			$this->set('attached_files', $attached_files);
			}
			//----total deliverable size---//
			$total_deliverable = $this->Deliverable->find('list',array(
				'fields' => 'id',
				'conditions' => array('Deliverable.project_id' => $data['Project']['id']),
			));
	  
			$size_total_deliverrable = sizeof($total_deliverable);
			
			//----total completed deliverable size---//
			$s_completed = 'completed';
			   
			$completed_deliverable = $this->Deliverable->find('list',array(
				'fields' => 'id',
				'conditions' => array(
					'Deliverable.project_id' => $data['Project']['id'],
					'Deliverable.deliverable_status' => 'completed')
				));
			  
			$size_completed_deliverrable = sizeof($completed_deliverable);
			 
			//----total in progress deliverable size---//
			$in_Progress_deliverable = $this->Deliverable->find('list',array(
				'fields' => 'id',
				'conditions' => array(
						'Deliverable.project_id' => $data['Project']['id'],
						'Deliverable.deliverable_status' => 'in_progress'
				)
			));
						
			  
			$size_in_progress_deliverrable = sizeof($in_Progress_deliverable);
			
			//----total cancelled deliverable size---//
			$cancelled_deliverable = $this->Deliverable->find('list',array(
				'fields' => 'id',
				'conditions' => array(
					'Deliverable.project_id' => $data['Project']['id'],
					'Deliverable.deliverable_status' =>'cancelled'
				)
			));
			  
			$size_cancelled_deliverrable = sizeof($cancelled_deliverable);
			
			//------------------------------//
						
			$allChanges = $this->ChangeRequest->find('all',array(
				//'table' => 'deliverables',
				//'alias' => 'Deliverable',
				'conditions' => array(
				),
			));
			$allDeliverables = $this->Deliverable->find('all',array(
				'conditions' => array(
				'Deliverable.project_id' => $id
				),
			));
			
			$this->set('allDeliverables', $allDeliverables);
			$this->set('allChanges', $allChanges);
			$this->set('data', $data);
			$this->set(compact('size_total_deliverrable','size_cancelled_deliverrable','size_in_progress_deliverrable','size_completed_deliverrable'));
		}

		public function detail($id) {
			$data = $this->Deliverable->read(null,$id);
			$allChanges = $this->ChangeRequest->find('all',array(
				//'table' => 'deliverables',
				//'alias' => 'Deliverable',
				'conditions' => array(
				'ChangeRequest.deliverable_id' => $id
				),
			));
			$this->set('allChanges', $allChanges);
			$this->set('data', $data);
		}
		
		public function viewChangeRequest($deliverables_id){
			
			if (!$deliverables_id) { 
				$this->Session->setFlash('Sorry, there was no property ID submitted.'); 
				$this->redirect(array('action'=>'index'), null, true); 
			}
			$this->Deliverables->id = $deliverables_id;
			if (!$this->Deliverables->exists()){
				throw new NotFoundException(__('Invalid quotation'));
			}
			$data = $this->Deliverables->read(null, $deliverables_id);
			$this->set('data', $data);
			
			$this->render();
			
		}
		
		public function viewCr($id)
		{
			$data = $this->ChangeRequest->read(null, $id);
			$this->set('data', $data);
			
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
		}
		
		public function delete($id)
		{
			$this->Project->id = $id;
			if ($this->Project->delete($id)){
				$this->Session->setFlash(__('Project deleted'));
				return $this->redirect(array('action' => 'index'));
			}
		}

		public function ajaxDeleteNoteDetail() {
			if($this->request->is(array('post', 'put'))) {
				$this->autoRender=false;
				if($this->NoteDetail->delete($this->request->data['note_detail_id'])) {
					echo 'deleted';
				}
			}
			die;
		}

		//send mail
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