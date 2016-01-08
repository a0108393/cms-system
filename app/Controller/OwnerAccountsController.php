<?php
App::uses('AppController', 'Controller');

class OwnerAccountsController extends AppController{
	
	public $uses = array('OwnerAccount', 'Country');
	
	function beforeRender()
    {
        parent::beforeRender();
		$countries = $this->Country->find('list', array('fields' => 'id, name'));
        $this->set(compact('countries'));
	}
	public function index()
	{
		if ($this->request->is('post') || $this->request->is('put')){
			if(!empty($this->request->data['OwnerAccount']['id']))
				$this->OwnerAccount->id = $this->request->data['OwnerAccount']['id'];
			if(isset($this->request->data['OwnerAccount']['logo'])){
				$this->request->data['OwnerAccount']['logo'] = json_encode($this->request->data['OwnerAccount']['logo']);
			}
            $this->OwnerAccount->create();
            if ($this->OwnerAccount->save($this->request->data)){
                $this->Session->setFlash(__('The owner account has been saved'));
                return $this->redirect(array('plugin' => false, 'controller' => 'OwnerAccounts', 'action' => 'index'));
            } 
        }
		else {
			$item = $this->OwnerAccount->find('first', array(
				'order' => array('OwnerAccount.id' => 'desc')
			));
			if(!empty($item)) {
				$this->request->data = $this->OwnerAccount->read(null, $item['OwnerAccount']['id']);
				$this->set('item', $item);
			}
        }
	}
}
