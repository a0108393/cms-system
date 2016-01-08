<?php

class ModulesController extends AppController{
	public function index(){
		$plugin_names = CakePlugin::loaded();
		$plugin_names[] = 'Core';

		$db_plugins = $this->Module->find('all');
		
		$existing_plugin = array();
		foreach($db_plugins as $item){
			$existing_plugin[] = $item['Module']['name'];
		}
		
		foreach($plugin_names as $plugin){
			if(!in_array($plugin, $existing_plugin)){
				$db_plugins[]['Module'] = array(
					'id' => 0,
					'name' => $plugin,
					'default_owner_bit' => 0,
                    'default_group_bit' => 0,
                    'default_public_bit' => 0
				);
			}
		}
		$render_data = array();
		foreach($db_plugins as $key=>$mod){
			if($mod['Module']['id']){
				$render_data[$key][] = $mod['Module']['name'] . '<input type="hidden" name="rows['. $key .'][name]" value="' . $mod['Module']['name'] . '"/><input type="hidden" name="rows['. $key .'][id]" value="' . $mod['Module']['id'] . '" />';
			}else{
				$render_data[$key][] = $mod['Module']['name'] . '<input type="hidden" name="rows['. $key .'][name]" value="' . $mod['Module']['name'] . '"/>';
			}
			$render_data[$key][] = '<label class="checkbox '. $this->_checkBit(OWNER_READ_BIT, $mod['Module']['default_owner_bit']) .'"><input data-toggle="checkbox" name="rows['. $key .'][default_owner_bit][]" type="checkbox" value="' . OWNER_READ_BIT . '" '. $this->_checkBit(OWNER_READ_BIT, $mod['Module']['default_owner_bit']) .'/></label>';
			$render_data[$key][] = '<label class="checkbox '. $this->_checkBit(OWNER_WRITE_BIT, $mod['Module']['default_owner_bit']) .'"><input data-toggle="checkbox" name="rows['. $key .'][default_owner_bit][]" type="checkbox" value="' . OWNER_WRITE_BIT . '" '. $this->_checkBit(OWNER_WRITE_BIT, $mod['Module']['default_owner_bit']) .'/></label>';
			$render_data[$key][] = '<label class="checkbox '. $this->_checkBit(OWNER_DELETE_BIT, $mod['Module']['default_owner_bit']) .'"><input data-toggle="checkbox" name="rows['. $key .'][default_owner_bit][]" type="checkbox" value="' . OWNER_DELETE_BIT . '" '. $this->_checkBit(OWNER_DELETE_BIT, $mod['Module']['default_owner_bit']) .'/></label>';
			$render_data[$key][] = '<label class="checkbox '. $this->_checkBit(GROUP_READ_BIT, $mod['Module']['default_group_bit']) .'"><input data-toggle="checkbox" name="rows['. $key .'][default_group_bit][]" type="checkbox" value="' . GROUP_READ_BIT . '" '. $this->_checkBit(GROUP_READ_BIT, $mod['Module']['default_group_bit']) .'/></label>';
			$render_data[$key][] = '<label class="checkbox '. $this->_checkBit(GROUP_WRITE_BIT, $mod['Module']['default_group_bit']) .'"><input data-toggle="checkbox" name="rows['. $key .'][default_group_bit][]" type="checkbox" value="' . GROUP_WRITE_BIT . '" '. $this->_checkBit(GROUP_WRITE_BIT, $mod['Module']['default_group_bit']) .'/></label>';
			$render_data[$key][] = '<label class="checkbox '. $this->_checkBit(GROUP_DELETE_BIT, $mod['Module']['default_group_bit']) .'"><input data-toggle="checkbox" name="rows['. $key .'][default_group_bit][]" type="checkbox" value="' . GROUP_DELETE_BIT . '" '. $this->_checkBit(GROUP_DELETE_BIT, $mod['Module']['default_group_bit']) .'/></label>';
			$render_data[$key][] = '<label class="checkbox '. $this->_checkBit(OTHER_READ_BIT, $mod['Module']['default_public_bit']) .'"><input data-toggle="checkbox" name="rows['. $key .'][default_public_bit][]" type="checkbox" value="' . OTHER_READ_BIT . '" '. $this->_checkBit(OTHER_READ_BIT, $mod['Module']['default_public_bit']) .'/></label>';
			$render_data[$key][] = '<label class="checkbox '. $this->_checkBit(OTHER_WRITE_BIT, $mod['Module']['default_public_bit']) .'"><input data-toggle="checkbox" name="rows['. $key .'][default_public_bit][]" type="checkbox" value="' . OTHER_WRITE_BIT . '" '. $this->_checkBit(OTHER_WRITE_BIT, $mod['Module']['default_public_bit']) .'/></label>';
			$render_data[$key][] = '<label class="checkbox '. $this->_checkBit(OTHER_DELETE_BIT, $mod['Module']['default_public_bit']) .'"><input data-toggle="checkbox" name="rows['. $key .'][default_public_bit][]" type="checkbox" value="' . OTHER_DELETE_BIT . '" '. $this->_checkBit(OTHER_DELETE_BIT, $mod['Module']['default_public_bit']) .'/></label>';
		}
		
		if($this->request->is('post')){
			$data = $this->request->data['rows'];
			foreach($data as $key=>$item){
				if(isset($item['default_owner_bit'])){
					$data[$key]['default_owner_bit'] = array_sum($item['default_owner_bit']);
				}else{
					$data[$key]['default_owner_bit'] = 0;
				}
				if(isset($item['default_group_bit'])){
					$data[$key]['default_group_bit'] = array_sum($item['default_group_bit']);
				}else{
					$data[$key]['default_group_bit'] = 0;
				}
				if(isset($item['default_public_bit'])){
					$data[$key]['default_public_bit'] = array_sum($item['default_public_bit']);
				}else{
					$data[$key]['default_public_bit'] = 0;
				}
			}
			
			$this->Module->saveMany($data);
			$this->Session->setFlash(__('All setting has been saved'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('data', $render_data);
	}
	
	
	function _checkBit($myBit, $defineBit){
		if(($defineBit & $myBit) <> 0 ){
			return 'checked';
		}
		return '';
	}
}
