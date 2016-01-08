<?php

?>
<header class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container-fluid">
			<a class="btn btn-navbar collapsed" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			
			<h1><?php echo $page_title; ?></h2>
			
				<?php if ($this->Session->read('Auth.User.id')): ?>
				<ul class="nav pull-right">
					<li>
						<?php echo $this->Html->link(__('Change password'), array('controller' => 'users', 'action' => 'changepass')); ?>
					</li>
					<li>
						<a href="<?php echo $this->Html->url(array('plugin' => null, 'controller' => 'users', 'action' => 'profiles')); ?>">
							<?php echo __('You are logged in as: %s', $this->Session->read('Auth.User.name')); ?>
						</a>
					</li>
					<li>
						<?php echo $this->Html->link(__('Log out'), array('plugin' => null, 'controller' => 'users', 'action' => 'logout')); ?>
					</li>
				</ul>
				<?php endif; ?>
		
		</div>
	</div>
</header>