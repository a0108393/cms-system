<?php
class Company extends AppModel {
	
	var $actsAs  = array('Permissionable.Permissionable');
	
	public $tablePrefix = 'company_';
	
	public $validate = array(
		'name' => array(
			'duplicate' => array(
				'rule' => array('notDuplicate'),
				'message' => 'This Company is already taken'
			),
			'empty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Company name is require'
			)
        ),
		'industry_id' => array(
			'rule'    => array('notEmpty'),
			'message' => 'Industry name field is require'
        ),
		'ratecard_id' => array(
			'rule'    => array('notEmpty'),
			'message' => 'Ratecard field is require'
        ),
		'currency_id' => array(
			'rule'    => array('notEmpty'),
			'message' => 'Currency field is require'
        ),
		'country_id' => array(
			'rule'    => array('notEmpty'),
			'message' => 'Country field is require'
        ),
		
		'address' => array(
			'rule'    => array('notEmpty'),
			'message' => 'Address field is require'
        ),
		'website' => array(
			'rule' => 'url'
		)
	);
}