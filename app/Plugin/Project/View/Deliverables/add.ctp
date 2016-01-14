

<?php

$EnquiryNo = 3;
for($i = 1; $i < $EnquiryNo +1; $i++) {
	echo "Enquiry ";
	echo $i;
	echo $this->Form->create('ProjectEnquiries');
	echo $this->Form->input('title');
	
	//to be edit, add enquiry type 
	echo $this->Form->input('type', array(
		'label' => array('text' => __('Enquiry type'), 'class' => 'col-lg-2 control-label'),
		   'options' => array(1, 2, 3, 4, 5),
           'empty' => '(choose one)'
	));
	echo $this->Form->inputDatepicker('duedate');
	
	echo $this->Form->input('Enquiry info', array('type' => 'textarea'));
	
	echo $this->Form->submit(__('Save'), array('id' => 'submitForm'));
	echo $this->Form->end();

	echo '<hr />';
	
}
?>


<table id="tblMeeting" class="table">

	<thead>
		<tr>
			<th width="100%" class="position-relative">Paid Enquiries -- you will need to pay for the following enquiries<a class="add-load-detail" href="Javascript:void(0);"><i class="glyphicon glyphicon-plus"></i></a></th>
		</tr>
  	</thead>

	
	
	<div class="alert alert-danger" role="alert">Warning!</div>
  	<tbody>
	
  		<tr id="custom-clone-fields">
		<td>

		

<?php
// to add RequestPay() in Controller to redirect for admin to confirm the amount payable
    echo $this->Form->create('Deliverable', array('action' => 'getPayRequest'));
	echo $this->Form->input('type', array(
		'label' => array('text' => __('Enquiry Type'), 'class' => 'col-lg-2 control-label'),
		'options' => array(1, 2, 3, 4, 5),
        'empty' => '(choose one)'
	));
    echo $this->Form->submit(__('Confirm Pay Request'), array('id' => 'PaySubmitForm'));
	echo $this->Form->end();
	
	
    echo $this->Form->create('ProjectEnquiries');
	echo $this->Form->input('title');
	
	//to be edit, add enquiry type 
	echo $this->Form->input('type', array(
		'label' => array('text' => __('Enquiry Type'), 'class' => 'col-lg-2 control-label'),
		'options' => array(1, 2, 3, 4, 5),
        'empty' => '(choose one)'
	));
	echo $this->Form->inputDatepicker('duedate');
	
	echo $this->Form->input('Enquiry info', array('type' => 'textarea'));
	
	echo $this->Form->submit(__('Save'), array('id' => 'submitForm'));
	echo $this->Form->end();
		?>
		
	
		</td>

			<td class="col4"><div class="position-relative"><a class="del-load-detail" href="Javascript:void(0);"><i class="glyphicon glyphicon-trash"></i></a></div></td>
			<input type="hidden" class="id-note">
		</tr>

  	</tbody>
</table>
<?php	
	/*
	echo $this->Form->submit(__('Save'), array('id' => 'submitForm'));
	echo $this->Form->end();
	*/
?>
<div style="display:none" id="get-list-user-assigned-to">
	<select class="public-assigned-to form-control" name="data[assigned_to]">
		<?php
			if(!empty($staffs)) {
				foreach ($staffs as $key => $value) {
					echo '<option value="'.	$key .'">'. $value .'</option>';
				}
			}
		?>
	</select>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		var submit_status = false;
		$('#submitForm').click(function() {
			console.log($('#MeetingMinuteAddForm'));
		});
		$('#attendees').select2({
			'width': 400
		});
		$('#cc_list').select2({
			'width': 400
		});
		var json_project = [];
		$('#attendees, #cc_list').on("select2-selecting", function(e){
			var value = e.val;
			
			var val_att = $('#attendees').val();
			var val_cc = $('#cc_list').val();
			
			if(val_cc != null || val_att != null) {
				
				if(val_cc != null ) {
					if (val_cc.indexOf(value) >= 0) {
						return false;
					}

				}
				if(val_att != null) {
					if (val_att.indexOf(value) >= 0) {
						return false;
					}
				}
			}
		});
		var count_ = $('.load-detail table tbody tr').length;
		$('.add-load-detail').click(function(){
			var l = $("#custom-clone-fields").clone().appendTo("#tblMeeting tbody");
			l.attr('id','custom-row'+count_);
			l.find('.id-note').attr('value',count_);
			l.find('select').attr('name','data[NoteDetail]['+count_+'][type]');
			l.find('textarea').attr('name','data[NoteDetail]['+count_+'][description]');
			l.show();
			count_ ++;
		});
		//event delete row
		$('.del-load-detail').live('click',function(){
			$(this).parent().parent().parent().remove();
		});
		$('.add-load-detail').trigger('click');
	})
	function changeType (el) {
		if(el.val() == 3){
			id_note = el.parent().parent().find('.id-note').val();
			el.parent().parent().find('.col3').append($('#get-list-user-assigned-to').html());
			el.parent().parent().find('.col3').find('select').attr('name','data[NoteDetail]['+ id_note +'][assigned_to]');
			el.parent().parent().find('.col4').prepend('<input type="text" value="" name="data[NoteDetail]['+ id_note +'][due_date]" class="form-control" id="NoteDetail_date_' + id_note + '" />');
			console.log($('#NoteDetail_date_' + id_note + '').attr('name'));
			$('#NoteDetail_date_' + id_note + '').datetimepicker({
				'format' : '<?php echo phpToJsDateFormat(Configure::read('Settings.Formats.date_format')); ?>' + ' HH:mm:ss'
			});
		}
		else {
			el.parent().parent().find('.col3').html('');
			el.parent().parent().find('.col4').find('.form-control').remove();
		}
	}
</script>
