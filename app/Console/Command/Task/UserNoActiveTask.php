<?php
App::uses('ConnectionManager', 'Model'); 
App::uses('CakeEmail', 'Network/Email');
class UserNoActiveTask extends Shell {
    public $uses = array('User');
    public function execute() {
		$this->User->virtualFields['TotalDaysLogin'] = 'FLOOR((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(User.last_login))/86400)';
		$this->User->virtualFields['TotalDaysJoin'] = 'FLOOR((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(User.last_login))/86400)';
		$users = $this->User->find('all', array(
			'fields' => 'User.*, FLOOR((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(User.last_login))/86400) as User_TotalDaysLogin, FLOOR((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(User.last_login))/86400) as User_TotalDaysJoin',
			'conditions' => array(
				'User.active' => 1,
			),
			'permissionable' => false,
			'group' => 'User.id HAVING (User_TotalDaysLogin >= 90 and User_TotalDaysJoin >= 90)'
		));
		if(!empty($users)) {
			$db = ConnectionManager::getDataSource('default');
			foreach($users as $k => $user) {
				$content = 'User has not logged in for more than 90 days.';
				$arr_options = array(
					'to' => $user['User']['email'],
					'subject' => __('User has not logged in for more than 90 days'),
					'viewVars' => array('content' => $content)
				);
				if($this->_sendEmail($arr_options)) {
					$user['User']['active'] = 0;
					if (!$db->isConnected()) {
					   echo __('Could not connect to database.');
					} else {
						$db->rawQuery('UPDATE users SET active=0 where id='.$user['User']['id']);
					}
				}
			}
		}
	}
	private function _sendEmail($arr = array())
	{
		$sender = (Configure::read('Settings.Accounting.accounting_email') == '') ? 'testing@onlinebizsoft.com' : Configure::read('Settings.Accounting.accounting_email');
		$email_options = array(
			'sender' => array($sender => PAGE_NAME),
			'from' => array($sender => PAGE_NAME),
			'to' => array($sender),
			'subject' => __('Ratecard change information'),
			'viewVars' => array('content' => ''),
			'template' => 'default',
			'layout' => 'default'
		);
		
		$options = array_merge($email_options, $arr);
		$email = new CakeEmail($options);
		$email->emailFormat('html');
		return $email->send();
	}
}