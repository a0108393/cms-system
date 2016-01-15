	<header>
	<script>
	function trigggerPay() {
	var el = document.getElementById('ChangeRequestType').value;
	
	if(el == 'Free') {
	document.getElementById('ChangeRequestAmountChargeable').value = 0.00;
	document.getElementById('ChangeRequestWaive').value = 0;
	}
	}
	</script>
	
	</header>
	
	<h3><?php echo __('View Deliverables Details'); ?></h3>

	<table class="table table-striped">
		<tr>
			<td width="20%" class="briefLeft"><?php echo __('ID');?></td>
			<td><?php echo $data['Deliverable']['id']; ?></td>
		</tr>
		
		<tr>
			<td width="20%" class="briefLeft"><?php echo __('Name');?></td>
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
			<td width="20%" class="briefLeft"><?php echo __('Date Posted');?></td>
			<td><?php echo $data['Deliverable']['date']; ?></td>
		</tr>
		
		
		<!--detail of the deliverables-->
		<!--to add-->
		<tr>
			<td width="20%" class="briefLeft"><?php echo __('Detail of the Deliverables');?></td>
			<td>
			
			<?php echo $data['Deliverable']['details']; ?>
			
			</td>
		</tr>
			
	</table>

	
	
	<?php
	echo '<h3>change requests of the deliverable</h3>';
	echo '<table class= "table table-striped">';
	foreach($allChanges as $change){
	
		echo '<tr>';
		echo '<td width="20%" class="briefLeft">';
		echo __('ID');
		echo '</td>';
		echo '<td>';
		echo $change['ChangeRequest']['deliverable_id']; 
		'</td>';	
		echo '</tr>';
		
		echo '<tr>';
		echo '<td width="20%" class="briefLeft">';
		echo __('Name of change request');
		echo '</td>';
		echo '<td>';
		echo $change['ChangeRequest']['name']; 
		'</td>';	
		echo '</tr>';
		
		echo '<tr>';
		echo '<td width="20%" class="briefLeft">';
		echo __('Detail of change request');
		echo '</td>';
		echo '<td>';
		echo 'to be added'; 
		'</td>';	
		echo '</tr>';
		
		echo '<tr>';
		echo '	<td width="20%" class="briefLeft">';
		echo __('Date of change request made');
		echo '</td>';
		echo '	<td>';
		echo $change['ChangeRequest']['date']; 
		echo '</td>';
		echo '</tr>';	
		
		echo '<tr>';
		echo '<td width="20%" class="briefLeft">';
		echo __('Type of change request');
		echo '</td>';
		echo '<td>';
		echo $change['ChangeRequest']['type']; 
		'</td>';	
		echo '</tr>';
		
				
		echo '<tr>';
		echo '<td width="20%" class="briefLeft">';
		echo __('Amount payable');
		echo '</td>';
		echo '<td>';
		echo $change['ChangeRequest']['cost']; 
		'</td>';	
		echo '</tr>';
		
						
		echo '<tr>';
		echo '<td width="20%" class="briefLeft">';
		echo __('Waive');
		echo '</td>';
		echo '<td>';
		echo $change['ChangeRequest']['waived']; 
		'</td>';	
		echo '</tr>';
		
						
		echo '<tr>';
		echo '<td width="20%" class="briefLeft">';
		echo __('Client apprival status for paid change request');
		echo '</td>';
		echo '<td>';
		echo $change['ChangeRequest']['status']; 
		'</td>';	
		echo '</tr>';
		/*
		echo '<tr>';
		echo '<td width="20%" class="briefLeft">';
		echo 'past change requests ';
		foreach($allChanges as $change){
		echo $change['ChangeRequest']['deliverable_id'];
		echo ' ' ;
		echo $change['ChangeRequest']['date'];
		}
		echo '</td>';
		echo '</tr>';
	*/
        echo '</td>';
		echo '</tr>';
		echo '</table>';
	    
		
		echo '<table class= "table table-striped" onchange="trigggerPay()">';
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
		echo $this->Form->create('ChangeRequest');
		

		echo '<td width="20%" class="briefLeft">';
		
		echo $this->Form->input('status', array(
	    //'fieldName' => 'data[ChangeRequest][status]',
	    'label' => array('text' => __('Approval status'), 'class' => 'col-lg-2 control-label'),
	    'options' => array('Approved', 'review', 'Pending Approval(client)', 'submitted' ,'Closed'),
		//'value' => 'closed',
		));
		echo '</td>';

		
		//to be editted
		echo '<tr>';
		echo '<td width="20%" class="briefLeft">';
		
		echo $this->Form->input('Reply Detail', array('type' => 'textarea'));
		
		echo '</td>';
		echo '</tr>';
		
		
		echo '<tr>';
		echo '<td width="20%" class="briefLeft">';
		
		echo $this->Form->inputDatepicker('date_due', array(
	  //'fieldName' => 'data[ChangeRequest][status]',
	  'label' => array('text' => __('Estimate Submission Date'), 'class' => 'col-lg-2 control-label'),
	   ));
	   
	    echo '</td>';
		echo '</tr>';
		
		echo '<tr>';
		echo '<td width="20%" class="briefLeft">';
	   
	 /* 
	  echo $this->Form->input('type', array(
	  //'fieldName' => 'data[ChangeRequest][status]',
	  'empty' =>'Select Type',
	  'label' => array('text' => __('Type'), 'class' => 'col-lg-2 control-label','id'=>'ChangeRequestType'),
	  'options' => array('free','paid')
	  
		));
      */
	    echo '';
	    echo '<label class = "col-lg-2 control-label">';
	    echo 'Change Request Type';
		echo '</label>';
		echo '<select class = "form-control" id ="ChangeRequestType" onchange ="triggerPaid()">';
		echo '<option value ="Free">Free';
		echo '<option value = "Paid">Paid';
		echo '</select>';
   
		
		/*
		popup for admin to fill if change type to paid or it is originally paid
		if(data[ChangeRequest][type] = 'paid'){
		echo $this
		}
		*/
		
		echo '<tr>';
		echo '<td width="20%" class="briefLeft">';
		echo $this->Form->input('Amount chargeable S$');		
		echo '</td>';
		echo '</tr>';
		

		
		echo '<tr>';
		echo '<td width="20%" class="briefLeft">';
		echo $this->Form->input('Waive %');		
		echo '</td>';
		echo '</tr>';
	 
	 
	     
		}
			echo '</td>';
			echo '</tr>';
			echo '</table>';
			}
	?>

  
