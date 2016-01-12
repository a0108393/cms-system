<h3><?php echo __('View Meeting Minute'); ?></h3>
<?php
	echo $this->Form->create('ChangeRequest');
	echo $this->Form->input('title', array('readonly' => true));
	echo $this->Form->input('client_id', array(
		'label' => array('text' => __('Client'), 'class' => 'col-lg-2 control-label'),
		'options' => $clients,
		'empty'=>'Select client',
		'disabled' => true
	));
	echo $this->Form->inputDatepicker('date', array(
		'disabled' => true
	));
	echo $this->Form->inputDatepicker('start_time', array(
		'id' => 'start_time',
		'datepicker_setup' => array(
			'format' => 'HH:mm',
			'pickDate' => false,
			'pickTime' => true,
			'useMinutes' => true,
			'useSeconds' => false,
			'useCurrent' => true
		),
		'disabled' => true
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
		),
		'disabled' => true
	));
	echo $this->Form->input('minute_taker', array(
		'options' => $staffs,
		'empty'=>'Select staff',
		'disabled' => true
	));
	echo $this->Form->input('attendees', array(
		'options' => $staffs,
		'id' => 'attendees',
		'multiple' => true,
		'div'=>array('class'=>'form-group channels'),
		'class' => 'select2',
		'disabled' => true
	));
	echo $this->Form->input('cc_list', array(
		'options' => $staffs,
		'id' => 'cc_list',
		'multiple' => true,
		'div'=>array('class'=>'form-group channels'),
		'class' => 'select2',
		'disabled' => true
	));
	echo $this->Form->input('agenda_description', array('type' => 'textarea', 'readonly' => true, 'noeditor' => true));
?>	

<script type="text/javascript">
	$(document).ready(function() {
		$('#attendees').select2({
			'width': 400
		});
		$('#cc_list').select2({
			'width': 400
		});
	})

</script>
<?php if(!empty($note_details)) : ?>
	<table class="table">
		<thead>
			<tr>
				<th width="15%">No.</th>
				<th width="15%">Type</th>
				<th>Description</th>
				<th width="20%">Assigned To</th>
				<th width="20%" class="position-relative">Due Date</th>
			</tr>
	  	</thead>
	  	<tbody>
		<?php 
		$note_type = Configure::read('note.type');
		foreach ($note_details as $k => $item) : ?>
		<tr id="custom-row<?php echo $k; ?>">
			<td><?php echo $k + 1; ?></td>
			<td class="col1">
				<?php echo $note_type[$item['NoteDetail']['type']]; ?>
			</td>
			<td class="col2">
				<?php echo nl2br($item['NoteDetail']['description']); ?>
			</td>
			<td class="col3">
				<?php
				if(!empty($item['NoteDetail']['assigned_to'])) 
					echo $staffs[$item['NoteDetail']['assigned_to']]; 
				?>
			</td>
			<td class="col4">
			<?php if(!empty($item['NoteDetail']['due_date']))
				echo formatDateTime($item['NoteDetail']['due_date']);
			?>
			</td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>
