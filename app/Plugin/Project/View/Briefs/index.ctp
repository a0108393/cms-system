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
		$this->Paginator->sort('project_title', __('Project title')),
		$this->Paginator->sort('job_number',__('Job number')),
		$this->Paginator->sort('company_name', __('Company name')),
		$this->Paginator->sort('date', __('Date')),
		$this->Paginator->sort('user_name', __('User created')),
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
			$this->Form->inputFilter('Brief','project_title', 'input', array('placeholder' => 'Project title')),
			$this->Form->inputFilter('Brief','job_number', 'input', array('placeholder' => 'Job number')),
			$this->Form->inputFilter('Brief','company_name', 'select', array('options' => $companies, 'placeholder' => 'Company')),
			$this->Form->inputFilter('Brief','date', 'range_date', array('placeholder' => '')),
			$this->Form->inputFilter('Brief','user_name', 'input', array('placeholder' => 'User created')),
			$this->Form->inputFilter('Brief','id', 'range', array('placeholder' => '')),
			$this->Form->input('Search[Table]',array('type' => 'hidden','value' => 'Brief')).
			'<button type="submit" class="btn btn-info  btn-xs">Search</button>'			
		);
	?>
	<tbody id="search" <?php echo isset($this->request->query['Search'])?'':'style="display:none"'; ?>>
		<?php echo $this->Html->tableCells($from_s); ?>
	<tbody>
	<?php
	$rows = array();
	$key = 0;
	foreach($data as $item) {
		$icon_edit = '';
		if($this->Session->read('Auth.User.id') != $item['Brief']['user_created'] && $item['Brief']['status'] == 0) {
			continue;
		}
		else {
			$key++;			
			$rows[] = array(
				$key,
				$this->Html->link($item['Brief']['project_title'], array('controller' => 'briefs', 'action' => 'view', $item['Brief']['id'])),
				$item['Brief']['job_number'],
				$item['Brief']['company_name'],
				formatDate($item['Brief']['date']),
				((empty($item['Brief']['user_name'])) ? __('Visitor') : $item['Brief']['user_name']),
				$item['Brief']['id'],
				$this->Html->gridAction('edit', array('action' => 'edit', $item['Brief']['id']), $item).
				$this->Html->gridAction('trash', array('action' => 'delete', $item['Brief']['id']), $item, __('Are you sure?'))
			);
		}
	 }
	echo $this->Html->tableCells($rows);
 ?>
</table>
<?php echo $this->Form->end(); ?>
<?php echo $this->Paginator->pagination(); ?>
<?php echo $this->Paginator->limitbox(); ?>