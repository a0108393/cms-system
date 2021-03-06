<?php
echo $this->Form->create('Deliverable');

/*echo $this->Form->input('project_id', array(
  'fieldName' => 'data[Deliverable][project_id]',
  'label' => array('text' => __('Project'), 'class' => 'col-lg-2 control-label'),
  'options' => $project_names,
  'empty'=>'Select project',
 ));
*/
echo $this->Form->input('name', array(
  'fieldName' => 'data[Deliverable][name]',
  'label' => array('text' => __('Name'), 'class' => 'col-lg-2 control-label')
 ));
 
 echo $this->Form->input('Final Deliverables', array('type' => 'textarea'));

 echo $this->Form->input('no_of_changes', array(
  'fieldName' => 'data[Deliverable][no_of_changes]',
  'label' => array('text' => __('Revision Allowed'), 'class' => 'col-lg-2 control-label')
 ));

 echo $this->Form->inputDatepicker('deadline', array(
  'fieldName' => 'data[Deliverable][deadline]',
  'label' => array('text' => __('Deadline'), 'class' => 'col-lg-2 control-label')
 ));

 echo $this->Form->input('assignTo', array(
  'fieldName' => 'data[Deliverable][assignTo]',
  'label' => array('text' => __('Assign To'), 'class' => 'col-lg-2 control-label'),
  'options' => $project_names,
  'empty'=>'Select project',
 ));
 
 echo $this->Form->inputDatepicker('due_date', array(
  'fieldName' => 'data[Deliverable][due_date]',
  'label' => array('text' => __('Due Date'), 'class' => 'col-lg-2 control-label')
 ));
/*if(in_array(Configure::read('Settings.Company.SalesStaffGroupId'), $group) || $this->Session->read('Auth.User.id') == SUPER_ADMIN) {
	echo $this->Form->input('job_number');
	echo $this->Form->input('company_id', array(
		'select' => $companies
	));
	echo $this->Form->inputDatepicker('date');
}*/

echo $this->Form->inputUpload(array(
			'fieldName' => 'data[Deliverable][attached_files]',
			'title' => 'Select a file or drop file to here', 
			'label' => 'Deliverable files',
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
							  <input type="hidden" value="'.$value.'" name="data[Brief][attached_files]['.$key.']">
					  </li>
					 ';
			}
	echo ' </ul></div></div></div>';
}
echo $this->Form->input('Deliverable details', array('type' => 'textarea'));
echo $this->Form->submit(__('Save'), array(
		'name' => 'data[Deliverable][onlySave]',
		'after' => '<input type="submit" class="btn btn-success" name="data[Deliverable][saveAndSend]" value="' . __('Save and send') . '" />'
	));

echo $this->Form->end();
?>