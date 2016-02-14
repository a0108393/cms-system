<head>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
</head>



<body>




  <h2>Project Dashboard</h2>

  <div class="top-btn-group">
<?php
	echo $this->Html->topAction(__('Add Project'), 'plus-sign', array('action' => 'add'), 'inverse');
	echo $this->Html->topAction(__('View Closed Projects'), 'plus-sign', array('action' => 'add'), 'inverse');
?>
<div id="show-search" class="btn btn-default pull-right"><span class="glyphicon glyphicon-search"></span> Search</div>
<?php echo $this->Form->create('Search', array('type' => 'Get' , 'class' => 'form-inline' ,'id' => 'form-search-common')); ?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		__('ID.'),
		$this->Paginator->sort('name', __('Name')),
		$this->Paginator->sort('client',__('Client')),
		$this->Paginator->sort('date_created', __('Date Created')),
		__('Actions')
	));
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>
	<?php 
		$from_s = array(
			'',
			$this->Form->inputFilter('Project','name', 'input', array('placeholder' => 'Name')),
			$this->Form->inputFilter('Project','owner', 'input', array('placeholder' => 'Client')),
			$this->Form->inputFilter('Project','date', 'date_created', array('placeholder' => '')),
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
	//	if($this->Session->read('Auth.User.id') != $item['Project']['owner'] || $this->Session->read('Auth.User.id')= 46) {
	//		continue;
	//	}
	//	else {
			$key++;			
			$rows[] = array(
				$key,
				$this->Html->link($item['Project']['name'], array('controller' => 'Dashboards', 'action' => 'view', $item['Project']['id'])),
				$item['Project']['owner'],
				$item['Project']['project_start'],
				
				//$this->Html->gridAction('response', array('action' => 'response', $item['Brief']['id']), $item).
				$this->Html->gridAction('trash', array('action' => 'delete', $item['Project']['id']), $item, __('Are you sure?'))
			
			);
//	}
	 }
	echo $this->Html->tableCells($rows);
 ?>
</table>

<?php echo $this->Form->create('Search', array('type' => 'Get' , 'class' => 'form-inline' ,'id' => 'form-search-common')); ?>
<?php

	echo '<table class="table table-bordered">';
	
		$tableHeaders = $this->Html->tableHeaders(array(
		__('ID.'),
		$this->Paginator->sort('name', __('Name')),
		$this->Paginator->sort('assignment', __('Assignment')),
		$this->Paginator->sort('date', __('Date')),
	    $this->Paginator->sort('deadline', __('Deadline')),
		$this->Paginator->sort('project_name', __('Project Name')),
		$this->Paginator->sort('deliverable_name', __('Deliverable Name'))
	));
	
	
	echo '<thead>';

	echo $tableHeaders;
	echo '</thead>';

	echo '</table>';


?>


<?php echo $this->Form->end(); ?>
<?php echo $this->Paginator->pagination(); ?>
<?php echo $this->Paginator->limitbox(); ?>
  



</body>