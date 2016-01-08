<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width">
		<title>Login</title>
		<?php
		echo $this->Html->css(array(
			'bootstrap',
			'flat-ui',
			'custom'
		));
		echo $this->Html->script(array(
			'html5',
			'jquery-1.10.2.min',
		));

		echo $this->fetch('script');
		echo $this->fetch('css');
		?>
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
				
					<div class="login-screen">
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