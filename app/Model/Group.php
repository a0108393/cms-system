<?php 
class Group extends AppModel{
    
	public $validate = array(
        'name' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A Name is required'
            ),
			'duplicate' => array(
				'rule' => array('notDuplicate'),
				'message' => 'This group name is already taken'
			)
        ),
        'alias' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A alias is required'
            )
        )
    );
    
}