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
		$this->Paginator->sort('title', __('Title')),
		$this->Paginator->sort('client_name',__('Client name')),
		$this->Paginator->sort('date', __('Date')),
		$this->Paginator->sort('start_time', __('Start time')),
		$this->Paginator->sort('end_time', __('End time')),
		$this->Paginator->sort('id', __('Id')),
		__('Actions')
	));
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>
	<?php 
		$from_s = array(
			'',
			$this->Form->inputFilter('MeetingMinute','title', 'input', array('placeholder' => 'Title')),
			$this->Form->inputFilter('MeetingMinute','client_name', 'select', array('options' => $clients, 'placeholder' => 'Client name')),
			$this->Form->inputFilter('MeetingMinute','date', 'range_date', array('placeholder' => '')),
			'',
			'',
			$this->Form->inputFilter('MeetingMinute','id', 'range', array('placeholder' => '')),
			$this->Form->input('Search[Table]',array('type' => 'hidden','value' => 'MeetingMinute')).
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
			$this->Html->link($item['MeetingMinute']['title'], array('controller' => 'MeetingMinutes', 'action' => 'view', $item['MeetingMinute']['id'])),
			$item['MeetingMinute']['client_name'],
			formatDate($item['MeetingMinute']['date']),
			$item['MeetingMinute']['start_time'],
			$item['MeetingMinute']['end_time'],
			$item['MeetingMinute']['id'],
			$this->Html->gridAction('edit', array('action' => 'edit', $item['MeetingMinute']['id']), $item).
			$this->Html->gridAction('trash', array('action' => 'delete', $item['MeetingMinute']['id']), $item, __('Are you sure?'))
		);
	 }
	echo $this->Html->tableCells($rows);
 ?>
</table>
<?php echo $this->Form->end(); ?>
<?php echo $this->Paginator->pagination(); ?>
<?php echo $this->Paginator->limitbox(); ?>