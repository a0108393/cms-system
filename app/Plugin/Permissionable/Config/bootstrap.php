<?php
define('GLOBAL_P',1);
define('ROW_P',0);
define('DENY_P',-1);

define('OWNER_READ_BIT',256);
define('OWNER_WRITE_BIT',128);
define('OWNER_DELETE_BIT',64);
define('GROUP_READ_BIT',32);
define('GROUP_WRITE_BIT',16);
define('GROUP_DELETE_BIT',8);
define('OTHER_READ_BIT',4);
define('OTHER_WRITE_BIT',2);
define('OTHER_DELETE_BIT',1);

App::import('Lib', 'Permissionable.Access');

$allow_action = array(
		//array('c' => 'modules','a' => array('index'))
	);

$crud_mapping = array(
	array('c' => 'modules','a' => array('edit' => array('index'))),
	array('c' => 'permissions','a' => array('edit' => array('index'))),
	array('c' => 'users','a' => array('edit' => array('index')))
);

Configure::write('AllowAction.Permissionable', $allow_action);
Configure::write('CrudMapping.Permissionable', $crud_mapping);