<html>
	<head>
	</head>
	<body>
		<table class="table table-striped">
			<tr>
				<td><?php echo __('CR Name');?>
				</td>
				<td><?php echo $data['ChangeRequest']['name']; ?>
				</td>
			</tr>
			
			<tr>
				<td><?php echo __('Clients');?>
				</td>
				<td>
					<?php
						echo $this->Form->input('', array(
						'id' => 'multiple-suggest-select',
						'type' => 'text',
						));
					?>
				</td>
			</tr>
			
		</table>
		<?php
				echo $this->Form->inputUpload(array(
				   'fieldName' => 'data[Deliverable][deliverable_file]',
				   'title' => 'Select a file or drop file to here', 
				   'verticalFrom' => true,
				   'maxFiles' => 10
				));
		?>
				
	</body>
	<script type="text/javascript">
		$('#multiple-suggest-select').tokenInput(
		<?php echo json_encode($all_clients); ?>
			,{
				preventDuplicates: true
				
			}
	);
	</script>
</html>