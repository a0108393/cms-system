<!-- app/View/Country/form.ctp -->
<?php 

?>
<div class="country form">
<?php echo $this->Form->create('Country'); ?>
<fieldset>
        <legend><?php echo __('Add Country'); ?></legend>
<?php
    echo $this->Form->input('name');
    echo $this->Form->submit('Submit');
?>
</fieldset>
<?php echo $this->Form->end(); ?>
</div>