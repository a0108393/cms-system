<?php echo $this->Form->create(); ?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		array(__('Module') => array('style' => 'vertical-align: middle')),
		__('Owner View'). '<br/><label id="headercheck_2" class="checkbox"><input data-toggle="checkbox" type="checkbox" /></label>',
		__('Owner Edit'). '<br/><label id="headercheck_3" class="checkbox"><input data-toggle="checkbox" type="checkbox" /></label>',
		__('Owner Delete'). '<br/><label id="headercheck_4" class="checkbox"><input data-toggle="checkbox" type="checkbox" /></label>',
		__('Group View'). '<br/><label id="headercheck_5" class="checkbox"><input data-toggle="checkbox" type="checkbox" /></label>',
		__('Group Edit'). '<br/><label id="headercheck_6" class="checkbox"><input data-toggle="checkbox" type="checkbox" /></label>',
		__('Group Delete'). '<br/><label id="headercheck_7" class="checkbox"><input data-toggle="checkbox" type="checkbox" /></label>',
		__('Public View'). '<br/><label id="headercheck_8" class="checkbox"><input data-toggle="checkbox" type="checkbox" /></label>',
		__('Public Edit'). '<br/><label id="headercheck_9" class="checkbox"><input data-toggle="checkbox" type="checkbox" /></label>',
		__('Public Delete'). '<br/><label id="headercheck_10" class="checkbox"><input data-toggle="checkbox" type="checkbox" /></label>'
	));
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>
<?php
	echo $this->Html->tableCells($data, null, null, true);
 ?>
</table>
<?php echo $this->Form->submit('Submit', array('bootstrap' => false)); ?>
<?php echo $this->Form->end(); ?>
<script type="text/javascript">
	$('th .checkbox').click(function(){
		var column = $(this).attr('id').split('_')[1];
			var $els = $('.column-' + column + ' .checkbox');
		if($(this).hasClass('checked')){
			$els.removeClass('checked');
			$els.children('input[type="checkbox"]').prop('checked', false);
		}else{
			$els.addClass('checked');
			$els.children('input[type="checkbox"]').prop('checked', true);
		}
	});
	for(var i = 2; i <= 10; i++){
		var head_el = $('#headercheck_' + i);
		var check  = findCheck(i);
			if(check == 1){
				head_el.addClass('checked');
				head_el.children('input[type="checkbox"]').prop('checked', true);
			}else{
				head_el.removeClass('checked');
				head_el.children('input[type="checkbox"]').prop('checked', false);
			}
	}
	function findCheck(i){
		var $els = $('.column-' + i + ' .checkbox');
		var check = 1;
		$els.each(function(){
			if(!$(this).hasClass('checked')){
				check = 0;
			}
		});
		return check;
	}
</script>