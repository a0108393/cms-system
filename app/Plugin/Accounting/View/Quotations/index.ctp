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
		$this->Paginator->sort('id', __('Id')),
		$this->Paginator->sort('estimate_number', __('Estimate number')),
		$this->Paginator->sort('company_name', __('Company')),
		$this->Paginator->sort('po_ref_number', __('PO/Ref Number')),
		$this->Paginator->sort('status', __('Status')),
		$this->Paginator->sort('subject', __('Subject')),
		$this->Paginator->sort('ratecard_name', __('Ratecard')),
		''
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
			$this->Form->inputFilter('Quotation','id', 'range', array('placeholder' => 'Id')),
			$this->Form->inputFilter('Quotation','estimate_number', 'input', array('placeholder' => 'Estimate number')),
			$this->Form->inputFilter('Quotation','company_name', 'input', array('placeholder' => 'Company name')),
			$this->Form->inputFilter('Quotation','po_ref_number', 'input', array('placeholder' => 'PO/Ref Number')),
			$this->Form->inputFilter('Quotation','status', 'select', array('placeholder' => 'status', 'options' => Configure::read('accounting.quotation_status'))),
			$this->Form->inputFilter('Quotation','subject', 'input', array('placeholder' => 'Subject')),
			$this->Form->inputFilter('Quotation','ratecard_id', 'select', array('placeholder' => 'ratecard', 'options' => $ratecards)),
			$this->Form->input('Search[Table]',array('type' => 'hidden','value' => 'Quotation')).
			'<button type="submit" class="btn btn-info  btn-xs">Search</button>'			
		);
	?>
	<tbody id="search" <?php echo isset($this->request->query['Search'])?'':'style="display:none"'; ?>>
		<?php echo $this->Html->tableHeaders($from_s); ?>
	<tbody>
<?php
	$rows = array();
	if($data && count($data) >0 ){
		$options = Configure::read('accounting.quotation_status');
		$group = array();
		foreach ($this->Session->read('Auth.Group') as $key => $value) {
			$group[] = $value['id'];
		}
		foreach($data as $k => $item){ 
			$history = '';
			if($item['Quotation']['history_link'] == 0)
				$history = $this->Html->gridAction('folder-open', array('action' => 'history', $item['Quotation']['random_key']), $item, false, array('title' => 'History'));
			$client_edit = '';
			//client view
			if(( in_array(Configure::read('Settings.Company.DefaultGroupId'), $group) && $this->Session->read('Auth.User.company_id') == $item['Quotation']['client_id']) || $this->Session->read('Auth.User.id') == SUPER_ADMIN) {
				$client_edit .= $this->Html->gridAction('download-alt', array('action' => 'viewPdf', $item['Quotation']['id']), $item);
				if($item['Quotation']['status'] < 5)
					$client_edit .= $this->Html->gridAction('pencil', array('action' => 'clientView', $item['Quotation']['id']), $item);
			}
			//staff view
			if(in_array(Configure::read('Settings.Company.SalesStaffGroupId'), $group) || $this->Session->read('Auth.User.id') == SUPER_ADMIN) {
				if($item['Quotation']['status'] < 5)
					$client_edit .= $this->Html->gridAction('cog', array('action' => 'followUp', $item['Quotation']['id']), $item, false, array('title' => 'Follow up'));
			}
			$rows[] = array(
				$item['Quotation']['id'],
				$this->Html->link($item['Quotation']['estimate_number'], array('action' => 'view', $item['Quotation']['id'])),
				$item['Quotation']['company_name'],
				$item['Quotation']['po_ref_number'],
				$options[$item['Quotation']['status']],
				$item['Quotation']['subject'],
				$item['Quotation']['ratecard_name'],
				$client_edit . $history .
				(($item['Quotation']['status'] < 3) ? $this->Html->gridAction('edit', array('action' => 'edit', $item['Quotation']['id']), $item, false, array('title' => 'Edit')) : '' ) .
				$this->Html->gridAction('trash', array('action' => 'delete', $item['Quotation']['id']), $item, __('Are you sure?'), array('title' => 'Delete')) 
			);
		}
	}
	echo $this->Html->tableCells($rows);
 ?>
</table>
<?php echo $this->Paginator->pagination(); ?>