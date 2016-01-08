<?php
/**
* list all history in system
* 
*/
class HistoriesController extends AppController
{
	
	public $uses = array('History');
	
	public $paginate = array(
		'fields' => 'History.*, User.name',
		'joins' => array(
			array(
				'table' => 'users',
				'alias' => 'User',
				'type' => 'LEFT',
				'conditions' => 'User.id = History.user_modified'
			)
		),
        'order' => array(
            'History.date_modified' => 'desc'
        ),
		'group' => 'History.id',
		'limit' => ROWPERPAGE
    );

    public function index(){
        $this->History->recursive = 0;
		$this->History->virtualFields = array(
			'user_name' => 'User.name'
		);
		$data = $this->paginate('History'); 
        $this->set('data', $data);
    }
}