<?php 

?>
<div class="settings form">
<?php 
	echo $this->Form->create('Setting');
	
	echo $this->Form->input('DefaultGroupId', array('label' => array('class' => 'col-lg-2 control-label', 'text' => 'Client Group'), 'options' => $groups, 'class' => 'select-ui-primary'));
	echo $this->Form->input('JobGroupId', array('options' => $groups, 'class' => 'select-ui-primary'));
	echo $this->Form->input('SalesStaffGroupId', array('options' => $groups, 'class' => 'select-ui-primary'));
	echo $this->Form->input('AffiliateGroupId', array('options' => $groups, 'class' => 'select-ui-primary'));
	echo $this->Form->input('DefaultChannel', array('options' => $channels, 'class' => 'select-ui-primary'));
	echo $this->Form->input('DefaultRatecard', array('options' => $ratecard, 'class' => 'select-ui-primary'));
	echo $this->Form->end(_('Save'));
?>
</div>