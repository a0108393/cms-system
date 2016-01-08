<?php
echo $this->Form->create('Brief');
echo $this->Form->input('first_name', array('required' => true));
echo $this->Form->input('last_name', array('required' => true));
echo $this->Form->input('contact_no', array('required' => true));
echo $this->Form->input('email', array('required' => true));
echo $this->Form->input('project_title');
echo $this->Form->input('job_number');
echo $this->Form->input('company_id', array(
	'select' => $companies
));
echo $this->Form->inputDatepicker('date');
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

$this->Captcha->render(array('modelName'=>'Brief','required'=>true));
echo $this->Form->end(__('Save'));
?>