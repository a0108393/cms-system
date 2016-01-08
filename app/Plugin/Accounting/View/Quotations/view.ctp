<?php
	if(!empty($data)):
	$options = Configure::read('accounting.quotation_status');
?>
<div class="form-horizontal">
	<div class="form-group">
		<label for="" class="col-lg-2 control-label"><?php echo __('Estimate Number'); ?></label>
		<div class="col-lg-10" style="padding-top: 7px;">
			<?php echo $data['Quotation']['estimate_number']; ?>
		</div>
	</div>
	<div class="form-group">
		<label for="" class="col-lg-2 control-label"><?php echo __('Client'); ?></label>
		<div class="col-lg-10" style="padding-top: 7px;">
			<?php echo $companies[$data['Quotation']['client_id']]; ?>
		</div>
	</div>
	<div class="form-group">
		<label for="" class="col-lg-2 control-label"><?php echo __('Date'); ?></label>
		<div class="col-lg-10" style="padding-top: 7px;">
			<?php echo formatDate($data['Quotation']['date']); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="" class="col-lg-2 control-label"><?php echo __('PO/Ref Number'); ?></label>
		<div class="col-lg-10" style="padding-top: 7px;">
			<?php echo ($data['Quotation']['po_ref_number']); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="" class="col-lg-2 control-label"><?php echo __('Tax'); ?></label>
		<div class="col-lg-10" style="padding-top: 7px;">
			<?php echo ($data['Quotation']['tax']. '%'); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="" class="col-lg-2 control-label"><?php echo __('Validity'); ?></label>
		<div class="col-lg-10" style="padding-top: 7px;">
			<?php echo ($data['Quotation']['validity']); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="" class="col-lg-2 control-label"><?php echo __('Status'); ?></label>
		<div class="col-lg-10" style="padding-top: 7px;">
			<?php echo $options[$data['Quotation']['status']]; ?>
		</div>
	</div>
	<div class="form-group">
		<label for="" class="col-lg-2 control-label"><?php echo __('Subject'); ?></label>
		<div class="col-lg-10" style="padding-top: 7px;">
			<?php echo $data['Quotation']['subject']; ?>
		</div>
	</div>
	<div class="form-group">
		<label for="" class="col-lg-2 control-label"><?php echo __('Ratecard'); ?></label>
		<div class="col-lg-10" style="padding-top: 7px;">
			<?php echo $ratecards[$data['Quotation']['ratecard_id']]; ?>
		</div>
	</div>
	
	

	<table class="table table-striped">
	<?php
		$tableHeaders = $this->Html->tableHeaders(array(
			__('No.'),
			__('Services'),
			__('Description'),
			__('Quantity'),
			__('Price'),
			__('Waiver'),
		));
	?>
	<thead>
		<?php echo $tableHeaders; ?>
	</thead>
	<?php 
		$key = 0;
		if(!empty($data['QuotationDetail'])) {
			foreach($data['QuotationDetail'] as $item){ 
				$key++;
				$rows[] = array(
					$key,
					$product_service[$item['product_service_id']],
					nl2br($item['product_description']),
					$item['quantity'],
					$item['price'],
					(($item['waiver_status'] == '1') ? 'No' : 'Yes'),
				);
			}
			echo $this->Html->tableCells($rows);
		}

	?>
	</table>
	<div class="form-group">
		<label for="" class="col-lg-2 control-label"><?php echo __('Discount'); ?></label>
		<div class="col-lg-10" style="padding-top: 7px;">
			<?php echo $data['Quotation']['discount']. (($data['Quotation']['discount_type'] == 'percentage') ? '%' : ''); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="" class="col-lg-2 control-label"><?php echo __('Total'); ?></label>
		<div class="col-lg-10" style="padding-top: 7px;">
			<?php echo $data['Quotation']['total']; ?>
		</div>
	</div>
	<div class="form-group">
		<label for="" class="col-lg-2 control-label"><?php echo __('Payment structure'); ?></label>
		<div class="col-lg-10" style="padding-top: 7px;">
			<?php echo $data['Quotation']['payment_structure']; ?>
		</div>
	</div>
	<div class="form-group">
		<label for="" class="col-lg-2 control-label"><?php echo __('Late payment'); ?></label>
		<div class="col-lg-10" style="padding-top: 7px;">
			<?php echo $data['Quotation']['late_payment']; ?>
		</div>
	</div>
	<div class="form-group">
		<label for="" class="col-lg-2 control-label"><?php echo __('Bank information'); ?></label>
		<div class="col-lg-10" style="padding-top: 7px;">
			<?php echo $data['Quotation']['bank_information']; ?>
		</div>
	</div>
	<!--client view-->
	<?php if( in_array(Configure::read('Settings.Company.DefaultGroupId'), $group) && $this->Session->read('Auth.User.company_id') == $data['Quotation']['client_id']): 
		$approve = '';
		if($data['Quotation']['status'] == 3)
			$approve = $this->Html->link(__('Approve'), array('action' => 'approve', $data['Quotation']['id']), array('class' => 'btn btn-success'));
	?>

	<div class="form-group">
		<label for="" class="col-lg-2 control-label"></label>
		<div class="col-lg-10" style="padding-top: 7px;">
			<?php echo $this->Html->link(__('Download pdf'), array('action' => 'viewPdf', $data['Quotation']['id']), array('class' => 'btn btn-info')). ' '. $approve; ?>
		</div>
	</div>
	<?php endif; ?>
	<!--staff view-->
	<?php if( in_array(Configure::read('Settings.Company.SalesStaffGroupId'), $group) ): ?>
		<?php if($data['Quotation']['status'] == 5 && $data['Quotation']['invoice_status'] == 0): ?>
			<div class="form-group">
				<label for="" class="col-lg-2 control-label"></label>
				<div class="col-lg-10" style="padding-top: 7px;">
					<?php echo $this->Form->postLink(__('Generate Invoice'),
						array('action' => 'generateInvoice', $data['Quotation']['id']),
						array('class' => 'btn btn-success', 'confirm' => 'Are you sure?')
					); ?>
				</div>
			</div>

		<?php endif; ?>
		<?php if(isset($list_invoices)): ?>
			<ul>
			<?php foreach ($list_invoices as $key => $value): ?>
				<li><?php echo $this->Html->link($value['Quotation']['estimate_number'], array('action' => 'view', $value['Quotation']['id'])); ?></li>
			<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	<?php endif; ?>
</div>

<?php endif; ?>