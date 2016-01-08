<!-- app/View/Ratecard Edit list products/form.ctp -->
<?php 
	$modifer = '<div class="pull-right">' .
	'<table>' .
		'<tr>' .
			'<td>'. __('Modifier %: ') .'</td>' .
			'<td><input type="text" width="100" id="modifer-discount-rate" value="" /></td>' .
			'<td><input type="button" class="btn btn-success" value="Apply" onclick="modiferPrice();"/></td>' .
		'</tr>' .
	'</table></div>';
	
	echo $this->Form->create();
	echo $this->Form->submit('Save', array(
		'div' => 'top-btn-group',
		'before' => '', 
		'after' => '<a class="btn btn-info" href="javascript:void(0)" onclick="recloneValue();">Reclone</a>'. $modifer));
?>

<table class="table table-striped customedit">
	<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		__('Services'),
		__('Standard price'),
		__('Custom price')
	));
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>
	<?php foreach($services as $key=>$item){  ?>
	<tr>
		<td><?php echo $item['Service']['name']; ?>
			<input type="hidden" name="mapdata[<?php echo $key; ?>][ratecard_id]" value="<?php echo $item['RatecardService']['ratecard_id']; ?>">
			<input type="hidden" name="mapdata[<?php echo $key; ?>][service_id]" value="<?php echo $item['RatecardService']['service_id']; ?>">
		</td>
		<td>
			<?php echo $item['Service']['price']; ?>
		</td>
		<td>
			<?php if($item['Service']['locked']){ ?>
				<input type="text" value="<?php echo $item['RatecardService']['amount']; ?>" disabled="disabled" /> <span><?php echo __('Locked'); ?></span>
				<input type="hidden" name="mapdata[<?php echo $key; ?>][amount]" value="<?php echo $item['RatecardService']['amount']; ?>" />
			<?php }else{ ?>
				<input type="text" class="service_amount_new" name="mapdata[<?php echo $key; ?>][amount]" value="<?php echo $item['RatecardService']['amount']; ?>" default-data="<?php echo $item['Service']['price']; ?>">
			<?php } ?>
		</td>
		
	</tr>
	<?php } ?>
</table>
<div class="form-group">
	<div class="col-lg-10">
	<label id="send_notify_check" class="checkbox">
		<span class="icons">
			<span class="first-icon fui-checkbox-unchecked"></span>
			<span class="second-icon fui-checkbox-checked"></span>
		</span>
	<input type="checkbox" name="data[Ratecard][notify]" />
	<?php echo __('Send notify to clients.'); ?>
	</label>
	</div>
</div>

<?php 

	echo $this->Form->submit('Save', array('div' => 'top-btn-group', 'before' => '', 'after' => '<a class="btn btn-info" href="javascript:void(0)" onclick="recloneValue();">Reclone</a>'));
	echo $this->Form->end();

?>
<script type="text/javascript">
	function recloneValue(){
		BootstrapDialog.confirm('Are you sure reclone?', function(result){
			if(result){
				$('.service_amount_new').each(function(){
					$(this).val($(this).attr('default-data'));
				});
			}
		});
	}
	$('#send_notify_check').click(function(){
		var check = $(this).children('input').prop('checked');
		if(check == false){
			$('#notify_message').removeClass('displaynone');
		}else{
			$('#notify_message').addClass('displaynone').val('');
		}
	});
</script>