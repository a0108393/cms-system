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
		<td width="20%" class="briefLeft"><?php echo __('type');?></td>
		<td><?php echo $data['Deliverable']['type']; ?></td>
	</tr>
	
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('date');?></td>
		<td><?php echo $data['Deliverable']['date']; ?></td>
	</tr>
	
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('project_name');?></td>
		<td><?php echo $data['Deliverable']['project_id']; ?></td>
	</tr>
	
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('no_of_changes');?></td>
		<td><?php echo $data['Deliverable']['no_of_changes']; ?></td>
	</tr>
	
	
	
</table>

<?php
echo '<h3>change requests of the deliverable</h3>';
echo '<table>';
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
	echo '	<td width="20%" class="briefLeft">';
	echo __('Date');
	echo '</td>';
	echo '	<td>';
	echo $change['ChangeRequest']['date']; 
	echo '</td>';
	echo '</tr>';
	echo '</table>';
	}

?>

<?php
echo 'past change requests';
foreach($allChanges as $change){
echo $change['ChangeRequest']['deliverable_id'];
echo $change['ChangeRequest']['date'];
}
?>