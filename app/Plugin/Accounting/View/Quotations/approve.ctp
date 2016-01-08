<?php 
	echo $this->Form->create('Quotation'); 
	echo '<div class="form-group"><label for="QuotationValidity" class="col-lg-2 control-label">Validity</label><div class="col-lg-10" style="padding-top:7px;">';
	echo ceil($data['Quotation']['validity'] + (strtotime($data['Quotation']['date']) - time())/86400 ) . ' days';
	echo '</div></div>';
?>
	<div class="form-group">
		<label class="col-lg-2 control-label"></label>
		<div class="col-lg-10">
		<label id="send_notify_check" class="checkbox">
			<span class="icons">
				<span class="first-icon fui-checkbox-unchecked"></span>
				<span class="second-icon fui-checkbox-checked"></span>
			</span>
		<input type="checkbox" id="checked_term" name="data[Quotation][term]" />
		<?php echo __('I have read and understand the <a href="">Terms and Conditions</a> of Service by agreeing to accept this cost estimate'); ?>
		</label>
		</div>
	</div>
<?php
	echo $this->Form->input('approval_signature');
	echo $this->Form->submit(__('Approve'), array('id' => 'btnApprove'));
	echo $this->Form->end();
?>
<script type="text/javascript">
	$('#btnApprove').click(function() {
		if (document.getElementById('checked_term').checked) {
			$.ajax({
				url: '<?php echo Router::url(array('plugin' => 'accounting', 'controller' => 'quotations', 'action' => 'compareSignature'))?>',
				type: 'post',
				data: 'signature=' + $('#QuotationApprovalSignature').val(),
				success: function(data) {
					if(data == 'pass') {
						$('#QuotationApproveForm').submit();
					}
					else {
						BootstrapDialog.show({
							title: 'Signature incorrect',
							message: data
						});
					}
				}
			});
		}
		else {
			BootstrapDialog.show({
				title: 'Form error',
				message: 'Please check Terms and Conditions'
			});
		}
		return false;
	});
</script>