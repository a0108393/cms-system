<h3><?php echo __('View Deliverables Details'); ?></h3>

<table class="table">
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('ID');?></td>
		<td><?php echo $data['Deliverable']['id']; ?></td>
	</tr>
	
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('name');?></td>
		<td><?php echo $data['Deliverable']['name']; ?></td>
	</tr>
		
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('Deliverable Type');?></td>
		<td><?php echo $data['Deliverable']['type']; ?></td>
	</tr>
	
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('Project Name');?></td>
		<td><?php echo $data['Project']['name']; ?></td>
	</tr>
	
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('No of Free Change Request Allowed');?></td>
		<td><?php echo $data['Deliverable']['no_of_changes']; ?></td>
	</tr>
	
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('No of Change Request Made');?></td>
		<!--count number of change request under a specific deliverable-->
		<?php
		$count = 0;
		foreach( $change_requests as $changeMade){		
		if($changeMade == $data['Deliverable']['id']){
		$count ++;
		}
		}
		?>
		
		<td><?php echo $count; ?></td>
	</tr>
	
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('date');?></td>
		<td><?php echo $data['Deliverable']['date']; ?></td>
	</tr>
	
	
	<!--detail of the deliverables-->
    <!--to add-->
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('Detail of the Deliverables');?></td>
		<td><?php echo 'to be addede'; ?></td>
	</tr>
		
</table>

<?php
echo '<h3>
Changes requested for: '.$data['Deliverable']['name'].'</h3>';

	foreach($allChanges as $change){
		echo '<p><u>'.$change['ChangeRequest']['name'].'</u></p>';

		echo '<table class="table table-striped">';
		$row1 = array(
			__('ID'),
			$change['ChangeRequest']['id'],
		);
		$row2 = array(
			__('Date'),
			$change['ChangeRequest']['date'],
		);
		$row3 = array(
			__('Details'),
			$change['ChangeRequest']['remarks'],	
		);
		$row4 = array(
			__('Type'),
			$change['ChangeRequest']['type'],	
		);
		$row5 = array(
			__('Cost'),
			$change['ChangeRequest']['cost'],	
		);
		$row6 = array(
			__('waived'),
			$change['ChangeRequest']['waived'],	
		);
	
		echo $this->Html->tableCells($row1);
		echo $this->Html->tableCells($row2);
		echo $this->Html->tableCells($row3);
		echo $this->Html->tableCells($row4);	
		
		if($change['ChangeRequest']['type'] == 'Free'){
			echo $this->Html->tableCells(array(
				__('Cost'),__('N.A.')));	
		}else{
			echo $this->Html->tableCells($row5);	
			echo $this->Html->tableCells($row6);	
		}
		
		echo '</table>';
	}
	
?>
<!-- Display Change Request End-->
<h3>Make New Change Request</h3>
<?php
if( $this->Session->read('Auth.User.id') == 46) {
    /*
	A “reply” button will be show at the end of each change request, 
	when admin user click on it, a popup session will be shown on the screen, he would need to fill in:
    -Approval status: approved/review/rejected (by default would be pending)
    -Reply Detail [textarea]
    -estimated submission date[date picker]
    -type(free/paid)

     If the change request is not free(or ”type” option is set to “paid”), there will be 2 more fields for admin to fill in:
    -amount chargeable[integer]
    -waive[integer]
	*/
	
	echo $this->Form->create('ChangeRequestClient');
	
	echo $this->Form->input('name', array(
		'label' => array('text' => __('Title'), 'class' => 'col-lg-2 control-label')
	));
	// read only form fields here
	
	//to be editted
	echo $this->Form->input('Reply Detail');
	
	echo $this->Form->inputDatepicker('date_due', array(
  //'fieldName' => 'data[ChangeRequest][status]',
  'label' => array('text' => __('Estimate Submission Date'), 'class' => 'col-lg-2 control-label'),
   ));
   
   echo $this->Form->input('type', array(
  //'fieldName' => 'data[ChangeRequest][status]',
  'label' => array('text' => __('Type'), 'class' => 'col-lg-2 control-label'),
  'options' => array('free','paid'),
    ));
    
	/*
	popup for admin to fill if change type to paid or it is originally paid
	if(data[ChangeRequest][type] = 'paid'){
	echo $this
	}
    */
 
	}
?>
