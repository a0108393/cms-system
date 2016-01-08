<?php
App::uses('AppController', 'Controller');

class MeetingMinutesController extends AppController {

	public $uses = array('Project.MeetingMinute', 'User', 'Project.Brief', 'Company.Company', 'Project.NoteDetail', 'Group');
	public $paginate = array(
		'joins' => array(
			array(
				'table' => 'company_companies',
				'alias' => 'Company',
				'type' => 'LEFT',
				'conditions' => 'Company.id = MeetingMinute.client_id'
			)
		),
		'order' => array(
            'MeetingMinute.id' => 'desc'
        ),
        'limit' => ROWPERPAGE
	);
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
		$this->MeetingMinute->recursive = 2;
		$this->MeetingMinute->virtualFields = array(
			'client_name' => 'Company.name'
		);
		$data = $this->paginate('MeetingMinute');
        $this->set('data', $data);
	}

	public function add()
	{
		if($this->request->is(array('post', 'put'))) {
			if(empty($this->request->data['NoteDetail'])) {
				throw new Exception("Error Processing Request", 1);
			}
			// print_r($this->request->data); die;
			$this->MeetingMinute->create();
			if( !in_array($this->request->data['MeetingMinute']['minute_taker'], $this->request->data['MeetingMinute']['attendees']) && !in_array($this->request->data['MeetingMinute']['minute_taker'], $this->request->data['MeetingMinute']['cc_list']))
				array_push($this->request->data['MeetingMinute']['attendees'], $this->request->data['MeetingMinute']['minute_taker']);
			if(!empty($this->request->data['MeetingMinute']['cc_list']))
				$list_user = array_merge($this->request->data['MeetingMinute']['attendees'], $this->request->data['MeetingMinute']['cc_list']);
			else
				$list_user = $this->request->data['MeetingMinute']['attendees'];
			$this->request->data['MeetingMinute']['date'] = sqlFormatDate($this->request->data['MeetingMinute']['date']);
			$this->request->data['MeetingMinute']['attendees'] = json_encode($this->request->data['MeetingMinute']['attendees']);
			$this->request->data['MeetingMinute']['cc_list'] = json_encode($this->request->data['MeetingMinute']['cc_list']);
			foreach ($this->request->data['NoteDetail'] as $key => $value) {
				if(!empty($value['due_date']))
					$this->request->data['NoteDetail'][$key]['due_date'] = sqlFormatDateTime($value['due_date']);
			}
			if($this->MeetingMinute->saveAll($this->request->data)) {
				$id = $this->MeetingMinute->id;
				/*if($this->MeetingMinute->title == $this->request->data['MeetingMinute']['title'])
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
					$content = '<p>Title: <a href="'. Router::url(array('plugin' => false, 'controller' => 'MeetingMinutes', 'action' => 'view', $id), true) .'">'. $this->request->data['MeetingMinute']['title'] .'</a></p>';
					$content .= '<p>Date: '. formatDate($this->request->data['MeetingMinute']['date']) .'</p>';
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
		$this->MeetingMinute->id = $id;
		if (!$this->MeetingMinute->exists()) {
			throw new NotFoundException(__('Invalid Meeting Minute'));
		}
		if($this->request->is(array('post', 'put'))) {
			$this->request->data['MeetingMinute']['id'] = $id;
			if( !in_array($this->request->data['MeetingMinute']['minute_taker'], $this->request->data['MeetingMinute']['attendees']) && !in_array($this->request->data['MeetingMinute']['minute_taker'], $this->request->data['MeetingMinute']['cc_list']))
				array_push($this->request->data['MeetingMinute']['attendees'], $this->request->data['MeetingMinute']['minute_taker']);
			$this->request->data['MeetingMinute']['date'] = sqlFormatDate($this->request->data['MeetingMinute']['date']);
			$this->request->data['MeetingMinute']['attendees'] = json_encode($this->request->data['MeetingMinute']['attendees']);
			$this->request->data['MeetingMinute']['cc_list'] = json_encode($this->request->data['MeetingMinute']['cc_list']);

			foreach ($this->request->data['NoteDetail'] as $key => $value) {
				if(!empty($value['due_date']))
					$this->request->data['NoteDetail'][$key]['due_date'] = sqlFormatDateTime($value['due_date']);
			}
			if($this->MeetingMinute->saveAll($this->request->data)) {
				$this->Session->setFlash(__('The Meeting minute has been saved'));
				$this->redirect(array('action' => 'index'));
			}
		}
		else {
			$this->request->data = $this->MeetingMinute->read(null, $id);
			$this->request->data['MeetingMinute']['date'] = formatDate($this->request->data['MeetingMinute']['date']);
			$this->request->data['MeetingMinute']['attendees'] = json_decode($this->request->data['MeetingMinute']['attendees'], true);
			$this->request->data['MeetingMinute']['cc_list'] = json_decode($this->request->data['MeetingMinute']['cc_list'], true);
			$note_details = $this->NoteDetail->getByNote($id);
			$this->set('note_details', $note_details);
		}
	}

	public function view($id) {
		$this->request->data = $this->MeetingMinute->read(null, $id);
		$this->request->data['MeetingMinute']['date'] = formatDate($this->request->data['MeetingMinute']['date']);
		$this->request->data['MeetingMinute']['attendees'] = json_decode($this->request->data['MeetingMinute']['attendees'], true);
		$this->request->data['MeetingMinute']['cc_list'] = json_decode($this->request->data['MeetingMinute']['cc_list'], true);
		$note_details = $this->NoteDetail->getByNote($id);
		$this->set('note_details', $note_details);
	}

	public function delete($id)
	{
		$this->MeetingMinute->id = $id;
		// $data = $this->MeetingMinute->read(null, $id);
		if ($this->MeetingMinute->delete($id)){
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

