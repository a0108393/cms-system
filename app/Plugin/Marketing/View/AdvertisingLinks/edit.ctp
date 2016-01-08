<h1><?php echo __('Edit Link'); ?></h1>
<?php
    echo $this->Form->create('AdvertisingLink', array('action' => 'edit'));
    echo $this->Form->input('description');
	echo $this->Form->input('marketing_channels_id', array(
		'options' => $channels,
		'class'=>'select-ui-primary'
	));
	echo $this->Form->input('destination_url');
	echo $this->Form->input('generated_url');
    echo $this->Form->input('id', array('type' => 'hidden'));
    echo $this->Form->end('Save Advertising Link');
?>