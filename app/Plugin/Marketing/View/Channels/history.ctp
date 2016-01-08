<h2>
<?php echo __('View history'); ?>
</h2>
<?php echo $this->Form->create('Search', array('type' => 'Get' , 'class' => 'form-inline' ,'id' => 'form-search-common')); ?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		$this->Paginator->sort('id', __('Id')),
		$this->Paginator->sort('name', __('Name')),
		''
	));
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>
<?php
	$rows = array();
	if(!empty($channels)){
		foreach($channels as $item) {
			$rows[] = array(
				$item['Channel']['id'],
				$item['Channel']['name'],
				$this->Html->gridAction('eye-open', array('action' => 'view', $item['Channel']['id']), $item)
			);
		}
	}
	echo $this->Html->tableCells($rows);
 ?>
</table>
<?php echo $this->Paginator->pagination(); ?>