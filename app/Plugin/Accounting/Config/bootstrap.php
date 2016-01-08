<?php

$allow_action = array(
		
);

$crud_mapping = array(
	array('c' => 'ratecards','a' => array('view' => array('index', 'notify', 'history', 'viewRatecard'), 'edit' => array('edit','edit_price'))),
	array('c' => 'services','a' => array('view' => array('index', 'notify', 'history') )),
	array('c' => 'categories','a' => array('view' => array('index') )),
	array('c' => 'quotations','a' => array('view' => array('index', 'bindRatecard', 'bindProductService', 'bindDetailHistory', 'approve', 'compareSignature', 'viewPdf'), 'edit' => array('edit', 'history', 'notify', 'followUp', 'sendFollowUp', 'sendFirstExpiry', 'generateInvoice') )),
);
 
Configure::write('AllowAction.Accounting', $allow_action);
Configure::write('CrudMapping.Accounting', $crud_mapping);