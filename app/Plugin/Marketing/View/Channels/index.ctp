<div id="top-btn-group">
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
		__('Actions')
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
			$this->Form->inputFilter('Channel','id', 'range', array('placeholder' => 'Id')),
			$this->Form->inputFilter('Channel','name', 'input', array('placeholder' => 'Name')),
			$this->Form->input('Search[Table]',array('type' => 'hidden','value' => 'Channel')).
			'<button type="submit" class="btn btn-info  btn-xs">Search</button>'			
		);
	?>
	<tbody id="search" <?php echo isset($this->request->query['Search'])?'':'style="display:none"'; ?>>
		<?php echo $this->Html->tableHeaders($from_s); ?>
	<tbody>
<?php
	$rows = array();
	if(!empty($channels)){
		foreach($channels as $item) {
			$history = '';
			if($item['Channel']['history_link'] == 0) {
				$history = $this->Html->gridAction('folder-open', array('action' => 'history', $item['Channel']['id']), $item, false, array('title' => __('View history')));
			} 
			$rows[] = array(
				$item['Channel']['id'],
				$item['Channel']['name'],
				$history . $this->Html->gridAction('edit', array('action' => 'edit', $item['Channel']['id']), $item).
				$this->Html->gridAction('trash', array('action' => 'delete', $item['Channel']['id']), $item, __('Are you sure?'))
			);
		}
	}
	echo $this->Html->tableCells($rows);
 ?>
</table>
<?php echo $this->Paginator->pagination(); ?>