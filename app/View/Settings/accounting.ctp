<?php 

?>
<div class="settings form">
<?php 
	echo $this->Form->create('Setting');

	echo $this->Form->input('terms_and_conditions', array('type' => 'textarea'));
	echo $this->Form->input('accounting_email', array('label' => array('class' => 'col-lg-2 control-label', 'text' => 'Email system'), 'type' => 'text'));
	echo $this->Form->input('payment_structure', array('type' => 'text'));
	echo $this->Form->input('late_payment', array('type' => 'text'));
	echo $this->Form->input('bank_and_account_information', array('type' => 'text'));
	echo $this->Form->input('sandbox_account', array('type' => 'text'));
	echo $this->Form->input('paypal_account', array('type' => 'text'));
	echo $this->Form->input('sandbox_test', array(
		'label' => false,
		'class' => '',
		'before' => '<label class="col-lg-2 control-label">'. __('Sandbox test site') .'</label><div class="col-lg-10"><label class="radio Ratecard-retainer_type">',
		'separator' => '</label><label class="radio Ratecard-retainer_type">',
		'after' => '</label></div>',
		'type' => 'radio',
		'legend' => false,
		'options' => $sandboxtest,
		'data-toggle' => 'radio'
	));
	echo $this->Form->input('validity', array('type' => 'number', 'required' => 'required'));
	
	echo $this->Form->input('apply_tax', array(
		'label' => false,
		'class' => '',
		'before' => '<label class="col-lg-2 control-label">'. __('Apply Tax') .'</label><div class="col-lg-10"><label class="radio">',
		'separator' => '</label><label class="radio">',
		'after' => '</label></div>',
		'type' => 'radio',
		'legend' => false,
		'options' => array('1' => __('Yes'), '0' => __('No')),
		'data-toggle' => 'radio'
	));
	echo $this->Form->input('tax_percentage', array('type' => 'number', 'default' => 0));
	echo $this->Form->input('follow_up_days', array('type' => 'number', 'required' => 'required'));
	echo $this->Form->input('cost_estimate_expiry_notice', array('type' => 'number', 'required' => 'required', 'default' => 5));
	
	
	echo $this->Form->end(_('Save'));
?>
</div>