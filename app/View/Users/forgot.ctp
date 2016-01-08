
<?php 
	echo $this->Form->create('User');
?>
	<div class="box">
		<div class="login-form">
		<?php
			$this->Form->inputDefaults(array(
				'label' => false,
				'div' => 'form-group',
				'class' => 'form-control',
				'novalidate' => true,
				'error' => false
			));
			echo $this->Form->input('your_email',array(
				'placeholder' => __('Email'),
				'after' => '<label class="login-field-icon fui-mail" for="emai."></label>'
			));
			echo $this->Captcha->render(array(
					'modelName'=>'User',
					'bootstrap' => false,
					'imgwidth' => 290,
					'imgheight' => 60,
					'after' => '<label class="login-field-icon fui-lock" for="password"></label>'
					)); 
			echo $this->Form->submit(__('Send'), array(
					'class' => 'btn btn-info btn-lg btn-block',
					'before' => '<div>'
				));
		?>
		</div>
	</div>
<?php
	echo $this->Form->end();
?>
