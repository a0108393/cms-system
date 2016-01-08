<?php
class EventsController extends AppController{
	var $name = 'Events';
	public $uses = array('Marketing.Event', 'History');

	public $paginate = array(
		'conditions' => array(
			'Event.history_status' => 1
		),
	   'limit' => ROWPERPAGE
	);
	
	public function index() {
		$this->Event->recursive = 0;
        $this->set('events', $this->paginate('Event'));
		// $this->set('channels', $this->Channel->find('all',array('order'=>'Channel.id desc')));
	}
	function add()
	{
		if ($_POST) {
			$data = $this->request->data;
			$data['Event']['start_date'] = sqlFormatDate($data['Event']['start_date']);
			$data['Event']['end_date'] = sqlFormatDate($data['Event']['end_date']);
			if(strtotime($data['Event']['end_date']) < strtotime($data['Event']['start_date'])) {
				$this->Session->setFlash('End date cannot be earlier than start date!');
			}
			else {
				if ($this->Event->save($data)) {
					$this->Session->setFlash('Your event has been saved.');
					$this->redirect(array('action' => 'index'));
				}
				else {
					prd($this->Event->validationErrors);
				}
			}
        }
        $this->render('edit');
	}
	function edit($id)
	{
		$this->Event->id = $id;
		$data = $this->Event->read();
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['Event']['start_date'] = sqlFormatDate($this->request->data['Event']['start_date']);
			$this->request->data['Event']['end_date'] = sqlFormatDate($this->request->data['Event']['end_date']);
			if(strtotime($this->request->data['Event']['end_date']) < strtotime($this->request->data['Event']['start_date'])){
				$this->Session->setFlash('End date cannot be earlier than start date!');
			}
			else{
				$diff = array_diff($this->data['Event'], $data['Event']);
				if(!empty($diff)) {
					$this->request->data['Event']['history_link'] = 0;
				}
				if ($this->Event->save($this->request->data)) {
					//save history
					if(!empty($diff)) {
						$status = '';
						if(in_array('name', array_keys($diff)))
							$status .= (empty($status)) ? __('Change name') : ', ' . __('Change name');
						if(in_array('start_date', array_keys($diff)))
							$status .= (empty($status)) ? __('Change start date') : ', ' . __('Change start date');
						if(in_array('end_date', array_keys($diff)))
							$status .= (empty($status)) ? __('Change end date') : ', ' . __('Change end date');
						unset($data['Event']['id']);
						$data['Event']['history_status'] = 2;
						$data['Event']['history_link'] = $id;
						$data['Event']['name'] = $data['Event']['name'] . '-bk' . substr(md5(microtime()),rand(0,26),5);
						$this->Event->create();
						$this->Event->disablePermissionable('Event');
						if($this->Event->save($data)) {
							
							$history['History']['plugin'] = 'marketing';
							$history['History']['controller'] = 'Events';
							$history['History']['action'] = 'view';
							$history['History']['history_id'] = $this->Event->id;
							$history['History']['action_status'] = $status;
							$history['History']['original_id'] = $id;
							$history['History']['user_modified'] = $this->Session->read('Auth.User.id');
							$history['History']['date_modified'] = gmdate('Y-m-d H:i:s');
							$this->History->save($history);
						}
						else {
							prd( $this->Event->validationErrors);
						}
					}
					$this->Session->setFlash('Your event has been updated.');
					$this->redirect(array('action' => 'index'));
				}
			}
		}
		else {
			$data['Event']['start_date'] = formatDate($data['Event']['start_date']);
			if(strtotime($data['Event']['end_date']) != 0)
				$data['Event']['end_date'] = formatDate($data['Event']['end_date']);
			else
				$data['Event']['end_date'] = "";
			$this->request->data = $data;
		}
		if (empty($this->data)) {
			
		} else {
			$data = $this->data;
			
		}
	}

	public function view($id)
	{
		$this->request->data = $this->Event->read(null, $id);
		$this->render('edit');
	}

	public function history($id)
	{
		$this->paginate = array(
			'conditions' => array(
				'Event.history_link' => $id
			),
		   'permissionable' => false,
			'order' => 'Event.id ASC',
		   'limit' => ROWPERPAGE
		);
		$this->set('data', $this->paginate('Event'));
	}

	function delete($id)
	{
		$this->Event->id = $id;
        if (!$this->Event->exists()) {
            throw new NotFoundException(__('Invalid Event'));
        }
    	$data = $this->Event->read();
    	$data['Event']['history_status'] = 2;
		$data['Event']['history_link'] = -1;
		if ($this->Event->save($data)) {
			$history['History']['plugin'] = 'marketing';
			$history['History']['controller'] = 'Events';
			$history['History']['action'] = 'view';
			$history['History']['history_id'] = $id;
			$history['History']['action_status'] = __('Delete event');
			$history['History']['user_modified'] = $this->Session->read('Auth.User.id');
			$history['History']['date_modified'] = gmdate('Y-m-d H:i:s');
			$this->History->save($history);
			$this->Session->setFlash('The event with id: ' . $id . ' has been deleted.');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Event was not deleted'));
        return $this->redirect(array('action' => 'index'));
	}
}