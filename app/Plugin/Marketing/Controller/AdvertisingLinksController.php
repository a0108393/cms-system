<?php
class AdvertisingLinksController extends AppController {
	var $layout = 'advertisinglink';
	public $uses = array('Marketing.AdvertisingLink','Marketing.Channel','Marketing.LinkVisit','Marketing.Enquiry', 'History');

	public $paginate = array(
		'conditions' => array(
			'AdvertisingLink.history_status' => 1
		),
		'joins' => array(
			array(
				'table' => 'marketing_channels', 
				'alias' => 'Channel', 
				'type' => 'LEFT', 
				'conditions' => array('Channel.id = AdvertisingLink.marketing_channels_id')
			),
			array(
				'table' => 'marketing_enquiries', 
				'alias' => 'Enquiries', 
				'type' => 'LEFT', 
				'conditions' => array('Enquiries.marketing_advertising_links_id = AdvertisingLink.id')
			),
		),
	   'group' => 'AdvertisingLink.id',
	   'limit' => ROWPERPAGE
	);
	function beforeRender()
    {
        parent::beforeRender();
		$channels = $this->Channel->find('list',array(
			'fields' => 'id,name',
			'conditions' => array(
				'Channel.history_status' => 1
			),
			'permissionable' => false
		));
        $this->set(compact('channels'));
    }

	public function index() {
		$this->AdvertisingLink->recursive = 0;
		$this->AdvertisingLink->virtualFields = array(
			'channel_name' => 'Channel.name',
			'no_enquiry' => 'COUNT(Enquiries.id)',
			'conversion' => 'IF(visits = 0, 0, COUNT(Enquiries.id)/visits)',
		);
        $this->set('links', $this->paginate('AdvertisingLink'));
	}
	function add()
	{
		if ($_POST) {
			// _get_rand_alphanumeric
			
			if(strpos($this->request->data['AdvertisingLink']['destination_url'],'http') === FALSE){
				if(substr($this->request->data['AdvertisingLink']['destination_url'],0,1) == '/'){
					$this->request->data['AdvertisingLink']['destination_url'] = Router::url('/', true) . substr($this->request->data['AdvertisingLink']['destination_url'],1);
				}
				else{
					$this->request->data['AdvertisingLink']['destination_url'] = Router::url('/', true) . $this->request->data['AdvertisingLink']['destination_url'];
				}
			}
			$this->request->data['AdvertisingLink']['generated_url'] = $this->_get_rand_alphanumeric(5);
            if ($this->AdvertisingLink->save($this->request->data)) {
                $this->Session->setFlash('Your advertising link has been saved.');
                $this->redirect(array('plugin' => 'marketing', 'controller' => 'advertisingLinks', 'action' => 'index'));
            }
        }
	}
	function edit($id)
	{
		$this->AdvertisingLink->id = $id;
		$data = $this->AdvertisingLink->read();
		if (empty($this->data)) {
			$this->data = $data;
		} 
		else {
			if(strpos($this->request->data['AdvertisingLink']['destination_url'],'http') === FALSE){
				if(substr($this->request->data['AdvertisingLink']['destination_url'],0,1) == '/'){
					$this->request->data['AdvertisingLink']['destination_url'] = Router::url('/', true) . substr($this->request->data['AdvertisingLink']['destination_url'],1);
				}
				else{
					$this->request->data['AdvertisingLink']['destination_url'] = Router::url('/', true) . $this->request->data['AdvertisingLink']['destination_url'];
				}
			}
			$diff = array_diff($this->data['AdvertisingLink'], $data['AdvertisingLink']);
			if(!empty($diff)) {
				$this->request->data['AdvertisingLink']['history_link'] = 0;
			}
			if ($this->AdvertisingLink->save($this->data)) {
				//save history
				$diff = array_diff($this->data['AdvertisingLink'], $data['AdvertisingLink']);
				if(!empty($diff)) {
					$data['AdvertisingLink']['generated_url'] = $data['AdvertisingLink']['generated_url'] . '-bk' . substr(md5(microtime()),rand(0,26),5);
					$data['AdvertisingLink']['description'] = $data['AdvertisingLink']['description'] . '-bk' . substr(md5(microtime()),rand(0,26),5);
					$status = '';
					if(in_array('description', array_keys($diff)))
						$status .= (empty($status)) ? __('Change description') : ', ' . __('Change description');
					if(in_array('marketing_channels_id', array_keys($diff)))
						$status .= (empty($status)) ? __('Change channel') : ', ' . __('Change channel');
					if(in_array('destination_url', array_keys($diff)))
						$status .= (empty($status)) ? __('Change destination link') : ', ' . __('Change destination link');
					if(in_array('generated_url', array_keys($diff))) {
						$status .= (empty($status)) ? __('Change generated link') : ', ' . __('Change generated link');
					}
					unset($data['AdvertisingLink']['id']);
					$data['AdvertisingLink']['history_status'] = 2;
					$data['AdvertisingLink']['history_link'] = $id;
					
					$this->AdvertisingLink->create();
					$this->AdvertisingLink->disablePermissionable('AdvertisingLink');
					if($this->AdvertisingLink->save($data)) {
						
						$history['History']['plugin'] = 'marketing';
						$history['History']['controller'] = 'advertisingLinks';
						$history['History']['action'] = 'view';
						$history['History']['history_id'] = $this->AdvertisingLink->id;
						$history['History']['action_status'] = $status;
						$history['History']['original_id'] = $id;
						$history['History']['user_modified'] = $this->Session->read('Auth.User.id');
						$history['History']['date_modified'] = gmdate('Y-m-d H:i:s');
						$this->History->save($history);
					}
					else {
						prd( $this->AdvertisingLink->validationErrors);
					}
				}
				$this->Session->setFlash('Your advertising link has been updated.');
				$this->redirect(array('plugin' => 'marketing', 'controller' => 'advertisingLinks', 'action' => 'index'));
			}
		}
	}

