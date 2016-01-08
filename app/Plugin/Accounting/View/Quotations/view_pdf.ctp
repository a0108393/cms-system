<style type="text/css">
	table td {
		padding: 5px 10px;
	}
	table.first {
		border-collapse: collapse;
	}
	table.first td {
		border: 1px solid #000;
	}
</style>
<?php 
if(!empty($quotation_detail)) { ?>
	<table class="first" cellpadding="4">
		<tr>
			<td><?php echo __('Estimate Number')?></td>
			<td><?php echo $data['Quotation']['estimate_number'];?></td>
		</tr>
		<tr>
			<td><?php echo __('Client')?></td>
			<td><?php echo $companies[$data['Quotation']['client_id']];?></td>
		</tr>
		<tr>
			<td><?php echo __('Date')?></td>
			<td><?php echo formatDate($data['Quotation']['date']);?></td>
		</tr>
		<tr>
			<td><?php echo __('PO/Ref Number')?></td>
			<td><?php echo $data['Quotation']['po_ref_number'];?></td>
		</tr>
		<tr>
			<td><?php echo __('Tax (percentage)')?></td>
			<td><?php echo $data['Quotation']['tax'];?></td>
		</tr>
		<tr>
			<td><?php echo __('Subject')?></td>
			<td><?php echo $data['Quotation']['subject'];?></td>
		</tr>
		<tr>
			<td><?php echo __('Ratecard')?></td>
			<td><?php echo $ratecards[$data['Quotation']['ratecard_id']];?></td>
		</tr>
		<tr>
			<td colspan="2">
				<table class="second" cellpadding="4" cellspacing="6">
					<tr>
						<td><?php echo __('Services');?></td>
						<td><?php echo __('Description');?></td>
						<td><?php echo __('Qty');?></td>
						<td><?php echo __('Price');?></td>
						<td><?php echo __('Waiver');?></td>
					</tr>
<?php
	foreach($quotation_detail as $k => $item) {
		echo '<tr><td>'. $product_service[$item['QuotationDetail']['product_service_id']] .'</td>';
		echo '<td>'.nl2br($item['QuotationDetail']['product_description']) .'</td>';
		echo '<td>'. $item['QuotationDetail']['quantity'] .'</td>';
		echo '<td>'. $item['QuotationDetail']['price'] .'</td>';
		echo '<td>'. (($item['QuotationDetail']['waiver_status'] == 1) ? "No" : "Yes") .'</td></tr>';
	}
?>	
				</table>
			</td>
		</tr>
	</table>
<?php } ?>