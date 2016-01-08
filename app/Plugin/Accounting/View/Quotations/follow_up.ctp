<?php
	echo $this->Form->create('Quotation');
	echo $this->Form->input('choose_follow', array(
		'label' => false,
		'class' => '',
		'before' => '<label class="col-lg-2 control-label">'. __('Select type') .'</label><div class="col-lg-10"><label class="radio">',
		'separator' => '</label><label class="radio">',
		'after' => '</label></div>',
		'type' => 'radio',
		'legend' => false,
		'options' => array('1' => __('Number of days'), '0' => __('Next follow up')),
		'data-toggle' => 'radio'
	));
	echo $this->Form->input('follow_up_days', array(
		'type' => 'number',
		'div'=>array('class'=>'form-group displaynone', 'id' => 'follow_up_days'),
		'default' => Configure::read('Settings.Accounting.follow_up_days')
	));
	echo $this->Form->inputDatepicker('next_follow_up', array(
		'div'=>array('class'=>'form-group displaynone', 'id' => 'next_follow_up'),
		'default' => formatDate($data['Quotation']['next_follow_up'])
	));
	echo $this->Form->submit(__('Save'), array('id' => 'btnSubmit'));
	echo $this->Form->end();
?>
<script type="text/javascript">
	$('#QuotationChooseFollow1').click(function() {
		$('#follow_up_days').removeClass('displaynone');
		$('#next_follow_up').addClass('displaynone');
	});
	$('#QuotationChooseFollow0').click(function() {
		$('#follow_up_days').addClass('displaynone');
		$('#next_follow_up').removeClass('displaynone');
	});
	
</script>