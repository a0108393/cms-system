<?php

App::uses('Component', 'Controller');
App::uses('File', 'Utility');

class UploadAjaxComponent extends Component {
	
	
	public function initialize(Controller $controller){
		$this->controller = $controller;
		
		if(isset($this->controller->request->data['ajax_files_uploaded'])){
			$files = $this->controller->request->data['ajax_files_uploaded'];
			foreach($files as $filename){
				$file = new File(UPLOAD_TEMP . DS . $filename);
				if($file->exists()){
					$file->copy(UPLOAD_PATH . DS . $filename);
					$file->delete();
				}
			}
		}
		if(isset($this->controller->request->data['ax-type']) && $this->controller->request->data['ax-type'] == 'import'){
			
			$file_name	= ( isset($_REQUEST['ax-file-name']) && !empty($_REQUEST['ax-file-name']) )?$_REQUEST['ax-file-name']:'';
			$full_size	= isset($_REQUEST['ax-file-size'])?$_REQUEST['ax-file-size']:0;
			$response = array(
				'name'=>$file_name,
				'size'=>$full_size,
				'status'=> 1, 
				'info'=>'Import successfull!'
			);
			$src = $_FILES['ax_file_input']['tmp_name'];
			$rows = array();
			$keys = array();
			$file = fopen($src, "r");
			$key = 0;
			while (($data = fgetcsv($file, 1000, ",")) !== FALSE)
			{
				if($key == 0){
					$keys = $data;
				}else{
					$rows[$key - 1] = $data;
				}
				$key++;
			}
			
			fclose($file);
			
			if(empty($rows) || empty($keys)){
				$response['status'] = -1;
				$response['info'] = 'CSV row empty!';
				die(json_encode($response));
			}
			
			$data = array();
			foreach($rows as $key=>$row){
				$data[$key] = array_combine($keys, $row);
			}
			
			$newdata = array();
			$modelkey = array();
			foreach($data as $k=>$item){
				foreach($item as $name=>$value){
					$names = explode('.', $name);
					if(!in_array($names[0], $modelkey)){
						$modelkey[] = $names[0];
					}
					$newdata[$k][$names[0]][$names[1]] = $value;
				}
			}
			
			
			if(isset($this->controller->request->data['ax-model'])){
				$model = $this->controller->request->data['ax-model'];
				if($model){
					if($this->controller->loadModel($model)){
						if(in_array($model, $modelkey)){
							foreach($newdata as $rdb){
								$this->controller->{$model}->saveAssociated($rdb);
							}
							$response['status'] = 1;
							$response['info'] = 'All rows have been saved successfull!';
							die(json_encode($response));
						}else{
							$response['status'] = -1;
							$response['info'] = 'Model key in CSV not correct <b>' .$model .'</b>';
							die(json_encode($response));
						}
					}else{
						$response['status'] = -1;
						$response['info'] = 'Not isset model <b>' .$model .'</b> in this system';
						die(json_encode($response));
					}
				}else{
					$response['status'] = -1;
					$response['info'] = '<b>ax-model</b> value is not null!';
					die(json_encode($response));
				}
			}else{
				$response['status'] = -1;
				$response['info'] = 'Missing option: <br/> data => array(ax-type => import, ax-model => Current Model name)';
				die(json_encode($response));
			}
		}
	}
}