<div class="top-btn-group">
<?php
echo $this->Html->topAction(__('Add Ratecard'), 'plus-sign', array('action' => 'add'), 'inverse');
?>
<div id="show-search" class="btn btn-default pull-right"><span class="glyphicon glyphicon-search"></span> Search</div>
</div>
<?php echo $this->Form->create('Search', array('type' => 'Get' , 'class' => 'form-inline' ,'id' => 'form-search-common')); ?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		__('No.'),
		$this->Paginator->sort('name', __('Ratecard title')),
		__('Companies'),
		$this->Paginator->sort('discount', __('Discount')),
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
			$this->Form->inputFilter('Ratecard','name', 'input', array('placeholder' => 'Name')),
			'',
			$this->Form->inputFilter('Ratecard','discount', 'range', array('placeholder' => '')),
			$this->Form->inputFilter('Ratecard','id', 'range', array('placeholder' => '')),
			$this->Form->input('Search[Table]',array('type' => 'hidden','value' => 'Ratecard')).'<button type="submit" class="btn btn-info  btn-xs">Search</button>'			
		);
	?>
	<tbody id="search" <?php echo isset($this->request->query['Search'])?'':'style="display:none"'; ?>>
		<?php echo $this->Html->tableHeaders($from_s); ?>
	<tbody>
<?php
	$rows = array();
	$key = 0;
	foreach($data as $item){ 
		$history = '';
		if($item['Ratecard']['history_link'] == 0) {
			$history = $this->Html->gridAction('folder-open', array('action' => 'history', $item['Ratecard']['id']), $item, false, array('title' => __('View history')));
		}
		$key++;
		$rows[] = array(
			$key,
			$this->Html->gridLink($item['Ratecard']['name'], array('action' => 'view', $item['Ratecard']['id']), $item),
			$item['Ratecard']['companies'],
			($item['Ratecard']['type'] == 1) ? $item['Ratecard']['discount'] . '%' : 'custom',
			$item['Ratecard']['id'],
			$history . $this->Html->gridAction('edit', array('action' => 'edit', $item['Ratecard']['id']), $item).
			(($item['Ratecard']['default'] != 1) ? $this->Html->gridAction('trash', array('action' => 'delete', $item['Ratecard']['id']), $item) : '').
			(($item['Ratecard']['type'] == 2) ? $this->Html->gridAction('usd', array('action' => 'edit_price', $item['Ratecard']['id']), $item) : '')
		);
	 }
	echo $this->Html->tableCells($rows);
 ?>
</table>
<?php echo $this->Form->end(); ?>
<?php echo $this->Paginator->pagination(); ?>
<?php echo $this->Paginator->limitbox(); ?>