<h2>
<?php echo __('View history'); ?>
</h2>
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
		__('Actions'),
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
	?>
	<tbody id="search" <?php echo isset($this->request->query['Search'])?'':'style="display:none"'; ?>>
		<?php echo $this->Html->tableHeaders($from_s); ?>
	<tbody>
<?php
	$rows = array();
	if($links && count($links) >0 ){
		foreach($links as $k => $item){ 
			$rows[] = array(
				$item['AdvertisingLink']['id'],
				$item['AdvertisingLink']['description'],
				$item['AdvertisingLink']['channel_name'],
				'<a href="' . $item['AdvertisingLink']['destination_url'] . '" target="_blank">' . $item['AdvertisingLink']['destination_url'] . '</a>',
				'<div style="position:relative;"><span class="generate_url">' . Router::url('/dlink/', true).$item['AdvertisingLink']['generated_url'] . '</span></div>',
				$item['AdvertisingLink']['visits'],
				$this->Html->gridAction('eye-open', array('action' => 'view', $item['AdvertisingLink']['id']), $item)
			);
		}
	}
	echo $this->Html->tableCells($rows);
 ?>
</table>
<?php echo $this->Paginator->pagination(); ?>