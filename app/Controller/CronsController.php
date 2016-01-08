<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
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
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class CronsController extends AppController {
	
	public $uses = array('User', 'Accounting.Quotation', 'Accounting.QuotationDetail');
	
	/**
	 * @Description : cronjobs Follow-up System
	 *
	 * @return 	: html
	 * @Author 	: tungpa - tungbk29@gmail.com
	 */
	public function sendFollowUp()
	{
		$this->autoRender=false;
		$quotations = $this->Quotation->find('all', array(
			'fields' => 'Quotation.*, Company.name, QuotationDetail.*,Service.name',
			'conditions' => array(
				'next_follow_up' => date('Y-m-d'),
				'history_status' => 1,
				'send_second_expiry' => 0,
				'status <' => 2,
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
		$saleStaff = $this->User->find('list', array(
			'fields' => 'User.id, User.email',
			'conditions' => array(
				'User.group_id' => Configure::read('Settings.Company.SalesStaffGroupId')
			)
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
				$day = floor((time() - strtotime($item['date']))/86400);
				$content = '<p><a href="'. Router::url(array('plugin' => 'accounting', 'controller' => 'quotations', 'action' => 'view', $id), true) .'">Estimate '. $item['estimate_number'] .'</a></p>';
				$content .= '<p>Date of Issue: '. $day .'</p>';
				$content .= '<p>'. (((int) $item['validity'] - $day) > 0) ? ((int) $item['validity'] - $day). ' days till' : 'No expiry' .'</p>';
				$content .= '<p>Client: <a href="'. Router::url(array('plugin' => 'accounting', 'controller' => 'quotations', 'action' => 'viewClient', $item['client_id']), true) .'">'. $item['company_name'] .'</a></p>';
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
	/**
	 * @Description : cronjobs Send first cost estimate expiry notification email to client
	 *
	 * @return 	: html
	 * @Author 	: tungpa - tungbk29@gmail.com
	 */
	 
	public function sendFirstExpiry()
	{
		$this->autoRender=false;
		$this->Quotation->virtualFields['TotalDays'] = 'validity - FLOOR((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(Quotation.date))/86400)';
		$dayNotice = Configure::read('Settings.Accounting.cost_estimate_expiry_notice');
		$quotations = $this->Quotation->find('all', array(
			'fields' => 'Quotation.*, ( validity - FLOOR((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(Quotation.date))/86400) ) as Quotation_TotalDays',
			'conditions' => array(
				'history_status' => 1,
				'send_first_expiry' => 0,
				'status <' => 2,
			),
			'group' => 'Quotation.id HAVING Quotation_TotalDays = ' . $dayNotice
		));
		
		if(!empty($quotations)) {
			foreach($quotations as $k => $item) {
				$clientUser = $this->User->find('list', array(
					'fields' => 'User.id, User.email',
					'conditions' => array(
						'User.company_id' => $item['Quotation']['client_id'],
						'User.group_id' => Configure::read('Settings.Company.DefaultGroupId'),
						'User.active' => 1
					)
				));
				print_r($clientUser);die;
				if(!empty($clientUser)) {
					$content = $dayNotice .' days';
					$arr_options = array(
						'to' => $clientUser,
						'subject' => sprintf(__('Send first cost estimate expiry notification email to client %s days before validity reaches expiry'), $dayNotice),
						'viewVars' => array('content' => $content)
					);
					if($this->_sendemail($arr_options)) {
						$data['Quotation']['id'] = $item['Quotation']['id'];
						$data['Quotation']['send_first_expiry'] = 1;
						$this->Quotation->save($data);
					}
				}
			}
		}
	}
	/**
	 * @Description : cronjobs Send second cost estimate expiry notification email to client
	 *
	 * @return 	: html
	 * @Author 	: tungpa - tungbk29@gmail.com
	 */
	 
	public function sendSecondExpiry()
	{
		$this->autoRender=false;
		$this->Quotation->virtualFields['TotalDays'] = 'validity - FLOOR((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(Quotation.date))/86400)';
		$dayNotice = 1;
		$quotations = $this->Quotation->find('all', array(
			'fields' => 'Quotation.*, ( validity - FLOOR((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(Quotation.date))/86400) ) as Quotation_TotalDays',
			'conditions' => array(
				'history_status' => 1,
				'send_second_expiry' => 0,
				'status <' => 2,
			),
			'group' => 'Quotation.id HAVING Quotation_TotalDays = ' . $dayNotice
		));
		if(!empty($quotations)) {
			foreach($quotations as $k => $item) {
				$clientUser = $this->User->find('list', array(
					'fields' => 'User.id, User.email',
					'conditions' => array(
						'User.company_id' => $item['Quotation']['client_id'],
						'User.group_id' => Configure::read('Settings.Company.DefaultGroupId'),
						'User.active' => 1
					)
				));
				if(!empty($clientUser)) {
					$content = $dayNotice .' days';
					$arr_options = array(
						'to' => $clientUser,
						'subject' => __('Send second expiry notification email to client 1 day before validity reaches expiry'),
						'viewVars' => array('content' => $content)
					);
					if($this->_sendemail($arr_options)) {
						$data['Quotation']['id'] = $item['Quotation']['id'];
						$data['Quotation']['send_second_expiry'] = 1;
						$this->Quotation->save($data);
					}
				}
			}
		}
	}
	/**
	 * @Description : cronjobs Send Expired notification email to client on expiry
	 *
	 * @return 	: html
	 * @Author 	: tungpa - tungbk29@gmail.com
	 */
	 
	public function sendOnExpiry()
	{
		$this->autoRender=false;
		$this->Quotation->virtualFields['TotalDays'] = 'validity - FLOOR((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(Quotation.date))/86400)';
		$dayNotice = 0;
		$quotations = $this->Quotation->find('all', array(
			'fields' => 'Quotation.*, ( validity - FLOOR((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(Quotation.date))/86400) ) as Quotation_TotalDays',
			'conditions' => array(
				'history_status' => 1,
				'status <' => 2
			),
			'group' => 'Quotation.id HAVING Quotation_TotalDays <= ' . $dayNotice
		));
		if(!empty($quotations)) {
			foreach($quotations as $k => $item) {
				$clientUser = $this->User->find('list', array(
					'fields' => 'User.id, User.email',
					'conditions' => array(
						'User.company_id' => $item['Quotation']['client_id'],
						'User.group_id' => Configure::read('Settings.Company.DefaultGroupId'),
						'User.active' => 1
					)
				));
				if(!empty($clientUser)) {
					$content = 'on expiry';
					$arr_options = array(
						'to' => $clientUser,
						'subject' => __('Send Expired notification email to client on expiry'),
						'viewVars' => array('content' => $content)
					);
					if($this->Quotation->sendEmail($arr_options)) {
						$item['Quotation']['status'] = 6;
						$this->Quotation->save($item);
					}
				}
			}
		}
	}
	
}