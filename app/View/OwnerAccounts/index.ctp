<div class="settings form">
<?php 
	echo $this->Form->create('OwnerAccount');
	echo $this->Form->input('company_name');
	echo $this->Form->input('address');
	echo $this->Form->input('email');
	echo $this->Form->input('website');
	
	echo $this->Form->input('contact_number');
	echo $this->Form->input('fax');
?>
	<input type="hidden" name="data[OwnerAccount][id]" value="<?php echo (isset($this->request->data['OwnerAccount']['id']) ? $this->request->data['OwnerAccount']['id'] : 0)?>" />
	<div class="form-group">
		<label class="col-lg-2 control-label" for="OwnerAccountLogo">Logo</label>
		<div class="col-lg-10">
			<?php if(isset($this->data['OwnerAccount']['logo']) && $this->data['OwnerAccount']['logo'] != null){ 
			$image = array_values((array)json_decode($this->data['OwnerAccount']['logo']));
			?>
				<div class="logo_thumbnail">
					<input type="hidden" name="full_file_name" value="<?php echo $image[0]; ?>" />
					<img style="width: 250px;" src="<?php echo $this->base .'/uploads/'. $image[0]; ?>" /><br/>
					<label class="checkbox" style="padding: 0 0 0 25px; margin-top: 10px;"><input data-toggle="checkbox" type="checkbox" name="delete_file" value="1"/><?php echo __('Check to delete file'); ?></label>
				</div>
			<?php } ?>
			<?php echo $this->Form->inputUpload(array(
				'fieldName' => 'data[OwnerAccount][logo]', 
				'title' => 'Select a file or drop file to here',
				'allowExt' => array('png','jpg','gif','bmp','jpeg'),
				'autoStart' => true
				)); ?>
		</div>
	</div>
<?php
	echo $this->Form->input('timezone', array(
		'options' => tz_list(),
		'class' => 'select2'
	)); 
	echo $this->Form->input('country_id', array(
		'options' => $countries,
		'class' => 'select-ui-primary'
	));
	echo $this->Form->end(_('Save'));
?>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#OwnerAccountTimezone').select2({
			'width': '100%'
		})
	})
</script>