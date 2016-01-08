<?php 
	$act = $this->action;
?>

<div class="user form">
<?php echo $this->Form->create('User');

	echo $this->Form->input('name');
	if($act != 'add'){
		echo $this->Form->input('email', array('disabled' => 'disabled'));
	}else{
		echo $this->Form->input('email');
	}
	/* echo $this->Form->input('password',array('autocomplete' => 'off')); */
	if($act != 'profiles'){
		/*echo $this->Form->input('group_id', array(
			'options' => $groups,
			'label' => false,
			'class' => '',
			'before' => '<label class="col-lg-2 control-label">'. __('Group') .'</label><div class="col-lg-10"><label class="radio inline User-group_id">',
			'separator' => '</label><label class="radio inline User-group_id">',
			'after' => '</label></div>',
			'type' => 'radio',
			'legend' => false,
			'data-toggle' => 'radio'
		));*/
		echo '<div class="form-group"><label class="col-lg-2 control-label">Group</label><div class="col-lg-10">';

		foreach ($groups as $key => $value) {
			$condition = array();
			$checked = '';
			if(!empty($this->request->data['Group'])) {
				foreach ($this->request->data['Group'] as $k => $item) {
					if($key == $item['id']) {
						$checked = ' checked';
						$condition['checked'] = true;
					}
				}
			}
			
			$condition['type'] = 'checkbox';
			$condition['id'] = 'UserGroupId'. $key;
			$condition['name'] = 'data[Group][Group][]';
			$condition['label'] = false;
			$condition['div'] = false;
			$condition['value'] = $key;
			$condition['before'] = '<label class="User_group_id checkbox'. $checked .'"><span class="icons"><span class="first-icon fui-checkbox-unchecked"></span><span class="second-icon fui-checkbox-checked"></span></span>';
			$condition['after'] = '</label>'. $value .'</div></label>';
			echo $this->Form->input('group_id', $condition);
		}
		
		echo '</div></div>';
		/*echo $this->Form->input('Group',array(
            'label' => array('text' => __('Groups'), 'class' => 'col-lg-2 control-label'),
            'type' => 'select',
            'multiple' => 'checkbox',
            'options' => $groups,
            'selected' => $this->Html->value('Group.Group'),
        ));*/
	}
	//extra fields
	?>
	<div class="extra_fields client">
	<?php
		echo $this->Form->input('client_designation');
			echo $this->Form->input('company_id', array(
			'options' => $companies,
			'class' => 'select-ui-primary'
		));
	?>
	</div>
	<div class="extra_fields job_candidate">
	<?php
		echo $this->Form->input('candidate_address', array('type' => 'text'));
		echo $this->Form->inputDatepicker('candidate_dob', array('label' => array('text' => 'Date of Birth', 'class' => 'col-lg-2 control-label')));
		echo $this->Form->input('candidate_last_drawn_salary');
		echo $this->Form->input('candidate_expected_salary');
?>
		<div class="form-group">
			<label class="col-lg-2 control-label"></label>
			<div class="col-lg-10" id="result_salary">
			</div>
		</div>
