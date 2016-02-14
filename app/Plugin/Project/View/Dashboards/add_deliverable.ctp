<html>
<head>

</head>
<body>

<?php
echo $this->Form->create('Deliverable');

echo $this->Form->input('name', array(
  'fieldName' => 'data[Deliverable][name]',
  'label' => array('text' => __('Deliverable Name'), 'class' => 'col-lg-2 control-label'),

 ));

echo $this->Form->input('Final Deliverable', array('type' => 'textarea'));

echo $this->Form->inputDatepicker('Deadline');
echo $this->Form->inputDatepicker('start_time', array(
		'id' => 'start_time',
		'datepicker_setup' => array(
			'format' => 'HH:mm',
			'pickDate' => false,
			'pickTime' => true,
			'useMinutes' => true,
			'useSeconds' => false,
			'useCurrent' => true
		)
	));
echo $this->Form->inputDatepicker('end_time', array(
		'id' => 'end_time',
		'datepicker_setup' => array(
			'format' => 'HH:mm',
			'pickDate' => false,
			'pickTime' => true,
			'useMinutes' => true,
			'useSeconds' => false,
			'useCurrent' => true
		)
	));
	
	
 

echo $this->Form->input('Revision Allowed');


echo $this->Form->input('Assign To', array(
						'id' => 'multiple-suggest-select',
						'type' => 'text',
						));



echo $this->Form->inputDatepicker('Due Date');


echo $this->Form->inputUpload(array(
				'fieldName' => 'data[Deliverable][deliverable_file]',
				'title' => 'Select a file or drop file to here',
				'label' => 'Brief files',
				'verticalFrom' => true,
				'maxFiles' => 10
				));
				
				if(!empty($attached_files)) {
				echo '<div class="form-group">
				<label class="col-lg-2 control-label" ></label>
				<div class="col-lg-10">';
				echo '<div id="wrap-portfolio_files"><ul>';
					foreach( $attached_files as $key => $value ){
						echo '<li><a href="'.$this->Html->url('/uploads/'.$value).'">'.$key.'</a> 
									  <a onClick="delete_file(this)" href="javascript:void(0)" class="delete-file">x</a>
									  <input type="hidden" value="'.$value.'" name="data[Deliverable][deliverable_file]['.$key.']">
							  </li>';
									}
				}
echo $this->Form->input('Deliverable details', array('type' => 'textarea'));
echo $this->Form->submit(__('Save and Close'), array(
		'name' => 'data[Deliverable][onlySave]',
		'after' => '<input type="submit" class="btn btn-success" name="data[Deliverable][saveAndSend]" value="' . __('Save and Close') . '" />'
	));

echo $this->Form->end();
?>
</body>
<script type="text/javascript">
		$('#multiple-suggest-select').tokenInput(
		<?php echo json_encode($all_clients); ?>
			,{
				preventDuplicates: true
				
			}
	);
	</script>
</html>