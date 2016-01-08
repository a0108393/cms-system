<div class="top-btn-group">
<?php
echo $this->Html->topAction(__('Add New'), 'plus-sign', array('action' => 'add'), 'inverse');
?>
<div id="show-export" class="btn btn-default pull-right"><span class="glyphicon glyphicon-save"></span> Export</div>
<div id="show-search" class="btn btn-default pull-right"><span class="glyphicon glyphicon-search"></span> Search</div>
</div>

<?php echo $this->Form->create('Search', array('type' => 'Get' , 'class' => 'form-inline' ,'id' => 'form-search-common')); ?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		$this->Paginator->sort('id', __('Id')),
		$this->Paginator->sort('name', __('Name')),
		$this->Paginator->sort('email', __('Email')),
		$this->Paginator->sort('contact', __('Contact')),
		$this->Paginator->sort('company', __('Company')),
		$this->Paginator->sort('channel_name', __('Channel Name')),
		''
	));
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>
	<?php 
		$from_s = array(
			$this->Form->inputFilter('Enquiry','id', 'range', array('placeholder' => 'Id')),
			$this->Form->inputFilter('Enquiry','name', 'input', array('placeholder' => 'Name')),
			$this->Form->inputFilter('Enquiry','email', 'input', array('placeholder' => 'Email')),
			$this->Form->inputFilter('Enquiry','contact', 'input', array('placeholder' => 'Contact')),
			$this->Form->inputFilter('Enquiry','company', 'input', array('placeholder' => 'Company')),
			$this->Form->inputFilter('Enquiry','marketing_channels_id', 'select', array('placeholder' => 'channel', 'options' => $channels)),
			$this->Form->input('Search[Table]',array('type' => 'hidden','value' => 'Enquiry')).
			'<button type="submit" class="btn btn-info  btn-xs">Search</button>'			
		);
		$checks = array(
			$this->Form->checkExport('id', __('Id')),
			$this->Form->checkExport('name', __('Name')),
			$this->Form->checkExport('email', __('Email')),
			$this->Form->checkExport('contact', __('Contact')),
			$this->Form->checkExport('company', __('Company')),
			$this->Form->checkExport('channel_name', __('Channel Name')),
			'<button type="button" class="btn btn-info  btn-xs" onclick="exportToCSV($(this),\'Enquiry\');">Export</button>'
		)
	?>
	<tbody id="search" <?php echo isset($this->request->query['Search'])?'':'style="display:none"'; ?>>
		<?php echo $this->Html->tableHeaders($from_s); ?>
	<tbody>
	<tbody id="export-options" class="displaynone">
		<?php echo $this->Html->tableCells($checks); ?>
	<tbody>
<?php
	$rows = array();
	if($data && count($data) >0 ){
		foreach($data as $item){ 
			$rows[] = array(
				$item['Enquiry']['id'],
				$item['Enquiry']['name'],
				$item['Enquiry']['email'],
				$item['Enquiry']['contact'],
				$item['Enquiry']['company'],
				$item['Enquiry']['channel_name'],
				$this->Html->gridAction('edit', array('action' => 'edit', $item['Enquiry']['id']), $item).
				$this->Html->gridAction('trash', array('action' => 'delete', $item['Enquiry']['id']), $item, __('Are you sure?'))
			);
		}
	}
	echo $this->Html->tableCells($rows);
 ?>
</table>
<?php echo $this->Paginator->pagination(); ?>