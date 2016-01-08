<?php
$allow_action = array(
	array('c' => 'advertisinglinks','a' => array('go')),
	array('c' => 'enquiries','a' => array('bindAjax','bindSuggestAjax')),
);

$crud_mapping = array(
	array('c' => 'advertisinglinks','a' => array('view' => array('index', 'history'))),
	array('c' => 'channels','a' => array('view' => array('index', 'history'))),
	array('c' => 'events','a' => array('view' => array('index', 'history'))),
	array('c' => 'reports','a' => array('view' => array('index','chart', 'enquiries', 'links', 'history'))),
	array('c' => 'enquiries','a' => array('view' => array('index', 'history'),'edit'=> array('add_client','add'))),
	array('c' => 'affiliates','a' => array('view' => array('index', 'history'))),
);

Configure::write('AllowAction.Marketing', $allow_action);
Configure::write('CrudMapping.Marketing', $crud_mapping);