<!-- app/View/Company/form.ctp -->
<?php 

?>
<div class="company form">
<?php echo $this->Form->create('Company'); ?>
<?php
    echo $this->Form->input('name', array('readonly' => true));
    echo $this->Form->input('industry_id', array(
		'options' => $industries,
		'class' => 'select-ui-primary',
		'disabled' => true
	));
    echo $this->Form->input('co_reg_no', array('readonly' => true));
    echo $this->Form->input('address', array('type' => 'text', 'readonly' => true));
    echo $this->Form->input('ratecard_id', array(
		'options' => $ratecards,
		'class' => 'select-ui-primary',
		'disabled' => true
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
		</div>
	</div>
	<?php
	echo $this->Form->input('currency_id', array(
		'options' => $currencies,
		'class' => 'select-ui-primary',
		'disabled' => true
	));
	echo $this->Form->input('country_id', array(
		'options' => $countries,
		'class' => 'select-ui-primary',
		'disabled' => true
	));
	echo $this->Form->input('contact_number', array('label' => array('text' => 'Contact No', 'class' => 'col-lg-2 control-label'), 'readonly' => true));
    echo $this->Form->input('website', array('readonly' => true));
?>
<?php echo $this->Form->end(); ?>
</div>
