<div class="company form">
<?php echo $this->Form->create('Quotation'); ?>
	<input type="hidden" name="notify_status" class="notify_status" value="0" />
	<select id="clone_product" style="display:none">
		<option value="">Select one</option>
		<?php
		if(!empty($product_service)) {
			foreach($product_service as $k => $item) {
				echo '<option value="'. $k .'">'. $item .'</option>';
			}
		}
		?>
	</select>
	<div id="error_message" style="color: red;"></div>
<?php
    echo $this->Form->input('estimate_number',array('default' => $this->Quotation->render(3).'-1', 'readonly' => true));
	echo $this->Form->input('client_id', array(
		'empty'=>'Select one',
		'options' => $companies,
		'div'=>array('class'=>'form-group channels'),
		'class' => 'select2',
		'id'=>'company_select',
		'after'=>'<img class="loading" src="'.$this->base.'/images/loading.gif'.'" /> </div>'
	));
    echo $this->Form->inputDatepicker('date', array( 'label' => array('class' => 'col-lg-2 control-label', 'text' => 'Date' ), 'class' => 'form-control','id'=>'datetimepicker', 'default' => formatDate(date('m/d/Y'))));
	// var_dump(Configure::read('Settings.Accounting.apply_tax'));
	
	echo $this->Form->input('po_ref_number', array('label' => array('text' => 'PO/Ref Number', 'class' => 'col-lg-2 control-label')));
	if(Configure::read('Settings.Accounting.apply_tax') == 1) {
		echo $this->Form->input('tax', array('default' => Configure::read('Settings.Accounting.tax_percentage'), 'label' => array('class' => 'col-lg-2 control-label', 'text' => 'Tax (percentage)'), 'readonly' => 'readonly'));
	}
	else {
		echo '<div class="form-group"><label for="QuotationPoRefNumber" class="col-lg-2 control-label">Tax (percentage)</label><div class="col-lg-10"><a href="javascript:void(0)" id="apply_tax">Apply tax</a></div></div>';
	}
	echo $this->Form->input('currency_id', array(
		'options' => $currencies,
		'class' => 'select-ui-primary'
	));
	echo $this->Form->input('validity', array('default' => Configure::read('Settings.Accounting.validity')));
	// echo $this->Form->input('status', array(
	// 	'options' => Configure::read('accounting.quotation_status'),
	// 	'class' => 'select-ui-primary'
	// ));
	echo $this->Form->input('subject');
	echo $this->Form->input('ratecard_id',array(
		'id'=>'accounting_ratecard',
		'class' => 'select-ui-primary',
		'div' => array(
			'class' => 'form-group'
		),
		'empty'=>'Select one',
		'options' => ($ratecard_standard),
		'default' => $default_ratecard
	));
?>
<table class="table" id="tblProductService">
	<thead>
		<tr>
			<th><?php echo __('Product/Service')?></th>
			<th><?php echo __('Description')?></th>
			<th><?php echo __('Qty')?></th>
			<th><?php echo __('Unit Price')?></th>
			<th><?php echo __('Amount')?></th>
			<th><?php echo __('Action')?></th>
		</tr>
	</thead>
	<tbody>
		<tr id="mainrow">
			<td class="col1">
				<select name="data[QuotationDetail][0][product_service_id]" id="product_service" class="select2" onchange="change_product($(this))" required="required">
					<option value="">Select one</option>
					<?php
					if(!empty($product_service)) {
						foreach($product_service as $k => $item) {
							echo '<option value="'. $k .'">'. $item .'</option>';
						}
					}
					?>
				</select>
				<div class="displaynone error">Please select product/service</div>
			</td>
			<td class="col2"><div class="dv-populate"><textarea class="form-control" noeditor="true" rows="5" name="data[QuotationDetail][0][product_description]"></textarea></div></td>
			<td class="col3">
				<input name="data[QuotationDetail][0][quantity]" seperator="&lt;/div&gt;" class="form-control col_quantity" type="text" value="1" id="row_qty_0">
				<div class="displaynone error">Quantity is number and not empty.</div>
			</td>
			<td class="col4">
				<input name="data[QuotationDetail][0][price]" seperator="&lt;/div&gt;" class="form-control col_price" type="text" value="" id="row_price_0">
				<div class="displaynone error">Price is number and not empty.</div>
			</td>
			<td class="col5">
				<div class="amount_result"></div>
				<input type="hidden" name="data[QuotationDetail][0][waiver_status]" class="waiver_status" value="1" />
			</td>
			<td class="col6"><a href="javascript:void(0)" onclick="apply_waiver($(this))" class="apply_waiver">Apply Waiver</a></td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="6"><button type="button" id="btnAddRow" class="btn btn-info">Add row</button></td>
		</tr>
	</tfoot>
