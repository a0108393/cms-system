<?php
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
class User extends AppModel {
    
	// var $actsAs  = array('Permissionable.Permissionable');
	public $hasAndBelongsToMany = array(
        'Group' =>
            array(
                'className' => 'Group',
                'joinTable' => 'users_groups',
                'foreignKey' => 'user_id',
                'associationForeignKey' => 'group_id',
                'unique' => true,
            )
    );
	
	public $validate = array(
        'name' => array(
            'rule'    => array('notEmpty'),
			'message' => 'Name is required'
        ),
        'contact' => array(
            'rule'    => array('notEmpty'),
			'message' => 'Contact No is required'
        ),
        /*'group_id' => array(
            'rule'    => array('notEmpty'),
			'message' => 'Group is required'
        ),*/
        
		'your_email' => array(
            'rule'    => array('notEmpty'),
			'message' => 'Email is required'
        ),
		'current_password' => array(
            'rule'    => array('notEmpty'),
			'message' => 'Password is required'
        ),
		'new_password' => array(
            'rule'    => array('notEmpty'),
			'message' => 'Password is required'
        ),
		'confirm_password' => array(
            'rule'    => array('notEmpty'),
			'message' => 'Password is required'
        ),
		
		'email' => array(
			'duplicate' => array(
				'rule' => array('notDuplicate'),
				'message' => 'This Email is already taken'
			),
			'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Email is required',
                'type' => 'text'
            )
		),
		'captcha'=>array(
			'rule' => array('matchCaptcha'),
			'message'=>'Failed validating human check.'
		)
    );
	
	public function beforeSave($options = array()) {
		if (isset($this->data[$this->alias]['password'])) {
			$passwordHasher = new SimplePasswordHasher();
			$this->data[$this->alias]['password'] = $passwordHasher->hash(
				$this->data[$this->alias]['password']
			);
		}
		return true;
	}
	
	public function afterFind($results, $primary = false) {
		foreach ($results as $key => $val) {
			if (isset($val[$this->alias]['date_joined']) && $val[$this->alias]['date_joined'] != '0000-00-00 00:00:00'){
				$results[$key][$this->alias]['date_joined'] = formatDate($val[$this->alias]['date_joined']);
			}
			if (isset($val[$this->alias]['last_login']) && $val[$this->alias]['last_login'] != '0000-00-00 00:00:00'){
				$results[$key][$this->alias]['last_login'] = formatDateTime($val[$this->alias]['last_login']);
			}else{
				$results[$key][$this->alias]['last_login'] = __('Never');
			}
			// if (isset($val[$this->alias]['active'])){
				// $states = $this->stateOptions();
				// $results[$key][$this->alias]['active'] = $states[$results[$key][$this->alias]['active']];
			// }
		}
		return $results;
	}
	
	public function stateOptions($val = null){
		$options = array(0 => __('Not active'), 1 => __('Active'), 2 => __('Blocked'), 3 => __('Expired'));
		if($val && isset($options[$val])){
			return $options[$val];
		}else{
			return $options;
		}
	}

	public function getByGroup($group_id, $fields = 'User.id, User.name') {
		return $this->find('list', array(
			'fields' => $fields,
			'joins' => array(
				array(
					'table' => 'users_groups',
					'alias' => 'UsersGroup',
					'type' => 'LEFT',
					'conditions' => 'User.id = UsersGroup.user_id'
				)
			),
			'conditions' => array(
				'UsersGroup.group_id' => $group_id
			)
		));
	}
}
