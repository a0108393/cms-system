<?php
App::uses('AppController', 'Controller');

class DeliverablesController extends AppController {

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
  $this->set(compact('clients', 'project_ids', 'project_names','change_requests' /*'change_status'*/));
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
		if($this->request->is(array('post', 'put'))) {
			$this->request->data['Deliverable']['date'] = gmdate('Y-m-d h:i:s');
			
			if(empty($this->request->data['Deliverable']['project_id'])){
				echo 'no project selected';
				$this->Session->setFlash(__('No project selected!'));
				return false;
			}
			
			if( $this->Deliverable->saveAll($this->request->data)){
					$this->Session->setFlash('Deliverable Created!');
					$this->redirect(array('action' => 'index'));
			}
			
		}
  
	}
 
    //add a new project
	public function addproject() {
		
		
	}
	
	public function edit($id) {
		$this->Deliverable->id = $id;
		if (!$this->Deliverable->exists()) {
			throw new NotFoundException(__('Invalid Deliverable'));
		}
		// Deliverable fields: 	id name type 0: free 1: not free date project_id no_of_changes
		if($this->request->is(array('post', 'put'))) {
			$this->request->data['Deliverable']['id'] = $id;
			
			
			if($this->Deliverable->saveAll($this->request->data)) {
				$this->Session->setFlash(__('The Deliverable has been saved'));
				$this->redirect(array('action' => 'index'));
			}
		}
		else {
			$this->request->data = $this->Deliverable->read(null, $id);
			$this->request->data['Deliverable']['date'] = formatDate($this->request->data['Deliverable']['date']);
		}
	}

	public function view($id) {
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

	public function delete($id)
	{
		$this->Deliverable->id = $id;
		if ($this->Deliverable->delete($id)){
			$this->Session->setFlash(__('Deliverable deleted'));
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