</table>
<?php 
	echo $this->Form->input('subtotal', array('readonly' => true));
	echo $this->Form->input('waiver', array('readonly' => true));
?>
<div class="form-group">
	<label for="QuotationDiscount" class="col-lg-2 control-label">Discount</label>
	<div class="col-lg-10">
		<input name="data[Quotation][discount]" seperator="&lt;/div&gt;" class="form-control" type="text" id="QuotationDiscount">
		<select name="data[Quotation][discount_type]" class="select-ui-primary" id="discount_type">
			<?php 
				if( count(Configure::read('accounting.quotation_discount')) > 0) {
					foreach(Configure::read('accounting.quotation_discount') as $k => $item) {
						echo "<option value=\"$k\">$item</option>";
					}
				}
			?>
		</select>
	</div>
</div>
<?php
	echo $this->Form->input('total_tax', array('readonly' => true));
	echo $this->Form->input('total', array('readonly' => true));
	echo $this->Form->input('payment_structure', array('type' => 'textarea', 'default' => Configure::read('Settings.Accounting.payment_structure')));
	echo $this->Form->input('late_payment', array('type' => 'textarea', 'default' => Configure::read('Settings.Accounting.late_payment')));
	echo $this->Form->input('bank_information', array('type' => 'textarea', 'default' => Configure::read('Settings.Accounting.bank_and_account_information')));
	echo '<div class="form-group"><div class="col-lg-offset-2 col-lg-10">';

	echo $this->Form->button(__('Save Estimate'), array(
		'type' => 'submit',
		'id' => 'btnOnlySave',
		'class' => 'btn btn-info'
	));
	echo $this->Form->button(__('Save and Send'), array(
		'type' => 'button',
		'id' => 'btnSaveSend',
		'class' => 'btn btn-success'
	));
	echo $this->Form->button(__('Cancel'), array(
		'type' => 'button',
		'id' => 'btnCancel',
		'class' => 'btn btn-default'
	));
	echo '</div>';
	echo $this->Form->end(); ?>
</div>
<?php
$this->Js->get('#company_select')->event('change', 
	$this->Js->request(array(
		'controller'=>'quotations',
		'action'=>'bindRatecard'
		), array(
		'update'=>'#accounting_ratecard',
		'async' => true,
		'method' => 'post',
		'dataExpression'=>true,
		'before'   => '$("#company_select").next().next().attr("style", "display:block;")', 
		'complete' => '$("#company_select").next().next().attr("style", "display:none");$("#error_message").find(".error_client").remove();$("#error_message").find(".error_ratecard").remove()',
		'data'=> $this->Js->serializeForm(array(
			'isForm' => true,
			'inline' => true
			))
	))
);

