<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width">
		<title>Enquiry</title>
		<?php
		echo $this->Html->css(array(
			'bootstrap',
			'flat-ui',
			'select2',
			'ajaxupload/style',
			'custom'
		));
		echo $this->Html->script(array(
			'html5',
			'jquery-1.10.2.min',
			'select2',
			'tinyMCE4.0/tinymce.min',
			'ajaxupload',
		));

		echo $this->fetch('script');
		echo $this->fetch('css');
		?>
		<script type="text/javascript">
		tinymce.init({
			selector: "textarea:not([noeditor])",
			theme: "modern",
			document_base_url: "<?php echo Router::url('/', true); ?>",
			relative_urls : false,
			remove_script_host : false,
			convert_urls : true,
			plugins: [
				"advlist autolink lists link image charmap print preview hr anchor pagebreak",
				"searchreplace wordcount visualblocks visualchars code fullscreen",
				"insertdatetime media nonbreaking save table contextmenu directionality",
				"emoticons template paste textcolor"
			],
			toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
			toolbar2: "print preview media | forecolor backcolor emoticons",
			image_advtab: true,
			image_list: "<?php echo Router::url('/getmedia.php', true); ?>",
			templates: [
				{title: 'Test template 1', content: 'Test 1'},
				{title: 'Test template 2', content: 'Test 2'}
			]
		});
		</script>
	</head>
	<body class="admin-login">
		<div id="wrap">

			<header class="navbar navbar-inverse navbar-fixed-top">
				<div class="navbar-inner">
					<div class="container-fluid">
						<h4 style="text-align: center; color: #fff">Welcome to CRM!</h4>
					</div>
				</div>
			</header>

			<div id="push"></div>
			<div id="content-container" class="container-fluid">
				
					<div class="login-screen enquiry">
					<?php
						echo $this->Session->flash();
						echo $this->fetch('content');
					?>
					</div>
				
			</div>

		</div>
		<?php //echo $this->element('footer'); ?>
	</body>
</html>