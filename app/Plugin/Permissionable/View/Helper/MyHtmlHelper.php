<?php
/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('HtmlHelper', 'View/Helper');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class MyHtmlHelper extends HtmlHelper {
	/**
	 * Render action button with permission check
	 *
	 * @param string $title 
	 * @param array $url 
	 * @param array $item - model item
	 */	
	public function menuAction($title, $icon, $url = null, $options = array(), $item = false, $confirmMessage = false)
	{
	/**
	 * Render link at menu list with permission check
	 *
	 * @param string $title = user
	 * @param string $icon = home
	 * @param array $url 
	 * @param array $item - model item
	 */	
		$options['escape'] = false;
		if($confirmMessage){
			$options['onclick'] = 'confirmDialog(this.href, \''. str_replace("'","\'", $confirmMessage). '\'); return false;';
			$confirmMessage = false;
		}
		
		if($icon){
			$title = '<i class="glyphicon glyphicon-'. $icon .'"></i>' . '<span>'. $title . '</span>';
		}
		
		$cur_url = array(
			'plugin' => strtolower($this->plugin),
			'controller' => $this->params['controller'],
			'action' => $this->action
		);
		
		$url_repare = array();
		if(is_array($url)){
			if(isset($url['plugin'])){
				$url_repare['plugin'] = strtolower($url['plugin']);
			}else{
				$url_repare['plugin'] = '';
			}
			if(isset($url['controller'])){
				$url_repare['controller'] = $url['controller'];
			}
			if(isset($url['action'])){
				$url_repare['action'] = $url['action'];
			}
			if(isset($url['plugin']) && isset($url['controller']) && !isset($url['action'])){
				$url_repare['action'] = 'index';
			}
			
			if($cur_url['action'] == 'edit' || $cur_url['action'] == 'view' || $cur_url['action'] == 'edit_price' || $cur_url['action'] == 'delete'){
				$cur_url['action'] = 'index';
			}
			
			$check = array_diff_assoc($cur_url, $url_repare);
			if(count($check) == 0){
				if(isset($options['class'])){
					$options['class'] .= ' current';
				}else{
					$option['class'] = 'current';
				}
			}
		}
		
		if(is_array($url)){
			if(!isset($url['action'])) {
				$url['action'] = 'index';
			}
			//if(isset($url['action'])){
				$module = isset($url['plugin']) ? $url['plugin'] : $this->plugin;
				$controller	 = isset($url['controller']) ? $url['controller'] : $this->params['controller'];
				$user_module = Access::__getPermissionCurrentModule($module);
				$action = $url['action'];
				
				switch ($action){
					case "add":
						if(!Access::checkPermissionCreateModule($module)){
							return '';
						}
						return parent::link($title, $url, $options, $confirmMessage);
					default:
						if(!Access::checkRow($module, $controller, $action, $item)){
							
							return '';
						}
						return parent::link($title, $url, $options, $confirmMessage);
				}
			//}
		}
		else{
			die('not support');
		}
		return parent::link($title, $url, $options, $confirmMessage);
    }
	public function topAction($title, $icon, $url = null, $type = false, $item = false, $confirmMessage = false)
	{
	/**
	* Render link at menu list with permission check
	*
	* @param string $title = user
	* @param string $icon = home
	* @param string $type = default
	* @param array $url 
	* @param array $item - model item
	* @param string $confirmMessage = are you sure? ...
	*/	
		$options = array();
		$options['escape'] = false;
		if($confirmMessage){
			$options['onclick'] = 'confirmDialog(this.href, \''. str_replace("'","\'", $confirmMessage). '\'); return false;';
			$confirmMessage = false;
		}
		
		if($icon){
			$title = '<i class="glyphicon glyphicon-'. $icon .'"></i>' . '<span>'. $title . '</span>';
		}
		$options['class'] = 'btn';
		if($type){
			$options['class'] .= ' btn-' . $type;
		}
		if(is_array($url)){
			if(!isset($url['action'])) {
				$url['action'] = 'index';
			}
			//if(isset($url['action'])){
				$module = isset($url['plugin']) ? $url['plugin'] : $this->plugin;
				$controller	 = isset($url['controller']) ? $url['controller'] : $this->params['controller'];
				$user_module = Access::__getPermissionCurrentModule($module);
				$action = $url['action'];
				
				switch ($action){
					case "add":
						if(!Access::checkPermissionCreateModule($module)){
							return '';
						}
						return parent::link($title, $url, $options, $confirmMessage);
					default:
						if(!Access::checkRow($module, $controller, $action, $item)){
							return '';
						}
						return parent::link($title, $url, $options, $confirmMessage);
				}
			//}
		}
		else{
			die('not support');
		}
		return parent::link($title, $url, $options, $confirmMessage);
    }
	
	public function gridAction($icon, $url = null, $item = false, $confirmMessage = false, $option = array())
	{
		$title = '';
		$options = $option;
		$options['escape'] = false;
		if($confirmMessage){
			$options['onclick'] = 'confirmDialog(this.href, \''. str_replace("'","\'", $confirmMessage). '\'); return false;';
			$confirmMessage = false;
		}
		
		if($icon){
			$title = '<i class="glyphicon glyphicon-'. $icon .'"></i>';
		}
		
		if(is_array($url)){
			if(!isset($url['action'])) {
				$url['action'] = 'index';
			}		
			//if(isset($url['action'])){
				$module = isset($url['plugin']) ? $url['plugin'] : $this->plugin;
				$controller	 = isset($url['controller']) ? $url['controller'] : $this->params['controller'];
				$user_module = Access::__getPermissionCurrentModule($module);
				$action = $url['action'];
				
				switch ($action){
					case "add":
						if(!Access::checkPermissionCreateModule($module)){
							return '';
						}
						return parent::link($title, $url, $options, $confirmMessage);
					default:
						if(!Access::checkRow($module, $controller, $action, $item)){
							return '';
						}
						return parent::link($title, $url, $options, $confirmMessage);
				}
			//}
		}
		else{
			die('not support');
		}
		return parent::link($title, $url, $options, $confirmMessage);
    }
	
	public function gridLink($title = 'view', $url = null, $item = false, $confirmMessage = false)
	{
		
		$options = array();
		$options['escape'] = false;
		if($confirmMessage){
			$options['onclick'] = 'confirmDialog(this.href, \''. str_replace("'","\'", $confirmMessage). '\'); return false;';
			$confirmMessage = false;
		}
		
		if(is_array($url)){
			if(!isset($url['action'])) {
				$url['action'] = 'index';
			}		
			//if(isset($url['action'])){
				$module = isset($url['plugin']) ? $url['plugin'] : $this->plugin;
				$controller	 = isset($url['controller']) ? $url['controller'] : $this->params['controller'];
				$user_module = Access::__getPermissionCurrentModule($module);
				$action = $url['action'];
				
				switch ($action){
					case "add":
						if(!Access::checkPermissionCreateModule($module)){
							return $title;
						}
						return parent::link($title, $url, $options, $confirmMessage);
					default:
						if(!Access::checkRow($module, $controller, $action, $item)){
							return $title;
						}
						return parent::link($title, $url, $options, $confirmMessage);
				}
			//}
		}
		else{
			die('not support');
		}
		return parent::link($title, $url, $options, $confirmMessage);
    }
}
