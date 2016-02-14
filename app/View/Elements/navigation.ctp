<nav class="navbar-inverse sidebar">
	<div class="navbar-inner">
		<ul class="nav nav-stacked" id="sidebar-menu">
			<li>
				<?php echo $this->Html->menuAction(__('Marketing'), 'compressed',array('plugin' => 'marketing', 'controller' => 'channels'), array('class' => 'dropdown-close sidebar-item hasChild')); ?>
				<ul class="nav nav-stacked sub-nav  submenu-content">
					<li>
						<?php echo $this->Html->menuAction(__('Events'), '',array('plugin' => 'marketing', 'controller' => 'events'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('Manage  Links'), '',array('plugin' => 'marketing', 'controller' => 'advertisingLinks'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('Manage  Channels'), '',array('plugin' => 'marketing', 'controller' => 'channels'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('Marketing Enquiries'), '',array('plugin' => 'marketing', 'controller' => 'enquiries'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('Our Affiliates'), '',array('plugin' => 'marketing', 'controller' => 'affiliates','action'=>'index'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('Enquiry Reports'), '',array('plugin' => 'marketing', 'controller' => 'reports', 'action' => 'enquiries'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('Link Reports'), '',array('plugin' => 'marketing', 'controller' => 'reports', 'action' => 'links'), array('class' => 'sidebar-item')); ?>
					</li>
				</ul>
			</li>
			<li>
				<?php echo $this->Html->menuAction(__('Company'), 'compressed',array('plugin' => 'company', 'controller' => 'companies'), array('class' => 'dropdown-close sidebar-item hasChild')); ?>
				<ul class="nav nav-stacked sub-nav  submenu-menus">
					<li>
						<?php echo $this->Html->menuAction(__('Companies'), '',array('plugin' => 'company', 'controller' => 'companies'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('Industries'), '',array('plugin' => 'company', 'controller' => 'industries'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('Users'), '',array('plugin' => 'company', 'controller' => 'users', 'action' => 'index'), array('class' => 'sidebar-item')); ?>
					</li>
				</ul>
			</li>
			<li>
				<?php echo $this->Html->menuAction(__('Accounting'), 'compressed',array('plugin' => 'accounting', 'controller' => 'ratecards'), array('class' => 'dropdown-close sidebar-item hasChild')); ?>
				<ul class="nav nav-stacked sub-nav  submenu-menus">
					<li>
						<?php echo $this->Html->menuAction(__('Ratecards'), '',array('plugin' => 'accounting', 'controller' => 'ratecards'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('Categories'), '',array('plugin' => 'accounting', 'controller' => 'categories'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('Services'), '',array('plugin' => 'accounting', 'controller' => 'services'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('Quotations'), '',array('plugin' => 'accounting', 'controller' => 'quotations', 'action' => 'index'), array('class' => 'sidebar-item')); ?>
					</li>
				</ul>
			</li>
			
			<li>
				<?php echo $this->Html->menuAction(__('Project'), 'compressed',array('plugin' => 'project', 'controller' => 'briefs'), array('class' => 'dropdown-close sidebar-item hasChild')); ?>

				<ul class="nav nav-stacked sub-nav  submenu-menus">
					<li>
						<?php echo $this->Html->menuAction(__('Dashboard'), '',array('plugin' => 'project', 'controller' => 'Dashboards', 'action' => 'index'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('Briefs'), '',array('plugin' => 'project', 'controller' => 'briefs', 'action' => 'index'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('MeetingMinutes'), '',array('plugin' => 'project', 'controller' => 'MeetingMinutes', 'action' => 'index'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('Deliverables'), '',array('plugin' => 'project', 'controller' => 'Deliverables', 'action' => 'index'), array('class' => 'sidebar-item')); ?>
					</li>
				</ul>	

				
			</li>		
			<li>
				<?php echo $this->Html->menuAction(__('User'), 'user',array('plugin' => false, 'controller' => 'users'), array('class' => 'dropdown-close sidebar-item hasChild')); ?>
				<ul class="nav nav-stacked sub-nav  submenu-users">
					<li>
						<?php echo $this->Html->menuAction(__('Users'), '',array('plugin' => false, 'controller' => 'users', 'action' => 'index'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('Add user'), '',array('plugin' => false, 'controller' => 'users', 'action' => 'add'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('Groups'), '',array('plugin' => false, 'controller' => 'groups', 'action' => 'index'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('Add group'), '',array('plugin' => false, 'controller' => 'groups', 'action' => 'add'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('User permissions'), '',array('plugin' => 'permissionable', 'controller' => 'users', 'action' => 'index'), array('class' => 'sidebar-item')); ?>
					</li>
				</ul>
			</li>
			<li>
				<?php echo $this->Html->menuAction(__('Permission'), 'cog', array('plugin' => 'permissionable', 'controller' => 'modules'), array('class' => 'dropdown-close sidebar-item hasChild')); ?>
				<ul class="nav nav-stacked sub-nav  submenu-settings">
					<li>
						<?php echo $this->Html->menuAction(__('Modules'), '',array('plugin' => 'permissionable', 'controller' => 'modules', 'action' => 'index'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('Permissions'), '',array('plugin' => 'permissionable', 'controller' => 'permissions', 'action' => 'index'), array('class' => 'sidebar-item')); ?>
					</li>
				</ul>
			</li>
			<li>
				<?php echo $this->Html->menuAction(__('Setting'), 'cog', array('plugin' => false, 'controller' => 'countries'), array('class' => 'dropdown-close sidebar-item hasChild')); ?>
				<ul class="nav nav-stacked sub-nav  submenu-settings">
					<li>
						<?php echo $this->Html->menuAction(__('Countries'), '',array('plugin' => false, 'controller' => 'countries', 'action' => 'index'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('Currencies'), '',array('plugin' => false, 'controller' => 'currencies', 'action' => 'index'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('Company'), '',array('plugin' => false, 'controller' => 'settings', 'action' => 'company'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('Formats'), '',array('plugin' => false, 'controller' => 'settings', 'action' => 'formats'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('Accounting'), '',array('plugin' => false, 'controller' => 'settings', 'action' => 'accounting'), array('class' => 'sidebar-item')); ?>
					</li>
					<li>
						<?php echo $this->Html->menuAction(__('Owner Account'), '',array('plugin' => false, 'controller' => 'ownerAccounts', 'action' => 'index'), array('class' => 'sidebar-item')); ?>
					</li>
					
				</ul>
			</li>
		</ul>
	</div>
</nav>