	public function view($id)
	{
		$this->data = $this->AdvertisingLink->read(null, $id);
	}

	public function history($id)
	{
		$this->paginate = array(
			'joins' => array(
				array(
					'table' => 'marketing_channels', 
					'alias' => 'Channel', 
					'type' => 'LEFT', 
					'conditions' => array('Channel.id = AdvertisingLink.marketing_channels_id')
				)
			),
			'conditions' => array(
				'AdvertisingLink.history_link' => $id
			),
			'permissionable' => false,
			'order' => 'AdvertisingLink.id ASC',
		  	'limit' => ROWPERPAGE
		);
		$this->AdvertisingLink->virtualFields = array(
			'channel_name' => 'Channel.name'
		);
		$this->set('links', $this->paginate('AdvertisingLink'));
	}

	function delete($id)
	{
		$links = $this->Enquiry->find('all',array('conditions'=>array('marketing_advertising_links_id' => $id)));
		if(empty($links)) {
			$data = $this->AdvertisingLink->read(null, $id);
			$data['AdvertisingLink']['history_status'] = 2;
			$data['AdvertisingLink']['history_link'] = -1;
			if ($this->AdvertisingLink->save($data)) {
					
				$history['History']['plugin'] = 'marketing';
				$history['History']['controller'] = 'advertisingLinks';
				$history['History']['action'] = 'view';
				$history['History']['history_id'] = $id;
				$history['History']['action_status'] = __('Delete');
				$history['History']['user_modified'] = $this->Session->read('Auth.User.id');
				$history['History']['date_modified'] = gmdate('Y-m-d H:i:s');
				$this->History->save($history);
				$this->Session->setFlash('The advertising link with id: ' . $id . ' has been deleted.');
				$this->redirect(array('plugin' => 'marketing', 'controller' => 'advertisingLinks', 'action' => 'index'));
			}
			else {
				prd( $this->AdvertisingLink->validationErrors);
			}
		}
		else {
			$this->Session->setFlash('There are enquiries associated with the Link.');
			$this->redirect(array('plugin' => 'marketing', 'controller' => 'advertisingLinks', 'action' => 'index'));
		}
	}
	/*
	* redirect the visitor to when this URL is clicked
	* var string link
	*/
	function go($link)
	{
		$this->autoRender = false; 
		if($link) {
			
			$data = $this->AdvertisingLink->find("first",array('conditions'=>array('AdvertisingLink.generated_url'=>$link)));
			if(count($data) > 0) {
				$check = $this->LinkVisit->find('all',array(
					'conditions' => array(
						'date_format(time_click,\'%d\')' => gmdate('d'),
						'client_ip'=>$this->request->clientIp(),
						'marketing_advertising_links_id' => $data['AdvertisingLink']['id'])
				));
				if(empty($check)){
					$arr['LinkVisit']['marketing_advertising_links_id'] = $data['AdvertisingLink']['id'];
					$arr['LinkVisit']['time_click'] = gmdate('Y-m-d H:i:s');;
					$arr['LinkVisit']['client_ip'] = $this->request->clientIp();
					
					$data['AdvertisingLink']['visits']++;
					if ($this->AdvertisingLink->save($data)) {
						$this->LinkVisit->save($arr);
						$this->redirect($data['AdvertisingLink']['destination_url']);
					}
				}
				else{
					$this->redirect($data['AdvertisingLink']['destination_url']);
				}
			}
			else {
				$this->Session->setFlash('Link error!');
				$this->redirect(array('action' => 'index'));
			}
		}
	}
	function _get_rand_alphanumeric($length) {
		// inicializa variables
        $password = "";
        $i = 0;
        $possible = "0123456789bcdfghjkmnpqrstvwxyz"; 
        
        // agrega random
        while ($i < $length){
            $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
            
            if (!strstr($password, $char)) { 
                $password .= $char;
                $i++;
            }
        }
        return $password; 
	}
}