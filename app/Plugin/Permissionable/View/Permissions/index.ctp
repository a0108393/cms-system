<?php 
echo $this->Form->create(); 
?>

<table class="table table-striped" id="collapse_table">
<?php
	$tableHeaders = $this->Html->tableHeaders($data['header']);
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>
	<tbody>
	
<?php 
$count = 0; 
if(!empty($data['rows'])) :
	foreach($data['rows'] as $key1=>$row) : 
	$count++; ?>
	<tr class="parent_row<?php echo ($count == 1) ? ' current-show':''; ?>">
		<td colspan="<?php echo count($data['header']); ?>">
			<?php echo $row['name']; ?>
			<input type="hidden" name="data[<?php echo $key1; ?>][module_id]" value="<?php echo $row['module_id']; ?>" />
		</td>
	</tr>
	<?php foreach($row['config_key'] as $key2=>$config_key) { ?>
		<tr class="child_row<?php echo ($count == 1) ? ' first':''; ?>">
			<td class="title">
				<?php echo $config_key['title']; ?>
			</td>
			<?php foreach($config_key['groups'] as $key3=>$group){ ?>
			<td>
				<?php if(isset($group['per_id'])){ ?>
					<input type="hidden" name="data[<?php echo $key1; ?>][<?php echo $key2; ?>][<?php echo $key3; ?>][per_id]" value="<?php echo $group['per_id']; ?>" />
				<?php } ?>
					<input type="hidden" name="data[<?php echo $key1; ?>][<?php echo $key2; ?>][<?php echo $key3; ?>][aro_id]" value="<?php echo $key3; ?>" />
					<input class="hasTooltip" title="<?php echo __('No acess'); ?>" type="radio" name="data[<?php echo $key1; ?>][<?php echo $key2; ?>][<?php echo $key3; ?>][value]" value="-1" <?php echo ($group['value'] == -1) ? 'checked="checked"':''; ?>/>
					<input class="hasTooltip" title="<?php echo __('Row acess'); ?>" type="radio" name="data[<?php echo $key1; ?>][<?php echo $key2; ?>][<?php echo $key3; ?>][value]" value="0" <?php echo ($group['value'] == 0) ? 'checked="checked"':''; ?>/>
					<input class="hasTooltip" title="<?php echo __('Global acess'); ?>" type="radio" name="data[<?php echo $key1; ?>][<?php echo $key2; ?>][<?php echo $key3; ?>][value]" value="1" <?php echo ($group['value'] == 1) ? 'checked="checked"':''; ?>/>
			</td>
			<?php } ?>
		</tr>
	<?php } ?>
<?php 
	endforeach; 
endif;
?>
	</tbody>
</table>
<?php echo $this->Form->submit('Submit', array('bootstrap' => false)); ?>
<?php echo $this->Form->end(); ?>
<script type="text/javascript">
	$('#collapse_table tr.parent_row').click(function(){
		var $el = $(this);
		var visible = $el.nextUntil('tr.parent_row').toggle().is(":visible");
		if(visible){
			$('#collapse_table tr.parent_row.current-show').removeClass('current-show').nextUntil('tr.parent_row').hide();
			$el.addClass('current-show');
		}else{
			$el.removeClass('current-show');
		}
	});
</script>