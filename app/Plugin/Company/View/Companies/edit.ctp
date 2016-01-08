<!-- app/View/Company/form.ctp -->
<?php 

?>
<div class="company form">
<?php echo $this->Form->create('Company', array('id' => 'edit_data_form')); ?>
<?php
    echo $this->Form->input('name');
    echo $this->Form->input('industry_id', array(
		'options' => $industries,
		'class' => 'select-ui-primary'
	));
    echo $this->Form->input('co_reg_no');
    echo $this->Form->input('address', array('type' => 'text'));
    echo $this->Form->input('ratecard_id', array(
		'options' => $ratecards,
		'class' => 'select-ui-primary'
	));
    ?>
	<div class="form-group">
		<label class="col-lg-2 control-label" for="CompanyLogo">Logo</label>
		<div class="col-lg-10">
			<?php if(isset($this->data['Company']['logo']) && $this->data['Company']['logo'] != null){ 
			$image = array_values((array)json_decode($this->data['Company']['logo']));
			?>
				<div class="logo_thumbnail">
					<input type="hidden" name="full_file_name" value="<?php echo $image[0]; ?>" />
					<img style="width: 250px;" src="<?php echo $this->base .'/uploads/'. $image[0]; ?>" /><br/>
					<label class="checkbox" style="padding: 0 0 0 25px; margin-top: 10px;"><input data-toggle="checkbox" type="checkbox" name="delete_file" value="1"/><?php echo __('Check to delete file'); ?></label>
				</div>
			<?php } ?>
			<?php echo $this->Form->inputUpload(array(
				'fieldName' => 'data[Company][logo]', 
				'title' => 'Select a file or drop file to here',
				'allowExt' => array('png','jpg','gif','bmp','jpeg'),
				'autoStart' => true
				)); ?>
		</div>
	</div>
	<?php
	echo $this->Form->input('currency_id', array(
		'options' => $currencies,
		'class' => 'select2'
	));
	echo $this->Form->input('country_id', array(
		'options' => $countries,
		'class' => 'select2'
	));
	echo $this->Form->input('contact_number', array('label' => array('text' => 'Contact No', 'class' => 'col-lg-2 control-label')));
    echo $this->Form->input('website');
	$after = $this->Html->link('Cancel', array('action' => 'index'), array('class' => 'btn btn-default'));
	if(isset($this->data['Company']['id'])){
		$after .= $this->Html->topAction(__('Delete'), '', array('action' => 'delete', $this->data['Company']['id']),'danger', $this->data, 'Are you sure?');
	}
    echo $this->Form->submit('Submit', array(
		'class' => 'btn btn-primary',
		'div' =>  'form-group',
		'before' => '<div class="col-lg-offset-2 col-lg-10">',
		'after' => $after . '</div>',
	));
	
   
?>
<?php echo $this->Form->end(); ?>
</div>
<script type="text/javascript">
	$('.select2').select2({
		'width' : '100%'
	});
</script>
