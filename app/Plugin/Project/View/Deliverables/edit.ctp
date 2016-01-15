<?php
	echo $this->Form->create('Deliverable');
	echo $this->Form->input('name', array(
		'label' => array('text' => __('Title'), 'class' => 'col-lg-2 control-label'),
		'placeholder'=>'Title of deliverable',
		'required' => true
	));
	echo $this->Form->input('details', array(
		'label' => array('text' => __('Deliverable Details'), 'class' => 'col-lg-2 control-label'),
		'required' => false
	));
	
?>

<?php	
	echo $this->Form->end('Save Changes');
?>
