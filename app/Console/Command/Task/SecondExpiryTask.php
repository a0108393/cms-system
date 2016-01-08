<?php
class SecondExpiryTask extends Shell {
    public $uses = array('User', 'Accounting.Quotation', 'Accounting.QuotationDetail', 'Settings');
    public function execute() {
		//load setting
		$settings = $this->Settings->find('first', array('conditions' => array('param_name' => 'DefaultGroupId')));
		$clientGroupId = 0;
		if(!empty($settings)) {
			$clientGroupId = $settings['Settings']['param_value'];
		}
		$this->Quotation->virtualFields['TotalDays'] = 'validity - FLOOR((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(Quotation.date))/86400)';
		$dayNotice = 1;
		$quotations = $this->Quotation->find('all', array(
			'fields' => 'Quotation.*, ( validity - FLOOR((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(Quotation.date))/86400) ) as Quotation_TotalDays',
			'conditions' => array(
				'Quotation.history_status' => 1,
				'send_first_expiry' => 1,
				'send_second_expiry' => 0,
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
						'User.active' => 1,
						
					),
					'permissionable' => false
				));

				if(!empty($clientUser)) {
					$content = '<a href="'. FULL_PATH_DOMAIN . Router::url(array('plugin' => 'accounting', 'controller' => 'quotations', 'action' => 'view', $item['Quotation']['id'])) .'">' .$item['Quotation']['estimate_number'] . '</a><br />';
					$content .= $dayNotice .' days';
					$arr_options = array(
						'to' => $clientUser,
						'subject' => __('Send second expiry notification email to client 1 day before validity reaches expiry'),
						'viewVars' => array('content' => $content)
					);
					if($this->Quotation->sendemail($arr_options)) {
						$data['Quotation']['id'] = $item['Quotation']['id'];
						$data['Quotation']['send_second_expiry'] = 1;
						$this->Quotation->save($data);
					}
				}
			}
		}
	}
}