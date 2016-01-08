<?php
class FollowUpTask extends Shell {
    public $uses = array('User', 'Accounting.Quotation', 'Accounting.QuotationNotify', 'Settings');
    public function execute() {
		$quotations = $this->Quotation->find('all', array(
			'fields' => 'Quotation.*, Company.name, QuotationDetail.*,Service.name',
			'conditions' => array(
				'next_follow_up' => date('Y-m-d'),
				'Quotation.history_status' => 1,
				'send_first_expiry' => 0,
				'status <' => 5,
			),
			'joins' => array(
				array(
					'table' => 'company_companies',
					'alias' => 'Company',
					'type' => 'LEFT',
					'conditions' => array('Quotation.client_id = Company.id')
				),
				array(
					'table' => 'accounting_quotation_details',
					'alias' => 'QuotationDetail',
					'type' => 'LEFT',
					'conditions' => array('Quotation.id = QuotationDetail.quotation_id')
				),
				array(
					'table' => 'accounting_services',
					'alias' => 'Service',
					'type' => 'LEFT',
					'conditions' => array('Service.id = QuotationDetail.product_service_id')
				)
			),
			'group' => 'QuotationDetail.id',
			'order' => 'Quotation.id asc'
		));
		//load setting
		$settings = $this->Settings->find('all');
		$saleGroupId = 0;
		if(!empty($settings)) {
			foreach($settings as $k => $item) {
				if($item['Settings']['param_name'] == 'SalesStaffGroupId')
					$saleGroupId = $item['Settings']['param_value'];
			}
		}
		$saleStaff = $this->User->find('list', array(
			'fields' => 'User.id, User.email',
			'conditions' => array(
				'User.group_id' => $saleGroupId
			),
			'permissionable' => false
		));

		$arrQuotation = array();
		if(!empty($quotations)) {
			$id = 0;
			$i = 0;
			foreach($quotations as $k => $item) {
				if($item['Quotation']['id'] == $id) {
					$arrQuotation[$i]['child'][] = $item['QuotationDetail'];
					$arrQuotation[$i]['child'][$k]['service_name'] = $item['Service']['name'];
				}
				else {
					$i++;
					$id = $item['Quotation']['id'];
					$arrQuotation[$i] = $item['Quotation'];
					$arrQuotation[$i]['company_name'] = $item['Company']['name'];
					$arrQuotation[$i]['child'][$k] = $item['QuotationDetail'];
					$arrQuotation[$i]['child'][$k]['service_name'] = $item['Service']['name'];
					
				}
			}
		}
		
		if(!empty($saleStaff) && !empty($arrQuotation)) {
			foreach($arrQuotation as $k => $item) {
				//get quotation notify
				$quotation_notify = $this->QuotationNotify->find('all', array(
					'fields' => 'QuotationNotify.*',
					'conditions' => array(
						'quotation_id' => $item['id']
					)
				));
				$day = floor((time() - strtotime($item['date']))/86400);
				$content = '<p><a href="'. FULL_PATH_DOMAIN . Router::url(array('plugin' => 'accounting', 'controller' => 'quotations', 'action' => 'view', $id), false) .'">Estimate '. $item['estimate_number'] .'</a></p>';
				$content .= '<p>Date of Issue: '. $day .'</p>';
				$content .= '<p>'. (((int) $item['validity'] - $day) > 0) ? ((int) $item['validity'] - $day). ' days till' : 'No expiry' .'</p>';
				$content .= '<p>Client: <a href="'. FULL_PATH_DOMAIN . Router::url(array('plugin' => 'accounting', 'controller' => 'quotations', 'action' => 'viewClient', $item['client_id']), false) .'">'. $item['company_name'] .'</a></p>';
				if(!empty($quotation_notify)) {
					$content .= '<p>List of contacts estimate has been sent to</p>';
					foreach($quotation_notify as $m => $vl) {
						$content .= '<p>'. $vl['QuotationNotify']['user_name'] . ' - ' . $vl['QuotationNotify']['user_contact'] . '</p>';
					}
				}
				$content .= '<p>Subject: '. $item['subject'] .'</p>';
				$content .= '<table><tr><td>No.</td><td>Product/Service</td><td>Qty</td><td>Price</td></tr>';
				$m = 0;
				foreach($item['child'] as $n => $vl) {
					$content .= '<tr><td>'. $m .'</td>';
					$content .= '<td>'. $vl['service_name'] .'</td>';
					$content .= '<td>'. $vl['quantity'] .'</td>';
					$content .= '<td>'. $vl['price'] .'</td></tr>';
					$m++;
				}
				$content .= '</table>';
				$arr_options = array(
					'to' => $saleStaff,
					'subject' => sprintf(__('Cost Estimate %s Follow-up for %s'),$item['estimate_number'], $item['company_name']),
					'viewVars' => array('content' => $content)
				);
				
				if($this->Quotation->sendEmail($arr_options)) {
					
					$next_day = date( 'Y-m-d',strtotime($item['next_follow_up'] ." + ". $item['follow_up_days'] ." days"));
					$data['Quotation']['id'] = $item['id'];
					$data['Quotation']['next_follow_up'] = $next_day;
					$this->Quotation->save($data);
				}
			}
		}
    }
}