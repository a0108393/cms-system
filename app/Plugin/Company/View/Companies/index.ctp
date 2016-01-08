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
		$this->Paginator->sort('name', __('Company name')),
		$this->Paginator->sort('industry_name', __('Industry')),
		$this->Paginator->sort('co_reg_no',__('Co. Reg. No')),
		$this->Paginator->sort('rate_title', __('Ratecard')),
		$this->Paginator->sort('country', __('Country')),
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
			$this->Form->inputFilter('Company','name', 'input', array('placeholder' => 'Name')),
			$this->Form->inputFilter('Company','industry_id', 'select', array('placeholder' => 'Industry', 'options' => $industries)),
			$this->Form->inputFilter('Company','co_reg_no', 'input', array('placeholder' => 'Code')),
			$this->Form->inputFilter('Company','ratecard_id', 'select', array('placeholder' => 'Card', 'options' => $ratecards)),
			$this->Form->inputFilter('Company','country_id', 'select', array('placeholder' => 'Country', 'options' => $countries)),
			$this->Form->inputFilter('Company','id', 'range', array('placeholder' => '')),
			$this->Form->input('Search[Table]',array('type' => 'hidden','value' => 'Company')).
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
		$history = '';
		if($item['Company']['history_link'] == 0) {
			$history = $this->Html->gridAction('folder-open', array('action' => 'history', $item['Company']['id']), $item, false, array('title' => __('View history')));
		} 
		$rows[] = array(
			$key,
			$item['Company']['name'],
			($item['Company']['industry_name']) ? $item['Company']['industry_name'] : __('Not Classified'),
			$item['Company']['co_reg_no'],
			$this->Html->gridLink($item['Company']['rate_title'], array('plugin' => 'accounting', 'controller' => 'ratecards', 'action' => 'view', $item['Company']['ratecard_id']), $item),
			$item['Company']['country'],
			$item['Company']['id'],
			$history . $this->Html->gridAction('edit', array('action' => 'edit', $item['Company']['id']), $item) 
			// . $this->Html->gridAction('trash', array('action' => 'delete', $item['Company']['id']), $item, __('Are you sure?'))
		);
	 }
	echo $this->Html->tableCells($rows);
 ?>
</table>
<?php echo $this->Form->end(); ?>
<?php echo $this->Paginator->pagination(); ?>
<?php echo $this->Paginator->limitbox(); ?>