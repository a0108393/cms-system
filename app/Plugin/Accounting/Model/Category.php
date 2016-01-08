<?php
class Category extends AppModel {
	
	public $tablePrefix = 'accounting_';
	public $validate = array(
        'name' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A Name is required'
            ),
			'duplicate' => array(
				'rule' => array('notDuplicate'),
				'message' => 'This category name is already taken'
			)
        )
    );
}