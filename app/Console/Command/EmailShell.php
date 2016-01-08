<?php
App::uses('CakeEmail', 'Network/Email');
class EmailShell extends AppShell {
	public $uses = array('User', 'Accounting.Quotation');
	var $tasks = array('Project',
				'DbConfig',
				'Model',
				'Controller',
				'View',
				'Plugin'
				);
	var $QuotationsController = null;
		
	function initialize()
	{
		$this->_loadModels();
	}
    public function main() {
		// $city = $this->User->find('list', array('fields' => 'id, name', 'conditions' => array('permissionable' => false), 'order' => array('User.name ASC')));
		App::import('Controller', 'Accounting.Quotations');
		$this->out('aaaa');
		$this->QuotationsController = new QuotationsController();
		$this->QuotationsController->sendFollowUp();
    }
}