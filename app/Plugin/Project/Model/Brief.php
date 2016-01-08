<?php
class Brief extends AppModel {

	public $validate = array(
	    'project_title' => array(
	        'rule'    => array('notEmpty'),
			'message' => 'Project title is required'
	    ),
	    'job_number' => array(
	        'rule'    => array('notEmpty'),
			'message' => 'Job number is required'
	    ),
	    'date' => array(
	        'rule'    => array('notEmpty'),
			'message' => 'Date is required'
	    ),
	    
	);
}