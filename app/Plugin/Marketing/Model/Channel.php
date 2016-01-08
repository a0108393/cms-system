<?php 
class Channel extends AppModel{
	public $tablePrefix = 'marketing_';

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
        )
    );
	var $actsAs  = array('Permissionable.Permissionable');
		
	public  function beforeSave($options = array()){
		$this->data[$this->alias]['user_id'] = AuthComponent::user('id');
		return true;
    }
}