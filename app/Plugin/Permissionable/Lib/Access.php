<?php

/**
 * Permissionable
 *
 * Provides static class app-wide for Permissionable info getting/setting
 *
 * @package     permissionable
 * @subpackage  permissionable.libs
 * @author      Joshua McNeese <jmcneese@gmail.com>
 */
class Access{
	public static $userData = array();	
	
	public static function setDefaultPermsModule() {
		$arr = array('_read' => GLOBAL_P, '_update' => GLOBAL_P, '_delete' => GLOBAL_P,'_create' => GLOBAL_P);
		$plugin_names = CakePlugin::loaded();
		$plugin_names[] = 'Core';
		$arr_mod = array();
		foreach($plugin_names as $key => $val){
			$arr_mod[] = array('name' => $val,'perms' => $arr);
		}
		
		return  $arr_mod;
	}
	
	public static function setPermsModule($user) {
		$data = $user;
		$group_arr = array(-1);
		if(isset($data['Group']) && is_array($data['Group']) && !empty($data['Group'])){
			$group_arr = array();
			foreach($data['Group'] as $val_group){
				$group_arr[] = $val_group['id'];
			}
		}
		
		if(is_array($data) && !empty($data) && isset($data['User']['id'])){
			if($data['User']['id'] != SUPER_ADMIN){
				$options['joins'] = array(
					array('table' => 'module_permissions',
						'alias' => 'UserModulePermission',
						'type' => 'left',
						'conditions' => array(
							'Module.id = UserModulePermission.module_id',
							'UserModulePermission.type = "user"',
							'UserModulePermission.aro_id ='.$data['User']['id']
						)
					),
					array('table' => 'module_permissions',
						'alias' => 'GroupModulePermission',
						'type' => 'left',
						'conditions' => array(
							'Module.id = GroupModulePermission.module_id',
							'GroupModulePermission.type = "group"',
							'GroupModulePermission.aro_id' => $group_arr
						)
					)
				);
				
				$options['fields'] = array('Module.id as id,Module.name as name,(IF( UserModulePermission._read IS NULL , max(GroupModulePermission._read), UserModulePermission._read )) AS _read, (IF( UserModulePermission._update IS NULL , max(GroupModulePermission._update), UserModulePermission._update )) AS _update, (IF( UserModulePermission._delete IS NULL , max(GroupModulePermission._delete), UserModulePermission._delete )) AS _delete, (IF( UserModulePermission._create IS NULL , max(GroupModulePermission._create), UserModulePermission._create )) AS _create');
				$options['group'] = 'Module.id';
				$result = ClassRegistry::init('Permissionable.Module')->find('all',$options);
				$arr = $user;
				$arr['Group'] = $group_arr;
				foreach($result as $val){
					$mar = array('name' => $val['Module']['name'],'perms' => $val[0]);
					$arr['Module'][] = $mar;
				}
				
				return Access::$userData = $arr;
			}
			else{
				$arr = array('User' => $user);
				$arr['Module'] = Access::setDefaultPermsModule();
				return Access::$userData = $arr;
			}
		}
		else{
			return Access::$userData = array();
		}
	}
		
	public static function __getAllowActions($module, $name){
		if(!$module){
			$module = 'Core';
		}
		if(Configure::check('AllowAction.'.ucfirst($module))){
			foreach(Configure::read('AllowAction.'.ucfirst($module)) as $val){
				if(strtolower($val['c']) == strtolower($name)){
					return $val['a'];
				}
			}
		}
		return array();
	}
	
	public static function __getcrudMapping($module, $name){
		if(!$module){
			$module = 'Core';
		}
		if(Configure::check('CrudMapping.'.ucfirst($module))){
			foreach(Configure::read('CrudMapping.'.ucfirst($module)) as $val){
				if(strtolower($val['c']) == strtolower($name)){
					return $val['a'];
				}
			}
		}
		return array();
	}
		
	public static function __getPermissionCurrentModule($module) {
		if(!$module){
			$module = 'Core';
		}
		if(!empty(Access::$userData) && $module){	
			foreach(Access::$userData['Module'] as $val){
				if(strtolower($val['name']) == strtolower($module))
					return $val;
			}
		}
		
		return array();
	}
	
	public static function __combineMapping($arr){
		$arr_k = array();
		if(!empty($arr)){
			if(isset($arr['view']))
				$arr_k = Set::merge($arr_k,$arr['view']);
			if(isset($arr['edit']))
				$arr_k = Set::merge($arr_k,$arr['edit']);
			if(isset($arr['delete']))
				$arr_k = Set::merge($arr_k,$arr['delete']);
			return $arr_k;
		}
		return array();
	}
	
