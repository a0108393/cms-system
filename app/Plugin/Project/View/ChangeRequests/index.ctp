<div class="top-btn-group">
<?php
	echo $this->Html->topAction(__('Add New'), 'plus-sign', array('action' => 'add'), 'inverse');
?>
<div id="show-search" class="btn btn-default pull-right"><span class="glyphicon glyphicon-search"></span> Search</div>
<?php echo $this->Form->create('Search', array('type' => 'Get' , 'class' => 'form-inline' ,'id' => 'form-search-common')); ?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		__('No.'),
		$this->Paginator->sort('name', __('Title')),
		$this->Paginator->sort('type', __('Type')),
		$this->Paginator->sort('date', __('Date')),
		$this->Paginator->sort('id', __('Id')),
		__('Actions')
	));
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>
	<?php 
		$from_s = array(
			$this->Form->inputFilter('ChangeRequest','name', 'input', array('placeholder' => 'Title')),
			$this->Form->inputFilter('ChangeRequest','type', 'input', array('placeholder' => 'Type')),
			$this->Form->inputFilter('ChangeRequest','date', 'range_date', array('placeholder' => '')),
			$this->Form->inputFilter('ChangeRequest','id', 'range', array('placeholder' => '')),
			
			$this->Form->input('Search[Table]',array('type' => 'hidden','value' => 'ChangeRequest')).
			'<button type="submit" class="btn btn-info  btn-xs">Search</button>'			
		);
	?>
	<tbody id="search" <?php echo isset($this->request->query['Search'])?'':'style="display:none"'; ?>>
		<?php echo $this->Html->tableCells($from_s); ?>
	<tbody>
	<?php
	$rows = array();
	$key = 0;
	foreach($data as $item){ 
		$key++;			
		$rows[] = array(
			$key,
			$this->Html->link($item['ChangeRequest']['name'], array('controller' => 'ChangeRequests', 'action' => 'view', $item['ChangeRequest']['id'])),
			$item['ChangeRequest']['type'],
			formatDate($item['ChangeRequest']['date']),
			$item['ChangeRequest']['name'],
			$this->Html->gridAction('edit', array('action' => 'edit', $item['ChangeRequest']['id']), $item).
			$this->Html->gridAction('trash', array('action' => 'delete', $item['ChangeRequest']['id']), $item, __('Are you sure?'))
		);
	 }
	echo $this->Html->tableCells($rows);
 ?>
</table>
<?php echo $this->Form->end(); ?>
<?php echo $this->Paginator->pagination(); ?>
<?php echo $this->Paginator->limitbox(); ?>