?>
<script type="text/javascript">
	$.fn.formatNumber = function() {
		$(this).keydown(function (e) {
			// Allow: backspace, delete, tab, escape, enter and .
			if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 109, 110, 189, 190]) !== -1 ||
				 // Allow: Ctrl+A
				(e.keyCode == 65 && e.ctrlKey === true) || 
				 // Allow: home, end, left, right
				(e.keyCode >= 35 && e.keyCode <= 39)) {
					 // let it happen, don't do anything
					 return;
			}
			// Ensure that it is a number and stop the keypress
			if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
				e.preventDefault();
			}
			
		});
		return this;
		
	};
	$.fn.setupQty = function() {
		$(this).keyup(function(){
			tr_parent = $(this).parent().parent();
			if( tr_parent.children('.col3').children('input').val() != '' && tr_parent.children('.col4').children('input').val() != '') {
				$(this).next().addClass('displaynone');
				amount_result = parseFloat(tr_parent.children('.col3').children('input').val()) * parseInt(tr_parent.children('.col4').children('input').val());
				tr_parent.children('.col5').children('.amount_result').text(format1(amount_result,''));
				update_subtotal();
			}
		});
		return this;
	};
	
	var fixId = 0;
	BootstrapDialog.confirm = function(message, callback) {
		new BootstrapDialog({
			title: 'Confirmation',
			message: message,
			closable: true,
			data: {
				'callback': callback
			},
			buttons: [{
					label: 'No',
					cssClass: 'btn-default',
					action: function(dialog) {
						typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(false);
						dialog.close();
					}
				}, {
					label: 'Yes',
					cssClass: 'btn-primary',
					action: function(dialog) {
						typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(true);
						dialog.close();
					}
				}]
		}).open();
	};
	$('#row_qty_0').formatNumber().setupQty();
	$('#row_price_0').formatNumber().setupQty();
	$('#product_service').select2({
		'width': '200'
	});
	$('#company_select').select2({
		'width': '200'
	});
	//event click apply tax
	$('#apply_tax').click(function() {
		$(this).parent().append('<input name="data[Quotation][tax]" seperator="&lt;/div&gt;" class="form-control" readonly="readonly" type="text" value="<?php echo Configure::read('Settings.Accounting.tax_percentage');?>" id="QuotationTax">');
		$('#error_message').find('.error_tax').remove();
		$(this).addClass('displaynone');
	});
	//event change ratecard
	$('#accounting_ratecard').click(function() {
		if($(this).val() != '') {
			$('#error_message').find('.error_ratecard').remove();
		}
	});
	//event click button addrow
	$('#btnAddRow').click(function() {
		var name = $('#tblProductService tbody tr').length;
		var newRow='<tr id="tr_row_'+name+'">';
                // newRow+='<td class="text-center">'+ parseInt(name + 1) +'</td>';
                newRow+='<td class="col1"><select class="select2" name="data[QuotationDetail]['+ name +'][product_service_id]" id="row_product_service_'+name+'" title="Please choose product" onchange="change_product($(this))"></select><div class="displaynone error">Please select product/service</div></td>';
                newRow+='<td class="col2"><div class="dv-populate"><textarea class="form-control" noeditor="true" rows="5" name="data[QuotationDetail]['+ name +'][product_description]"></textarea></div></td>';
                newRow+='<td class="col3"><input name="data[QuotationDetail]['+ name +'][quantity]" seperator="&lt;/div&gt;" class="form-control col_quantity" type="text" value="1" id="row_qty_'+name+'" /><div class="displaynone error">Quantity is number and not empty.</div></td>';
                newRow+='<td class="col4"><input name="data[QuotationDetail]['+ name +'][price]" seperator="&lt;/div&gt;" class="form-control col_price" type="text" value="" id="row_price_'+name+'" /><div class="displaynone error">Price is number and not empty.</div></td>';
				newRow += '<td class="col5"><div class="amount_result"></div><input type="hidden" name="data[QuotationDetail]['+ name +'][waiver_status]" class="waiver_status" value="1" /></td>'
                newRow+='<td class="col6"><a href="javascript:void(0)" onclick="apply_waiver($(this))" class="apply_waiver">Apply Waiver</a></td>';      
                newRow += '</tr>';
		if($('#tblProductService tbody tr').length == 0){
			$('#tblProductService tbody').append(newRow);
			
		}
		else{
			$('#tblProductService tbody tr:last').after(newRow);
		}
		$('#clone_product option').clone().appendTo("#row_product_service_"+name);
		$('#row_product_service_'+name+ '').select2({
			'width' : '200'
		});
		$('#row_qty_'+ name + '').formatNumber().setupQty();
		$('#row_price_'+ name + '').formatNumber().setupQty();
	});
	//change discount
	$('#QuotationDiscount').formatNumber().keyup(function(){
		update_total();
	});
	$('#discount_type').change(function() {
		update_total();
	});
	var click_save = true;
	//event click save only
	$('#btnOnlySave').click(function() {
		if(validate() && click_save) {
			click_save = false;
			$('.notify_status').val(0);
			$('#QuotationAddForm').submit();
		}
		else {
			return false;
		}
	});
	$('#btnSaveSend').click(function () {
		if(validate() && click_save) {
			click_save = false;
			$('.notify_status').val(3);
			$('#QuotationAddForm').submit();
			return false;
		}
		else {
			return false;
		}
	});
	$('#btnCancel').click(function() {
		window.location.href = '<?php echo Router::url(array('plugin' => 'accounting', 'controller' => 'quotations', 'action' => 'index'))?>';
	});
