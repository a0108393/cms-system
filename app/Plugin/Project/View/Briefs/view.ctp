<h2><?php echo __('View Creative Brief'); ?></h2>
<?php if(!empty($data)): ?>
<?php 
	if(($this->Session->read('Auth.User.id') == $data['Brief']['user_created'] && $data['Brief']['status'] == 4) || ($this->Session->read('Auth.User.id') == $data['Brief']['user_modified'] && $data['Brief']['status'] == 2)) {
		echo $this->Form->create('Brief');
		echo $this->Form->input('text_approved', array('label' => array('text' => 'Approval key', 'class' => 'col-lg-2 control-label')));
		echo $this->Form->submit(__('Approve'), array('name' => 'data[Brief][approve]'));
		echo $this->Form->end();
	}
?>

<table class="table">
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('Project title');?></td>
		<td><?php echo $data['Brief']['project_title']; ?></td>
	</tr>
<?php if(in_array(Configure::read('Settings.Company.SalesStaffGroupId'), $group)): ?>
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('Job number');?></td>
		<td><?php echo $data['Brief']['job_number']; ?></td>
	</tr>
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('Company name');?></td>
		<td><?php echo $companies[$data['Brief']['company_id']]; ?></td>
	</tr>
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('Date');?></td>
		<td><?php echo formatDate($data['Brief']['date']); ?></td>
	</tr>
<?php endif; ?>
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('Created by');?></td>
		<td><?php echo ((!empty($created_by)) ?  $created_by['User']['name'] : __('Visitor')); ?></td>
	</tr>
	<tr>
		<td width="20%" class="briefLeft"><?php echo __('Files'); ?></td>
		<td>
		<?php
			if(isset($attached_files)) {
			echo '<div id="wrap-portfolio_files"><ul>';
					foreach( $attached_files as $key => $value ){
						echo '<li><a href="'.$this->Html->url('/uploads/'.$value).'">'.$key.'</a> 
							  </li>
							 ';
					}
			echo ' </ul></div>';
			}
		?>
		</td>
	</tr>
</table>
<h3><?php echo __('Creative Brief Form'); ?></h3>
<div class="brief_list">
	<div class="brief_header">
		<h4><?php echo __('Project Background'); ?></h4>
		<p class="description">What is the brand / campaign / project history?</p>
	</div>
	<div class="brief_content">
	<?php echo $data['Brief']['project_background']; ?>
	</div>
</div>
<div class="brief_list">
	<div class="brief_header">
		<h4><?php echo __('Creative Task'); ?></h4>
		<p class="description">What is required? e.g. To produce a new concept/ design; an adaptation from print or other templates.</p>
	</div>
	<div class="brief_content">
	<?php echo $data['Brief']['creative_task']; ?>
	</div>
</div>
<div class="brief_list">
	<div class="brief_header">
		<h4><?php echo __('Target Audience'); ?></h4>
		<p class="description">Who are the primary and secondary audience (if any)?</p>
	</div>
	<div class="brief_content">
	<?php echo $data['Brief']['target_audience']; ?>
	</div>
</div>
<div class="brief_list">
	<div class="brief_header">
		<h4><?php echo __('Key Message'); ?></h4>
		<p class="description">What is the single most important message to highlight?</p>
	</div>
	<div class="brief_content">
	<?php echo $data['Brief']['key_message']; ?>
	</div>
</div>
<div class="brief_list">
	<div class="brief_header">
		<h4><?php echo __('Brand Positioning'); ?></h4>
		<p class="description">What is the tone and manner?</p>
	</div>
	<div class="brief_content">
	<?php echo $data['Brief']['brand_positioning']; ?>
	</div>
</div>
<div class="brief_list">
	<div class="brief_header">
		<h4><?php echo __('Deliverables'); ?></h4>
		<p class="description">What are the specific requirements and outputs to be produced?</p>
	</div>
	<div class="brief_content">
	<?php echo $data['Brief']['deliverables']; ?>
	</div>
</div>
<div class="brief_list">
	<div class="brief_header">
		<h4><?php echo __('Mandatories'); ?></h4>
		<p class="description">What must be included or complied with?</p>
	</div>
	<div class="brief_content">
	<?php echo $data['Brief']['mandatories']; ?>
	</div>
</div>
<div class="brief_list">
	<div class="brief_header">
		<h4><?php echo __('Timeline'); ?></h4>
		<p class="description">Submission deadline and deliverables.</p>
	</div>
	<div class="brief_content">
	<?php echo $data['Brief']['timeline']; ?>
	</div>
</div>
<div class="brief_list">
	<div class="brief_header">
		<h4><?php echo __('Supporting Materials / References'); ?></h4>
		<p class="description">Files, websites, samples provided by client for references.</p>
	</div>
	<div class="brief_content">
	<?php echo $data['Brief']['supporting']; ?>
	</div>
</div>
<h5><?php echo __('Brief PDF Files'); ?></h5>
<?php
	if(!empty($allVersions)) {
		echo '<ul>';
		foreach ($allVersions as $key => $value) {
			if($key == 0) {
				echo '<li>' . $this->Html->link(__('Final version'), array('action' => 'viewPdf', $value['Brief']['id'])) . '</li>';
			}
			else {
				echo '<li>' . $this->Html->link(sprintf(__('Version %d'), $value['Brief']['version']), array('action' => 'viewPdf', $value['Brief']['id'])) . '</li>';
			}
		}
		echo '</ul>';
	}
?>

<?php endif; ?>