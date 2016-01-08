<?php

App::uses('AppController', 'Controller');

class CategoriesController extends AppController{
	public $uses = array('Accounting.Category', 'Accounting.Service', 'History');
	public $paginate = array(
		'conditions' => array('Category.history_status' => 1),
        'order' => array(
            'Category.name' => 'asc'
        ),
		'limit' => ROWPERPAGE
    );
	public function index() {
		$this->Category->recursive = 0;
        $data = $this->paginate('Category');
        $this->set('data', $data);
	}
	function add()
	{
		if ($_POST) {
            if ($this->Category->save($this->request->data)) {
                $this->Session->setFlash('The category has been saved.');
                $this->redirect(array('action' => 'index'));
            }
        }
		$this->render('edit');
	}
	function edit($id)
	{
		$this->Category->id = $id;
		$data = $this->Category->read();
		if (empty($this->data)) {
			$this->data = $data;
		} else {
			if ($this->Category->save($this->data)) {
				if($data['Category']['name'] != trim($this->data['Category']['name'])) {
					unset($data['Category']['id']);
					$data['Category']['history_status'] = 2;
					$data['Category']['name'] = $data['Category']['name'] . '-bk' . substr(md5(microtime()),rand(0,26),5);
					$this->Category->create();

					if($this->Category->save($data)) {
						
						$history['History']['plugin'] = 'accounting';
						$history['History']['controller'] = 'Categories';
						$history['History']['action'] = 'view';
						$history['History']['history_id'] = $this->Category->id;
						$history['History']['action_status'] = __('Change name');
						$history['History']['original_id'] = $id;
						$history['History']['user_modified'] = $this->Session->read('Auth.User.id');
						$history['History']['date_modified'] = gmdate('Y-m-d H:i:s');
						$this->History->save($history);
					}
					else {
						prd( $this->Category->validationErrors);
					}
				}
				$this->Session->setFlash('The category has been updated.');
				$this->redirect(array('action' => 'index'));
			}
		}
	}

	public function view($id)
	{
		$this->Category->id = $id;
		$this->data = $this->Category->read();
		$this->render('edit');
	}

	function delete($id)
	{
		$service = $this->Service->find('all',array('conditions'=>array('category_id' => $id, 'Service.history_status' => 1)));
		if(empty($service)){
			$data = $this->Category->read(null, $id);
			
			if ($this->Category->delete($id)) {
				unset($data['Category']['id']);
				$data['Category']['history_status'] = 2;
				$data['Category']['name'] = $data['Category']['name'] . '-bk' . substr(md5(microtime()),rand(0,26),5);
				$this->Category->create();
				if($this->Category->save($data)) {
					
					$history['History']['plugin'] = 'accounting';
					$history['History']['controller'] = 'Categories';
					$history['History']['action'] = 'view';
					$history['History']['history_id'] = $this->Category->id;
					$history['History']['action_status'] = __('Delete');
					$history['History']['user_modified'] = $this->Session->read('Auth.User.id');
					$history['History']['date_modified'] = gmdate('Y-m-d H:i:s');
					$this->History->save($history);
				}
				else {
					prd( $this->Category->validationErrors);
				}
				$this->Session->setFlash('The category with id: ' . $id . ' has been deleted.');
				$this->redirect(array('action' => 'index'));
			}
		}
		else{
			$this->Session->setFlash('The category has service belonging to that.');
			$this->redirect(array('action' => 'index'));
		}	
	}
}