/*list function*/
	//validate waiver
	function validate() {
		var flag = true;
		$('#tblProductService tbody tr').each(function() {
			message = '';
			parent_tr = $(this);
			//check input price
			if( parent_tr.children('.col4').children('.col_price').val() == '') {
				flag = flag && false;
				parent_tr.children('.col4').find('.error').removeClass('displaynone');
				parent_tr.children('.col4').children('.col_price').focus();
			}
			else 
				parent_tr.children('.col4').find('.error').addClass('displaynone');
			//check input quantity	
			if( parent_tr.children('.col3').find('.col_quantity').val() == '') {
				flag = flag && false;
				parent_tr.children('.col3').find('.error').removeClass('displaynone');
				parent_tr.children('.col3').find('.col_quantity').val().focus();
			}
			else 
				parent_tr.children('.col3').find('.error').addClass('displaynone');
			//check select product
			if( parent_tr.children('.col1').children('select.select2').val() == '') {
				flag = flag && false;
				parent_tr.children('.col1').find('.error').removeClass('displaynone');
				parent_tr.children('.col2').find('textarea').focus();
			}
			else 
				parent_tr.children('.col1').find('.error').addClass('displaynone');
		});	
		
		if($('#QuotationValidity').val() == '') {
			flag = flag && false;
			$('#QuotationValidity').focus();
		}if($('#company_select').val() == '') {
			message += '<p class="error_client"><?php echo __("Please select client")?></p>'
			flag = flag && false;
			$('#company_select').focus();
		}if( typeof $('#QuotationTax').val() == 'undefined') {
			message += '<p class="error_tax"><?php echo __("Please apply tax")?></p>'
			flag = flag && false;
		}if($('#accounting_ratecard').val() == '') {
			message += '<p class="error_ratecard"><?php echo __("Please select ratecard")?></p>'
			flag = flag && false;
			$('#accounting_ratecard').focus();
		}
		if(message != '') {
			$('#error_message').html(message);
			$('html, body').animate({ scrollTop: 0 }, 'slow');
		}
		return flag;
	}
	//format number
	function format1(n, currency) {
		return currency + " " + n.toFixed(2).replace(/./g, function(c, i, a) {
			return i > 0 && c !== "." && (a.length - i) % 3 === 0 ? "," + c : c;
		});
	}
	//change attribute
	function resetAttr(selector, contextElem, id, name)
	{
	   var input_name = id;
	   if(name)
            input_name = name;
		$(selector, contextElem)
			.attr('id', id)
            .attr('name', input_name)
			.attr('readonly', true);
	}
	//delete row
	function removeRowTable(el) {
		BootstrapDialog.confirm('Are you sure?', function(result){
			if(result) {
				$(el).remove();
			}
		});
		
	}
	function change_product(el) {
		if( el.val() != '' && el.val() != 'Select one') {			
			tr_parent = el.parent().parent();
			
			$.ajax({
				url: '<?php echo Router::url(array('plugin' => 'accounting', 'controller' => 'quotations', 'action' => 'bindProductService'))?>',
				type: 'post',
				data: 'product_service_id=' + el.val() + '&ratecard_id=' + $('#accounting_ratecard').val(),
				success: function(data) {
					el.next().next().addClass('displaynone');
					tr_parent.children('.col4').children('.error').addClass('displaynone');
					if(data != 'nodata') {
						objData = jQuery.parseJSON(data);
						tr_parent.children('.col2').children('div').children('textarea').val(objData.html);
						tr_parent.children('.col4').children('input').val(objData.price);
						amount_result = parseFloat(objData.price) * parseInt(tr_parent.children('.col3').children('input').val());
						tr_parent.children('.col5').children('.amount_result').text(format1(amount_result,''));
						update_subtotal();
					}
				}
			});
		}
		else {
			el.parent().parent().next().removeClass('displaynone');
		}
	}
	function update_subtotal() {
		subtotal = 0;
		waiver = 0;
		tax = 0;
		discount = 0;
		total = 0;
		$('#tblProductService tbody tr').each(function() {
			currency = $(this).children('.col5').children('.amount_result').text();
			if($(this).children('.col5').children('.waiver_status').val() == 1) {
				subtotal += (parseFloat(Number(currency.replace(/[^0-9\.]+/g,""))));
			}
			else {
				waiver += (parseFloat(Number(currency.replace(/[^0-9\.]+/g,""))));
			}
		});
		$('#QuotationSubtotal').val(subtotal);
		$('#QuotationWaiver').val(waiver);
		
		if( typeof $('#QuotationTax').val() != 'undefined') {
			tax = (subtotal) * parseInt($('#QuotationTax').val())/100;
			$('#QuotationTotalTax').val(subtotal* parseInt($('#QuotationTax').val())/100);
		}
		else {
			$('#QuotationTotalTax').val(0);
		}
		
		if( $('#QuotationDiscount').val() != '') 
			discount = $('#QuotationDiscount').val();
		if($('#discount_type').val() == 'percentage') {
			discount = (subtotal) * discount / 100;
		}
		total = (subtotal) - discount + tax;
		$('#QuotationTotal').val(total);
		
	}
	function update_total() {
		subtotal = 0; 
		waiver = 0;
		tax = 0;
		discount = 0;
		total = 0;
		if($('#QuotationSubtotal').val() != '')
			subtotal = $('#QuotationSubtotal').val();
		if($('#QuotationWaiver').val() != '')
			waiver = $('#QuotationWaiver').val();
		if($('#QuotationDiscount').val() != '')
			discount = $('#QuotationDiscount').val();
		if($('#discount_type').val() == 'percentage')
			discount = (subtotal) * discount / 100;
		
		if( typeof $('#QuotationTax').val() != 'undefined') {
			tax = (subtotal) * parseInt($('#QuotationTax').val())/100;
		}
		total = (subtotal) - discount + tax;
		$('#QuotationTotal').val(total);
	}
	//apply waiver
	function apply_waiver(el) {
		BootstrapDialog.confirm('Please ...!', function(result){
			if(result) {
				tr_parent = el.parent().parent();
				tr_parent.children('.col1').children('select').attr('readonly', true);
				tr_parent.children('.col2').children('div').children('textarea').attr('readonly', true);
				tr_parent.children('.col3').children('input').attr('readonly', true);
				tr_parent.children('.col4').children('input').attr('readonly', true);
				tr_parent.children('.col5').children('input').val('-1');
				tr_parent.children('.col6').html('<a href="javascript:void(0)" onclick="remove_waiver($(this))">Remove waiver</a>');
				update_subtotal();
			}
		});
	}
	//remove waiver
	function remove_waiver(el) {
		BootstrapDialog.confirm('Please ...!', function(result){
			if(result) {
				tr_parent = el.parent().parent();
				tr_parent.children('.col1').children('select').attr('readonly', false);
				tr_parent.children('.col2').children('div').children('textarea').attr('readonly', false);
				tr_parent.children('.col3').children('input').attr('readonly', false);
				tr_parent.children('.col4').children('input').attr('readonly', false);
				tr_parent.children('.col5').children('input').val('1');
				tr_parent.children('.col6').html('<a href="javascript:void(0)" onclick="apply_waiver($(this))">Apply waiver</a>');
				update_subtotal();
			}
		});
	}
</script>