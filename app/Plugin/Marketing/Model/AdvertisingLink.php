<?php
class AdvertisingLink extends AppModel{
	public $tablePrefix = 'marketing_';
	public $name = 'AdvertisingLink';
	var $validate = array(
        'description' => array(
            'rule' => 'notEmpty'
        ),
		'destination_url' => array(
            'rule' => 'notEmpty'
        ),
		
    );
	var $actsAs  = array('Permissionable.Permissionable');
		
	public  function beforeSave($options = array()){
		$this->data[$this->alias]['user_id'] = AuthComponent::user('id');
		return true;
    }
	
}