
<table class="table table-striped">
<?php
	$tableHeaders = $this->Html->tableHeaders(array(
		__('No.'),
		__('Services'),
		__('Standard price'),
		__('Ratecard price')
	));
?>
<thead>
	<?php echo $tableHeaders; ?>
</thead>
<?php 
	$key = 0;
	foreach($data as $item){ 
		$key++;
		$rows[] = array(
			$key,
			$item['Service']['name'],
			$item['Service']['price'],
			$item['RatecardService']['amount']
		);
	 }
	echo $this->Html->tableCells($rows);

?>
</table>
<?php 
