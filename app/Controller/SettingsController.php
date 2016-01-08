<?php
App::uses('AppController', 'Controller');

class SettingsController extends AppController{
	
	public $uses = array('Setting','Currency','Group', 'Marketing.Channel', 'Accounting.Ratecard');
	
	function beforeRender()
    {
        parent::beforeRender();
		$dateformats = array(
			'd M Y' => 'DD MMM YYYY',
			'd/m/Y' => 'DD/MM/YYYY',
			'd-m-Y' => 'DD-MM-YYYY',
			'd.m.Y' => 'DD.MM.YYYY',
			'm/d/Y' => 'MM/DD/YYYY',
			'm-d-Y' => 'MM-DD-YYYY',
			'm.d.Y' => 'MM.DD.YYYY',
			'Y/m/d' => 'YYYY/MM/DD',
			'Y-m-d' => 'YYYY-MM-DD',
			'Y.m.d' => 'YYYY.MM.DD'
		);
		$timeformats = array(
			'g:i:s A' => '12-hour',
			'H:i:s' => '24-hour'
		);
		
		$startWeek = array(
			0 => 'Sunday',
			1 => 'Monday',
			2 => '‎Tuesday',
			3 => '‎Wednesday',
			4 => 'Thursday',
			5 => '‎Friday',
			6 => '‎Saturday'
		);
		$numberFormats = array(
			1 => '1,234.00',
			2 => '1 234.00',
			3 => '1.234,00',
			4 => '1 234,00'
		);
		$currencyDisplayFormats = array(
			1 => '$1,234.00 USD',
			2 => '$1,234.00'
		);
		$sandboxtest = array(
			'https://www.sandbox.paypal.com/cgi-bin/webscr' => __('Yes'),
			'https://www.paypal.com/cgi-bin/webscr' => __('No')
		);
		
		$this->set(compact('dateformats', 'timeformats', 'startWeek', 'numberFormats', 'currencyDisplayFormats', 'sandboxtest'));
	}
	public function formats(){
		$this->set('currencies', $this->Currency->find('list', array('fields' => 'id, code')));
		$this->_globalSave('Formats');
	}
	public function accounting(){
		$this->_globalSave('Accounting');
	}
	public function company(){
		$this->set('groups', $this->Group->find('list', array('fields' => 'id, name')));
		$this->set('channels', $this->Channel->find('list', array('fields' => 'id, name', 'conditions' => array('Channel.history_status' => 1), 'permissionable' => false)));
		$this->set('ratecard', $this->Ratecard->find('list', array('fields' => 'id, name', 'conditions' => array('Ratecard.history_status' => 1))));
		$this->_globalSave('Company');
	}
	
	function _globalSave($a){
		$olddata = $this->Setting->find('all', array('conditions' => array('Setting.category' => $a)));
		
		//prepare data render
		$old_data = array();
		foreach($olddata as $old){
			$old_data['Setting'][$old['Setting']['param_name']] = $old['Setting']['param_value'];
		}
		
		if($this->request->is('post') || $this->request->is('put')){
			
			$newdata = $this->request->data['Setting'];
			
			$db_save = array();
			foreach($newdata as $key=>$value){
				$db_save[] = array('param_name' => $key, 'param_value' => $value, 'category' => $a);
			}
			foreach($olddata as $db_data){
				foreach($db_save as $key=>$row){
					if($db_data['Setting']['param_name'] == $row['param_name']){
						$db_save[$key]['id'] = $db_data['Setting']['id'];
					}
				}
			}
			
			$this->Setting->saveMany($db_save);
			$this->Session->setFlash(__('All setting has been saved'));
			return $this->redirect(array('action' => strtolower($a)));
		}else{
            $this->request->data = $old_data;
		}
	}
}