<?php

App::uses('AppController', 'Controller');

class CurrenciesController extends AppController{
	
	public $uses = array('Currency', 'Country');
	public $paginate = array(
        'fields' => 'Currency.*, Country.name',
        'joins' => array(
            array(
                'table' => 'countries',
                'alias' => 'Country',
                'type' => 'LEFT',
                'conditions' => array('Currency.country_id = Country.id')
            )
        ),
		'order' => array(
			'Currency.order' => 'asc'
		),
		'limit' => ROWPERPAGE
	);
	public function beforeFilter() {
		parent::beforeFilter();
	}

    public function index(){
        $this->Currency->recursive = 0;
        $this->Currency->virtualFields = array(
            'country_name' => 'Country.name'
        );
        $this->set('data', $this->paginate('Currency'));
    }
    public function add(){
		//gender country select list
		$countries = $this->Country->find('all');
		$country_option = array();
		foreach($countries as $item){
			$country_option[$item['Country']['id']] = $item['Country']['name'];
		}
		$this->set('countries', $country_option);
		
        if ($this->request->is('post')){
            $this->Currency->create();
            if ($this->Currency->save($this->request->data)){
                $this->Session->setFlash(__('The currency has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(
                __('The currency could not be saved. Please, try again.')
            );
        }
		$this->render('edit');
    }
    public function edit($id = null) {
		//gender country select list
		$countries = $this->Country->find('all');
		$country_option = array();
		foreach($countries as $item){
			$country_option[$item['Country']['id']] = $item['Country']['name'];
		}
		$this->set('countries', $country_option);
		
		
        $this->Currency->id = $id;
        if (!$this->Currency->exists()) {
            throw new NotFoundException(__('Invalid currency'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Currency->save($this->request->data)) {
                $this->Session->setFlash(__('The currency has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(
                __('The currency could not be saved. Please, try again.')
            );
        } else {
            $this->request->data = $this->Currency->read(null, $id);
        }
    }

    public function delete($id = null) {
        $this->Currency->id = $id;
        if (!$this->Currency->exists()) {
            throw new NotFoundException(__('Invalid currency'));
        }
        if ($this->Currency->delete()) {
            $this->Session->setFlash(__('Currency deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Currency was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
	public function ajaxSort(){
		if($this->request->is('post')){
			parse_str($_POST['pages'], $pageOrder);
			foreach($pageOrder['rowsort'] as $key => $value){
				$data = array('id' => $value, 'order' => $key);
				$this->Currency->save($data);
			}
			die('1');
		}else{
			return $this->redirect(array('action' => 'index'));
		}
	}
}
