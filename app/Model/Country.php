<?php

class Country extends AppModel {
	
	public $validate = array(
		'name' => array(
			'empty' => array(
				'rule' => array('notEmpty'),
				'message' => 'User name is require'
			),
			'duplicate' => array(
				'rule' => array('notDuplicate'),
				'message' => 'This currency is already taken'
			)
        )
	);
}
