<?php

class PermissionsController extends AppController{
	
	public $uses = array('Permissionable.Permission', 'Permissionable.Module', 'Group');

	public function index(){
	
		$db_plugins = $this->Module->find('all');
		$groups = $this->Group->find('all');
		$db_permissions = $this->Permission->find('all', array(
			'conditions' => array('Permission.type' => 'group')
		));
		
		//prepare permission data
		$permissions = array();
		foreach($db_permissions as $db_per){
			$permissions[$db_per['Permission']['module_id']][$db_per['Permission']['aro_id']] = array(
				'per_id' => $db_per['Permission']['id'],
				'type' => $db_per['Permission']['type'],
				'_read' => $db_per['Permission']['_read'],
				'_update' => $db_per['Permission']['_update'],
				'_delete' => $db_per['Permission']['_delete'],
				'_create' => $db_per['Permission']['_create']
			);
		}
		$per_config = array('_read' => __('Read'), 
							'_update' => __('Update'),
							'_delete' => __('Delete'),
							'_create' => __('Create')
							);
		
		
		
		//defind table data
		$data = array();
		$data['header'][] = __('Module');
		foreach($groups as $group){
			$data['header'][] = $group['Group']['name'];
		}
		
		foreach($db_plugins as $key=>$module){
		
			$mod = $module['Module'];
			$data['rows'][$key]['name'] = $mod['name'];
			$data['rows'][$key]['module_id'] = $mod['id'];
			
			foreach($per_config as $k=>$title){
			
				$data['rows'][$key]['config_key'][$k]['title'] = $title;
				
				foreach($groups as $group){
				
					$aro = $group['Group'];
					//$data['rows'][$key]['config_key'][$k]['aro_id'] = $aro['id'];
					
					if( isset( $permissions[$mod['id']][$aro['id']] ) ){
						$data['rows'][$key]['config_key'][$k]['groups'][$aro['id']]['per_id'] = $permissions[$mod['id']][$aro['id']]['per_id'];
						$data['rows'][$key]['config_key'][$k]['groups'][$aro['id']]['value'] = $permissions[$mod['id']][$aro['id']][$k];
					}else{
						$data['rows'][$key]['config_key'][$k]['groups'][$aro['id']]['value'] = -1;
					}
				}
			}
		}
		
		
		$this->set('data', $data);
		
		//save request data
		if($this->request->is('post')){
			$request = $this->request->data;
			$datasave = array();
			
			foreach($request as $mod_key=>$module){
				foreach($per_config as $con_key=>$name){
					foreach($module[$con_key] as $group){
					
						if(isset($group['per_id'])){
							$datasave[$module['module_id']][$group['aro_id']]['id'] = $group['per_id'];
						}
						$datasave[$module['module_id']][$group['aro_id']]['module_id'] = $module['module_id'];
						$datasave[$module['module_id']][$group['aro_id']]['aro_id'] = $group['aro_id'];
						$datasave[$module['module_id']][$group['aro_id']]['type'] = 'group';
						$datasave[$module['module_id']][$group['aro_id']][$con_key] = $group['value'];
						
					}
				}
			}
			
			$complete_data = array();
			foreach($datasave as $items){
				foreach($items as $item){
					$complete_data[] = $item;
				}
			}
			$this->Permission->saveMany($complete_data);
			$this->Session->setFlash(__('All setting has been saved'));
			return $this->redirect(array('action' => 'index'));
		}
	}
}
