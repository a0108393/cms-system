<head>
	<script>
		function trigggerPay() {
		var el = document.getElementById('ChangeRequestType').value;
		
		if(el == 'Free') {
		alert("hello");
		document.getElementById('ChangeRequestAmountChargeableS$').value = 0;
		document.getElementById('ChangeRequestWaive%').value = 0;
		}
		}
		
		function triggerForward() {
		
		}
		
		var room = 1;
		function add_fields() {
		room++;
		var objTo = document.getElementById('add_fields')
		var divtest = document.createElement("div");
		var fieldname = "admin"+room;
		divtest.innerHTML =
		'<label class = "col-lg-2 control-label">Forward to</label><select class = "form-control" id ="' + fieldname +'" onchange ="triggerForward()"><option value ="Free">admin 1 (to be editted)<option value = "Paid">admin 2 (to be editted)</select>';
		
		/*'<div class="label">Room ' + room +':</div><div class="content"><span>Width: <input type="text" id="' + fieldname +'" style="width:48px;" name="width[]" value="" /><small>(ft)</small> X</span><span>Length: <input type="text" style="width:48px;" namae="length[]" value="" /><small>(ft)</small></span></div>';
		*/
		objTo.appendChild(divtest)
		}
	</script>
</head>

<body>
<div id="start">	 
<?php echo '<p><img 
      id="editableImage" src="'.$this->base.'/images/exaple-image.jpg'.'"></a></p>'
?>
</div>
    <p><button id="buttonEditImage">Edit image!</button></p>

    <div id="PaintWebTarget"></div>
	<div id ="discussion" class = "form-group">Im discussion plugin <br></div>
	
<h4>Project Details</h4>
<!--- project table start--->
<div class="panel panel-info">
	<!-- Default panel contents -->
	<div class="panel-heading">
		<h6>
			<?php echo $data['Project']['name']; ?>
		</h6>
	</div>
	
	<div class="panel-body"></div>
<table class="table table-hover">

	<tr>
		<td width="20%" class="briefLeft"><?php echo __('Project Id');?></td>
		<td><?php echo $data['Project']['id']; ?></td>
	</tr>
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('Owner');?></td>
		<td><?php echo $data['Project']['owner']; ?></td>
	</tr>
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('Member');?></td>
		<td><?php echo $data['Project']['member_id']; ?></td>
	</tr>
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('Invite');?></td>
		<td><?php echo $data['Project']['member_id']; ?></td>
	</tr>
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('AM/PM');?></td>
		<td>
			<!--<?php echo $data['Project']['owner']; ?>-->
		</td>
	</tr>
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('Project Start');?></td>
		<td><?php echo $data['Project']['project_start']; ?></td>
	</tr>
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('Project Deadline');?></td>
		<td><?php echo $data['Project']['deadline']; ?></td>
	</tr>
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('View Timeline');?></td>
		<td></td>
	</tr>
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('Files');?></td>
		<td></td>
	</tr>
	<?php if(!empty($allDeliverables)){ ?>
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('Total Deliverables');?>
			<br>
			<?php echo __('Completed');?>
			<br>
			<?php echo __('In Progress');?>
			<br>
			<?php echo __('Cancelled');?>
		</td>
		<td><?php echo $size_total_deliverrable; ?>
			<br>
			<?php echo $size_completed_deliverrable; ?>
			<br>
			<?php echo $size_in_progress_deliverrable; ?>
			<br>
			<?php echo $size_cancelled_deliverrable; ?>
		</td>
	</tr>
	
	<?php } //if(hasDeliverables)end ?>
	<!--detail of the deliverables-->
	<!--to add-->
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('Detail of the Deliverables');?></td>
		<td>
			<?php echo $data['Project']['detail']; ?>
		</td>
	</tr>
</table>
</div>
<!--- project table end--->

<br /><br /><br /><br /><br /><br />

<h4>Deliverables
<?php echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i>', 
									array('controller' => 'Dashboards', 'action' => 'addDeliverable', $data['Project']['id']), 
									array('escape' => false)
								);
?>
</h4>

