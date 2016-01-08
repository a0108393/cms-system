<?php
class SaveReport extends AppModel{
	public $tablePrefix = 'marketing_';
	public $name = 'SaveReport';

	public  function beforeSave($options = array()){
		$this->data[$this->alias]['user_id'] = AuthComponent::user('id');
		return true;
    }
}