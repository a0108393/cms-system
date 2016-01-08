<div class="top-btn-group">
<?php
	echo $this->Form->inputUpload(array(
	'url' => $this->Html->url(array(
			"plugin" => "company",
			"controller" => "industries",
			"action" => "import"
		)),
	'successCallback' => '',
	'finishCallback' => 'window.location.reload();',
	'title' => 'Import CSV',
	'allowExt' => array('csv')
	));
?>
<?php
echo $this->Html->topAction(__('Add Industry'), 'plus-sign', array('action' => 'add'), 'inverse');
?>
<div id="show-search" class="btn btn-default pull-right"><span class="glyphicon glyphicon-search"></span> Search</div>
</div>
<?php echo $this->Form->create('Search', array('type' => 'Get' , 'class' => 'form-inline' ,'id' => 'form-search-common')); ?>

<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		__('No.'),
		$this->Paginator->sort('name', __('Industry name')),
		$this->Paginator->sort('no_company', __('No of companies')),
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
			$this->Form->inputFilter('Industry','name', 'input', array('placeholder' => 'Name')),
			'',
			$this->Form->inputFilter('Industry','id', 'range', array('placeholder' => '')),
			$this->Form->input('Search[Table]',array('type' => 'hidden','value' => 'Industry')).
			'<button type="submit" class="btn btn-info  btn-xs">Search</button>'			
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
			$item['Industry']['name'],
			$item['Industry']['no_company'],
			$item['Industry']['id'],
			$this->Html->gridAction('edit', array('action' => 'edit', $item['Industry']['id']), $item).
			$this->Html->gridAction('trash', array('action' => 'delete', $item['Industry']['id']), $item, __('Are you sure?'))
		);
	 }
	echo $this->Html->tableCells($rows);
 ?>
</table>
<?php echo $this->Form->end(); ?>
<?php echo $this->Paginator->pagination(); ?>
<?php echo $this->Paginator->limitbox(); ?>