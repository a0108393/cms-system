<?php
$allow_action = array(
	array('c' => 'briefs','a' => array('index')),
);
$crud_mapping = array(
	array('c' => 'briefs', 'a' => array('view' => array('index', 'viewPdf'), 'edit' => array('add', 'edit', 'visitor'))),
	array('c' => 'MeetingMinutes', 'a' => array('view' => array('index', 'viewPdf'), 'edit' => array('add', 'edit', 'visitor', 'ajaxDeleteNoteDetail'))),
	array('c' => 'ChangeRequests', 'a' => array('view' => array('index', 'viewPdf'), 'edit' => array('add', 'edit', 'visitor')))
	
	

);
Configure::write('AllowAction.Project', $allow_action);
Configure::write('CrudMapping.Project', $crud_mapping);