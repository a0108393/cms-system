<?php
App::uses('CakeEmail', 'Network/Email');
class CronShell extends AppShell {
    var $uses = array('User');
	public $tasks = array('FollowUp', 'FirstExpiry', 'SecondExpiry', 'OnExpiry', 'UserNoActive');
    public function main() {
		switch($this->args[0]) {
			// case 'followup':
				// $this->FollowUp->execute();
				// break;
			// case 'firstexpiry':
				// $this->FirstExpiry->execute();
				// break;
			// case 'secondexpiry':
				// $this->SecondExpiry->execute();
				// break;
			// case 'onexpiry':
				// $this->OnExpiry->execute();
				// break;
			// case 'usernoactive':
				// $this->UserNoActive->execute();
			case 'daily':
				$this->FollowUp->execute();
				$this->FirstExpiry->execute();
				$this->SecondExpiry->execute();
				$this->OnExpiry->execute();
				$this->UserNoActive->execute();
				break;
			default:
				$this->FollowUp->execute();
				break;
		}
    }
}