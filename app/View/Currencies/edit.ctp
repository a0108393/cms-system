<!-- app/View/Currency/form.ctp -->
<?php 

?>
<div class="country form">
<?php echo $this->Form->create('Currency'); ?>
<fieldset>
        <legend><?php echo __('Add Currency'); ?></legend>
<?php
    echo $this->Form->input('name');
    echo $this->Form->input('code');
	echo $this->Form->input('country_id', array(
		'options' => $countries,
		'class' => 'select-ui-primary'
	));
    echo $this->Form->submit('Submit');
?>
</fieldset>
<?php echo $this->Form->end(); ?>
</div>