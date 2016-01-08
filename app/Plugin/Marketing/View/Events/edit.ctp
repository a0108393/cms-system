<h1>
<?php
	switch ($this->action) {
		case 'add':
			echo __('Add Event');
			break;
		case 'edit':
			echo __('Edit Event');
			break;
		default:
			echo __('View Event');
			break;
	}
?>
</h1>
<?php
echo $this->Form->create('Event');
if($this->action != 'view') {
	echo $this->Form->input('name');
	echo $this->Form->inputDatepicker('start_date',array( 
		'label' => array('class' => 'col-lg-2 control-label', 'text' => 'Start Date'), 
		'class' => 'form-control',
		'id'=>'datetimepicker',
		'datepicker_setup' => array(
			'endDate' => gmdate('Y-m-d')
		)
	));
	echo $this->Form->inputDatepicker('end_date',array( 'label' => array('class' => 'col-lg-2 control-label', 'text' => 'End Date'), 'class' => 'form-control','id'=>'datetimepicker_end'));
	echo $this->Form->end('Save Event');
}
else {
	echo $this->Form->input('name', array('disabled' => true));
	echo $this->Form->inputDatepicker('start_date',array( 
		'disabled' => true,
		'value' => formatDate($this->request->data['Event']['start_date'])
	));
	echo $this->Form->inputDatepicker('end_date',array( 
		'value' => formatDate($this->request->data['Event']['end_date']),
		'disabled' => true));
	echo $this->Form->end();
}
?>