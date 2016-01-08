<?php
App::uses('CakeEmail', 'Network/Email');
class Quotation extends AppModel {
	
	public $tablePrefix = 'accounting_';
	public $hasMany = array(
        'QuotationDetail' => array(
            'className' => 'QuotationDetail',
            'foreignKey' => 'quotation_id',
            'order' => 'QuotationDetail.id ASC',
            'dependent' => true
        )
    );
	public $validate = array(
		'estimate_number' => array(
            'rule'    => array('notEmpty'),
			'message' => 'Estimate number field is require'
        )
	);
	public function sendEmail($arr = array())
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
	/*public function getAllQuotation($id = 0, $condition = array())
	{
		$base_condition = array('status >' => 2);
		if($id > 0) 
			$base_condition['Quotation.id'] = $id;
		$conditions = array_merge($condition, $base_condition);
		$quotations = $this->find('all', array(
			'fields' => 'Quotation.*, Company.name, QuotationDetail.*,Service.name,Ratecard.name',
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'company_companies',
					'alias' => 'Company',
					'type' => 'LEFT',
					'conditions' => array('Quotation.client_id = Company.id')
				),
				array(
					'table' => 'accounting_ratecards',
					'alias' => 'Ratecard',
					'type' => 'LEFT',
					'conditions' => array('Quotation.ratecard_id = Ratecard.id')
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

		$arrQuotation = array();
		if(!empty($quotations)) {
			$id = 0;
			$i = -1;
			foreach($quotations as $k => $item) {
				if($item['Quotation']['id'] == $id) {
					$arrQuotation[$i]['child'][] = $item['QuotationDetail'];
					$arrQuotation[$i]['child'][$k]['service_name'] = $item['Service']['name'];
					$arrQuotation[$i]['child'][$k]['ratecard_name'] = $item['Ratecard']['name'];
				}
				else {
					$i++;
					$id = $item['Quotation']['id'];
					$arrQuotation[$i] = $item['Quotation'];
					$arrQuotation[$i]['company_name'] = $item['Company']['name'];
					$arrQuotation[$i]['ratecard_name'] = $item['Ratecard']['name'];
					$arrQuotation[$i]['child'][$k] = $item['QuotationDetail'];
					$arrQuotation[$i]['child'][$k]['service_name'] = $item['Service']['name'];
					
				}
			}
		}
		return $arrQuotation;
	}*/
}