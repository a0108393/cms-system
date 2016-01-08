<?php

App::uses('Component', 'Controller'); 

class CommonComponent extends Component {
	
	var $components = array('Session');
	
	public function flashErrorDisplay($errors = array(), $mainmsg = 'We found error while processing your entries'){
		$return_msg = "<b>" . $mainmsg . "</b><br/><ul>";
		foreach($errors as $key => $value){
			if(is_array($value)){
				$arr = array_unique($value);
				foreach($arr as $msg){
					$return_msg .= "<li>". $msg ."</li>";
				}
			}else{
				$return_msg .= "<li>". $value ."</li>";
			}
			
		}
		$return_msg .= "</ul>";
		$this->Session->setFlash($return_msg,'default', array('class' => 'alert alert-danger'));
	}
}