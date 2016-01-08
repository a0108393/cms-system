<?php
echo $this->Form->create('Brief');
echo $this->Form->input('project_title');
if(in_array(Configure::read('Settings.Company.SalesStaffGroupId'), $group) || $this->Session->read('Auth.User.id') == SUPER_ADMIN) {
	echo $this->Form->input('job_number');
	echo $this->Form->input('company_id', array(
		'select' => $companies
	));
	echo $this->Form->inputDatepicker('date');
}

echo $this->Form->inputUpload(array(
			'fieldName' => 'data[Brief][attached_files]',
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
							  <input type="hidden" value="'.$value.'" name="data[Brief][attached_files]['.$key.']">
					  </li>
					 ';
			}
	echo ' </ul></div></div></div>';
}
echo $this->Form->input('project_background', array('type' => 'textarea'));
echo $this->Form->input('creative_task', array('type' => 'textarea'));
echo $this->Form->input('target_audience', array('type' => 'textarea'));
echo $this->Form->input('key_message', array('type' => 'textarea'));
echo $this->Form->input('brand_positioning', array('type' => 'textarea'));
echo $this->Form->input('deliverables', array('type' => 'textarea'));
echo $this->Form->input('mandatories', array('type' => 'textarea'));
echo $this->Form->input('timeline', array('type' => 'textarea'));
echo $this->Form->input('supporting', array('type' => 'textarea'));

if($this->action == 'edit' && $this->request->data['Brief']['status'] >= 1) {
	//check sale staff
	if(in_array(Configure::read('Settings.Company.SalesStaffGroupId'), $group)) {
		if($this->request->data['Brief']['status'] == 2) {
			echo '<div class="form-group"><label class="col-lg-2 control-label"></label><div class="col-lg-10">';
			echo __('Please approve and send request');
			echo '<br />';
			echo $this->Html->link(__('Approve'), array('plugin' => 'project', 'controller' => 'briefs', 'action' => 'view', $this->request->data['Brief']['id']), array('class' => 'btn btn-success'));
			echo '</div></div>';
			
		}
		if($this->request->data['Brief']['status'] == 3) {
			echo $this->Form->submit(_('Save and send'), array('name' => 'data[Brief][saveAndSend]'));
		}
	}
	//check client
	if(in_array(Configure::read('Settings.Company.DefaultGroupId'), $group)) {
		if($this->request->data['Brief']['status'] == 4) {
			echo '<div class="form-group"><label class="col-lg-2 control-label"></label><div class="col-lg-10">';
			echo __('Please approve and send request');
			echo '<br />';
			echo $this->Html->link(__('Approve'), array('plugin' => 'project', 'controller' => 'briefs', 'action' => 'view', $this->request->data['Brief']['id']), array('class' => 'btn btn-success'));
			echo '</div></div>';
		}
		if($this->request->data['Brief']['status'] == 1) {
			echo $this->Form->submit(_('Save and send'), array('name' => 'data[Brief][saveAndSend]'));
		}
	}
}
else {
	echo $this->Form->submit(__('Save'), array(
		'name' => 'data[Brief][onlySave]',
		'after' => '<input type="submit" class="btn btn-success" name="data[Brief][saveAndSend]" value="' . __('Save and send') . '" />'
	));
}



echo $this->Form->end();
?>