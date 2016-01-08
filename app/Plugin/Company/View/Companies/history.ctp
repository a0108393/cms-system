<h2>
<?php echo __('View history'); ?>
</h2>
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
	$rows = array();
	$key = 0;
	if(!empty($data)){
		foreach($data as $item) {
			$key++;
			$rows[] = array(
				$key,
				$item['Company']['name'],
				($item['Company']['industry_name']) ? $item['Company']['industry_name'] : __('Not Classified'),
				$item['Company']['co_reg_no'],
				$this->Html->gridLink($item['Company']['rate_title'], array('plugin' => 'accounting', 'controller' => 'ratecards', 'action' => 'view', $item['Company']['ratecard_id']), $item),
				$item['Company']['country'],
				$item['Company']['id'],
				$this->Html->gridAction('eye-open', array('action' => 'view', $item['Company']['id']), $item)
			);
		}
	}
	echo $this->Html->tableCells($rows);
 ?>
</table>
<?php echo $this->Paginator->pagination(); ?>