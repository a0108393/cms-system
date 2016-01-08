<div id="actiongroup-container">
<?php
	echo $this->Html->topAction("Add Post", 'plus', array('action' => 'add'), 'info'); 
?>
</div>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		__('Id'),
		__('Title'),
		__('Created Date'),
		''
	));
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>
<?php
	$rows = array();
	foreach($posts as $item){ 
		$rows[] = array(
			$item['Post']['id'],
			$item['Post']['title'],
			date('d/m/Y', strtotime($item['Post']['created'])),
			$this->Html->gridAction('edit', array('action' => 'edit', $item['Post']['id']), $item).
			$this->Html->gridAction('trash', array('action' => 'delete', $item['Post']['id']), $item, __('Are you sure?'))
		);
	 }
	echo $this->Html->tableCells($rows);
 ?>
</table>
