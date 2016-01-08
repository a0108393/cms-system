<?php 
class Event extends AppModel{
	public $tablePrefix = 'marketing_';
	var $actsAs  = array('Permissionable.Permissionable');
	var $validate = array(
    	'name' => array(
            'duplicate' => array(
				'rule' => array('notDuplicate'),
				'message' => 'This line is already taken'
			),
			'empty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Line name is require'
			)
        ),
        'start_date' => array(
            'rule' => 'notEmpty',
            'message' => 'Start date is not empty'
        ),
        'end_date' => array(
        	'rule' => 'notEmpty',
        	'message' => 'End date is not empty'
    	)
    );
		
	public  function beforeSave($options = array()){
		$this->data[$this->alias]['user_id'] = AuthComponent::user('id');
		return true;
    }
}