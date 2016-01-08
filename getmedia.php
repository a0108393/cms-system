<?php
define('APP_DIR', 'app');
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));
define('WEBROOT_DIR', 'webroot');
define('WWW_ROOT', ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS);
define('UPLOAD_PATH', WWW_ROOT . 'images');
$directory = UPLOAD_PATH;

$output = '';
$output .= '[';


$filetype = array('jpg','png','jpeg','bmp','gif');

 if (is_dir($directory)){
    if ($dh = opendir($directory)){
        while (($file = readdir($dh)) !== false){
			$ext = pathinfo($file, PATHINFO_EXTENSION);
			if(in_array($ext, $filetype)){
				$output .= '{title: "' . $file .'", value: "images/' . $file .'"},';
			}
        }
		if($output != '['){
			$output = substr($output, 0, -1);
		}
        closedir($dh);
    }
}
$output .= ']';
print_r($output);die;
// Make output a real JavaScript file!
header('Content-type: text/javascript');
header('pragma: no-cache');
header('expires: 0'); 
echo $output;
?>