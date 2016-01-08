<?php

$allow_action = array(
		array('c' => 'industries','a' => array('import')),
	);

$crud_mapping = array(
	array('c' => 'companies','a' => array('view' => array('index', 'history'))),
	array('c' => 'industries','a' => array('view' => array('index', 'history'))),
	array('c' => 'users','a' => array('view' => array('index'))),
	array('c' => 'ratecards','a' => array('view' => array('index', 'history'))),
);
 
Configure::write('AllowAction.Company', $allow_action);
Configure::write('CrudMapping.Company', $crud_mapping);