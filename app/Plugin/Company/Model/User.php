<?php
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

class User extends AppModel {
	
	public $useTable = 'users';
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
			'empty' => array(
				'rule' => array('notEmpty'),
				'message' => 'User name is require'
			)
        ),
		'email' => array(
			'duplicate' => array(
				'rule' => array('notDuplicate'),
				'message' => 'This Email is already taken'
			)
		)
	);
	public function notDuplicate($field){
		$key = array_keys($field);
		$value = array_values($field);
		if($this->id){
			$lastdata = $this->findById($this->id);
			if($lastdata[$this->alias][$key[0]] == $value[0]){
				return true;
			}
		}
		$existing = $this->find('count',array('conditions'=> array($this->alias . '.' . $key[0] => $value[0])));
		if($existing){
			return false;
		}
		return true;
	}
	
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
				$results[$key][$this->alias]['last_login'] = formatDate($val[$this->alias]['last_login']);
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
}