<?php
		echo $this->Form->inputUpload(array(
			'fieldName' => 'data[User][candidate_resume]',
			'title' => 'Select a file or drop file to here', 
			'label' => 'Resume',
			'verticalFrom' => true
		));
		if(!empty($resume)) {
			echo '<div class="form-group">
				<label class="col-lg-2 control-label" ></label>
				<div class="col-lg-10">';
			echo '<div id="wrap-resume"><ul>';
					foreach( $resume as $key => $value ){
						echo '<li><a href="'.$this->Html->url('/uploads/'.$value).'">'.$key.'</a> 
									  <a onClick="delete_file(this)" href="javascript:void(0)" class="delete-file">x</a>
									  <input type="hidden" value="'.$value.'" name="data[User][candidate_resume]['.$key.']">
							  </li>
							 ';
					}
			echo ' </ul></div></div></div>';
		}
		echo $this->Form->input('candidate_portfolio_link');
		echo $this->Form->inputUpload(array(
			'fieldName' => 'data[User][candidate_portfolio_files]',
			'title' => 'Select a file or drop file to here', 
			'label' => 'Portfolio files',
			'verticalFrom' => true,
			'maxFiles' => 10
		));
		if(!empty($portfolio_files)) {
			echo '<div class="form-group">
				<label class="col-lg-2 control-label" ></label>
				<div class="col-lg-10">';
			echo '<div id="wrap-portfolio_files"><ul>';
					foreach( $portfolio_files as $key => $value ){
						echo '<li><a href="'.$this->Html->url('/uploads/'.$value).'">'.$key.'</a> 
									  <a onClick="delete_file(this)" href="javascript:void(0)" class="delete-file">x</a>
									  <input type="hidden" value="'.$value.'" name="data[User][candidate_portfolio_files]['.$key.']">
							  </li>
							 ';
					}
			echo ' </ul></div></div></div>';
		}
	?>
	</div>
	<?php
	echo $this->Form->input('contact',array(
		'label' => array('text' => __('Contact No'), 'class' => 'col-lg-2 control-label')
	));
	echo $this->Form->input('alt_contact');
	echo $this->Form->input('timezone', array(
		'options' => tz_list(),
		'class' => 'select2'
	));
	echo $this->Form->input('skype');
	echo $this->Form->input('facebook');
	echo $this->Form->input('linkedin');
	echo $this->Form->input('google');
	if($act != 'profiles'){
		echo $this->Form->input('active', array(
			'options' => $stateOptions,
			'class' => 'select-ui-primary',
			'default' => 1
		));
	}
	if($act != 'profiles'){
		echo $this->Form->submit('Submit', array(
			'class' => 'btn btn-primary',
			'div' =>  'form-group',
			'before' => '<div class="col-lg-offset-2 col-lg-10">',
			'after' => $this->Html->link('Cancel', array('action' => 'index'), array('class' => 'btn btn-default')) . '</div>',
		));
	}else{
		echo $this->Form->submit('Save');
	}
	echo $this->Form->end(); ?>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#UserCandidateLastDrawnSalary').change(function() {
			last_salary = parseFloat($(this).val()) || 0;
			expected_salary = parseFloat($('#UserCandidateExpectedSalary').val()) || 0;
			if(last_salary != 0) {
				result_salary = (expected_salary - last_salary)/last_salary*100;
				$('#result_salary').text(result_salary + '%');
			}
			else 
				$('#result_salary').text('');
		});
		$('#UserCandidateExpectedSalary').change(function() {
			expected_salary = parseFloat($(this).val()) || 0;
			last_salary = parseFloat($('#UserCandidateLastDrawnSalary').val()) || 0;
			if(last_salary != 0) {
				result_salary = (expected_salary - last_salary)/last_salary*100;
				$('#result_salary').text(result_salary + '%');
			}
			else 
				$('#result_salary').text('');
		});
		$('#UserTimezone').select2({
			'width': '100%'
		})
	});
	$('.extra_fields').hide();
	function showExtraFields(val, check){
		if(val == <?php echo (int)Configure::read('Settings.Company.DefaultGroupId'); ?>){								// default client ID
			if(check) {
				//$('.extra_fields').hide();
				$('.extra_fields.client').hide();
			}
			else {
				$('.extra_fields.client').show();
			}
			
		}else if(val == <?php echo (int)Configure::read('Settings.Company.JobGroupId'); ?>){							//default job candidate group
			if(check) {
				$('#UserCandidateAddress').removeAttr('required', 'required');
				$('#datetimepicker').removeAttr('required', 'required');
				$('#UserCandidateLastDrawnSalary').removeAttr('required', 'required');
				$('#UserCandidateExpectedSalary').removeAttr('required', 'required');
				$('.extra_fields.job_candidate').hide();
			}
			else {
				
				$('#UserCandidateAddress').attr('required', 'required');
				$('#datetimepicker').attr('required', 'required');
				$('#UserCandidateLastDrawnSalary').attr('required', 'required');
				$('#UserCandidateExpectedSalary').attr('required', 'required');
				$('.extra_fields.job_candidate').show();
			}
			
		}else{
			// $('.extra_fields').hide();
			// $('#UserCandidateAddress').removeAttr('required', 'required');
			// $('#datetimepicker').removeAttr('required', 'required');
			// $('#UserCandidateLastDrawnSalary').removeAttr('required', 'required');
			// $('#UserCandidateExpectedSalary').removeAttr('required', 'required');
		}
	}
	//var checked_group = $('label.User_group_id input[type="checkbox"]:checked').val();
	$.each($('label.User_group_id input[type="checkbox"]:checked'), function() {
		showExtraFields($(this).val(), false);
	});
	//showExtraFields(checked_group, false);
	$("label.User_group_id").click(function(){
		var group_id = $(this).children('input[type="checkbox"]').val();
		var check = $(this).children('input[type="checkbox"]').is(":checked");
		showExtraFields(group_id, check);
    });
	function delete_file(el){
		$(el).parent().remove();
	}
</script>