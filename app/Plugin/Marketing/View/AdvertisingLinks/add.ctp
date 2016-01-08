<h1>Add Advertising Link</h1>
<?php
echo $this->Form->create('AdvertisingLink');
echo $this->Form->input('description');
echo $this->Form->input('marketing_channels_id', array(
	'options' => $channels,
	'class'=>'select-ui-primary', 
	'default' => Configure::read('Settings.Company.DefaultChannel')
));
echo $this->Form->input('destination_url', array( 
    'after' => '<p>'. __('(use absolute URL for external pages and use relative URL path for internal pages)') .'</p></div>'
));
echo $this->Form->end('Save Link');
?>