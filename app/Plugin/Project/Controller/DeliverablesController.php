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
		//$this->User->find('all', array('conditions'=>array('Author.id'=>'2')));
		$clients = $this->Company->find('list',array(
			'fields' => 'id, name',
			'permissionable' => false,
			'conditions' => array(
				'Company.history_status' => 1
			),
			'order' => 'Company.name ASC'
		));
		$this->set(compact('clients', 'project_ids'));
	}

	public function index() {
		$this->Deliverable->recursive = 2;
		$data = $this->paginate('Deliverable');
        $this->set('data', $data);
	}

	public function getPayRequest() {
		$this->Deliverabless->recursive = 2;
		$data = $this->paginate('Deliverable');
        $this->set('data', $data);
	}

	
	public function add()
	{
		if($this->request->is(array('post', 'put'))) {
			if(empty($this->request->data['NoteDetail'])) {
				throw new Exception("Error Processing Request", 1);
			}
			// print_r($this->request->data); die;
			$this->Deliverables->create();
			if( !in_array($this->request->data['Deliverables']['minute_taker'], $this->request->data['Deliverabless']['attendees']) && !in_array($this->request->data['Deliverabless']['minute_taker'], $this->request->data['Deliverabless']['cc_list']))
				array_push($this->request->data['Deliverables']['attendees'], $this->request->data['Deliverabless']['minute_taker']);
			if(!empty($this->request->data['Deliverabless']['cc_list']))
				$list_user = array_merge($this->request->data['Deliverabless']['attendees'], $this->request->data['Deliverabless']['cc_list']);
			else
				$list_user = $this->request->data['Deliverabless']['attendees'];
			$this->request->data['Deliverabless']['date'] = sqlFormatDate($this->request->data['Deliverabless']['date']);
			$this->request->data['Deliverabless']['attendees'] = json_encode($this->request->data['Deliverabless']['attendees']);
			$this->request->data['Deliverabless']['cc_list'] = json_encode($this->request->data['Deliverabless']['cc_list']);
			foreach ($this->request->data['NoteDetail'] as $key => $value) {
				if(!empty($value['due_date']))
					$this->request->data['NoteDetail'][$key]['due_date'] = sqlFormatDateTime($value['due_date']);
			}
			if($this->Deliverabless->saveAll($this->request->data)) {
				$id = $this->Deliverabless->id;
				/*if($this->Deliverabless->title == $this->request->data['Deliverabless']['title'])
					die;
				$NoteDetails = array();
				foreach ($this->request->data['note_details'] as $k => $item) {
					$NoteDetails[$k]['type'] = $item['type'];
					$NoteDetails[$k]['description'] = $item['description'];
					$NoteDetails[$k]['note_id'] = $id;
					if(isset($item['assigned_to']))
						$NoteDetails[$k]['assigned_to'] = $item['assigned_to'];
					if(isset($item['due_date']))
						$NoteDetails[$k]['due_date'] = sqlFormatDateTime($item['due_date']);
				}
				$this->NoteDetail->saveMany($NoteDetails);*/
				//send notify
				$list_email = $this->User->find('list', array(
					'fields' => 'id, email',
					'conditions' => array(
						'id' => $list_user
					)
				));
				if(!empty($list_email)) {
					$content = '<p>Title: <a href="'. Router::url(array('plugin' => false, 'controller' => 'Deliverables', 'action' => 'view', $id), true) .'">'. $this->request->data['Deliverabless']['title'] .'</a></p>';
					$content .= '<p>Date: '. formatDate($this->request->data['Deliverabless']['date']) .'</p>';
					$arr_options = array(
						'to' => $list_email,
						'subject' => __('Metting Minute has been created'),
						'viewVars' => array('content' => $content)
					);
					$this->_sendemail($arr_options);
				}
				$this->Session->setFlash(__('The Meeting minute has been saved'));
				$this->redirect(array('action' => 'index'));
			}
		}
		
	}

	public function edit($id) {
		$this->Deliverabless->id = $id;
		
		if($this->request->is(array('post', 'put'))) {
			$this->request->data['Deliverabless']['id'] = $id;
			if( !in_array($this->request->data['Deliverabless']['minute_taker'], $this->request->data['Deliverabless']['attendees']) && !in_array($this->request->data['Deliverabless']['minute_taker'], $this->request->data['Deliverabless']['cc_list']))
				array_push($this->request->data['Deliverabless']['attendees'], $this->request->data['Deliverabless']['minute_taker']);
			$this->request->data['Deliverabless']['date'] = sqlFormatDate($this->request->data['Deliverabless']['date']);
			$this->request->data['Deliverabless']['attendees'] = json_encode($this->request->data['Deliverabless']['attendees']);
			$this->request->data['Deliverabless']['cc_list'] = json_encode($this->request->data['Deliverabless']['cc_list']);

			foreach ($this->request->data['NoteDetail'] as $key => $value) {
				if(!empty($value['due_date']))
					$this->request->data['NoteDetail'][$key]['due_date'] = sqlFormatDateTime($value['due_date']);
			}
			if($this->Deliverabless->saveAll($this->request->data)) {
				$this->Session->setFlash(__('The Meeting minute has been saved'));
				$this->redirect(array('action' => 'index'));
			}
		}
		else {
			$this->request->data = $this->Deliverabless->read(null, $id);
			$this->request->data['Deliverables']['date'] = formatDate($this->request->data['Deliverabless']['date']);
			$this->request->data['Deliverables']['attendees'] = json_decode($this->request->data['Deliverabless']['attendees'], true);
			$this->request->data['Deliverables']['cc_list'] = json_decode($this->request->data['Deliverabless']['cc_list'], true);
			$note_details = $this->NoteDetail->getByNote($id);
			$this->set('note_details', $note_details);
		}
	}

	public function view($id) {
		$data = $this->paginate('Deliverables');
        $this->set('data', $data);

	}
/*
	public function index() {
		$this->Deliverabless->recursive = 2;
		$data = $this->paginate('Deliverabless');
        $this->set('data', $data);
	}
*/
	
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
		
		
		
		
		if (!$deliverables_id) 
        { 
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
		$this->Deliverabless->id = $id;
		// $data = $this->Deliverabless->read(null, $id);
		if ($this->Deliverabless->delete($id)){
			$this->Session->setFlash(__('Meeting Minute deleted'));
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

