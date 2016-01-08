<?php
class Industry extends AppModel {
	
	public $tablePrefix = 'company_';
	
	public $validate = array(
		'name' => array(
			'duplicate' => array(
				'rule' => array('notDuplicate'),
				'message' => 'This Industry is already taken'
			),
			'empty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Industry name is require'
			)
        )
	);
	
}
