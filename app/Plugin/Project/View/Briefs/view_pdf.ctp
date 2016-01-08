<style type="text/css">
.brief_list, .brief_header, .brief_content, p, h4 {
	margin: 0;
	padding: 0;
}
.brief_list .brief_header {
	background-color: #1abc9c;
	color: #FFF;
	padding-left: 15px;
}
.table .briefLeft {
	background-color: #F0F0F0;
}
p {
	margin: 5px 0;
	padding: 0;
}
</style>
<h2>View Creative Brief</h2>
<?php if(!empty($data)): ?>
<table class="table">
	<tr>
		<td width="30%" class="briefLeft"><?php echo __('Project title'); ?></td>
		<td><?php echo ($data['Brief']['project_title']); ?></td>
	</tr>
	<tr>
		<td width="30%" class="briefLeft"><?php echo __('Job number');?></td>
		<td><?php echo ($data['Brief']['job_number']); ?></td>
	</tr>
	<tr>
		<td width="30%" class="briefLeft"><?php echo __('Company name');?></td>
		<td><?php echo ($companies[$data['Brief']['company_id']]); ?></td>
	</tr>
	<tr>
		<td width="30%" class="briefLeft"><?php echo __('Date');?></td>
		<td><?php echo (formatDate($data['Brief']['date'])); ?></td>
	</tr>
	<tr>
		<td width="30%" class="briefLeft"><?php echo __('Created by');?></td>
		<td><?php echo ((!empty($created_by)) ?  $created_by['User']['name'] : __('Visitor')); ?></td>
	</tr>
</table>
<h3><?php echo __('Creative Brief Form'); ?></h3>
<div class="brief_list">
	<div class="brief_header">
		<h4><?php echo __('Project Background'); ?></h4>
		<p style="padding: 0; margin:0;" class="description">What is the brand / campaign / project history?</p>
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
<?php endif; ?>