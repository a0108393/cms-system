<?php
class FirstExpiryTask extends Shell {
    public $uses = array('User', 'Accounting.Quotation', 'Accounting.QuotationDetail', 'Settings');
    public function execute() {
		//load setting
		$settings = $this->Settings->find('all');
		$dayNotice = 0;
		$clientGroupId = 0;
		if(!empty($settings)) {
			foreach($settings as $k => $item) {
				if($item['Settings']['param_name'] == 'cost_estimate_expiry_notice')
					$dayNotice = $item['Settings']['param_value'];
				if($item['Settings']['param_name'] == 'DefaultGroupId')
					$clientGroupId = $item['Settings']['param_value'];
			}
		}
		$this->Quotation->virtualFields['TotalDays'] = 'validity - FLOOR((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(Quotation.date))/86400)';
		$quotations = $this->Quotation->find('all', array(
			'fields' => 'Quotation.*, ( validity - FLOOR((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(Quotation.date))/86400) ) as Quotation_TotalDays',
			'conditions' => array(
				'Quotation.history_status' => 1,
				'send_first_expiry' => 0,
				'status <' => 5,
			),
			'group' => 'Quotation.id HAVING Quotation_TotalDays = ' . $dayNotice
		));

		if(!empty($quotations)) {
			foreach($quotations as $k => $item) {
				$clientUser = $this->User->find('list', array(
					'fields' => 'User.id, User.email',
					'conditions' => array(
						'User.company_id' => $item['Quotation']['client_id'],
						'User.group_id' => $clientGroupId,
						'User.active' => 1
					),
					'permissionable' => false
				));
				// print_r($clientUser);die;
				if(!empty($clientUser)) {
					$content = '<a href="'. FULL_PATH_DOMAIN . Router::url(array('plugin' => 'accounting', 'controller' => 'quotations', 'action' => 'view', $item['Quotation']['id'])) .'">' .$item['Quotation']['estimate_number'] . '</a><br />';
					$content .= $dayNotice .' days';
					$arr_options = array(
						'to' => $clientUser,
						'subject' => sprintf(__('Send first cost estimate expiry notification email to client %s days before validity reaches expiry'), $dayNotice),
						'viewVars' => array('content' => $content)
					);
					if($this->_sendEmail($arr_options)) {
						$data['Quotation']['id'] = $item['Quotation']['id'];
						$data['Quotation']['send_first_expiry'] = 1;
						$this->Quotation->save($data);
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