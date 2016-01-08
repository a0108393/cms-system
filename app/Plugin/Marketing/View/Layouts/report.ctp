<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width" />
	<?php echo $this->Html->charset(); ?>
	<title><?php echo $meta_title; ?></title>
	
	<?php
		echo $this->Html->meta('icon');
		echo $this->Html->css(array(
			'bootstrap',
			'bootstrap-dialog',
			'bootstrap-datetimepicker.min',
			'select2',
			'flat-ui',
			'custom',
			'ladda-themeless.min',
			'token-input',
			'ajaxupload/style',
		));

		echo $this->Html->script(array(
			'jquery',
			//'jquery-1.10.2.min',
			'jquery-ui-autocomplete',
			'jquery-ui.min',
			'jquery.select-to-autocomplete.min',
			'bootstrap.min',
			'select2',
			'moment-2.4.0',
			'jquery.tokeninput',
			'bootstrap-dialog',
			'bootstrap-switch',
			'bootstrap-select',
			'bootstrap-datetimepicker',
			'jqBootstrapValidation',
			'flatui-checkbox',
			'flatui-radio',
			'ajaxupload',
			'spin.min',
			'ladda.min',
			'tinyMCE4.0/tinymce.min',
			'application',
			'admin',
		));
		echo $this->Html->script('Marketing.highcharts');
		echo $this->Html->script('Marketing.exporting');
		echo $this->fetch('script');
		echo $this->fetch('css');
	?>
</head>
<body>
	<div id="wrap">
		<?php echo $this->element('header'); ?>
		<div id="header_top"></div>
		<div class="row" id="content_area">
			<div class="col-xs-2" id="main_left_sidebar">
				<?php echo $this->element('navigation'); ?>
			</div>
			<div class="col-xs-10" id="main_content_right">
				<div id="inner-content" class="col-xs-12">
					<?php
						$this->Html->addCrumb(__(''), '/', array('class' => 'glyphicon glyphicon-home', 'style' => 'font-size: 20px'));
						if (!empty($plugin)) {
							$this->Html->addCrumb(__(Inflector::humanize($plugin)), '/'.$plugin);
							$this->Html->addCrumb(__(Inflector::humanize($controller)), '/'.$plugin.'/'.$controller);
						} else {
							$this->Html->addCrumb(__(Inflector::humanize($controller)), '/'.$controller);
						}
					?>
					<?php echo $this->element('breadcrumb'); ?>
					<?php echo $this->Session->flash(); ?>
					<?php echo $this->fetch('content'); ?>
				</div>
			</div>
		</div>
		<?php echo $this->element('footer'); ?>
	</div>
	<?php 
	echo $this->Js->writeBuffer(); 
	?>
</body>
</html>
