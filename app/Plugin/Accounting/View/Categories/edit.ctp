<?php
$option = array();
if($this->action == 'view')
	$option = array('disabled' => true);
echo $this->Form->Create('Category');
echo $this->Form->input('name', $option);
if($this->action != 'view')
	echo $this->Form->submit(__('Save'));
echo $this->Form->end();
?>