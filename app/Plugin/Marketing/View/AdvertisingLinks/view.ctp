<h1><?php echo __('View Link'); ?></h1>
<?php
    echo $this->Form->create('AdvertisingLink');
    echo $this->Form->input('description', array('disabled' => true));
	echo $this->Form->input('marketing_channels_id', array(
		'options' => $channels,
		'class'=>'select-ui-primary',
		'disabled' => true
	));
	echo $this->Form->input('destination_url', array('disabled' => true));
	echo $this->Form->input('generated_url', array('disabled' => true, 'value' => Router::url('/dlink/', true).$this->data['AdvertisingLink']['generated_url']));
    echo $this->Form->end();
?>