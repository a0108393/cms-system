<?php
class Enquiry extends AppModel {
	
	var $captcha = ''; //intializing captcha var
	
	public $tablePrefix = 'marketing_';
	
	public $validate = array(
		'name' => array(
			'rule'    => array('notEmpty'),
			'message' => 'Name field is require'
        ),
		'email' => array(
			'rule'    => array('notEmpty'),
			'message' => 'Email field is require'
        ),
		'contact' => array(
			'rule'    => array('notEmpty'),
			'message' => 'Contact field is require'
        ),
		/* 'request' => array(
			'rule'    => array('notEmpty'),
			'message' => 'Request field is require'
        ), */
		/* 'enq_date' => array(
			'rule'    => array('notEmpty'),
			'message' => 'Enquiry date field is require'
        ), 
		'captcha'=>array(
			'rule' => array('matchCaptcha'),
			'message'=>'Failed validating human check.'
		),*/
	);
}