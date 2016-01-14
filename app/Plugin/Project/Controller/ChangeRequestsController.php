<?php
App::uses('AppController', 'Controller');

class ChangeRequestsController extends AppController {

	public $uses = array('Project.ChangeRequest', 'User', 'Project.Brief', 'Company.Company', 'Project.NoteDetail', 'Group');
	public function beforeFilter()
	{
		parent::beforeFilter();
		//$this->User->find('all', array('conditions'=>array('Author.id'=>'2')));
		$clients = $this->Company->find('list',array(
			'fields' => 'id, name',
			'permissionable' => false,
			'conditions' => array(
				'Company.history_status' => 1
			),
			'order' => 'Company.name ASC'
		));
		$staffs = $this->User->getByGroup(Configure::read('Settings.Company.SalesStaffGroupId'));
		$companies = $this->Company->find('list', array(
			'fields' => 'id, name',
			'permissionable' => false,
			'order' => 'name asc'
		));
		$this->set(compact('staffs', 'companies', 'clients'));
	}

	public function index() {
		$this->ChangeRequest->recursive = 2;
		$data = $this->paginate('ChangeRequest');
        $this->set('data', $data);
	}

	public function getPayRequest() {
		$this->ChangeRequest->recursive = 2;
		$data = $this->paginate('ChangeRequest');
        $this->set('data', $data);
	}

	
	public function add()
	{
		if($this->request->is(array('post', 'put'))) {
			if(empty($this->request->data['NoteDetail'])) {
				throw new Exception("Error Processing Request", 1);
			}
			// print_r($this->request->data); die;
			$this->ChangeRequest->create();
			if( !in_array($this->request->data['ChangeRequest']['minute_taker'], $this->request->data['ChangeRequest']['attendees']) && !in_array($this->request->data['ChangeRequest']['minute_taker'], $this->request->data['ChangeRequest']['cc_list']))
				array_push($this->request->data['ChangeRequest']['attendees'], $this->request->data['ChangeRequest']['minute_taker']);
			if(!empty($this->request->data['ChangeRequest']['cc_list']))
				$list_user = array_merge($this->request->data['ChangeRequest']['attendees'], $this->request->data['ChangeRequest']['cc_list']);
			else
				$list_user = $this->request->data['ChangeRequest']['attendees'];
			$this->request->data['ChangeRequest']['date'] = sqlFormatDate($this->request->data['ChangeRequest']['date']);
			$this->request->data['ChangeRequest']['attendees'] = json_encode($this->request->data['ChangeRequest']['attendees']);
			$this->request->data['ChangeRequest']['cc_list'] = json_encode($this->request->data['ChangeRequest']['cc_list']);
			foreach ($this->request->data['NoteDetail'] as $key => $value) {
				if(!empty($value['due_date']))
					$this->request->data['NoteDetail'][$key]['due_date'] = sqlFormatDateTime($value['due_date']);
			}
			if($this->ChangeRequest->saveAll($this->request->data)) {
				$id = $this->ChangeRequest->id;
				/*if($this->ChangeRequest->title == $this->request->data['ChangeRequest']['title'])
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
					$content = '<p>Title: <a href="'. Router::url(array('plugin' => false, 'controller' => 'ChangeRequests', 'action' => 'view', $id), true) .'">'. $this->request->data['ChangeRequest']['title'] .'</a></p>';
					$content .= '<p>Date: '. formatDate($this->request->data['ChangeRequest']['date']) .'</p>';
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
		$this->ChangeRequest->id = $id;
		if (!$this->ChangeRequest->exists()) {
			throw new NotFoundException(__('Invalid Meeting Minute'));
		}
		if($this->request->is(array('post', 'put'))) {
			$this->request->data['ChangeRequest']['id'] = $id;
			if( !in_array($this->request->data['ChangeRequest']['minute_taker'], $this->request->data['ChangeRequest']['attendees']) && !in_array($this->request->data['ChangeRequest']['minute_taker'], $this->request->data['ChangeRequest']['cc_list']))
				array_push($this->request->data['ChangeRequest']['attendees'], $this->request->data['ChangeRequest']['minute_taker']);
			$this->request->data['ChangeRequest']['date'] = sqlFormatDate($this->request->data['ChangeRequest']['date']);
			$this->request->data['ChangeRequest']['attendees'] = json_encode($this->request->data['ChangeRequest']['attendees']);
			$this->request->data['ChangeRequest']['cc_list'] = json_encode($this->request->data['ChangeRequest']['cc_list']);

			foreach ($this->request->data['NoteDetail'] as $key => $value) {
				if(!empty($value['due_date']))
					$this->request->data['NoteDetail'][$key]['due_date'] = sqlFormatDateTime($value['due_date']);
			}
			if($this->ChangeRequest->saveAll($this->request->data)) {
				$this->Session->setFlash(__('The Meeting minute has been saved'));
				$this->redirect(array('action' => 'index'));
			}
		}
		else {
			$this->request->data = $this->ChangeRequest->read(null, $id);
			$this->request->data['ChangeRequest']['date'] = formatDate($this->request->data['ChangeRequest']['date']);
			$this->request->data['ChangeRequest']['attendees'] = json_decode($this->request->data['ChangeRequest']['attendees'], true);
			$this->request->data['ChangeRequest']['cc_list'] = json_decode($this->request->data['ChangeRequest']['cc_list'], true);
			$note_details = $this->NoteDetail->getByNote($id);
			$this->set('note_details', $note_details);
		}
	}

	public function view($id) {
		$this->request->data = $this->ChangeRequest->read(null, $id);
		$this->request->data['ChangeRequest']['date'] = formatDate($this->request->data['ChangeRequest']['date']);
		//$this->request->data['ChangeRequest']['attendees'] = json_decode($this->request->data['ChangeRequest']['attendees'], true);
		//$this->request->data['ChangeRequest']['cc_list'] = json_decode($this->request->data['ChangeRequest']['cc_list'], true);
		//$note_details = $this->NoteDetail->getByNote($id);
		//$this->set('note_details', $note_details);
	}

	public function delete($id)
	{
		$this->ChangeRequest->id = $id;
		// $data = $this->ChangeRequest->read(null, $id);
		if ($this->ChangeRequest->delete($id)){
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

