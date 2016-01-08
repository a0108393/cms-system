<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
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
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
	public function getFieldProperties($fieldName) {
		if (isset($this->fieldProperties[$fieldName])) {
			return $this->fieldProperties[$fieldName];
		}
	}
	public function beforeFind($queryData) {
		
		if(isset(Router::getRequest()->query['Search'])){
			$search = Router::getRequest()->query['Search'];
			if(isset($search['Table']) && $search['Table'] == $this->alias){
				unset($search['Table']);
				foreach($search as $key => $val_parent){
					foreach($val_parent as $key_child => $val){
						if(is_array($val) && !empty($val)){
							if(isset($val['from']) && $val['from'] != ''){
								$queryData['conditions'][$key.'.'.$key_child.'  >=  '] = $val['from'];
							}
							if(isset($val['from_date']) && $val['from_date'] != ''){
								$queryData['conditions'][$key.'.'.$key_child.'  >=  '] = sqlFormatDate($val['from_date']);
							}
							if(isset($val['to']) && $val['to'] != ''){
								$queryData['conditions'][$key.'.'.$key_child.'  <=  '] = $val['to'];
							}
							if(isset($val['to_date']) && $val['to_date'] != ''){
								$queryData['conditions'][$key.'.'.$key_child.'  <=  '] = sqlFormatDate($val['to_date']);
							}
						}
						else{
							if($val != ''){
								if ($key_child == 'id') {
									$queryData['conditions'][$key.'.'.$key_child.' = '] = $val;
								
								} else {
									$queryData['conditions'][$key.'.'.$key_child.' LIKE '] = '%'. $val . '%';
								}
							}
						}
					}
				}
				//print_r($queryData);die;
				return $queryData;
			}
		}
	}
	
	//validate duplicate
	public function notDuplicate($field){
		$key = array_keys($field);
		$value = array_values($field);
		if($this->id){
			$lastdata = $this->findById($this->id);
			if($lastdata[$this->alias][$key[0]] == $value[0]){
				return true;
			}
		}
		$existing = $this->find('count',array('conditions'=> array($this->alias . '.' . $key[0] => $value[0])));
		if($existing){
			return false;
		}
		return true;
	}
	
	//validate captcha
	var $captcha = ''; 

	function matchCaptcha($inputValue){
		return $inputValue['captcha'] == $this->getCaptcha();
	}

	public function setCaptcha($value){
		$this->captcha = $value;
	}
	
	function getCaptcha(){
		return $this->captcha;
	}
}
