<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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

App::uses('Controller', 'Controller');
App::uses('CakeEmail', 'Network/Email');
/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	public $components = array(
		'Permissionable.Permissionable',
		'UploadAjax',
		'PhpExcel',
        'Session',
        'Common',
		'DebugKit.Toolbar',
        'Auth' => array(
			'authenticate' => array(
				'Form' => array(
					'fields' => array('username' => 'email')
				)
			)
        )
    );
	public $helpers = array(
			'Html' => array('className' => 'Permissionable.MyHtml'), 
			'Form' => array('className' => 'BootstrapForm'),
			'Paginator' => array('className' => 'BootstrapPaginator'),
			'Session',
			'Captcha'
		);
	
	public function __construct($request = null, $response = null) {
		$page_title = Router::getParam('plugin');
		
		if (empty($page_title)) {
			$page_title = Router::getParam('controller');
		}
		
		$this->set('page_title',   __(Inflector::humanize($page_title)));
		$this->set('meta_title',  Configure::read('App.default_meta_title_prefix') . __(Inflector::humanize(Router::getParam('controller'))) . Configure::read('App.default_meta_title_surfix'));
		$this->set('meta_keywords',  Configure::read('App.default_meta_keywords'));
		$this->set('meta_description',  Configure::read('App.default_meta_description'));
		$this->set('plugin',  Router::getParam('plugin'));
		$this->set('controller',  Router::getParam('controller'));
		$this->set('action',  Router::getParam('action'));
		
		
		return parent::__construct($request, $response);
	}
	
	public function beforeFilter() {
		//check isset user logged update info
		if ($this->Auth->loggedIn()) {
			App::import('Model', 'User');
			$user = new User();
			$this->Session->write('Auth', $user->findById($this->Auth->User('id')));
		}
		
		//load setting
		$this->loadModel('Settings');
		$settings = $this->Settings->find('all');
		$configs = array();
		foreach($settings as $key=>$row){
			$configs[$row['Settings']['category']][$row['Settings']['param_name']] = $row['Settings']['param_value'];
		}
		Configure::write('Settings', $configs);
		
		//set limit for paginate
		if(isset($this->request->query['limit'])){
			$this->paginate['limit'] = $this->request->query['limit'];
		}
		//setting the timezone for all dates using TimeHelper
		if($this->Session->read('Auth.User.timezone')){
			Configure::write('Config.timezone', $this->Session->read('Auth.User.timezone'));
		}
		if($this->name == 'Users'){
			$this->Auth->allow('forgot','captcha');
		}
		if($this->name == 'Enquiries')
			$this->Auth->allow('add_client');
		if($this->name == 'AdvertisingLinks')
			$this->Auth->allow('go');
		if($this->name == 'Briefs')
			$this->Auth->allow('visitor');
		$this->Auth->loginAction = Configure::read('Core.LoginAction');
		$this->Auth->loginRedirect = Configure::read('Core.LoginRedirect');
		$this->Auth->logoutRedirect = Configure::read('Core.LogoutRedirect');
		// prd($this->name);
		if(!$this->Permissionable->checkModuleAccess($this) && $this->name != 'ToolbarAccess') {
			die('denny');
		}
	}
	
	public function afterFilter() {
		if(isset(Router::getRequest()->query['Export'])){
			if(Router::getRequest()->query['type'] == 0){
				$export = Router::getRequest()->query['Export'];
				if($export['model'] && !empty($export['fields'])){
					$data = $this->paginate($export['model']);
					if(!empty($data)){
						$csv = "";
						foreach($export['fields'] as $key=>$title){
							$csv .= $title.",";
						}
						$csv .= "\n";
						foreach($data as $item){
							foreach($export['fields'] as $key=>$title){
								$csv .= $item[$export['model']][$key] . ",";
							}
							$csv .= "\n";
						}
						header("Content-type: text/csv");
						header("Content-disposition: csv; filename=" . date("Y-m-d__h-i-s__") . $export['model'] . ".csv; size=".strlen($csv));
						print $csv;
						exit();
					}
				}
			}
			else {
				$export = Router::getRequest()->query['Export'];
				if($export['model'] && !empty($export['fields'])){
					$data = $this->paginate($export['model']);
					$this->PhpExcel->createWorksheet(); 
					$this->PhpExcel->setDefaultFont('Calibri', 12);
					$table = array();
					foreach($export['fields'] as $key=>$title){
						$table[] = array('label' => $title);
					}
					// heading 
					$this->PhpExcel->addTableHeader($table, array('name' => 'Cambria', 'bold' => true)); 
					foreach($data as $item){
						$arr = array();
						foreach($export['fields'] as $key=>$title){
							$arr[] = $item[$export['model']][$key];
						}
						$this->PhpExcel->addTableRow($arr);
					}
					$this->PhpExcel->addTableFooter(); 
					$this->PhpExcel->output(date("Y-m-d__h-i-s__") . $export['model'] . ".xlsx"); 
				}
			}
		}
	}
}
