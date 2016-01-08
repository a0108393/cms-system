<?php 
	$act = $this->action;
?>

<div class="user form">
<?php 
	echo $this->Form->create('User', array(
		'novalidate' => true,
		'error' => false
	));
	
	echo $this->Form->input('current_password', array('type' => 'password')); 
	echo $this->Form->input('new_password', array('type' => 'password'));
	echo $this->Form->input('confirm_password', array('type' => 'password'));
	echo $this->Captcha->render(array('modelName'=>'User')); 
	echo $this->Form->submit('Submit');

	echo $this->Form->end(); ?>
</div>
