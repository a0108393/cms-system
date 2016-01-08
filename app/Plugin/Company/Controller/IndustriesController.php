<?php

App::uses('AppController', 'Controller');

class IndustriesController extends AppController{
	
	public $uses = array('Company.Industry', 'Company.Company');
	
	public $paginate = array(
		'joins' => array(array('table' => 'company_companies', 'alias' => 'Company', 'type' => 'LEFT', 'conditions' => array('Company.industry_id = Industry.id'))),
		'group' => 'Industry.id',
        'order' => array(
            'Industry.name' => 'asc'
        ),
		'limit' => ROWPERPAGE
    );
	
    public function index(){
        $this->Industry->recursive = 0;
		$this->Industry->virtualFields = array(
			'no_company' => 'COUNT(Company.id)',
		);
		$data = $this->paginate('Industry');
        $this->set('data', $data);
    }

    public function view($id = null){
        $this->Industry->id = $id;
        if (!$this->Industry->exists()){
            throw new NotFoundException(__('Invalid Industry'));
        }
        $this->set('data', $this->Industry->read(null, $id));
    }

    public function add(){
        if ($this->request->is('post')){
			$this->request->data['Industry']['created'] = gmdate('Y-m-d h:i:s');
            $this->Industry->create();
            if ($this->Industry->save($this->request->data)){
                $this->Session->setFlash(__('The industry has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
			$this->Common->flashErrorDisplay($this->Industry->invalidFields());
        }
		$this->render('edit');
    }

    public function edit($id = null) {
        $this->Industry->id = $id;
        if (!$this->Industry->exists()) {
            throw new NotFoundException(__('Invalid industry'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Industry->save($this->request->data)) {
                $this->Session->setFlash(__('The industry has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(
                __('The industry could not be saved. Please, try again.')
            );
        } else {
            $this->request->data = $this->Industry->read(null, $id);
        }
    }

    public function delete($id = null){
        //$this->request->onlyAllow('post');
        $this->Industry->id = $id;
        if (!$this->Industry->exists()) {
            throw new NotFoundException(__('Invalid Industry'));
        }
		
		$this->Company->updateAll(array('Company.industry_id' => 0), array('Company.industry_id' => $id));
		
        if ($this->Industry->delete()){
            $this->Session->setFlash(__('Industry deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Industry was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
	public function import(){
		
		$file_name	= ( isset($_REQUEST['ax-file-name']) && !empty($_REQUEST['ax-file-name']) )?$_REQUEST['ax-file-name']:'';
		$full_size	= isset($_REQUEST['ax-file-size'])?$_REQUEST['ax-file-size']:0;
		$src = $_FILES['ax_file_input']['tmp_name'];
		$rows = array();
		$file = fopen($src, "r");
		$key = 0;
		while (($data = fgetcsv($file, 1000, ",")) !== FALSE)
		{
			$rows[$key] = trim($data[0]);
			$key++;
		}
		fclose($file);
		$data = array_unique(array_filter($rows));
		$olddata = $this->Industry->find('list');
		$check_duplicate = array_intersect($olddata, $data);
		if(!empty($check_duplicate)){
			$info = "Some names are ready in database: \n";
			foreach($check_duplicate as $dup){
				$info .= $dup . "\n";
			}
			echo json_encode(array('name'=>$file_name, 'size'=>$full_size, 'status'=>-1, 'info'=>$info));
			die;
		}
		
		$datasave = array();
		foreach($data as $row){
			$datasave[]['name'] = $row;
		}
		if($this->Industry->saveMany($datasave)){
			echo json_encode(array('name'=>$file_name, 'size'=>$full_size, 'status'=>1, 'info'=>'File imported!'));
		}else{
			echo json_encode(array('name'=>$file_name, 'size'=>$full_size, 'status'=>-1, 'info'=>'Insert database error!'));
		}
		die;
	}
}