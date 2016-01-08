
<div class="top-btn-group">
<?php echo $this->Html->topAction(__('Add New'), 'plus-sign', array('action' => 'add'), 'inverse'); ?>

<div id="show-search" class="btn btn-default pull-right"><span class="glyphicon glyphicon-search"></span> Search</div>
</div>
<?php echo $this->Form->create('Search', array('type' => 'Get' , 'class' => 'form-inline' ,'id' => 'form-search-common')); ?>
<div class="table-responsive">
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		$this->Paginator->sort('id', __('Id')),
		$this->Paginator->sort('name', __('Name')),
		$this->Paginator->sort('email', __('Email')),
		$this->Paginator->sort('company', __('Company')),
		$this->Paginator->sort('active', __('State')),
		$this->Paginator->sort('date_joined', __('Date created')),
		''
	));
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>
	<?php 
		$from_s = array(
			$this->Form->inputFilter('User','id', 'range', array('placeholder' => '')),
			$this->Form->inputFilter('User','name', 'input', array('placeholder' => 'Name')),
			$this->Form->inputFilter('User','email', 'input', array('placeholder' => 'Email')),
			$this->Form->inputFilter('User','company_id', 'select', array('placeholder' => 'Company', 'options' => $companies)),
			$this->Form->inputFilter('User','active', 'select', array('placeholder' => 'state', 'options' => $stateOptions)),
			$this->Form->inputFilter('User','date_joined', 'range_date', array('placeholder' => 'Date')),
			$this->Form->input('Search[Table]',array('type' => 'hidden','value' => 'User')).'<button type="submit" class="btn btn-info  btn-xs">Search</button>'			
		);
	?>
	<tbody id="search" <?php echo isset($this->request->query['Search'])?'':'style="display:none"'; ?>>
		<?php echo $this->Html->tableHeaders($from_s); ?>
	<tbody>
<?php
	$rows = array();
	$options = array(0 => __('Not active'), 1 => __('Active'), 2 => __('Blocked'), 3 => __('Expired'));
	foreach($data as $item){ 
		$rows[] = array(
			$item['User']['id'],
			$item['User']['name'],
			$item['User']['email'],
			$item['User']['company'],
			$options[$item['User']['active']],
			$item['User']['date_joined'],
			$this->Html->gridAction('edit', array('action' => 'edit', $item['User']['id']), $item).
			$this->Html->gridAction('trash', array('action' => 'delete', $item['User']['id']), $item, __('Are you sure?'))
		);
	 }
	echo $this->Html->tableCells($rows);
 ?>
</table>
</div>
<?php echo $this->Form->end(); ?>
<?php echo $this->Paginator->pagination(); ?>
<?php echo $this->Paginator->limitbox(); ?>