<?php 
	$act = $this->action;
?>
<div class="client form">
<?php echo $this->Form->create('User');
	echo $this->Form->input('name');
	echo $this->Form->input('email');
	echo $this->Form->input('client_designation');
	echo $this->Form->input('contact');
	echo $this->Form->input('alt_contact');
	echo $this->Form->input('timezone', array(
		'options' => tz_list(),
		'class' => 'select2'
	)); 
	echo $this->Form->input('skype');
	echo $this->Form->input('facebook');
	echo $this->Form->input('linkedin');
	echo $this->Form->input('google');
	echo $this->Form->input('active', array(
		'options' => $stateOptions,
		'class' => 'select-ui-primary'
	));
	
	echo $this->Form->submit('Submit', array(
		'class' => 'btn btn-primary',
		'div' =>  'form-group',
		'before' => '<div class="col-lg-offset-2 col-lg-10">',
		'after' => $this->Html->link('Cancel', array('action' => 'index'), array('class' => 'btn btn-default')) . '</div>',
	));
	
	echo $this->Form->end(); ?>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#UserTimezone').select2({
			'width': '100%'
		})
	})
</script>