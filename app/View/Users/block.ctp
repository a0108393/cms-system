<?php 
	echo $this->Form->create('BlockedUser');
	echo $this->Form->input('block_reason', array('type' => 'textarea'));

	echo $this->Form->submit('Save');
	echo $this->Form->end(); ?>