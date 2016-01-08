<?php 

?>
<div class="settings form">
<?php 
	echo $this->Form->create('Setting');
	
	echo $this->Form->input('default_currency', array('options' => $currencies, 'class' => 'select-ui-primary'));
	echo $this->Form->input('date_format', array('options' => $dateformats, 'class' => 'select-ui-primary'));
	echo $this->Form->input('time_format', array('options' => $timeformats, 'class' => 'select-ui-primary'));
	echo $this->Form->input('start_week', array('options' => $startWeek, 'class' => 'select-ui-primary'));
	echo $this->Form->input('number_format', array('options' => $numberFormats, 'class' => 'select-ui-primary'));
	echo $this->Form->input('currency_format', array('options' => $currencyDisplayFormats, 'class' => 'select-ui-primary'));
	
	echo $this->Form->end(_('Save'));
?>
</div>