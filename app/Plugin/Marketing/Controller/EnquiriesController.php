<?php
class EnquiriesController extends AppController {
	
	public $uses = array('Marketing.Enquiry',
						 'Marketing.Channel',
						 'Marketing.AdvertisingLink',
						 'Marketing.Affiliate',
						 'User');
	
	
	var $paginate = array(
			'joins' => array(
				array(
					'table' => 'marketing_channels', 
					'alias' => 'Channel', 
					'type' => 'LEFT', 
					'conditions' => array('Channel.id = Enquiry.marketing_channels_id')
				)
			),
			'permissionable' => false,
			'group' => 'Enquiry.id',
			'limit' => ROWPERPAGE,
			'order' => array(
			'Enquiry.id' => 'DESC'
		)
	);
	function beforeRender(){
        parent::beforeRender();
		/*$user = CakeSession::read("Auth.User");
		if ( $user['group_id'] == 5 ) 
			$defaultAgent = $user['id'];
		else*/
			$defaultAgent = '';
		
		$userAgents = $this->User->find('list', array(
			'joins' => array(
				array(
					'table' => 'users_groups',
					'alias' => 'UsersGroup',
					'type' => 'LEFT',
					'conditions' => array('UsersGroup.user_id = User.id')
				)
			),
			'fields' => 'id, name',
			'conditions'=> array(
				'UsersGroup.group_id' => (int) Configure::read('Settings.Company.SalesStaffGroupId')
			)
		));
		$channels = $this->Channel->find('list',array('fields' => 'id,name', 'permissionable' => false, 'conditions' => array('Channel.history_status' => 1)));
        $this->set(compact('userAgents','defaultAgent', 'channels'));
    }
	public function index() {
		$this->Enquiry->recursive = 0;
        //$this->set('affiliates', $this->paginate('Enquiry'));
		$this->Enquiry->virtualFields = array(
			'channel_name' => 'Channel.name',
		);
		$data = $this->paginate('Enquiry');
        $this->set('data', $data);
		// $this->set('channels', $this->Channel->find('list',array('fields' => 'id,name')));
	}
	function add_client(){
		$this->layout = 'login';
		$marketing_adv = $this->AdvertisingLink->find('list',array(
			'fields'=>'id,description',
			'conditions' => array(
				'AdvertisingLink.history_status' => 1
			),
			'permissionable' => false
			// 'conditions' => array(
				// 'AdvertisingLink.marketing_channels_id'=>(int) Configure::read('marketing.default_channel')
			// ),
			// 'recursive' => -1
		));
		$this->set('files',array());
		//$this->set('channels', $this->Channel->find('list',array('fields' => 'id,name')));
		$this->set('advertisingLinks',$marketing_adv);	
		$this->set('affiliates', $this->Affiliate->find('list',array(
			'fields' => 'id,name',
			'joins' => array(
				array(
					'table' => 'users_groups',
					'alias' => 'UsersGroup',
					'type' => 'LEFT',
					'conditions' => array('Affiliate.id = UsersGroup.user_id')
				)
			),
			'conditions' => array(
				'UsersGroup.group_id' => (int) Configure::read('Settings.Company.AffiliateGroupId'),
				'Affiliate.active' => 1
			)
		)));
		
		if ( $this->request->is('post') || $this->request->is('put')){
			$this->Captcha = $this->Components->load('Captcha');
			$this->Enquiry->setCaptcha($this->Captcha->getVerCode());
			
			$this->Enquiry->create();
			$this->request->data['Enquiry']['agent_id'] = CakeSession::read("Auth.User.id");;
			$this->request->data['Enquiry']['enq_date'] = date('Y-m-d');
			$this->request->data['Enquiry']['enquiry_time'] = gmdate('Y-m-d H:i:s');
			if( isset( $this->request->data['Enquiry']['files'] ) ){
				$dataImage = $this->request->data['Enquiry']['files'];
				$this->request->data['Enquiry']['file_list'] = json_encode($dataImage);
			}else{
				$this->request->data['Enquiry']['file_list'] = null;
			}
            if ($this->Enquiry->save($this->request->data)){
                $this->Session->setFlash(__('Thank you!'));
                return $this->redirect(array('action' => 'add_client'));
            }
		}
		$this->render('edit_client');
		
	}
	function add(){
		$marketing_adv = $this->AdvertisingLink->find('list',array(
			'fields'=>'id,description',
			'conditions' => array('AdvertisingLink.marketing_channels_id'=>(int) Configure::read('Settings.Company.DefaultChannel'), 'AdvertisingLink.history_status' => 1),
			'recursive' => -1,
			'permissionable' => false
		));
		$this->set('files',array());
		$this->set('advertisingLinks',$marketing_adv);	
		$this->set('affiliates', $this->Affiliate->find('list',array(
			'fields' => 'id,name',
			'joins' => array(
				array(
					'table' => 'users_groups',
					'alias' => 'UsersGroup',
					'type' => 'LEFT',
					'conditions' => array('UsersGroup.user_id = Affiliate.id')
				)
			),
			'conditions' => array(
				'UsersGroup.group_id' => (int) Configure::read('Settings.Company.AffiliateGroupId'),
				'Affiliate.active' => 1
			)
		)));
		
		if ( $this->request->is('post') || $this->request->is('put')){
			$this->request->data['Enquiry']['enq_date'] = sqlFormatDate($this->request->data['Enquiry']['enq_date']);
			$this->request->data['Enquiry']['enquiry_time'] = gmdate('Y-m-d H:i:s');
			$this->Captcha = $this->Components->load('Captcha');
			$this->Enquiry->setCaptcha($this->Captcha->getVerCode());
			if( isset( $this->request->data['Enquiry']['files'] ) ){
				$dataImage = $this->request->data['Enquiry']['files'];
				$this->request->data['Enquiry']['file_list'] = json_encode($dataImage);
			}else{
				$this->request->data['Enquiry']['file_list'] = null;
			}
			$this->Enquiry->create();
			$this->request->data['Enquiry']['agent_id'] = CakeSession::read("Auth.User.id");
			//print_r( $this->request->data );die;
            if ($this->Enquiry->save($this->request->data)){
                $this->Session->setFlash(__('Enquiry has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
		}
		$this->render('edit');
	}
	function edit($id = null){
		$this->Enquiry->id = $id;
		
        if (!$this->Enquiry->exists()) {
            throw new NotFoundException(__('Invalid Enquiry'));
        }
		$data = $this->Enquiry->read(null, $id);
		$data['Enquiry']['enq_date'] = formatDate($data['Enquiry']['enq_date']);
		//$this->set('channels', $this->Channel->find('list',array('fields' => 'id,name')));
		$this->set('affiliates', $this->Affiliate->find('list',array(
			'fields' => 'id,name',
			'joins' => array(
				array(
					'table' => 'users_groups',
					'alias' => 'UsersGroup',
					'type' => 'LEFT',
					'conditions' => array('UsersGroup.user_id = Affiliate.id')
				)
			),
			'conditions' => array(
				'UsersGroup.group_id' => (int) Configure::read('Settings.Company.AffiliateGroupId'),
				'Affiliate.active' => 1
			)
		)));
		$this->Captcha = $this->Components->load('Captcha');
		$this->Enquiry->setCaptcha($this->Captcha->getVerCode());
		$files_decode = json_decode($data['Enquiry']['file_list']);
		$this->set('files',$files_decode);
		$marketing_adv = array();
		$marketing_adv = $this->AdvertisingLink->find('list',array('fields'=>'id,description',
																   'conditions' => array('AdvertisingLink.marketing_channels_id'=>$data['Enquiry']['marketing_channels_id'], 'AdvertisingLink.history_status' => 1),
																   'recursive' => -1,
																   'permissionable' => false
																   ));
		$this->set('advertisingLinks',$marketing_adv);	
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['Enquiry']['enq_date'] = sqlFormatDate($this->request->data['Enquiry']['enq_date']);
			if( isset( $this->request->data['Enquiry']['files'] ) ){
				$dataImage = $this->request->data['Enquiry']['files'];
				$cv_files_decode = (array)$files_decode;
				$data_remove = array_diff($cv_files_decode,$dataImage);
				foreach( $data_remove as $key => $value ){
					$file = new File(WWW_ROOT . 'uploads/'.$value, false, 0777);
					$file->delete();
				}
				//print_r( $this->request->data['Enquiry'] );die;
				
				$this->request->data['Enquiry']['file_list'] = json_encode($dataImage);
			}else{
				$this->request->data['Enquiry']['file_list'] = null;
			}
			
            if ($this->Enquiry->save($this->request->data)) {
                $this->Session->setFlash(__('The Enquiry has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(
                __('The Enquiry could not be saved. Please, try again.')
            );
        }else{
			$this->request->data = $data;
		}
	}
	function delete($id){
		if ($this->Enquiry->delete($id)) {
			$this->Session->setFlash('The Enquiry with id: ' . $id . ' has been deleted.');
			$this->redirect(array('action' => 'index'));
		}
	}
	public function bindAjax(){
		if($this->request->is('post')){
			$channels_id = $this->request->data['Enquiry']['marketing_channels_id'];
			$this->set('advertisingLinks', $this->AdvertisingLink->find('list',
				array(
					'fields' => 'id,description',
					'conditions' => array('AdvertisingLink.marketing_channels_id'=>$channels_id, 'AdvertisingLink.history_status' => 1),
					'recursive' => -1,
					'permissionable' => false
				)
			));
		}
		
		$this->layout = 'ajax';
	}
	
	// bind value dropdown suggest in Affiliate
	public function bindSuggestAjax(){
		if($this->request->is('post')){
			$keyup = $this->request->data['Enquiry']['affiliate_id'];
			$data = $this->Affiliate->find('list',array(
											'fields' => 'id,text',
											'conditions' => array('Affiliate.text LIKE'=>$keyup.'%'),
											));
			$this->set('keyup', $keyup);
			$this->set('affiliate', $data);
			$this->layout = 'ajax';
		}
	}
	
	
}