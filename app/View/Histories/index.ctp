
<div class="top-btn-group">
<div id="show-search" class="btn btn-default pull-right"><span class="glyphicon glyphicon-search"></span> Search</div>
</div>
<?php echo $this->Form->create('Search', array('type' => 'Get' , 'class' => 'form-inline' ,'id' => 'form-search-common')); ?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		__('No.'),
		$this->Paginator->sort('controller', __('Controller')),
		$this->Paginator->sort('action_status', __('Status')),
		$this->Paginator->sort('user_name', __('User modified')),
		$this->Paginator->sort('date_modified', __('Date modified')),
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
			$this->Form->inputFilter('History','controller', 'input', array('placeholder' => 'Controller')),
			$this->Form->inputFilter('History','action_status', 'select', array('options' => array(), 'placeholder' => 'Status')),
			$this->Form->inputFilter('History','user_name', 'index', array('placeholder' => 'User modified')),
			$this->Form->inputFilter('History','date_modified', 'range_date', array('placeholder' => '')),
			'',
			$this->Form->inputFilter('History','id', 'range', array('placeholder' => '')),
			$this->Form->input('Search[Table]',array('type' => 'hidden','value' => 'History')).
			'<button type="submit" class="btn btn-info  btn-xs">Search</button>'			
		);
		
	?>
	<tbody id="search" <?php echo isset($this->request->query['Search'])?'':'style="display:none"'; ?>>
		<?php echo $this->Html->tableCells($from_s); ?>
	<tbody>
	
	<?php
	$rows = array();
	$options = Configure::read('systems.history_status');
	$key = 0;

	foreach($data as $item){ 
		$link_original = '';
		if($item['History']['action_status'] != __('Delete')) 
			$link_original = $this->Html->link(__('View'), array('plugin' => $item['History']['plugin'], 'controller' => $item['History']['controller'], 'action' => $item['History']['action'], $item['History']['original_id']), array('class' => 'btn btn-info btn-xs', 'title' => 'View Original'));
		$key++;
		$rows[] = array(
			$key,
			ucfirst($item['History']['controller']),
			ucfirst($item['History']['action_status']),
			$item['History']['user_name'],
			formatDateTime($item['History']['date_modified']),
			$item['History']['id'],
			$this->Html->link(__('History'), array('plugin' => $item['History']['plugin'], 'controller' => $item['History']['controller'], 'action' => $item['History']['action'], $item['History']['history_id']), array('class' => 'btn btn-primary btn-xs', 'title' => 'View History')) .  $link_original
		);
	 }
	echo $this->Html->tableCells($rows);
 ?>
</table>
<?php echo $this->Form->end(); ?>
<?php echo $this->Paginator->pagination(); ?>
<?php echo $this->Paginator->limitbox(); ?>