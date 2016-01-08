<div class="top-btn-group">
	<?php
	echo $this->Html->topAction(__('Add Currency'), 'plus-sign', array('action' => 'add'), 'inverse');
	?>
	<div id="show-search" class="btn btn-default pull-right"><span class="glyphicon glyphicon-search"></span> Search</div>
</div>
<?php echo $this->Form->create('Search', array('type' => 'Get' , 'class' => 'form-inline' ,'id' => 'form-search-common')); ?>
<table class="table table-striped" id="Currency-table">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		$this->Paginator->sort('id', __('Id')),
		$this->Paginator->sort('name', __('Name')),
		$this->Paginator->sort('code', __('Code')),
		$this->Paginator->sort('country_name', __('Country')),
		''
	));
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>
	<?php 
		$from_s = array(
			$this->Form->inputFilter('Currency','id', 'range', array('placeholder' => 'Id')),
			$this->Form->inputFilter('Currency','name', 'input', array('placeholder' => 'Name')),
			$this->Form->inputFilter('Currency','code', 'input', array('placeholder' => 'Code')),
			$this->Form->inputFilter('Currency','country_name', 'input', array('placeholder' => 'Country')),
			$this->Form->input('Search[Table]',array('type' => 'hidden','value' => 'Currency')).
			'<button type="submit" class="btn btn-info  btn-xs">Search</button>'			
		);
	?>
	<tbody id="search" <?php echo isset($this->request->query['Search'])?'':'style="display:none"'; ?>>
		<?php echo $this->Html->tableHeaders($from_s); ?>
	<tbody>
<?php
	$rows = array();
	foreach($data as $item){ 
		echo $this->Html->tableCells(
			 array(
				$item['Currency']['id'],
				$item['Currency']['name'],
				$item['Currency']['code'],
				$item['Currency']['country_name'],
				$this->Html->gridAction('edit', array('action' => 'edit', $item['Currency']['id']), $item).
				$this->Html->gridAction('trash', array('action' => 'delete', $item['Currency']['id']), $item, __('Are you sure?'))
			),
			array('id' => 'rowsort-' . $item['Currency']['id']), 
			false
		);
	}
 ?>
</table>
<?php echo $this->Paginator->pagination(); ?>
<script type="text/javascript">
	// $("#Currency-table tbody").sortable({
	// 	update: function(event, ui){
	// 		$.post("<?php echo $this->Html->url(array('plugin' => null, 'controller' => 'currencies', 'action' => 'ajaxSort')); ?>", { pages: $('#Currency-table tbody').sortable('serialize') } );
	// 	}
	// });
</script>