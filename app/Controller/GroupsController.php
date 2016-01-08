<?php

App::uses('AppController', 'Controller');

class GroupsController extends AppController{
 
   public function beforeFilter(){
        parent::beforeFilter();
        $this->Auth->allow('add');
    }
	public $paginate = array(
		'joins' => array(
            array(
                'table' => 'users_groups',
                'alias' => 'UsersGroup',
                'type' => 'LEFT',
                'conditions' => array('UsersGroup.group_id = Group.id')
            ),
            array(
                'table' => 'users', 
                'alias' => 'User', 
                'type' => 'LEFT', 
                'conditions' => array('User.id = UsersGroup.user_id')
            )
        ),
		'group' => 'Group.id',
        'order' => array(
            'Group.name' => 'asc'
        ),
		'limit' => ROWPERPAGE
    );
	public function index(){
		$this->Group->recursive = 0;
		$this->Group->virtualFields = array(
			'no_user' => 'COUNT(User.id)',
		);
        $this->set('groups', $this->paginate());
	}
	
	public function add(){
		
		if ($this->request->is('post')){
            $this->Group->create();
			$this->request->data['Group']['created'] = gmdate('Y-m-d h:i:s');
            if ($this->Group->save($this->request->data)) {
                $this->Session->setFlash(__('The Group has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            /* $this->Session->setFlash(
                __('The Group could not be saved. Please, try again.')
            ); */
			$this->Common->flashErrorDisplay($this->Group->invalidFields());
        }
		$this->render('edit');
	}
	public function edit($id = null){
        $this->Group->id = $id;
        if (!$this->Group->exists()){
            throw new NotFoundException(__('Invalid Group'));
        }
        if ($this->request->is('post') || $this->request->is('put')){
			$this->request->data['Group']['modified'] = gmdate('Y-m-d h:i:s');
            if ($this->Group->save($this->request->data)) {
                $this->Session->setFlash(__('The group has been saved'));
                return $this->redirect(array('controller' => 'Groups', 'action' => 'index'));
            }
            $this->Session->setFlash(
                __('The group could not be saved. Please, try again.')
            );
        }else{
			$this->request->data = $this->Group->read(null, $id);
		}
    }
	public function delete($id = null) {
        $this->Group->id = $id;
        if (!$this->Group->exists()) {
            throw new NotFoundException(__('Invalid group'));
        }
        if ($this->Group->delete()) {
            $this->Session->setFlash(__('Group deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Group was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
}