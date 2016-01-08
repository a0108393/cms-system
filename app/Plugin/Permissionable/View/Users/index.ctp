<?php 

?>

<table class="table table-striped" id="collapse_table">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		__('Name'),
		__('Read'),
		__('Update'),
		__('Delete'),
		__('Create'),
		__('Actions')
	)); 
?>
	<thead>
		<?php 
		echo $tableHeaders;
		?>
	</thead>
	<tbody>
	
<?php $count = 0; foreach($users as $item){ $count++; ?>
	<tr class="parent_row<?php echo ($count == 1) ? ' current-show':''; ?>">
		<td colspan="7">
			<?php echo $item['User']['name']; ?>
		</td>
	</tr>
	<?php foreach($modules as $module){ ?>
		<form method="post" action="<?php echo $this->Html->url(array('action' => 'edit')); ?>">
		<tr class="child_row<?php echo ($count == 1) ? ' first':''; ?>">
			<td class="title">
				<?php echo $module['Module']['name']; ?>
				<input type="hidden" name="module_id" value="<?php echo $module['Module']['id']; ?>" />
				<input type="hidden" name="aro_id" value="<?php echo $item['User']['id']; ?>" />
				<input type="hidden" name="type" value="user" />
			</td>
			<?php foreach($configs as $key=>$config){ ?>
			<td width="15%">
				<?php 
				if(isset($genderValue[$item['User']['id']][$module['Module']['id']][$key])){
					$check = $genderValue[$item['User']['id']][$module['Module']['id']][$key];
				?>
					<input class="hasTooltip input<?php echo $key. '-1'; ?>" title="<?php echo __('No acess'); ?>" type="radio" name="<?php echo $key; ?>" value="-1" <?php echo ($check == -1) ? 'checked="checked"':''; ?>/>
					<input class="hasTooltip input<?php echo $key. '0'; ?>" title="<?php echo __('Row acess'); ?>" type="radio" name="<?php echo $key; ?>" value="0" <?php echo ($check == 0) ? 'checked="checked"':''; ?>/>
					<input class="hasTooltip input<?php echo $key. '1'; ?>" title="<?php echo __('Global acess'); ?>" type="radio" name="<?php echo $key; ?>" value="1" <?php echo ($check == 1) ? 'checked="checked"':''; ?>/>
				<?php
				}
				?>
			</td>
			<?php } ?>
			<td width="15%">
				<input onclick="submitPermission($(this), 'set');" type="button" class="btn btn-xs btn-success" name="set" value="<?php echo __('Set'); ?>"/>
				<input onclick="submitPermission($(this), 'reset');" type="<?php echo (empty($gender1[$item['User']['id']][$module['Module']['id']])) ? 'hidden':'button'; ?>" class="btn btn-xs btn-danger" name="reset" value="<?php echo __('Reset'); ?>"/>
			</td>
		</tr>
		</form>
	<?php } ?>
<?php } ?>
	</tbody>
</table>

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