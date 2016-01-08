<!-- app/View/Industry/form.ctp -->
<div class="industry form">
<?php echo $this->Form->create('Industry', array(
	'error' => false
)); ?>
<fieldset>
        <legend><?php echo __('Add Industry'); ?></legend>
<?php
    echo $this->Form->input('name');
?>
</fieldset>
<?php echo $this->Form->end(__('Save')); ?>
</div>