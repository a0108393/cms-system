<?php

class UsersController extends AppController{
	
	public $uses = array('Permissionable.User', 'Permissionable.Permission', 'Permissionable.Module');
	
	function beforeRender()
    {
        parent::beforeRender();
		
		$html_gender = array( 
			-1 => '<span class="glyphicon glyphicon-remove hasTooltip" title="'. __('No acess') .'"></span>',
			0 => '<span class="glyphicon glyphicon-ok hasTooltip" title="'. __('Row acess') .'"></span>',
			1 => '<span class="glyphicon glyphicon-ok-sign hasTooltip" title="'. __('Global acess') .'"></span>',
			2 => '<span class="glyphicon glyphicon-minus hasTooltip" title="'. __('Group acess') .'"></span>'
		);
        $this->set(compact('html_gender'));
    }
	
	public function index(){
		$configs = array('_read' => __('Read'), 
							'_update' => __('Update'),
							'_delete' => __('Delete'),
							'_create' => __('Create')
							);
		$users = $this->User->find('all', array(
			'conditions' => array('User.id <>' => SUPER_ADMIN)
			));
		$dataUser = $this->User->find('all', array(
			'conditions' => array('User.id <>' => SUPER_ADMIN),
			'fields' => array('User.name, User.id', 'Permission.*'),
			'joins' => array(
				array(
					'table' => 'module_permissions',
					'alias' => 'Permission', 
					'type' => 'LEFT', 
					'conditions' => array('Permission.type' => 'user', 'Permission.aro_id = User.id')
					)
				)
			));
		$dataGroup = $this->User->find('all', array(
			'conditions' => array('User.id <>' => SUPER_ADMIN),
			'fields' => array('User.name, User.id', 'GroupPermission.*'),
			'joins' => array(
				array(
					'table' => 'users_groups',
					'alias' => 'UsersGroup',
					'type' => 'LEFT',
					'conditions' => array('UsersGroup.user_id = User.id')
				),	
				array(
					'table' => 'module_permissions',
					'alias' => 'GroupPermission', 
					'type' => 'LEFT', 
					'conditions' => array('GroupPermission.type' => 'group', 'GroupPermission.aro_id = UsersGroup.group_id')
					)
				)
		));
		
		$gender1 = array();
		$gender2 = array();
		foreach($dataUser as $user_per){
			if($user_per['Permission']['id']){
				$gender1[$user_per['User']['id']][$user_per['Permission']['module_id']]['_read'] = $user_per['Permission']['_read'];
				$gender1[$user_per['User']['id']][$user_per['Permission']['module_id']]['_update'] = $user_per['Permission']['_update'];
				$gender1[$user_per['User']['id']][$user_per['Permission']['module_id']]['_delete'] = $user_per['Permission']['_delete'];
				$gender1[$user_per['User']['id']][$user_per['Permission']['module_id']]['_create'] = $user_per['Permission']['_create'];
			}
		}
		foreach($dataGroup as $group_per){
			$gender2[$group_per['User']['id']][$group_per['GroupPermission']['module_id']]['_read'] = $group_per['GroupPermission']['_read'];
			$gender2[$group_per['User']['id']][$group_per['GroupPermission']['module_id']]['_update'] = $group_per['GroupPermission']['_update'];
			$gender2[$group_per['User']['id']][$group_per['GroupPermission']['module_id']]['_delete'] = $group_per['GroupPermission']['_delete'];
			$gender2[$group_per['User']['id']][$group_per['GroupPermission']['module_id']]['_create'] = $group_per['GroupPermission']['_create'];
		}
		
		$modules = $this->Module->find('all');
		$genderValue = array();
		foreach($users as $item){
			foreach($modules as $module){
				if(!empty($gender1[$item['User']['id']][$module['Module']['id']])){
					$genderValue[$item['User']['id']][$module['Module']['id']]['_read'] = $gender1[$item['User']['id']][$module['Module']['id']]['_read'];
					$genderValue[$item['User']['id']][$module['Module']['id']]['_update'] = $gender1[$item['User']['id']][$module['Module']['id']]['_update'];
					$genderValue[$item['User']['id']][$module['Module']['id']]['_delete'] = $gender1[$item['User']['id']][$module['Module']['id']]['_delete'];
					$genderValue[$item['User']['id']][$module['Module']['id']]['_create'] = $gender1[$item['User']['id']][$module['Module']['id']]['_create'];
				}else{
					if(isset($gender2[$item['User']['id']][$module['Module']['id']]['_read'])){
						$genderValue[$item['User']['id']][$module['Module']['id']]['_read'] = $gender2[$item['User']['id']][$module['Module']['id']]['_read'];
					}else{
						$genderValue[$item['User']['id']][$module['Module']['id']]['_read'] = -1;
					}
					if(isset($gender2[$item['User']['id']][$module['Module']['id']]['_update'])){
						$genderValue[$item['User']['id']][$module['Module']['id']]['_update'] = $gender2[$item['User']['id']][$module['Module']['id']]['_update'];
					}else{
						$genderValue[$item['User']['id']][$module['Module']['id']]['_update'] = -1;
					}
					if(isset($gender2[$item['User']['id']][$module['Module']['id']]['_delete'])){
						$genderValue[$item['User']['id']][$module['Module']['id']]['_delete'] = $gender2[$item['User']['id']][$module['Module']['id']]['_delete'];
					}else{
						$genderValue[$item['User']['id']][$module['Module']['id']]['_delete'] = -1;
					}
					if(isset($gender2[$item['User']['id']][$module['Module']['id']]['_create'])){
						$genderValue[$item['User']['id']][$module['Module']['id']]['_create'] = $gender2[$item['User']['id']][$module['Module']['id']]['_create'];
					}else{
						$genderValue[$item['User']['id']][$module['Module']['id']]['_create'] = -1;
					}
				}
			}
		}

		$this->set(compact('users','modules','configs','genderValue','gender1'));
	}
	public function edit(){
		$response = array(
			'success' => 0,
			'_read' => -1,
            '_update' => -1,
            '_delete' => -1,
            '_create' => -1
		);
		if ($this->request->is('post')){
			$perData = $this->request->data;
			$this->Permission->deleteAll(array(
				'Permission.module_id' => $perData['module_id'],
				'Permission.aro_id' => $perData['aro_id'],
				'Permission.type' => $perData['type']
			));
			if($perData['action'] == 'set'){
				unset($perData['action']);
				if($this->Permission->save($perData)){
					$response['success'] = 1;
				} 
			}elseif($perData['action'] == 'reset'){
				
				$response['success'] = 1;
				$resetData = $this->User->find('first', array(
					'conditions' => array('User.id' => $perData['aro_id']),
					'fields' => array('GroupPermission.*'),
					'joins' => array(
						array(
							'table' => 'users_groups',
							'alias' => 'UsersGroup',
							'type' => 'LEFT',
							'conditions' => array('UsersGroup.user_id = User.id')
						),
						array(
							'table' => 'module_permissions',
							'alias' => 'GroupPermission', 
							'type' => 'LEFT', 
							'conditions' => array('GroupPermission.type' => 'group', 'GroupPermission.aro_id = UsersGroup.group_id', 'GroupPermission.module_id' => $perData['module_id'])
						)
					)
				));
				if(!empty($resetData)){
					$response['_read'] = $resetData['GroupPermission']['_read'];
					$response['_update'] = $resetData['GroupPermission']['_update'];
					$response['_delete'] = $resetData['GroupPermission']['_delete'];
					$response['_create'] = $resetData['GroupPermission']['_create'];
				}
			}
		}
		print_r(json_encode($response));die;
	}
}