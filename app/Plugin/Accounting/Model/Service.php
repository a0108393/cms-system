<?php
class Service extends AppModel {
	
	public $tablePrefix = 'accounting_';
	
	public $validate = array(
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
		'unit' => array(
            'rule'    => array('notEmpty'),
			'message' => 'Line unit field is require'
        ),
		'price_per_unit' => array(
            'rule'    => array('notEmpty'),
			'message' => 'Line price per unit field is require'
        ),
		'category_id' => array(
            'rule'    => array('notEmpty'),
			'message' => 'Line category field is require'
        ),
	);
}