<?php if(!empty($allDeliverables)){ ?>

<?php foreach($allDeliverables as $deliverable){ ?>
<div class="panel panel-info">
	<!-- Default panel contents -->
	<div class="panel-heading">
		<h6>
			<?php 
			echo $deliverable['Deliverable']['name']; 
			?>
		</h6>
	</div>
	
	<div class="panel-body"></div>
	<?php echo $this->Form->create('Deliverable');?>
	<!----deliverable table start--->
	<table class="table table-striped">
		<tr>
			<td width="20%" class="briefLeft">Deliverable Name
			</td>
			<td>
				<?php echo $deliverable['Deliverable']['name']; ?>
			</td>
		</tr>
		<tr>
			<td width="20%" class="briefLeft">Final Deliverable
			</td>
			<td>
				<?php echo $deliverable['Deliverable']['name']; ?>
			</td>
		</tr>
		<tr>
			<td width="20%" class="briefLeft">
				Deadline
			</td>
			<td>
				<?php echo $deliverable['Deliverable']['deliverable_deadline']; ?>
			</td>
		</tr>
		<tr>
			<td width="20%" class="briefLeft">
				Revisions Allowed
			</td>
			<td>
				<?php echo $deliverable['Deliverable']['no_of_changes']; ?>
			</td>
		</tr>
		<tr>
			<td width="20%" class="briefLeft">
				Date due
			</td>
			<td>
				<?php echo $data['Project']['deadline']; ?>
			</td>
		</tr>
		<tr>
			<td width="20%" class="briefLeft">
				Assign To
			</td>
			<td></td>
		</tr>
		
		<tr>
			<td width="20%" class="briefLeft">
				Date due
			</td>
			<td>
				<?php echo $deliverable['Deliverable']['description']; ?>
			</td>
		</tr>
		<tr>
		<?php
				echo $this->Form->inputUpload(array(
				'fieldName' => 'data[Deliverable][deliverable_file]',
				'title' => 'Select a file or drop file to here',
				'label' => 'Attach files',
				'verticalFrom' => true,
				'maxFiles' => 10
				));
				
				if(!empty($attached_files)) {
				echo '<div class="form-group">
				<label class="col-lg-2 control-label" ></label>
				<div class="col-lg-10">';
				echo '<div id="wrap-portfolio_files"><ul>';
					foreach( $attached_files as $key => $value ){
						echo '<li><a href="'.$this->Html->url('/uploads/'.$value).'">'.$key.'</a> 
									  <a onClick="delete_file(this)" href="javascript:void(0)" class="delete-file">x</a>
									  <input type="hidden" value="'.$value.'" name="data[Deliverable][deliverable_file]['.$key.']">
							  </li>';
									}
				}
				
			?>
			<?php echo $this->Form->submit(_('Save'));?>
		</tr>
	</table>
	<!--- deliverable table end--->
		
			
</div>

<?php
echo '<div class="row">';
if(empty($allChanges)){
	echo 'No Revisions made';
} else{
foreach($allChanges as $change){
	if($change['ChangeRequest']['deliverable_id'] != $deliverable['Deliverable']['id']){
		continue;
	}
	
	?>
<div class="col-md-4">
<div class="panel panel-success">
	<!-- Default panel contents -->
	<div class="panel-heading">
		<h7>
			Revision: <?php echo $change['ChangeRequest']['name']; ?>
		</h7>
			<?php	echo $this->Html->link('<span class="glyphicon glyphicon-plus"></span>', 
										array('controller' => 'Dashboards', 'action' => 'viewCr', $change['ChangeRequest']['id']), 
										array('escape' => false)
									);
			?>
			
		
	</div>
	<div class="panel-body">
<table class= "table table-striped">
	<tr>
		<td width="20%" class="briefLeft">
			Name of change request
		</td>
		<td>
			<?php echo $this->Html->link($change['ChangeRequest']['name'], array('action'=>'viewCr',$change['ChangeRequest']['id']));
				//echo $change['ChangeRequest']['name'];?>
		</td>
	</tr>
	<tr>
		<td width="20%" class="briefLeft">
			deliverable ID
		</td>
		<td>
			<?php echo $change['ChangeRequest']['deliverable_id']; ?>
		</td>
	</tr>
	<tr>
		<td width="20%" class="briefLeft">
			Detail of change request
		</td>
		<td>
			to be added
		</td>
	</tr>
	<tr>
		<td width="20%" class="briefLeft">
			Date of change request made
		</td>
		<td>
			<?php echo $change['ChangeRequest']['date']; ?>
		</td>
	</tr>
	<tr>
		<td width="20%" class="briefLeft">
			Type of change request
		</td>
		<td>
			<?php echo $change['ChangeRequest']['type']; ?>
		</td>
	</tr>
	<tr>
		<td width="20%" class="briefLeft">
			Amount payable
		</td>
		<td>
			<?php echo $change['ChangeRequest']['cost'];?>
		</td>
	</tr>
	<tr>
		<td width="20%" class="briefLeft">
			Waive
		</td>
		<td>
			<?php echo $change['ChangeRequest']['waived'];?>
		</td>
	</tr>
	<tr>
		<td width="20%" class="briefLeft">
			Client approval status for paid change request
		</td>
		<td>
			<?php echo $change['ChangeRequest']['status'];?>
		</td>
	</tr>
	</td>
	</tr>
</table>
</div></div>


<?php
	
	echo $this->Html->link(__('View More Info'), array('action'=>'viewCr',$change['ChangeRequest']['id']));
	
	echo $this->Form->end();

	?>
</div>
<!--- CR table end--->
<?php }//foreach change end
	}// if(!empty(allChange))end
	  

echo '</div>';
echo '<br /><br /><br /><br />';	
	}//foreach deliverable end
	?>

<?php
	}else{ // if(hasDeliverables)end 
	echo '<p>No deliverables created</p>';
	}
