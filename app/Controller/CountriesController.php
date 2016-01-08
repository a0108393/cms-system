<?php

App::uses('AppController', 'Controller');

class CountriesController extends AppController{
	
	public $paginate = array(
        'order' => array(
			'Country.name' => 'asc'
		),
		'limit' => ROWPERPAGE
    );
	
	public function beforeFilter() {
		parent::beforeFilter();
	}
	
    public function index(){
        $this->Country->recursive = 0;
        $this->set('data', $this->paginate('Country'));
    }
    public function add() {
        if ($this->request->is('post')){
            $this->Country->create();
            if ($this->Country->save($this->request->data)){
                $this->Session->setFlash(__('The country has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(
                __('The country could not be saved. Please, try again.')
            );
        }
		$this->render('edit');
    }
    public function edit($id = null) {
        $this->Country->id = $id;
        if (!$this->Country->exists()) {
            throw new NotFoundException(__('Invalid country'));
        }
        if($this->request->is('post') || $this->request->is('put')) {
            if ($this->Country->save($this->request->data)) {
                $this->Session->setFlash(__('The country has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(
                __('The country could not be saved. Please, try again.')
            );
        } else {
            $this->request->data = $this->Country->read(null, $id);
        }
    }

    public function delete($id = null) {
        $this->Country->id = $id;
        if (!$this->Country->exists()) {
            throw new NotFoundException(__('Invalid country'));
        }
        if ($this->Country->delete()) {
            $this->Session->setFlash(__('Country deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Country was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
	public function ajaxSort(){
		if($this->request->is('post')){
			parse_str($_POST['pages'], $pageOrder);
			foreach($pageOrder['rowsort'] as $key => $value){
				$data = array('id' => $value, 'order' => $key);
				$this->Country->save($data);
			}
			die('1');
		}else{
			return $this->redirect(array('action' => 'index'));
		}
	}
}