	public static function checkModule($module, $controller, $action) {
		$arr_action = Access::__getAllowActions($module, $controller);
			
		if(!empty($arr_action) && in_array($action,$arr_action)){
			return true;
		}
		
		$arr_mapp = Access::__getcrudMapping($module, $controller);
		$action = Access::__getMappingAction($arr_mapp, $action);
		
		if(Access::isLoggedin()){
			$user_module = Access::__getPermissionCurrentModule($module);
			
			switch ($action){
				case "view": 
					if(empty($user_module) || $user_module['perms']['_read'] == DENY_P){
						return false;
					}
					return true;
				case "edit":
					if(empty($user_module) || $user_module['perms']['_update'] == DENY_P){
						return false;
					}
					return true;
				case "delete":
					if(empty($user_module) || $user_module['perms']['_delete'] == DENY_P){
						return false;
					}
					return true;
				case "add":
					if(empty($user_module) || $user_module['perms']['_create'] == DENY_P){
						return false;
					}
					return true;
				default:
					return false;
			}
		}
		return true;
    }
	
	public static function __getMappingAction($arr, $action){
		if(!empty($arr)){
			foreach($arr as $key => $val){
				if(in_array($action,$val)){
					return $key;
				}
			}
		}
	
		return $action;
	}
	
	public static function __getPerms($per){
		if(!empty($per)){
			foreach($per as $key => $val){
				if(isset($val['perms']) && isset($per[0]['perms_merge'])){
					$val['perms'] = $per[0]['perms_merge'];
					return $val;
				}
				elseif(isset($val['perms'])){
					return $val;
				}
			}
		}
		return array(
			'user_id' => 0,
			'group_id' => 0,
			'perms' => 0
		);
	}
	
	public static function checkRow($module,$controller, $type = 'edit', $per = array()) {
		$user_module = Access::__getPermissionCurrentModule($module);
		$arr_mapp = Access::__getcrudMapping($module, $controller);
		$type = Access::__getMappingAction($arr_mapp, $type);
		$per = Access::__getPerms($per);
		 
		if(empty($per) || empty($user_module)){
			return false;
		}
		 
		switch ($type){
			case "view": 
				if($user_module['perms']['_read'] == GLOBAL_P ){
					return true;
				}
				if(Access::$userData['User']['id'] == $per['user_id']){
					if((OWNER_READ_BIT & $per['perms']) <> 0)
						return true;
				}
				if(in_array($per['group_id'],Access::$userData['Group'])){
					if((GROUP_READ_BIT & $per['perms']) <> 0)
						return true;
				}
				if((OTHER_READ_BIT & $per['perms']) <> 0)
					return true;
				return false;
			case "edit":
				if($user_module['perms']['_update'] == GLOBAL_P ){
					return true;
				}
				if($user_module['perms']['_update'] == DENY_P ){
					return false;
				}
				if(Access::$userData['User']['id'] == $per['user_id']){
					if((OWNER_WRITE_BIT & $per['perms']) <> 0)
						return true;
				}
				if(in_array($per['group_id'],Access::$userData['Group'])){
					if((GROUP_WRITE_BIT & $per['perms']) <> 0)
						return true;
				}
				if((OTHER_WRITE_BIT & $per['perms']) <> 0)
					return true;
				return false;
			case "delete":
				if($user_module['perms']['_delete'] == GLOBAL_P ){
					return true;
				}
				if($user_module['perms']['_delete'] == DENY_P ){
					return false;
				}
				if(Access::$userData['User']['id'] == $per['user_id']){
					if((OWNER_DELETE_BIT & $per['perms']) <> 0)
						return true;
				}
				if(in_array($per['group_id'],Access::$userData['Group'])){
					if((GROUP_DELETE_BIT & $per['perms']) <> 0)
						return true;
				}
				if((OTHER_DELETE_BIT & $per['perms']) <> 0)
					return true;
				return false;
			default:
					return false;
		}
	}
	
	public static function checkPermissionCreateModule($module) {
		$user_module = Access::__getPermissionCurrentModule($module);
		if(empty($user_module) || $user_module['perms']['_create'] == DENY_P){
			return false;
		}
		return true;
	}
	
	
	public static function isLoggedin() {
		return !empty(Access::$userData);  
	}
	
	public static function getUserData() {
		return  Access::$userData;  
	}
	

}

?>
