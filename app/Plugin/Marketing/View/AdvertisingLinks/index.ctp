<div id="top-btn-group">
<?php
echo $this->Html->topAction(__('Add New'), 'plus-sign', array('action' => 'add'), 'inverse');
?>
<div id="show-export" class="btn btn-default pull-right"><span class="glyphicon glyphicon-save"></span> Export</div>
<div id="show-search" class="btn btn-default pull-right"><span class="glyphicon glyphicon-search"></span> Search</div>
</div>
<div class="clearfix"></div>
<?php echo $this->Form->create('Search', array('type' => 'Get' , 'class' => 'form-inline' ,'id' => 'form-search-common')); ?>
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		$this->Paginator->sort('id', __('Id')),
		$this->Paginator->sort('description', __('Description')),
		$this->Paginator->sort('channel_name', __('Channel Name')),
		$this->Paginator->sort('destination_url', __('Destination Url')),
		$this->Paginator->sort('generated_url', __('Generated Url')),
		$this->Paginator->sort('visits', __('Visits')),
		$this->Paginator->sort('no_enquiry', __('Enquiries')),
		$this->Paginator->sort('conversion', __('Conversion Rate')),
		__('Actions')
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
			$this->Form->inputFilter('AdvertisingLink','id', 'range', array('placeholder' => 'Id')),
			$this->Form->inputFilter('AdvertisingLink','description', 'input', array('placeholder' => 'Name')),
			$this->Form->inputFilter('Channel','id', 'select', array('options' => $channels, 'placeholder' => 'Channel')),
			$this->Form->inputFilter('AdvertisingLink','destination_url', 'input', array('placeholder' => 'Destination Url')),
			$this->Form->inputFilter('AdvertisingLink','generated_url', 'input', array('placeholder' => 'Generated Url')),
			'',	'', '',
			$this->Form->input('Search[Table]',array('type' => 'hidden','value' => 'AdvertisingLink')).
			'<button type="submit" class="btn btn-info  btn-xs">Search</button>'			
		);
		$checks = array(
			$this->Form->checkExport('id', __('Id')),
			$this->Form->checkExport('description', __('Description')),
			$this->Form->checkExport('channel_name', __('Channel name')),
			$this->Form->checkExport('destination_url', __('Destination Url')),
			$this->Form->checkExport('generated_url', __('Generated Url')),
			$this->Form->checkExport('visits', __('Visits')),
			$this->Form->checkExport('no_enquiry', __('Enquiries')),
			'<button type="button" class="btn btn-info  btn-xs" onclick="exportToCSV($(this),\'AdvertisingLink\');">Export</button>'
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
	if(!empty($links)) {
		foreach($links as $k => $item){ 
			$history = '';
			if($item['AdvertisingLink']['history_link'] == 0) {
				$history = $this->Html->gridAction('folder-open', array('action' => 'history', $item['AdvertisingLink']['id']), $item, false, array('title' => __('View history')));
			}
			$rows[] = array(
				$item['AdvertisingLink']['id'],
				$item['AdvertisingLink']['description'],
				$item['AdvertisingLink']['channel_name'],
				'<a href="' . $item['AdvertisingLink']['destination_url'] . '" target="_blank">' . $item['AdvertisingLink']['destination_url'] . '</a>',
				'<div style="position:relative;"><span class="generate_url">' . Router::url('/dlink/', true).$item['AdvertisingLink']['generated_url'] . '</span>  &nbsp;&nbsp;<button type="button" class="copy-button btn btn-xs btn-success">Copy</button></div>',
				$item['AdvertisingLink']['visits'],
				$item['AdvertisingLink']['no_enquiry'],
				number_format($item['AdvertisingLink']['conversion']*100,2) . '%',
				$history . $this->Html->gridAction('edit', array('action' => 'edit', $item['AdvertisingLink']['id']), $item).
				$this->Html->gridAction('trash', array('action' => 'delete', $item['AdvertisingLink']['id']), $item, __('Are you sure?'))
			);
		}
	}
	echo $this->Html->tableCells($rows);
 ?>
</table>
<?php echo $this->Paginator->pagination(); ?>

<script type="text/javascript">
$(".copy-button").zclip({
    path: "<?php echo Router::url('/'); ?>marketing/js/ZeroClipboard.swf",
    copy: function(){
	return $(this).prev().text();
	}
});

</script>