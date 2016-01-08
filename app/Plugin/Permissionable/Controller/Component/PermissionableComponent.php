<?php

/**
 * PermissionableComponent
 *
 * Sets user info for PermissionableBehavior
 *
 * @package     permissionable
 * @subpackage  permissionable.controllers.components
 * @see         PermissionableBehavior
 * @uses		Component
 * @author      Joshua McNeese <jmcneese@gmail.com>
 * @license		Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
 * @copyright	Copyright (c) 2009,2010 Joshua M. McNeese, Curtis J. Beeson
 */
App::uses('Component', 'Controller');
class PermissionableComponent extends Component {
	var $components = array('Auth');
	var $user = array();
	
    public function initialize(Controller $controller) {
		App::import('Lib', 'Permissionable');
		Permissionable::setUserId(AuthComponent::user('id'));
		Permissionable::setGroupId(AuthComponent::user('group_id'));
		$group_arr = array(-1);
		$user_info = $controller->Session->read('Auth');
		if(isset($user_info['Group']) && is_array($user_info['Group']) && !empty($user_info['Group'])){
			$group_arr = array();
			foreach($user_info['Group'] as $val_group){
				$group_arr[] = $val_group['id'];
			}
		}
		Permissionable::setGroupIds($group_arr);
		Access::setPermsModule($user_info);
		Configure::write('ModulePermissionUserData',$this->user);
    }
	 
	public function checkModuleAccess(Controller $controller) {
		//var_dump($controller->plugin);die; 
		if( $controller->name == 'Users' && $controller->action == 'dashboard')
			return true;
		return Access::checkModule($controller->plugin, $controller->params['controller'], $controller->action); 
    }
	
	public function checkRowAccess(Controller $controller, $per = array()){  
		return Access::checkRow($controller->plugin,$controller->params['controller'],$controller->action, $per);
    }
}

?>