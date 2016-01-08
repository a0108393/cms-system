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
		__('No.'),
		$this->Paginator->sort('name', __('Name')),
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
			$this->Form->inputFilter('Category','name', 'input', array('placeholder' => 'Name')),
			$this->Form->inputFilter('Category','id', 'range', array('placeholder' => '')),
			$this->Form->input('Search[Table]',array('type' => 'hidden','value' => 'Category')).'<button type="submit" class="btn btn-info  btn-xs">Search</button>'			
		);
	?>
	<tbody id="search" <?php echo isset($this->request->query['Search'])?'':'style="display:none"'; ?>>
		<?php echo $this->Html->tableHeaders($from_s); ?>
	<tbody>
<?php
	$rows = array();
	$key = 0;
	foreach($data as $item){ 
		$key++;
		$rows[] = array(
			$key,
			$item['Category']['name'],
			$item['Category']['id'],
			$this->Html->gridAction('edit', array('action' => 'edit', $item['Category']['id']), $item).
			$this->Html->gridAction('trash', array('action' => 'delete', $item['Category']['id']), $item, __('Are you sure?'))
		);
	 }
	echo $this->Html->tableCells($rows);
 ?>
</table>
<?php echo $this->Form->end(); ?>
<?php echo $this->Paginator->pagination(); ?>
<?php echo $this->Paginator->limitbox(); ?>