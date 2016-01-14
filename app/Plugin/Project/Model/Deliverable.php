<?php
class Deliverable extends AppModel {
	//var $name = 'notes';
	var $belongsTo = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	public $validate = array(
	    'title' => array(
	        'duplicate' => array(
				'rule' => array('notDuplicate'),
				'message' => 'This title is already taken'
			),
			'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'Title is required',
                'type' => 'text'
            )
	    ),
	    'date' => array(
	        'rule'    => array('notEmpty'),
			'message' => 'Job number is required'
	    ),
	    'start_time' => array(
	        'rule'    => array('notEmpty'),
			'message' => 'Start time is required'
	    ),
	    'end_time' => array(
	        'rule'    => array('notEmpty'),
			'message' => 'End time is required'
	    ),
	    'client_id' => array(
	    	'rule' => array('notEmpty'),
	    	'message' => 'Client is required'
    	),
    	'minute_taker' => array(
	    	'rule' => array('notEmpty'),
	    	'message' => 'Minute taker is required'
    	),
	);
}