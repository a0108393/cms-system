
<div class="top-btn-group">
<?php
/* 	echo $this->Form->inputUpload(array(
	'url' => $this->here,
	'successCallback' => '',
	'finishCallback' => 'window.location.reload();',
	'title' => 'Import CSV',
	'allowExt' => array('csv'),
	'data' => array('ax-type' => 'import', 'ax-model' => 'User')
	)); */
echo $this->Html->topAction(__('Add New'), 'plus-sign', array('action' => 'add'), 'inverse');
?>
<div id="show-export" class="btn btn-default pull-right"><span class="glyphicon glyphicon-save"></span> Export</div>
<div id="show-search" class="btn btn-default pull-right"><span class="glyphicon glyphicon-search"></span> Search</div>

</div>

<?php echo $this->Form->create('Search', array('type' => 'Get' , 'class' => 'form-inline' ,'id' => 'form-search-common')); ?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		__('No.'),
		$this->Paginator->sort('name', __('Name')),
		$this->Paginator->sort('email',__('Email')),
		$this->Paginator->sort('group', __('Group')),
		$this->Paginator->sort('active', __('State')),
		$this->Paginator->sort('date_joined', __('Date joined')),
		$this->Paginator->sort('last_login', __('Last login')),
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
			$this->Form->inputFilter('User','name', 'input', array('placeholder' => 'Name')),
			$this->Form->inputFilter('User','email', 'input', array('placeholder' => 'Email')),
			'',//$this->Form->inputFilter('Group','id', 'select', array('options' => $groups, 'placeholder' => 'Group')),
			$this->Form->inputFilter('User','active', 'select', array('options' => $stateOptions, 'placeholder' => 'State')),
			$this->Form->inputFilter('User','date_joined', 'range_date', array('placeholder' => '')),
			'',
			$this->Form->inputFilter('User','id', 'range', array('placeholder' => '')),
			$this->Form->input('Search[Table]',array('type' => 'hidden','value' => 'User')).
			'<button type="submit" class="btn btn-info  btn-xs">Search</button>'			
		);
		$checks = array(
			'',
			$this->Form->checkExport('name', __('Name')),
			$this->Form->checkExport('email', __('Email')),
			'',//$this->Form->checkExport('group', __('Group')),
			$this->Form->checkExport('active', __('State')),
			$this->Form->checkExport('date_joined', __('Date joined')),
			$this->Form->checkExport('last_login', __('Last login')),
			$this->Form->checkExport('id', __('Id')),
			'<button type="button" class="btn btn-info  btn-xs" onclick="exportToCSV($(this),\'User\');">Export</button>'
		)
		
	?>
	<tbody id="search" <?php echo isset($this->request->query['Search'])?'':'style="display:none"'; ?>>
		<?php echo $this->Html->tableCells($from_s); ?>
	<tbody>
	<tbody id="export-options" class="displaynone">
		<?php echo $this->Html->tableCells($checks); ?>
	<tbody>
	
	<?php
	$rows = array();
	$options = array(0 => __('Not active'), 1 => __('Active'), 2 => __('Blocked'), 3 => __('Expired'));
	$key = 0;

	foreach($users as $item){ 
		$key++;
		$group = '';
		if(!empty($item['Group'])) {
			foreach ($item['Group'] as $k => $value) {
				$group .= $value['name'] . ', ';
			}
			$group = substr($group, 0, -2);
		}
		$staff_view = '';
		if($this->Session->read('Auth.User.group_id') == (int) Configure::read('Settings.Company.SalesStaffGroupId') || $this->Session->read('Auth.User.id') == 1) {
			$staff_view .= $this->Html->gridAction('refresh', array('action' => 'resetPassword', $item['User']['id']), $item, __('Are you sure?'), array('title' => __('Reset Password')));
			if(strpos($group, __('Client'))) {
				$staff_view .= $this->Html->gridAction('pencil', array('action' => 'resetSignature', $item['User']['id']), $item, __('Are you sure?'), array('title' => __('Reset Signature')));
			}
		}
		if($item['User']['active'] == 1 && $item['User']['id'] != SUPER_ADMIN)
			$staff_view .= $this->Html->gridAction('lock', array('action' => 'block', $item['User']['id']), $item);
		
		$rows[] = array(
			$key,
			$item['User']['name'],
			$item['User']['email'],
			$group,
			$options[$item['User']['active']],
			$item['User']['date_joined'],
			$item['User']['last_login'],
			$item['User']['id'],
			$staff_view.$this->Html->gridAction('edit', array('action' => 'edit', $item['User']['id']), $item).
			$this->Html->gridAction('trash', array('action' => 'delete', $item['User']['id']), $item, __('Are you sure?'))
		);
	 }
	echo $this->Html->tableCells($rows);
 ?>
</table>
<?php echo $this->Form->end(); ?>
<?php echo $this->Paginator->pagination(); ?>
<?php echo $this->Paginator->limitbox(); ?>