<?php
	echo $this->Form->create('MeetingMinute');
	echo $this->Form->input('title');
	echo $this->Form->input('client_id', array(
		'label' => array('text' => __('Client'), 'class' => 'col-lg-2 control-label'),
		'options' => $clients,
		'empty'=>'Select client',
	));
	echo $this->Form->inputDatepicker('date');
	echo $this->Form->inputDatepicker('start_time', array(
		'id' => 'start_time',
		'datepicker_setup' => array(
			'format' => 'HH:mm',
			'pickDate' => false,
			'pickTime' => true,
			'useMinutes' => true,
			'useSeconds' => false,
			'useCurrent' => true
		)
	));
	echo $this->Form->inputDatepicker('end_time', array(
		'id' => 'end_time',
		'datepicker_setup' => array(
			'format' => 'HH:mm',
			'pickDate' => false,
			'pickTime' => true,
			'useMinutes' => true,
			'useSeconds' => false,
			'useCurrent' => true
		)
	));
	echo $this->Form->input('minute_taker', array(
		'options' => $staffs,
		'empty'=>'Select staff',
	));
	echo $this->Form->input('attendees', array(
		'options' => $staffs,
		'id' => 'attendees',
		'multiple' => true,
		'div'=>array('class'=>'form-group channels'),
		'class' => 'select2',
		'required' => true
	));
	echo $this->Form->input('cc_list', array(
		'options' => $staffs,
		'id' => 'cc_list',
		'multiple' => true,
		'div'=>array('class'=>'form-group channels'),
		'class' => 'select2',
		// 'required' => true
	));
	echo $this->Form->input('agenda_description', array('type' => 'textarea'));
?>
<table id="tblMeeting" class="table">
	<thead>
		<tr>
			<th width="15%">Type</th>
			<th>Description</th>
			<th width="20%">Assigned To</th>
			<th width="20%" class="position-relative">Due Date<a class="add-load-detail" href="Javascript:void(0);"><i class="glyphicon glyphicon-plus"></i></a></th>
		</tr>
  	</thead>
  	<tbody>
  		<tr id="custom-clone-fields">
			<td class="col1">
				<select class="change-type-note form-control" name="load_type" onchange="changeType($(this))">
					<?php 
						foreach(Configure::read('note.type') as $k => $item) {
							echo '<option value="'.$k .'">'. $item .'</option>';
						}
					?>
				</select>
			</td>
			<td class="col2"><textarea noeditor="true" style="padding-left:0;height: 60px;" name="meeting_description" cols="30"></textarea></td>
			<td class="col3"></td>
			<td class="col4"><div class="position-relative"><a class="del-load-detail" href="Javascript:void(0);"><i class="glyphicon glyphicon-trash"></i></a></div></td>
			<input type="hidden" class="id-note">
		</tr>
<?php 
if(!empty($this->request->data['NoteDetail'])) :
	foreach ($this->request->data['NoteDetail'] as $k => $item) : ?>
		<tr id="custom-row<?php echo $k; ?>">
			<td class="col1">
				<input type="hidden" name="data[NoteDetail][<?php echo $k; ?>][id]" value="<?php echo $item['id']; ?>" />
				<select class="change-type-note form-control" name="data[NoteDetail][<?php echo $k; ?>][type]" onchange="changeType($(this))">
					<?php 
						foreach(Configure::read('note.type') as $key => $value) {
							if($item['type'] == $key)
								$selected = ' selected';
							else 
								$selected = '';
							echo '<option'. $selected .' value="'.$key .'">'. $value .'</option>';
						}
					?>
				</select>
			</td>
			<td class="col2">
				<textarea noeditor="true" style="padding-left:0;height: 60px;" name="data[NoteDetail][<?php echo $k; ?>][description]" cols="30"><?php echo $item['description'] ?></textarea>
			</td>
			<td class="col3">
			<?php if(!empty($item['assigned_to'])) : ?>
				<select class="public-assigned-to form-control" name="data[NoteDetail][<?php echo $k; ?>][assigned_to]">
		
					<?php
					foreach ($staffs as $key => $value) {
						if($item['assigned_to'] == $key)
							$selected = ' selected';
						else 
							$selected = '';
						echo '<option '. $selected .' value="'.	$key .'">'. $value .'</option>';
					}
				?>
				</select>
			<?php endif; ?>
			</td>
			<td class="col4">
			<?php if(!empty($item['due_date'])) : ?>
				<input type="text" name="data[NoteDetail][<?php echo $k; ?>][due_date]" class="form-control" id="NoteDetail_date_<?php echo $k; ?>" value="<?php echo formatDateTime($item['due_date']); ?>" />
				<script type="text/javascript">
					$(document).ready(function() {
						id_note = '<?php echo $k; ?>';
						$('#NoteDetail_date_' + id_note + '').datetimepicker({
							'format' : '<?php echo phpToJsDateFormat(Configure::read('Settings.Formats.date_format')); ?>' + ' HH:mm:ss'
						});
					})
				</script>
			<?php endif; ?>
				<div class="position-relative">
					<a class="del-load-detail-confirm" href="Javascript:void(0);"><i class="glyphicon glyphicon-trash"></i></a>
				</div>
			</td>
		</tr>
<?php 
	endforeach; 
endif; 
?>
  	</tbody>
</table>
<?php	
	// print_r($NoteDetail);
	echo $this->Form->submit(__('Save'), array('id' => 'submitForm'));
	echo $this->Form->end();
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
	$(document).ready(function() {
		var submit_status = false;
		$('#submitForm').click(function() {
			if(!submit_status) {
				$('#MeetingMinuteAddForm').submit();
				submit_status = true;
			}
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
		$('.del-load-detail-confirm').click(function() {
			el = $(this);
			BootstrapDialog.confirm('Are you sure?', function(result){
			if(result) {
				$.ajax({
					url: '<?php echo Router::url(array('plugin' => false, 'controller' => 'MeetingMinutes', 'action' => 'ajaxDeleteNoteDetail'));?>',
					type: 'post',
					data: 'note_detail_id=' + el.parent().parent().parent().children('.col1').find('input').val(),
					success: function(data) {
						if(data != '')
							el.parent().parent().parent().remove();
					}
				})
			}
		});
		});
		// $('.add-load-detail').trigger('click');
	})
	function changeType (el) {
		if(el.val() == 3){
			id_note = el.parent().parent().find('.id-note').val();
			el.parent().parent().find('.col3').append($('#get-list-user-assigned-to').html());
			el.parent().parent().find('.col3').find('select').attr('name','data[NoteDetail]['+ id_note +'][assigned_to]');
			el.parent().parent().find('.col4').prepend('<input type="text" value="" name="data[NoteDetail]['+ id_note +'][due_date]" class="form-control" id="NoteDetail_date_' + id_note + '" />');
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