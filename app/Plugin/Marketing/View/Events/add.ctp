<h1>Add Event</h1>
<?php
echo $this->Form->create('Event');
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
?>