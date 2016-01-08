<div class="top-btn-group">
<?php
echo $this->Html->topAction(__('Add New'), 'plus-sign', array('action' => 'add'), 'inverse');
?>
<div id="show-search" class="btn btn-default pull-right"><span class="glyphicon glyphicon-search"></span> Search</div>
</div>

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
	// $tableHeaders = $this->Html->tableHeaders(array(
		// __('Id'),
		// __('Name'),
		// ''
	// ));
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>
	<?php 
		$from_s = array(
			$this->Form->inputFilter('Event','id', 'range', array('placeholder' => 'Id')),
			$this->Form->inputFilter('Event','name', 'input', array('placeholder' => 'Name')),
			$this->Form->inputFilter('Event','start_date', 'range_date', array('placeholder' => '')),
			$this->Form->inputFilter('Event','end_date', 'range_date', array('placeholder' => '')),
			$this->Form->input('Search[Table]',array('type' => 'hidden','value' => 'Event')).
			'<button type="submit" class="btn btn-info  btn-xs">Search</button>'			
		);
	?>
	<tbody id="search" <?php echo isset($this->request->query['Search'])?'':'style="display:none"'; ?>>
		<?php echo $this->Html->tableHeaders($from_s); ?>
	<tbody>
<?php
	$rows = array();
	if($events && count($events) >0 ){
		foreach($events as $item){ 
			$history = '';
			if($item['Event']['history_link'] == 0) {
				$history = $this->Html->gridAction('folder-open', array('action' => 'history', $item['Event']['id']), $item, false, array('title' => __('View history')));
			}
			$rows[] = array(
				$item['Event']['id'],
				$item['Event']['name'],
				formatDate($item['Event']['start_date']),
				((strtotime($item['Event']['end_date']) ==0) ? "" : formatDate($item['Event']['end_date'])),
				$history . $this->Html->gridAction('edit', array('action' => 'edit', $item['Event']['id']), $item).
				$this->Html->gridAction('trash', array('action' => 'delete', $item['Event']['id']), $item, __('Are you sure?'))
			);
		}
	}
	echo $this->Html->tableCells($rows);
 ?>
</table>
<?php echo $this->Paginator->pagination(); ?>