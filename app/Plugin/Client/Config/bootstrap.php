<?php
	Configure::write('PermissionableEnable.Client', false);
	$allow_action = array(
		array('c' => 'posts','a' => array('test1','test2'))
	);
	
	$crud_mapping = array(
		array('c' => 'posts','a' => array('view' => array('index'),'edit' => array('test2'),'delete' => array('test3')))
	);
	
	Configure::write('AllowAction.Client', $allow_action);
	Configure::write('CrudMapping.Client', $crud_mapping);