<?php

define('APP_DIR', 'app');
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));
define('WEBROOT_DIR', 'webroot');
define('WWW_ROOT', ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS);
define('UPLOAD_TEMP', WWW_ROOT . 'uploads'. DS .'tmp');
define('UPLOAD_ENQ', WWW_ROOT . 'uploads' . DS . 'enq');

//save file
	$file_name	= ( isset($_REQUEST['ax-file-name']) && !empty($_REQUEST['ax-file-name']) )?$_REQUEST['ax-file-name']:'';
	$full_size	= isset($_REQUEST['ax-file-size'])?$_REQUEST['ax-file-size']:0;
	
	if($_FILES['ax_file_input']){
		// print_r($_REQUEST);die;
		$file = $_FILES['ax_file_input'];
		$file_name 	= !empty($file_name)? $file_name:$_FILES['ax_file_input']['name'];
		$full_size	= ($full_size)?$full_size:$_FILES['ax_file_input']['size'];
		
		$ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
		
		$filename = md5($file_name . date('i:s')) . '.' . $ext;
		if(isset($_REQUEST['ax-thumbPath']))
			move_uploaded_file($file['tmp_name'], WWW_ROOT . $_REQUEST['ax-thumbPath'] . $filename);
		move_uploaded_file($file['tmp_name'], UPLOAD_TEMP . DS . $filename);
		echo json_encode(array('path'=> $filename, 'name'=> $file_name, 'size'=>$full_size, 'status'=>1, 'info'=>'File uploaded'));
		die;
	}else{
		die('Invalid Url!');
	}
?>