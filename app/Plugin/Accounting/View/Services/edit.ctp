<!-- app/View/Service/form.ctp -->
<?php 

?>
<div class="company form">
<?php echo $this->Form->create('Service'); ?>

<?php
    echo $this->Form->input('name');
    echo $this->Form->input('description', array(
		'type' => 'textarea'
	));
	echo $this->Form->input('unit');
	echo $this->Form->input('price');
	echo $this->Form->input('deliverables', array(
		'type' => 'textarea'
	));
	echo $this->Form->input('locked', array(
		'label' => false,
		'class' => '',
		'default' => 0,
		'before' => '<label class="col-lg-2 control-label">'. __('Lock price') .'</label><div class="col-lg-10"><label class="radio inline Service-lock_price">',
		'separator' => '</label><label class="radio inline Service-lock_price">',
		'after' => '</label></div>',
		'type' => 'radio',
		'legend' => false,
		'options' => array(0 => __('No'), 1 => __('Yes')),
		'data-toggle' => 'radio'
	));
?>

<?php 
	echo $this->Form->input('category_id', array(
		'empty'=>'Select one',
		'type' => 'select',
		'options' => $categories,
		//'class' => 'select-ui-primary',
	));
	echo $this->Form->end(__('Save')); ?>
</div>
