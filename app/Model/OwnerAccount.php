<?php 
class OwnerAccount extends AppModel{
	public $tablePrefix = 'setting_';
	public $name = 'OwnerAccount';
	
	var $validate = array(
        'company_name' => array(
            'rule' => 'notEmpty'
        ),
		'address' => array(
            'rule' => 'notEmpty'
        ),
		'email' => array(
            'duplicate' => array(
				'rule' => array('notDuplicate'),
				'message' => 'This Email is already taken'
			)
        )
    );
	
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
}