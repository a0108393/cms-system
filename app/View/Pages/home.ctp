<?php
/**
 *
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 */

if (!Configure::read('debug')):
	throw new NotFoundException();
endif;

App::uses('Debugger', 'Utility');
?>
<?php
if (Configure::read('debug') > 0):
	Debugger::checkSecurityKeys();
endif;
?>
<?php 
	$user = $this->Session->read('User_loged');
	if($user){
		echo 'Hi, <b>' . $user . '</b>';
	}
?>
<p>
<?php
	/* if (version_compare(PHP_VERSION, '5.2.8', '>=')):
		echo '<span class="notice success">';
			echo __d('cake_dev', 'Your version of PHP is 5.2.8 or higher.');
		echo '</span>';
	else:
		echo '<span class="notice">';
			echo __d('cake_dev', 'Your version of PHP is too low. You need PHP 5.2.8 or higher to use CakePHP.');
		echo '</span>';
	endif; */
?>
</p>
<p>
	<?php
	/* 	if (is_writable(TMP)):
			echo '<span class="notice success">';
				echo __d('cake_dev', 'Your tmp directory is writable.');
			echo '</span>';
		else:
			echo '<span class="notice">';
				echo __d('cake_dev', 'Your tmp directory is NOT writable.');
			echo '</span>';
		endif; */
	?>
</p>
<p>
	<?php
		/* $settings = Cache::settings();
		if (!empty($settings)):
			echo '<span class="notice success">';
				echo __d('cake_dev', 'The %s is being used for core caching. To change the config edit %s', '<em>'. $settings['engine'] . 'Engine</em>', 'APP/Config/core.php');
			echo '</span>';
		else:
			echo '<span class="notice">';
				echo __d('cake_dev', 'Your cache is NOT working. Please check the settings in %s', 'APP/Config/core.php');
			echo '</span>';
		endif; */
	?>
</p>
<p>
	<?php
		/* $filePresent = null;
		if (file_exists(APP . 'Config' . DS . 'database.php')):
			echo '<span class="notice success">';
				echo __d('cake_dev', 'Your database configuration file is present.');
				$filePresent = true;
			echo '</span>';
		else:
			echo '<span class="notice">';
				echo __d('cake_dev', 'Your database configuration file is NOT present.');
				echo '<br/>';
				echo __d('cake_dev', 'Rename %s to %s', 'APP/Config/database.php.default', 'APP/Config/database.php');
			echo '</span>';
		endif; */
	?>
</p>
<?php
/* if (isset($filePresent)):
	App::uses('ConnectionManager', 'Model');
	try {
		$connected = ConnectionManager::getDataSource('default');
	} catch (Exception $connectionError) {
		$connected = false;
		$errorMsg = $connectionError->getMessage();
		if (method_exists($connectionError, 'getAttributes')):
			$attributes = $connectionError->getAttributes();
			if (isset($errorMsg['message'])):
				$errorMsg .= '<br />' . $attributes['message'];
			endif;
		endif;
	} */
?>
<p>
	<?php
	/* 	if ($connected && $connected->isConnected()):
			echo '<span class="notice success">';
				echo __d('cake_dev', 'CakePHP is able to connect to the database.');
			echo '</span>';
		else:
			echo '<span class="notice">';
				echo __d('cake_dev', 'CakePHP is NOT able to connect to the database.');
				echo '<br /><br />';
				echo $errorMsg;
			echo '</span>';
		endif; */
	?>
</p>

<?php
	/* App::uses('Validation', 'Utility');
	if (!Validation::alphaNumeric('cakephp')):
		echo '<p><span class="notice">';
			echo __d('cake_dev', 'PCRE has not been compiled with Unicode support.');
			echo '<br/>';
			echo __d('cake_dev', 'Recompile PCRE with Unicode support by adding <code>--enable-unicode-properties</code> when configuring');
		echo '</span></p>';
	endif; */
?>

<p>
	<?php
	/* 	if (CakePlugin::loaded('DebugKit')):
			echo '<span class="notice success">';
				echo __d('cake_dev', 'DebugKit plugin is present');
			echo '</span>';
		else:
			echo '<span class="notice">';
				echo __d('cake_dev', 'DebugKit is not installed. It will help you inspect and debug different aspects of your application.');
				echo '<br/>';
				echo __d('cake_dev', 'You can install it from %s', $this->Html->link('GitHub', 'https://github.com/cakephp/debug_kit'));
			echo '</span>';
		endif; */
	?>
</p>

<h3>Test Permission</h3>
<ul>
<li><a href="<?php echo Router::url(array('controller' => 'Client'), true); ?>">Manage Client</a></li>
<li><a href="<?php echo Router::url(array('controller' => 'Project'), true); ?>">Manage Project</a></li>
<li><a href="<?php echo Router::url(array('controller' => 'Users'), true); ?>">Manage User</a></li>

<li>
<?php
echo $this->Html->link(
    'Test bootstrap plugin',
    array('plugin' => 'BoostCake', 'controller' => 'BoostCake', 'action' => 'index')
);
?>
</li>
</ul>