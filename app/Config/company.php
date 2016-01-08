<?php
define('CLIENT_GROUP_ID', 5);
$config['company'] = array();
$config['accounting'] = array(
	'quotation_status' => array(
		'0' => 'Draft',
		'1' => 'Pending',
		'2' => 'Approved',
		'3' => 'Sent',
		'4' => 'Declined',
		'5' => 'Accepted',
		'6' => 'Expired'
	),
	'quotation_discount' => array(
		'percentage' => 'Percentage',
		'fixed' => 'Fixed'
	)
);
$config['note'] = array(
	'type' => array(
		0 => 'Discussion',
		1 => 'Note',
		2 => 'Info',
		3 => 'Action'
	)
);