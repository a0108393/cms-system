<h2>
<?php echo __('View history'); ?>
</h2>
<?php echo $this->Form->create('Search', array('type' => 'Get' , 'class' => 'form-inline' ,'id' => 'form-search-common')); ?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		__('No.'),
		$this->Paginator->sort('name', __('Ratecard title')),
		$this->Paginator->sort('type', __('Ratecard type')),
		$this->Paginator->sort('discount', __('Discount')),
		$this->Paginator->sort('id', __('Id')),
		__('Actions')
	));
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>
<?php
	$rows = array();
	$key = 0;
	$ratecard_type = array(1 => __('Use Flat Retainer Discount Rate'), 2 => __('Use Custom Line Item Rate'));
	foreach($data as $item) { 
		$key++;
		$rows[] = array(
			$key,
			$this->Html->gridLink($item['Ratecard']['name'], array('action' => 'viewRatecard', $item['Ratecard']['id']), $item),
			$ratecard_type[$item['Ratecard']['type']],
			($item['Ratecard']['type'] == 1) ? $item['Ratecard']['discount'] . '%' : 'custom',
			$item['Ratecard']['id'],
			$this->Html->gridAction('eye-open', array('action' => 'viewRatecard', $item['Ratecard']['id']), $item)
		);
	 }
	echo $this->Html->tableCells($rows);
 ?>
</table>
<?php echo $this->Form->end(); ?>