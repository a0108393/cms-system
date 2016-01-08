<?php
class LinkVisit extends AppModel{
	public $tablePrefix = 'marketing_';
	public $name = 'LinkVisit';

	public  function beforeSave($options = array()){
		$this->data[$this->alias]['user_id'] = AuthComponent::user('id');
		return true;
    }
	
}