<?php
class ReportsController extends AppController {
	var $name = 'Reports';
	var $layout = 'report';
	public $uses = array('Marketing.AdvertisingLink','Marketing.SaveReport','Marketing.LinkVisit','Marketing.Event');

	//report chart links
	function links()
	{
		//find SaveReport
		$data['SaveReport'] = $this->SaveReport->find('all', array(
			'conditions' => array(
				'user_id' => $this->Session->read('Auth.User.id'),
				'type' => 0
			)
		));
		if(!empty($this->request->query)) {
			if(!empty($this->request->query['save_filter'])) {
				// print_r($this->request->query);die;
				$_uri = $_SERVER['REQUEST_URI'];
				$n = strpos($_SERVER['REQUEST_URI'], '&title') - strlen($_SERVER['REQUEST_URI']);
				$_uri = substr($_uri, 0, $n);
				$url = "http" . (($_SERVER['SERVER_PORT']==443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_uri;
				$data['SaveReport']['url'] = $url;
				$data['SaveReport']['type'] = 0;
				$data['SaveReport']['created'] = gmdate('Y-m-d h:i:s');
				$data['SaveReport']['title'] = $this->request->query['title'];
				$data['SaveReport']['type_report'] = $this->request->query['type_report'];
				switch ($this->request->query['type_report']) {
					case 'day_view':
						$data['SaveReport']['start_date'] = sqlFormatDate($this->request->query['start_date']);
						$data['SaveReport']['end_date'] = sqlFormatDate($this->request->query['end_date']);
						break;
					case 'day_of_week':
						$data['SaveReport']['start_date'] = sqlFormatDate($this->request->query['day_of_week']);
						break;
					default:
						$data['SaveReport']['start_date'] = sqlFormatDate($this->request->query['time_of_day']);
						break;
				}
				$this->SaveReport->save($data);
				$this->redirect($url);
			}
			
			if($this->request->query['type_report'] == 'day_view') {
				if( (strtotime(sqlFormatDate($this->request->query['end_date'])) - strtotime(sqlFormatDate($this->request->query['start_date']))) < 0) {
				
				}
				else {
					$data['title'] = __('Link Report Chart - Day view');
					// echo $day;
					$list_links = $this->AdvertisingLink->find('all', array(
						'joins' => array(
							array(
								'table' => 'marketing_link_visits',
								'alias' => 'Visit',
								'type' => 'LEFT',
								'conditions' => array(
									'Visit.marketing_advertising_links_id = AdvertisingLink.id'
								)
							)
						),
						'conditions' => array(
							'Visit.time_click >' => sqlFormatDate($this->request->query['start_date']),
							'Visit.time_click <' => sqlFormatDate($this->request->query['end_date']),
							'AdvertisingLink.history_status' => 1
						),
						'permissionable' => false,
						'fields' => array('AdvertisingLink.id', 'AdvertisingLink.description, DAY(Visit.time_click) day, MONTH(Visit.time_click) month, YEAR(Visit.time_click) year, count(Visit.id) as click'),
						'group' => 'DAY(Visit.time_click),AdvertisingLink.id',
						'order' => 'AdvertisingLink.id ASC,Visit.time_click ASC'
					));
					$start_date = strtotime(sqlFormatDate($this->request->query['start_date']));
					$end_date = strtotime(sqlFormatDate($this->request->query['end_date']));
					//rebuild data
					$arrItem = array();
					$arrCate = array();
					print_r($list_links);
					foreach($list_links as $k => $item){
						$arrItem[$item['AdvertisingLink']['id']]['title'] = $item['AdvertisingLink']['description'];
						$n = '';
						for($i = $start_date; $i <= $end_date; $i += 86400) {
							// echo $i.'<br />';
							$_day = date('j', $i);
							$_month = date('n', $i);
							if($k == 0)
								$arrCate[] = "'". date('D', $i). " (". formatDate(date('Y-m-d', $i)) .")" . "'";
							if(!isset($arrItem[$item['AdvertisingLink']['id']]['day'][$_day . '-' . $_month]))
								$arrItem[$item['AdvertisingLink']['id']]['day'][$_day . '-' . $_month] = 0;
							if($item[0]['day'] == $_day && $item[0]['month'] == $_month){
								$arrItem[$item['AdvertisingLink']['id']]['day'][$_day . '-' . $_month] = $item[0]['click'];
							}
						}
					}
					$series = '';
					foreach($arrItem as $k => $item) {
						$series .= "{name: '". $item['title'] ."', data: [". implode(',',array_values($item['day'])) ."]},";
					}
					//find event
					
					$events = $this->Event->find('all',array(
						'conditions' => array(
							'AND' => array(
								'OR' => array(
									array(
										'AND' => array(
											array('Event.start_date >=' => sqlFormatDate($this->request->query['start_date'])),
											array('Event.start_date <=' => sqlFormatDate($this->request->query['end_date']))
									)),
									array(
										'AND' => array(
											array('Event.end_date >=' => sqlFormatDate($this->request->query['start_date'])),
											array('Event.end_date <=' => sqlFormatDate($this->request->query['end_date']))
									)),
									array(
										'AND' => array(
											array('Event.start_date <=' => sqlFormatDate($this->request->query['start_date'])),
											array('Event.end_date >=' => sqlFormatDate($this->request->query['end_date']))
									)),
									array(
										'AND' => array(
											array('Event.start_date >=' => sqlFormatDate($this->request->query['start_date'])),
											array('Event.end_date <=' => sqlFormatDate($this->request->query['end_date']))
									))
								),
								'Event.history_status' => 1
							)
						),
						'permissionable' => false,
						'order' => 'Event.start_date ASC'
					));
					$data['events'] = $events;
					$series = substr($series, 0, -1);
					$data['series'] = $series;
					$data['category'] = implode(',', array_values($arrCate));
					$data['count_series'] = count($arrCate);
					$data['start_date'] = $start_date;
					$data['end_date'] = $end_date;
				}
			}
			else if($this->request->query['type_report'] == 'day_of_week') {
				$data['title'] = __('Link Report Chart - Day of Week view');
				$first_day_of_week = strtotime('monday this week', strtotime(sqlFormatDate($this->request->query['day_of_week'])));
				$last_day_of_week = strtotime('sunday this week', strtotime(sqlFormatDate($this->request->query['day_of_week'])));
				if( Configure::read('Settings.Formats.start_week') == 0) {
					$first_day_of_week = strtotime('sunday last week', strtotime(sqlFormatDate($this->request->query['day_of_week'])));
					$last_day_of_week = strtotime('saturday this week', strtotime(sqlFormatDate($this->request->query['day_of_week'])));
				}
				//find list link
				$list_links = $this->AdvertisingLink->find('all', array(
					'joins' => array(
						array(
							'table' => 'marketing_link_visits',
							'alias' => 'Visit',
							'type' => 'LEFT',
							'conditions' => array(
								'Visit.marketing_advertising_links_id = AdvertisingLink.id'
							)
						)
					),
					'conditions' => array(
						'Visit.time_click >' => date('Y-m-d', $first_day_of_week),
						'Visit.time_click <' => date('Y-m-d', $last_day_of_week),
						'AdvertisingLink.history_status' => 1
					),
					'permissionable' => false,
					'fields' => array('AdvertisingLink.id', 'AdvertisingLink.description, DAY(Visit.time_click) day, MONTH(Visit.time_click) month, YEAR(Visit.time_click) year, count(Visit.id) as click'),
					'group' => 'DAY(Visit.time_click),AdvertisingLink.id',
					'order' => 'AdvertisingLink.id ASC,Visit.time_click ASC'
				));
				//rebuild data
				$arrItem = array();
				$arrCate = array();
				foreach($list_links as $k => $item){
					$arrItem[$item['AdvertisingLink']['id']]['title'] = $item['AdvertisingLink']['description'];
					$n = '';
					for($i = $first_day_of_week; $i <= $last_day_of_week; $i += 86400) {
						$_day = date('j', $i);
						$_month = date('n', $i);
						if($k == 0)
							$arrCate[] = "'". date('D', $i). " (". formatDate(date('Y-m-d', $i)) .")" . "'";
						if(!isset($arrItem[$item['AdvertisingLink']['id']]['day'][$_day . '-' . $_month]))
							$arrItem[$item['AdvertisingLink']['id']]['day'][$_day . '-' . $_month] = 0;
						if($item[0]['day'] == $_day && $item[0]['month'] == $_month){
							$arrItem[$item['AdvertisingLink']['id']]['day'][$_day . '-' . $_month] = $item[0]['click'];
						}
					}
				}
				$series = '';
				foreach($arrItem as $k => $item) {
					$series .= "{name: '". $item['title'] ."', data: [". implode(',',array_values($item['day'])) ."]},";
				}
				$series = substr($series, 0, -1);
				
				//find event
				$events = $this->Event->find('all',array(
					'conditions' => array(
						'AND' => array(
							'OR' => array(
								array(
									'AND' => array(
										array('Event.start_date >=' => date('Y-m-d', $first_day_of_week)),
										array('Event.start_date <=' => date('Y-m-d', $last_day_of_week))
								)),
								array(
									'AND' => array(
										array('Event.end_date >=' => date('Y-m-d', $first_day_of_week)),
										array('Event.end_date <=' => date('Y-m-d', $last_day_of_week))
								)),
								array(
									'AND' => array(
										array('Event.start_date <=' => date('Y-m-d', $first_day_of_week)),
										array('Event.end_date >=' => date('Y-m-d', $last_day_of_week))
								)),
								array(
									'AND' => array(
										array('Event.start_date >=' => date('Y-m-d', $first_day_of_week)),
										array('Event.end_date <=' => date('Y-m-d', $last_day_of_week))
								))
							),
							'Event.history_status' => 1
						)
					),
					'permissionable' => false,
					'order' => 'Event.start_date ASC'
				));
				$data['events'] = $events;
				$data['series'] = $series;
				$data['category'] = implode(',', array_values($arrCate));
				$data['count_series'] = count($arrCate);
				$data['start_date'] = $first_day_of_week;
				$data['end_date'] = $last_day_of_week;
			}
			else {
				$data['title'] = __('Link Report Chart - Time of Day view');
				//find list link
				$list_links = $this->AdvertisingLink->find('all', array(
					'joins' => array(
						array(
							'table' => 'marketing_link_visits',
							'alias' => 'Visit',
							'type' => 'LEFT',
							'conditions' => array(
								'Visit.marketing_advertising_links_id = AdvertisingLink.id',
								'AdvertisingLink.history_status' => 1
							)
						)
					),
					'conditions' => array(
						'DATE_FORMAT(Visit.time_click,\'%Y-%m-%d\')' => sqlFormatDate($this->request->query['time_of_day'])
					),
					'permissionable' => false,
					'fields' => array('AdvertisingLink.id', 'AdvertisingLink.description, HOUR(Visit.time_click) hour, DAY(Visit.time_click) day, MONTH(Visit.time_click) month, YEAR(Visit.time_click) year, count(Visit.id) as click'),
					'group' => 'HOUR(Visit.time_click),AdvertisingLink.id',
					'order' => 'AdvertisingLink.id ASC,Visit.time_click ASC'
				));
				//rebuild data
				$arrItem = array();
				$arrCate = array();
				foreach($list_links as $k => $item){
					$arrItem[$item['AdvertisingLink']['id']]['title'] = $item['AdvertisingLink']['description'];
					$n = '';
					for($i = 0; $i <= 23; $i++) {
						$arrCate[] = $i;
						if(!isset($arrItem[$item['AdvertisingLink']['id']]['hour'][$i]))
							$arrItem[$item['AdvertisingLink']['id']]['hour'][$i] = 0;
						if($item[0]['hour'] == $i){
							$arrItem[$item['AdvertisingLink']['id']]['hour'][$i] = $item[0]['click'];
						}
					}
				}
				$series = '';
				foreach($arrItem as $k => $item) {
					$series .= "{name: '". $item['title'] ."', data: [". implode(',',array_values($item['hour'])) ."]},";
				}
				
				$series = substr($series, 0, -1);
				$data['series'] = $series;
				$data['category'] = implode(',', array_values($arrCate));
				$data['count_series'] = count($arrCate);
				$events = $this->Event->find('all',array(
					'conditions' => array(
						'AND' => array(
							'Event.start_date <=' => sqlFormatDate($this->request->query['time_of_day']),
							'Event.end_date >=' => sqlFormatDate($this->request->query['time_of_day']),
							'Event.history_status' => 1
						)
					),
					'order' => 'Event.start_date ASC'
				));
				$data['events'] = $events;
				$data['start_date'] = strtotime(sqlFormatDate($this->request->query['time_of_day']));
				$data['end_date'] = strtotime(sqlFormatDate($this->request->query['time_of_day']));
			}

		}
		$this->set('data', $data);
		$this->render('index');
	}
	//report chart enquiries
	function enquiries()
	{
		//find SaveReport
		$data['SaveReport'] = $this->SaveReport->find('all', array(
			'conditions' => array(
				'user_id' => $this->Session->read('Auth.User.id'),
				'type' => 1
			)
		));
		if(!empty($this->request->query)) {
			//save filter
			if(!empty($this->request->query['save_filter'])) {
				// print_r($this->request->query);die;
				$_uri = $_SERVER['REQUEST_URI'];
				$n = strpos($_SERVER['REQUEST_URI'], '&title') - strlen($_SERVER['REQUEST_URI']);
				$_uri = substr($_uri, 0, $n);
				$url = "http" . (($_SERVER['SERVER_PORT']==443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_uri;
				$data['SaveReport']['url'] = $url;
				$data['SaveReport']['type'] = 1;
				$data['SaveReport']['created'] = gmdate('Y-m-d h:i:s');
				$data['SaveReport']['title'] = $this->request->query['title'];
				$data['SaveReport']['type_report'] = $this->request->query['type_report'];
				switch ($this->request->query['type_report']) {
					case 'day_view':
						$data['SaveReport']['start_date'] = sqlFormatDate($this->request->query['start_date']);
						$data['SaveReport']['end_date'] = sqlFormatDate($this->request->query['end_date']);
						break;
					case 'day_of_week':
						$data['SaveReport']['start_date'] = sqlFormatDate($this->request->query['day_of_week']);
						break;
					default:
						$data['SaveReport']['start_date'] = sqlFormatDate($this->request->query['time_of_day']);
						break;
				}
				$this->SaveReport->save($data);
				$this->redirect($url);
			}
			if($this->request->query['type_report'] == 'day_view') {
				if( (strtotime(sqlFormatDate($this->request->query['end_date'])) - strtotime(sqlFormatDate($this->request->query['start_date']))) < 0) {
				
				}
				else {
					$data['title'] = __('Enquiry Report Chart - Day view');
					// echo $day;
					$list_links = $this->AdvertisingLink->find('all', array(
						'joins' => array(
							array(
								'table' => 'marketing_enquiries',
								'alias' => 'Enquiry',
								'type' => 'LEFT',
								'conditions' => array(
									'Enquiry.marketing_advertising_links_id = AdvertisingLink.id',
									'AdvertisingLink.history_status' => 1
								)
							)
						),
						'conditions' => array(
							'Enquiry.enquiry_time >' => sqlFormatDate($this->request->query['start_date']),
							'Enquiry.enquiry_time <' => sqlFormatDate($this->request->query['end_date'])
						),
						'fields' => array('AdvertisingLink.id', 'AdvertisingLink.description, DAY(Enquiry.enquiry_time) day, MONTH(Enquiry.enquiry_time) month, YEAR(Enquiry.enquiry_time) year, count(Enquiry.id) as click'),
						'group' => 'DAY(Enquiry.enquiry_time),AdvertisingLink.id',
						'order' => 'AdvertisingLink.id ASC,Enquiry.enquiry_time ASC'
					));
					$start_date = strtotime(sqlFormatDate($this->request->query['start_date']));
					$end_date = strtotime(sqlFormatDate($this->request->query['end_date']));
					//rebuild data
					$arrItem = array();
					$arrCate = array();
					foreach($list_links as $k => $item){
						$arrItem[$item['AdvertisingLink']['id']]['title'] = $item['AdvertisingLink']['description'];
						$n = '';
						for($i = $start_date; $i <= $end_date; $i += 86400) {
							// echo $i.'<br />';
							$_day = date('j', $i);
							$_month = date('n', $i);
							if($k == 0)
								$arrCate[] = "'". date('D', $i). " (". formatDate(date('Y-m-d', $i)) .")" . "'";
							if(!isset($arrItem[$item['AdvertisingLink']['id']]['day'][$_day . '-' . $_month]))
								$arrItem[$item['AdvertisingLink']['id']]['day'][$_day . '-' . $_month] = 0;
							if($item[0]['day'] == $_day && $item[0]['month'] == $_month){
								$arrItem[$item['AdvertisingLink']['id']]['day'][$_day . '-' . $_month] = $item[0]['click'];
							}
						}
					}
					$series = '';
					foreach($arrItem as $k => $item) {
						$series .= "{name: '". $item['title'] ."', data: [". implode(',',array_values($item['day'])) ."]},";
					}
					//find event
					$events = $this->Event->find('all',array(
						'conditions' => array(
							'AND' => array(
								'OR' => array(
									array(
										'AND' => array(
											array('Event.start_date >=' => sqlFormatDate($this->request->query['start_date'])),
											array('Event.start_date <=' => sqlFormatDate($this->request->query['end_date']))
									)),
									array(
										'AND' => array(
											array('Event.end_date >=' => sqlFormatDate($this->request->query['start_date'])),
											array('Event.end_date <=' => sqlFormatDate($this->request->query['end_date']))
									)),
									array(
										'AND' => array(
											array('Event.start_date <=' => sqlFormatDate($this->request->query['start_date'])),
											array('Event.end_date >=' => sqlFormatDate($this->request->query['end_date']))
									)),
									array(
										'AND' => array(
											array('Event.start_date >=' => sqlFormatDate($this->request->query['start_date'])),
											array('Event.end_date <=' => sqlFormatDate($this->request->query['end_date']))
									))
								),
								'Event.history_status' => 1
							)
						),
						'permissionable' => false,
						'order' => 'Event.start_date ASC'
					));
					$data['events'] = $events;
					$series = substr($series, 0, -1);
					$data['series'] = $series;
					$data['category'] = implode(',', array_values($arrCate));
					$data['count_series'] = count($arrCate);
					$data['start_date'] = $start_date;
					$data['end_date'] = $end_date;
				}
			}
			else if($this->request->query['type_report'] == 'day_of_week') {
				$data['title'] = __('Enquiry Report Chart - Day of Week view');
				$first_day_of_week = strtotime('monday this week', strtotime(sqlFormatDate($this->request->query['day_of_week'])));
				$last_day_of_week = strtotime('sunday this week', strtotime(sqlFormatDate($this->request->query['day_of_week'])));
				if( Configure::read('Settings.Formats.start_week') == 0) {
					$first_day_of_week = strtotime('sunday last week', strtotime(sqlFormatDate($this->request->query['day_of_week'])));
					$last_day_of_week = strtotime('saturday this week', strtotime(sqlFormatDate($this->request->query['day_of_week'])));
				}
				//find list link
				$list_links = $this->AdvertisingLink->find('all', array(
					'joins' => array(
						array(
							'table' => 'marketing_enquiries',
							'alias' => 'Enquiry',
							'type' => 'LEFT',
							'conditions' => array(
								'Enquiry.marketing_advertising_links_id = AdvertisingLink.id',
								'AdvertisingLink.history_status' => 1
							)
						)
					),
					'conditions' => array(
						'Enquiry.enquiry_time >' => date('Y-m-d', $first_day_of_week),
						'Enquiry.enquiry_time <' => date('Y-m-d', $last_day_of_week)
					),
					'fields' => array('AdvertisingLink.id', 'AdvertisingLink.description, DAY(Enquiry.enquiry_time) day, MONTH(Enquiry.enquiry_time) month, YEAR(Enquiry.enquiry_time) year, count(Enquiry.id) as click'),
					'group' => 'DAY(Enquiry.enquiry_time),AdvertisingLink.id',
					'order' => 'AdvertisingLink.id ASC,Enquiry.enquiry_time ASC'
				));
				//rebuild data
				$arrItem = array();
				$arrCate = array();
				foreach($list_links as $k => $item){
					$arrItem[$item['AdvertisingLink']['id']]['title'] = $item['AdvertisingLink']['description'];
					$n = '';
					for($i = $first_day_of_week; $i <= $last_day_of_week; $i += 86400) {
						$_day = date('j', $i);
						$_month = date('n', $i);
						if($k == 0)
							$arrCate[] = "'". date('D', $i). " (". formatDate(date('Y-m-d', $i)) .")" . "'";
						if(!isset($arrItem[$item['AdvertisingLink']['id']]['day'][$_day . '-' . $_month]))
							$arrItem[$item['AdvertisingLink']['id']]['day'][$_day . '-' . $_month] = 0;
						if($item[0]['day'] == $_day && $item[0]['month'] == $_month){
							$arrItem[$item['AdvertisingLink']['id']]['day'][$_day . '-' . $_month] = $item[0]['click'];
						}
					}
				}
				$series = '';
				foreach($arrItem as $k => $item) {
					$series .= "{name: '". $item['title'] ."', data: [". implode(',',array_values($item['day'])) ."]},";
				}
				$series = substr($series, 0, -1);
				
				//find event
				$events = $this->Event->find('all',array(
					'conditions' => array(
						'AND' => array(
							'OR' => array(
								array(
									'AND' => array(
										array('Event.start_date >=' => date('Y-m-d', $first_day_of_week)),
										array('Event.start_date <=' => date('Y-m-d', $last_day_of_week))
								)),
								array(
									'AND' => array(
										array('Event.end_date >=' => date('Y-m-d', $first_day_of_week)),
										array('Event.end_date <=' => date('Y-m-d', $last_day_of_week))
								)),
								array(
									'AND' => array(
										array('Event.start_date <=' => date('Y-m-d', $first_day_of_week)),
										array('Event.end_date >=' => date('Y-m-d', $last_day_of_week))
								)),
								array(
									'AND' => array(
										array('Event.start_date >=' => date('Y-m-d', $first_day_of_week)),
										array('Event.end_date <=' => date('Y-m-d', $last_day_of_week))
								))
							),
							'Event.history_status' => 1
						)
					),
					'permissionable' => false,
					'order' => 'Event.start_date ASC'
				));
				$data['events'] = $events;
				$data['series'] = $series;
				$data['category'] = implode(',', array_values($arrCate));
				$data['count_series'] = count($arrCate);
				$data['start_date'] = $first_day_of_week;
				$data['end_date'] = $last_day_of_week;
			}
			else {
				$data['title'] = __('Enquiry Report Chart - Time of Day view');
				//find list link
				$list_links = $this->AdvertisingLink->find('all', array(
					'joins' => array(
						array(
							'table' => 'marketing_enquiries',
							'alias' => 'Enquiry',
							'type' => 'LEFT',
							'conditions' => array(
								'Enquiry.marketing_advertising_links_id = AdvertisingLink.id',
								'AdvertisingLink.history_status' => 1
							)
						)
					),
					'conditions' => array(
						'DATE_FORMAT(Enquiry.enquiry_time,\'%Y-%m-%d\')' => sqlFormatDate($this->request->query['time_of_day'])
					),
					'fields' => array('AdvertisingLink.id', 'AdvertisingLink.description, HOUR(Enquiry.enquiry_time) hour, DAY(Enquiry.enquiry_time) day, MONTH(Enquiry.enquiry_time) month, YEAR(Enquiry.enquiry_time) year, count(Enquiry.id) as click'),
					'group' => 'HOUR(Enquiry.enquiry_time),AdvertisingLink.id',
					'order' => 'AdvertisingLink.id ASC,Enquiry.enquiry_time ASC'
				));
				//rebuild data
				$arrItem = array();
				$arrCate = array();
				foreach($list_links as $k => $item){
					$arrItem[$item['AdvertisingLink']['id']]['title'] = $item['AdvertisingLink']['description'];
					$n = '';
					for($i = 0; $i <= 23; $i++) {
						$arrCate[] = $i;
						if(!isset($arrItem[$item['AdvertisingLink']['id']]['hour'][$i]))
							$arrItem[$item['AdvertisingLink']['id']]['hour'][$i] = 0;
						if($item[0]['hour'] == $i){
							$arrItem[$item['AdvertisingLink']['id']]['hour'][$i] = $item[0]['click'];
						}
					}
				}
				$series = '';
				foreach($arrItem as $k => $item) {
					$series .= "{name: '". $item['title'] ."', data: [". implode(',',array_values($item['hour'])) ."]},";
				}
				
				$series = substr($series, 0, -1);
				$data['series'] = $series;
				$data['category'] = implode(',', array_values($arrCate));
				$data['count_series'] = count($arrCate);
				$events = $this->Event->find('all',array(
					'conditions' => array(
						'AND' => array(
							array(
								array('Event.start_date <=' => sqlFormatDate($this->request->query['time_of_day'])
							)),
							array(
								array('Event.end_date >=' => sqlFormatDate($this->request->query['time_of_day'])
							))
						)
					),
					'permissionable' => false,
					'order' => 'Event.start_date ASC'
				));
				$data['events'] = $events;
				$data['start_date'] = strtotime(sqlFormatDate($this->request->query['time_of_day']));
				$data['end_date'] = strtotime(sqlFormatDate($this->request->query['time_of_day']));
			}
			
		}
		$this->set('data', $data);
	}
	
	
	function chart($type = 'default')
	{
		$max_value = 0;
		//No filter
		if(empty($this->request->query)){
			//Build data (link, visit) for chart
			$curr_year = date("Y",time());
			$list_links = $this->AdvertisingLink->find('all', array(
				'joins' => array(
					array(
						'table' => 'marketing_link_visits',
						'alias' => 'Visit',
						'type' => 'LEFT',
						'conditions' => array(
							'Visit.marketing_advertising_links_id = AdvertisingLink.id'
						)
					)
				),
				'conditions' => array(
					'date_format(Visit.time_click,\'%Y\')' => $curr_year
				),
				'fields' => array('AdvertisingLink.id','AdvertisingLink.description,MONTH(Visit.time_click) month,count(Visit.id) as click'),
				'group' => 'MONTH(Visit.time_click),AdvertisingLink.id',
				'order' => 'AdvertisingLink.id ASC,Visit.time_click ASC'
			));
			
			$arrItem = array();
			foreach($list_links as $k => $item){
				$arrItem[$item['AdvertisingLink']['id']]['title'] = $item['AdvertisingLink']['description'];
				for($i = 1; $i <= 12; $i++){
					if(!isset($arrItem[$item['AdvertisingLink']['id']]['month'][$i]))
						$arrItem[$item['AdvertisingLink']['id']]['month'][$i] = 0;
					if($item[0]['month'] == $i){
						$arrItem[$item['AdvertisingLink']['id']]['month'][$i] = $item[0]['click'];
					}
				}
			}
			$category = "'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'";
			$series = '';
			foreach($arrItem as $k => $item) {
				$series .= "{name: '". $item['title'] ."', data: [". implode(',',array_values($item['month'])) ."]},";
			}
			
			$series = substr($series, 0, -1);
			$count_series = 12;
			//get event in current year
			$events = $this->Event->find('all',array(
				'conditions' => array(
					'OR' => array(
						'date_format(Event.start_date,\'%Y\')' => $curr_year,
						'date_format(Event.end_date,\'%Y\')' => $curr_year
					)
				),
				'order' => 'Event.start_date ASC'
			));
			$data['events'] = $events;
			$data['curr_year'] = $curr_year;
			$data['category'] = $category;
			$data['series'] = $series;
			$data['count_series'] = $count_series;
			$this->set('data', $data);
		}
		else{
			$day_from = $this->request->query['select_year']. "-".sprintf("%02s", $this->request->query['month_from'])."-01 00:00:00";
			$day_to = $this->request->query['select_year']. "-".sprintf("%02s", $this->request->query['month_to'])."-".cal_days_in_month(CAL_GREGORIAN, $this->request->query['month_to'], $this->request->query['select_year'])." 23:59:59";
			$curr_year = $this->request->query['select_year'];
			$list_links = $this->AdvertisingLink->find('all', array(
				'joins' => array(
					array(
						'table' => 'marketing_link_visits',
						'alias' => 'Visit',
						'type' => 'LEFT',
						'conditions' => array(
							'Visit.marketing_advertising_links_id = AdvertisingLink.id'
						)
					)
				),
				'conditions' => array(
					'date_format(Visit.time_click,\'%Y\')' => $curr_year,
					'Visit.time_click >' => $day_from,
					'Visit.time_click <' => $day_to
				),
				'fields' => array('AdvertisingLink.id','AdvertisingLink.description,MONTH(Visit.time_click) month,count(Visit.id) as click'),
				'group' => 'MONTH(Visit.time_click),AdvertisingLink.id',
				'order' => 'AdvertisingLink.id ASC,Visit.time_click ASC'
			));
			$arr_month = Configure::read('marketing.list_month');
			$category = "";
			for($i = $this->request->query['month_from']; $i <= (int)$this->request->query['month_to']; $i++){
				if($i == (int)$this->request->query['month_to']){
					$category .= "'" . $arr_month[$i] . "'";
				}
				else {
					$category .= "'" . $arr_month[$i] . "'" . ", ";
				}
			}
			$arrItem = array();
			foreach($list_links as $k => $item){
				$arrItem[$item['AdvertisingLink']['id']]['title'] = $item['AdvertisingLink']['description'];
				for($i = $this->request->query['month_from']; $i <= $this->request->query['month_to']; $i++){
					if(!isset($arrItem[$item['AdvertisingLink']['id']]['month'][$i]))
						$arrItem[$item['AdvertisingLink']['id']]['month'][$i] = 0;
					if($item[0]['month'] == $i){
						$arrItem[$item['AdvertisingLink']['id']]['month'][$i] = $item[0]['click'];
					}
				}
			}
			$series = '';
			foreach($arrItem as $k => $item) {
				$series .= "{name: '". $item['title'] ."', data: [". implode(',',array_values($item['month'])) ."]},";
			}
			
			$series = substr($series, 0, -1);
			$count_series = ($this->request->query['month_to'] - $this->request->query['month_from'] + 1);
			//get event in current year
			$events = $this->Event->find('all',array(
				'conditions' => array(
					'OR' => array(
						array(
							'AND' => array(
								array('Event.start_date >' => $day_from),
								array('Event.start_date <' => $day_to)
						)),
						array(
							'AND' => array(
								array('Event.end_date >' => $day_from),
								array('Event.end_date <' => $day_to)
						))
					)
				),
				'order' => 'Event.start_date ASC'
			));
			$data['events'] = $events;
			$data['curr_year'] = $curr_year;
			$data['category'] = $category;
			$data['series'] = $series;
			$data['count_series'] = $count_series;
			$this->set('data', $data);
		}

		
	}
	
	//delete link save
	function delete($id)
	{
		// $_uri = $_SERVER['REQUEST_URI'];
		// $n = strpos($_SERVER['REQUEST_URI'], 'save_filter') - strlen($_SERVER['REQUEST_URI']);
		// $_uri = substr($_uri, 0, $n);
		// $url = "http" . (($_SERVER['SERVER_PORT']==443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_uri;
		// echo $_SERVER['REQUEST_URI'];die;
		$data = $this->SaveReport->read(null, $id);
        if (!$this->SaveReport->exists()) {
            throw new NotFoundException(__('Invalid links'));
        }
        if ($this->SaveReport->delete()){
			if($data['SaveReport']['type'] == 0)
				return $this->redirect(array('action' => 'links'));
			else
				return $this->redirect(array('action' => 'enquiries'));
        }
        $this->Session->setFlash(__('Product/Service was not deleted'));
        return $this->redirect(array('action' => 'index'));
	}
	
}