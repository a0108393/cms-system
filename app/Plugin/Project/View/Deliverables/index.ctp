<div class="top-btn-group">
<?php
	echo $this->Html->topAction(__('Add New'), 'plus-sign', array('action' => 'add'), 'inverse');
?>
<div id="show-search" class="btn btn-default pull-right"><span class="glyphicon glyphicon-search"></span> Search</div>
<?php echo $this->Form->create('Search', array('type' => 'Get' , 'class' => 'form-inline' ,'id' => 'form-search-common')); ?>
<?php

foreach($project_ids as $project_id){

	$rows = array();
	$key = 0;
	foreach($data as $item){ 
		if($project_id == $item['Project']['id']){
			$project_name = $item['Project']['name'];
			$key++;			
			$rows[] = array(
				$key,
				$this->Html->link($item['Deliverable']['name'], array('controller' => 'Deliverables', 'action' => 'view', $item['Deliverable']['id'])),
				$item['Deliverable']['type'],
				formatDate($item['Deliverable']['date']),
				$this->Html->gridAction('eye-open', array('action' => 'detail', $item['Deliverable']['id']), $item).
				$this->Html->gridAction('edit', array('action' => 'edit', $item['Deliverable']['id']), $item).
				$this->Html->gridAction('trash', array('action' => 'delete', $item['Deliverable']['id']), $item, __('Are you sure?'))
			);
		}
	}
	if($key == 0){
		continue;
	}


	echo '<h6>'. $project_name . '</h6>';
	echo '<table class="table table-striped">';
	
	$tableHeaders = $this->Html->tableHeaders(array(
		__('No.'),
		$this->Paginator->sort('name', __('Deliverable')),
		$this->Paginator->sort('type', __('Type')),
		$this->Paginator->sort('date', __('Date'))
	));
	
	
	echo '<thead>';

	echo $tableHeaders;
	echo '</thead>';
	 
	$from_s = array(
		$this->Form->inputFilter('Deliverable', 'name', 'input', array('placeholder' => 'Title')),
		$this->Form->inputFilter('Deliverable', 'type', 'input', array('placeholder' => 'Type')),
		$this->Form->inputFilter('Deliverable', 'id', 'range', array('placeholder' => '')),
		$this->Form->inputFilter('Deliverable', 'date', 'range_date', array('placeholder' => '')),
			
		$this->Form->input('Search[Table]',array('type' => 'hidden','value' => 'Deliverable')).
		'<button type="submit" class="btn btn-info  btn-xs">Search</button>'			
	);
	
	echo '<tbody id="search" ';
	echo isset($this->request->query['Search'])?'':'style="display:none"';
	echo '>';
	
	echo $this->Html->tableCells($from_s);
	echo '<tbody>';
	echo $this->Html->tableCells($rows);

	echo '</table>';
}

?>

<?php echo $this->Form->end(); ?>
<?php echo $this->Paginator->pagination(); ?>
<?php echo $this->Paginator->limitbox(); ?>