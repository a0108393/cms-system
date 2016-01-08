<h2>
<?php echo __('View history'); ?>
</h2>
<?php echo $this->Form->create('Search', array('type' => 'Get' , 'class' => 'form-inline' ,'id' => 'form-search-common')); ?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		$this->Paginator->sort('id', __('Id')),
		$this->Paginator->sort('name', __('Name')),
		$this->Paginator->sort('start_date', __('Start Date')),
		$this->Paginator->sort('end_date', __('End Date')),
		''
	));
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>
<?php
	$rows = array();
	if(!empty($data)){
		foreach($data as $item){ 
			$rows[] = array(
				$item['Event']['id'],
				$item['Event']['name'],
				formatDate($item['Event']['start_date']),
				((strtotime($item['Event']['end_date']) ==0) ? "" : formatDate($item['Event']['end_date'])),
				$this->Html->gridAction('eye-open', array('action' => 'view', $item['Event']['id']), $item)
			);
		}
	}
	echo $this->Html->tableCells($rows);
 ?>
</table>
<?php echo $this->Paginator->pagination(); ?>