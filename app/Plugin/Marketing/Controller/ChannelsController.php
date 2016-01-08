<?php
class ChannelsController extends AppController {
	var $name = 'Channels';
	public $uses = array('Marketing.AdvertisingLink','Marketing.Channel', 'History');

	public $paginate = array(
		'conditions' => array(
			'Channel.history_status' => 1
		),
	   'limit' => ROWPERPAGE
	);

	public function index() {
		$this->Channel->recursive = 0;
        $this->set('channels', $this->paginate('Channel'));
	}
	function add()
	{
		if ($_POST) {
            if ($this->Channel->save($this->request->data)) {
                $this->Session->setFlash('The channel has been saved.');
                $this->redirect(array('action' => 'index'));
            }
        }
        $this->render('edit');
	}
	function edit($id)
	{
		$this->Channel->id = $id;
		$data = $this->Channel->read();
		if (empty($this->data)) {
			$this->data = $data;
		} else {
			$diff = array_diff($this->data['Channel'], $data['Channel']);
			if(!empty($diff)) {
				$this->request->data['Channel']['history_link'] = 0;
			}
			if ($this->Channel->save($this->data)) {
				// check edit name and save history
				if(!empty($diff)) {
					unset($data['Channel']['id']);
					$data['Channel']['history_status'] = 2;
					$data['Channel']['history_link'] = $id;
					$data['Channel']['name'] = $data['Channel']['name'] . '-bk' . substr(md5(microtime()),rand(0,26),5);
					$this->Channel->create();
					$this->Channel->disablePermissionable('Channel');
					if($this->Channel->save($data)) {
						
						$history['History']['plugin'] = 'marketing';
						$history['History']['controller'] = 'Channels';
						$history['History']['action'] = 'view';
						$history['History']['history_id'] = $this->Channel->id;
						$history['History']['action_status'] = __('Change name');
						$history['History']['original_id'] = $id;
						$history['History']['user_modified'] = $this->Session->read('Auth.User.id');
						$history['History']['date_modified'] = gmdate('Y-m-d H:i:s');
						$this->History->save($history);
					}
					else {
						prd( $this->Channel->validationErrors);
					}
				}
				$this->Session->setFlash('The channel has been updated.');
				$this->redirect(array('action' => 'index'));
			}
		}
	}
	public function view($id)
	{
		$this->request->data = $this->Channel->read(null, $id);
		$this->render('edit');
	}

	public function history($id)
	{
		$this->paginate = array(
			'conditions' => array(
				'Channel.history_link' => $id
			),
			'permissionable' => false,
			'order' => 'Channel.id ASC',
		   'limit' => ROWPERPAGE
		);
		$this->set('channels', $this->paginate('Channel'));
	}

	function delete($id)
	{
		$links = $this->AdvertisingLink->find('all',array('conditions'=>array('marketing_channels_id' => $id, 'AdvertisingLink.history_status' => 1)));
		if(empty($links)){
			$data = $this->Channel->read(null, $id);
			$data['Channel']['history_status'] = 2;
			$data['Channel']['history_link'] = -1;
			if ($this->Channel->save($data)) {
				$history['History']['plugin'] = 'marketing';
				$history['History']['controller'] = 'Channels';
				$history['History']['action'] = 'view';
				$history['History']['history_id'] = $id;
				$history['History']['action_status'] = __('Delete');
				$history['History']['user_modified'] = $this->Session->read('Auth.User.id');
				$history['History']['date_modified'] = gmdate('Y-m-d H:i:s');
				$this->History->save($history);
				
				$this->Session->setFlash('The channel with id: ' . $id . ' has been deleted.');
				$this->redirect(array('action' => 'index'));
			}
			else {
				prd( $this->Channel->validationErrors);
			}
		}
		else{
			$this->Session->setFlash('The channel has links belonging to that.');
			$this->redirect(array('action' => 'index'));
		}	
	}
	
}