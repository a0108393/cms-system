<?php

class CaptchaHelper extends AppHelper {

	public $helpers = array('Html', 'Form');
	
	function render($settings = array()){
		$default_settings = array(
			'captchaType'=>'image', 
			'jquerylib'=>false,
			'fieldName'=>'captcha',
			'bootstrap' => true,
			'label' => 'Captcha',
			'before' => '',
			'after' => '',
			'div' => '',
			'class' => '',
			'imgwidth' => 150,
			'imgheight' => 50,
			'required' => false,
		);
		
		$settings = array_merge($default_settings, $settings);
		$fieldset = array(
			'autocomplete'=>'off',
			'label'=>false
			);
		if($settings['before']){
			$fieldset['before'] = $settings['before'];
		}
		if($settings['after']){
			$fieldset['after'] = $settings['after'];
		}
		if($settings['div']){
			$fieldset['div'] = $settings['div'];
		}
		$fieldset['class'] ='';
		if($settings['class']){
			$fieldset['class'] .= $settings['class'];
		}
		if($settings['required']){
			$fieldset['class'] .= ' required';
			$fieldset['required'] = $settings['required'];
		}
		switch($settings['captchaType']):
			case 'image':
				$html = $this->Html->image($this->Html->url(array('plugin' => null, 'controller'=>'users', 'action'=>'captcha', $settings['imgwidth'], $settings['imgheight']), true),array('id'=>'img-captcha','vspace'=>2));
				$html .= '<a href="#" id="a-reload" style="margin-left: 20px; font-size: 20px"><span class="glyphicon glyphicon-refresh"></span></a><br/>';
				$html .= __('Enter security code shown above:');
				$html .= $this->Form->input($settings['modelName'].'.'.$settings['fieldName'], $fieldset);
				if($settings['jquerylib'])  {
					$html .= '<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>';
				}

				$html .= '<script type="text/javascript">' .
				'jQuery(\'#a-reload\').click(function() {' .
				  'var $captcha = jQuery("#img-captcha");' .
					'$captcha.attr(\'src\', $captcha.attr(\'src\')+\'?\'+Math.random());' . 
				  'return false;' . 
				'});' . 
				'</script>';

			break;
			case 'math':
				$html = '<p>Answer simple math:</p>'.$settings['stringOperation'].' = ?';
				$html .= $this->Form->input($settings['modelName'].'.'.$settings['fieldName'], $fieldset);
			break;
		endswitch;
		if($settings['bootstrap']){
			?>
			<div class="form-group">
				<label class="col-lg-2 control-label"><?php echo $settings['label']; ?></label>
				<div class="col-lg-10">
					<?php echo $html; ?>
				</div>
			</div>
			<?php
		}else{
			echo $html;
		}
	}
}