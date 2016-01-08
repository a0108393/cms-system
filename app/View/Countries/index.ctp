<div class="top-btn-group">
	<?php
	echo $this->Html->topAction(__('Add Country'), 'plus-sign', array('action' => 'add'), 'inverse');
	?>
	<div id="show-search" class="btn btn-default pull-right"><span class="glyphicon glyphicon-search"></span> Search</div>
</div>
<?php echo $this->Form->create('Search', array('type' => 'Get' , 'class' => 'form-inline' ,'id' => 'form-search-common')); ?>
<table class="table table-striped" id="country_table">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		$this->Paginator->sort('id', __('Id')),
		$this->Paginator->sort('name', __('Name')),
		''
	));
?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>
	<?php 
		$from_s = array(
			$this->Form->inputFilter('Country','id', 'range', array('placeholder' => 'Id')),
			$this->Form->inputFilter('Country','name', 'input', array('placeholder' => 'Name')),
			$this->Form->input('Search[Table]',array('type' => 'hidden','value' => 'Country')).
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
				$item['Country']['id'],
				$item['Country']['name'],
				$this->Html->gridAction('edit', array('action' => 'edit', $item['Country']['id']), $item).
				$this->Html->gridAction('trash', array('action' => 'delete', $item['Country']['id']), $item, __('Are you sure?'))
			),
			array('id' => 'rowsort-' . $item['Country']['id']), 
			false
		);
	}
 ?>
</table>
<?php echo $this->Paginator->pagination(); ?>
<script type="text/javascript">
	$("#country_table tbody").sortable({
		update: function(event, ui){
			$.post("<?php echo $this->Html->url(array('plugin' => null, 'controller' => 'countries', 'action' => 'ajaxSort')); ?>", { pages: $('#country_table tbody').sortable('serialize') } );
		}
	});
</script>