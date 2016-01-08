<?php
class Ratecard extends AppModel {
	
	public $tablePrefix = 'accounting_';
	
	public $validate = array(
		'name' => array(
            'rule'    => array('notEmpty'),
			'message' => 'Ratecard name field is require'
        ),
		'discount' => array(
            'rule'    => array('notEmpty'),
			'message' => 'Ratecard name field is require'
        )
	);
}