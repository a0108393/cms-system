<div class="company form">
<?php echo $this->Form->create('Ratecard'); ?>
	<input type="hidden" name="data[Ratecard][sendType]" class="sendType" value="0" />
<?php
    echo $this->Form->input('content_notify', array(
		'type' => 'textarea'
	));
	if(!empty($clients)) {
		
		$i = 0;
		foreach($clients as $k => $item) {
			echo '<div class="form-group">';
			if(empty($i))
				echo '<label class="col-lg-2 control-label">'. __("Clients") .'</label>';
			else 
				echo '<label class="col-lg-2 control-label"></label>';
			echo '<div class="col-lg-10">
					<label class="send_notify_check checkbox">
						<span class="icons">
							<span class="first-icon fui-checkbox-unchecked"></span>
							<span class="second-icon fui-checkbox-checked"></span>
						</span>
						<input type="checkbox" data-toggle="checkbox" name="data[Ratecard][client]['. $k .']" />
						'. $item .'
					</label>
				</div>';
			echo '</div>';
			$i++;
		}
		
	}
	echo '<div class="form-group"><div class="col-lg-offset-2 col-lg-10">';
	echo $this->Form->button(__('Send to All'), array(
		'type' => 'button',
		'id' => 'btnSendAll',
		'class' => 'btn btn-info'
	));
	echo $this->Form->button(__('Send to Select'), array(
		'type' => 'button',
		'id' => 'btnSendSelect',
		'class' => 'btn btn-success'
	));
	echo $this->Form->button(__('Skip'), array(
		'type' => 'button',
		'id' => 'btnSkip',
		'class' => 'btn btn-default'
	));
	echo '</div></div>';
?>
<?php echo $this->Form->end(); ?>
<script type="text/javascript">
	$('#send_notify_check').click(function(){
		var check = $(this).children('input').prop('checked');
		if(check == false){
			$(this).parent().parent().next().removeClass('displaynone');
		}else{
			$(this).parent().parent().next().addClass('displaynone');
		}
	});
	$('#btnSendAll').click(function() {
		$('.sendType').val(2);
		$('#RatecardNotifyForm').find('input[type=checkbox]').prop('checked', 'checked');
		$('#RatecardNotifyForm').submit();
	});
	$('#btnSendSelect').click(function() {
		$('.sendType').val(2);
		var count_check = 0;
		$('#RatecardNotifyForm').find('input[type=checkbox]').each(function() {
			if($(this).prop('checked'))
				count_check++;
		});
		if(count_check > 0) {
			$('.sendType').val(1);
			$('#RatecardNotifyForm').submit();
		}
		else {
			BootstrapDialog.alert('<?php echo __("Please choose client!");?>');
		}
	});
	$('#btnSkip').click(function() {
		window.location.href = '<?php echo Router::url(array('plugin' => 'accounting', 'controller' => 'ratecards', 'action' => 'index'))?>';
	});
</script>