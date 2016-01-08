<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		__('No.'),
		$this->Paginator->sort('name', __('Name')),
		$this->Paginator->sort('unit', __('Unit')),
		$this->Paginator->sort('price', __('Price per unit')),
		$this->Paginator->sort('locked', __('Lock price')),
		$this->Paginator->sort('category_name', __('Category')),
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
	foreach($data as $item){ 
		$key++;
		$rows[] = array(
			$key,
			$item['Service']['name'],
			$item['Service']['unit'],
			$item['Service']['price'],
			($item['Service']['locked']) ? '<span class="fui-check"></span>':'<span class="fui-cross"></span>',
			$item['Service']['category_name'],
			$item['Service']['id'],
			$this->Html->gridAction('eye-open', array('action' => 'view', $item['Service']['id']), $item)
		);
	 }
	echo $this->Html->tableCells($rows);
 ?>
</table>
<?php echo $this->Form->end(); ?>
<?php echo $this->Paginator->pagination(); ?>
<?php echo $this->Paginator->limitbox(); ?>