?>
	
<?php
	/*
	$approvalStatus= $change['ChangeRequest']['status'];
	
	//client approved
	if($approvalStatus ==  'Client Approved'){
	
	echo '<div id = "add_fields">';
	echo '<label class = "col-lg-2 control-label">';
	echo 'Forward to';
	echo '</label>';
	echo '<select class = "form-control" id ="ForwardTo" onchange ="triggerForward()">';
	//to-do:add user group filter
	echo '<option value ="Free">admin 1 (to be editted)';
	echo '<option value = "Paid">admin 2 (to be editted)';
	echo '</select>';
	echo '</div>';
	
	echo  '<input type="button" id="more_fields" onclick="add_fields();" value="Add More" />';
	
	//client does not/have not approveFree Change Request”, “Pending Approval From Client” or “Closed
	}else if( $approvalStatus ==  'Free Change Request' ||
	$approvalStatus == 'Pending Approval From Client'||
	$approvalStatus == 'Closed') {
	
	}else if($approvalStatus == "Pending Approval From Admin") {
	
	echo '<table class= "table table-striped" onchange="trigggerPay()">';
	if( $this->Session->read('Auth.User.id') == 46) {
	
	//A “reply” button will be show at the end of each change request,
	//when admin user click on it, a popup session will be shown on the screen, he would need to fill in:
	//-Approval status: approved/review/rejected (by default would be pending)
	//-Reply Detail [textarea]
	//-estimated submission date[date picker]
	//-type(free/paid)
	
	//If the change request is not free(or ”type” option is set to “paid”), there will be 2 more fields for admin to fill in:
	//-amount chargeable[integer]
	//-waive[integer]
	//
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
	
	
	//echo $this->Form->input('type', array(
	//'fieldName' => 'data[ChangeRequest][status]',
	//'empty' =>'Select Type',
	//'label' => array('text' => __('Type'), 'class' => 'col-lg-2 control-label','id'=>'ChangeRequestType'),
	//'options' => array('free','paid')
	
	//));
	
	echo '';
	echo '<label class = "col-lg-2 control-label">';
	echo 'Change Request Type';
	echo '</label>';
	echo '<select class = "form-control" id ="ChangeRequestType" onchange ="triggerPaid()">';
	echo '<option value ="Free">Free';
	echo '<option value = "Paid">Paid';
	echo '</select>';
	
	
	
	//popup for admin to fill if change type to paid or it is originally paid
	//if(data[ChangeRequest][type] = 'paid'){
	//echo $this
	//}
	
	
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
	}*/
	?>
	</body>
	<script type="text/javascript"><!--
(function () {
	var img    = document.getElementById('editableImage'),
		btn    = document.getElementById('buttonEditImage'),
		target = document.getElementById('PaintWebTarget'),
		loadp  = document.createElement('p'),
		timeStart = null,

    // Create a PaintWeb instance.
    pw = new PaintWeb();

	pw.config.guiPlaceholder = target;
	pw.config.imageLoad      = img;
	pw.config.configFile     = 'config-example.json';
	loadp.appendChild(document.createTextNode('Loading, please wait...'));

  if (btn.addEventListener) {
    btn.addEventListener('click', pwStart, false);
  } else if (btn.attachEvent) {
    btn.attachEvent('onclick', pwStart);
  } else {
    btn.onclick = pwStart;
  }

  // Function called when the user clicks the "Edit image" button.
  function pwStart () {
    var parentDiv = document.getElementById("start");
	parentDiv.parentNode.insertBefore(loadp, btn.parentNode);

    timeStart = (new Date()).getTime();
    pw.init(pwInit);
  };

  // Function called when the PaintWeb application fires the "appInit" event.
  function pwInit (ev) {
    var initTime = (new Date()).getTime() - timeStart,
        str = 'Demo: Yay, PaintWeb loaded in ' + initTime + ' ms! ' +
              pw.toString();

    var parentDiv = document.getElementById("start");
	parentDiv.parentNode.insertBefore(loadp, btn.parentNode);

    if (ev.state === PaintWeb.INIT_ERROR) {
      alert('Demo: PaintWeb initialization failed.');
      return;

    } else if (ev.state === PaintWeb.INIT_DONE) {
      if (window.console && console.log) {
        console.log(str);
      } else if (window.opera) {
        opera.postError(str);
      }

    } else {
      alert('Demo: Unrecognized PaintWeb initialization state ' + ev.state);

      return;
    }

    img.style.display = 'none';
    btn.style.display = 'none';
  };

  
})();
    --></script>