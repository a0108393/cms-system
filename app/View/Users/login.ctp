<?php echo $this->Form->create('User'); ?>
<div class="box">
	<div class="login-form">
	<?php
		$this->Form->inputDefaults(array(
			'label' => false,
			'div' => 'form-group',
			'class' => 'form-control'
		));
		echo $this->Form->input('email', array(
			'placeholder' => __('Email'),
			'after' => '<label class="login-field-icon fui-mail" for="emai."></label>'
		));
		echo $this->Form->input('password', array(
			'placeholder' => __('Password'),
			'after' => '<label class="login-field-icon fui-lock" for="password"></label>',
			'autocomplete' => 'off'
		));
		
		echo $this->Form->submit(__('Log In'), array('class' => 'btn btn-info btn-lg btn-block','before' => '<div>'));
		echo $this->Html->link(__('Forgot password?'), array(
			'controller' => 'users',
			'action' => 'forgot',
			), array(
			'class' => 'login-link'
		));
	?>
	</div>
</div>
<?php echo $this->Form->end(); ?>