	<html>
	<head>

	</head>
	<body>

	<?php
	echo $this->Form->create('Project');

	echo $this->Form->input('name', array(
	  'fieldName' => 'data[Project][name]',
	  'label' => array('text' => __('Project Name'), 'class' => 'col-lg-2 control-label'),
	//  'options' => $project_names,
	//  'empty'=>'Select project',
	 ));

	echo $this->Form->input('owner', array(
	  'fieldName' => 'data[Project][owner]',
	  'label' => array('text' => __('Owner'), 'class' => 'col-lg-2 control-label'),
	  'options' => $client_users,
	  'empty'=>'Select owner',
	 ));

	echo $this->Form->input('member_id', array(
	  'fieldName' => 'data[Project][member_id]',
	  'label' => array('text' => __('Member'), 'class' => 'col-lg-2 control-label'),
	 ));
	 
	/* echo $this->Form->input('member_id', array(
	  'fieldName' => 'data[Project][member_id]',
	  'label' => array('text' => __('Invite'), 'class' => 'col-lg-2 control-label'),
	 ));
	 */
	 ?>
     <div class = "col-lg-2 control-label">Invite</div>
	 <?php
	  echo $this ->Form ->input('member_id', array(
	  'div' => array(
	  'id' =>'tags',
	  'name'=>'tags'
	  ),
	  ));

	// echo '<input name="tags" id="tags" value="foo,bar,baz" />';
	 echo $this->Form->inputDatepicker('project_start', array(
	'id' => 'project_start',
	'fieldName' => 'data[Project][project_start]',
	'label' => array('text' => __('Project Start'), 'class' => 'col-lg-2 control-label')
	)); 

	 echo $this->Form->inputDatepicker('deadline', array(
	 'id' => 'deadline',
	'fieldName' => 'data[Project][deadline]',
	'label' => array('text' => __('Deadline'), 'class' => 'col-lg-2 control-label')
	)); 



	echo $this->Form->inputUpload(array(
				'fieldName' => 'data[Project][project_file]',
				'title' => 'Select a file or drop file to here', 
				'label' => 'Project files',
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
								  <input type="hidden" value="'.$value.'" name="data[Project][project_file]['.$key.']">
						  </li>
						 ';
				}
		echo ' </ul></div></div></div>';
	}

	
	echo $this->Form->input('Project Detail', array('type' => 'textarea'));
	
	if($this->action == 'add') {

    echo $this->Form->submit(__('Save'), array(
		'name' => 'data[Project][onlySave]',
		'after' => '<input type="submit" name="data[Project][saveAndSend]" value="' . __('Save and send') . '" />'
	));
//	echo $this->Form->submit(_('Save and send'), array('name' => 'data[Project][saveAndSend]'));
	}

	echo $this->Form->end();
	?>
	</body>
	<script type="text/javascript">
	$('#tags').tagsInput({
	  autocomplete:{selectFirst:true,width:'100px',autoFill:true}
	});
	</script>

	</html>