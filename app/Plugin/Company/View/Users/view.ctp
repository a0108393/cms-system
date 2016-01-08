<?php 
	$act = $this->action;
?>
<div class="client form">
<?php echo $this->Form->create('User');
	echo $this->Form->input('name', array('readonly' => true));
	echo $this->Form->input('email', array('readonly' => true));
	echo $this->Form->input('company_id', array(
		'options' => $companies,
		'class' => 'select-ui-primary',
		'disabled' => true
	));
	echo $this->Form->input('client_designation', array('readonly' => true));
	echo $this->Form->input('contact', array('readonly' => true));
	echo $this->Form->input('alt_contact', array('readonly' => true));
	echo $this->Form->input('timezone', array(
		'options' => tz_list(),
		'class' => 'select2',
		'disabled' => true
	)); 
	echo $this->Form->input('skype', array('readonly' => true));
	echo $this->Form->input('facebook', array('readonly' => true));
	echo $this->Form->input('linkedin', array('readonly' => true));
	echo $this->Form->input('google', array('readonly' => true));
	echo $this->Form->input('active', array(
		'options' => $stateOptions,
		'class' => 'select-ui-primary',
		'disabled' => true
	));
	echo $this->Form->end(); ?>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		$('#UserTimezone').select2({
			'width': '100%'
		})
	})

</script>