<table class="table table-striped" id="tblQuotation">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		__('No.'),
		__('Estimate number'),
		__('Company'),
		__('PO/Ref Number'),
		__('Status'),
		__('Subject'),
		__('Ratecard'),
		__('Total'),
		''
	));
?>
<thead>
	<?php echo $tableHeaders; ?>
</thead>
<?php 
	$key = 0;
	if(!empty($histories)) {
		$options = Configure::read('accounting.quotation_status');
		foreach($histories as $item){ 
			$key++;
			$rows[] = array(
				$key,
				$item['Quotation']['estimate_number'],
				$item['Company']['name'],
				$item['Quotation']['po_ref_number'],
				$options[$item['Quotation']['status']],
				$item['Quotation']['subject'],
				$item['Ratecard']['name'],
				number_format($item['Quotation']['total'], 2),
				'<button type="button" rel="'. $item['Quotation']['id'] .'" class="viewDetail btn btn-info">'. __("Detail") .'</button>'
			);
		}
		echo $this->Html->tableCells($rows);
	}

?>
</table>
<div id="dvResult"></div>




<script type="text/javascript">
	$('.viewDetail').click(function() {
		$('#tblQuotation tbody').find('tr').removeClass('checked');
		$(this).parent().parent().addClass('checked');
		$('#dvResult').html('<img src="<?php echo $this->base;?>/images/loading.gif" />');
		$.ajax({
			url: '<?php echo Router::url(array('plugin' => 'accounting', 'controller' => 'quotations', 'action' => 'bindDetailHistory'))?>',
			type: 'post',
			data: 'quotation_id=' + $(this).attr('rel'),
			success: function(data) {
				$('#dvResult').html(data);
			}
		});
	});
</script>