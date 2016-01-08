<?php
App::uses('FormHelper', 'View/Helper');

/**
 * BootstrapFormHelper.
 *
 * Applies styling-rules for Bootstrap 3
 *
 * To use it, just save this file in /app/View/Helper/BootstrapFormHelper.php
 * and add the following code to your AppController:
 *   	public $helpers = array(
 *		    'Form' => array(
 *		        'className' => 'BootstrapForm'
 *	  	  	)
 *		);
 *
 * @link https://gist.github.com/Suven/6325905
 */
class BootstrapFormHelper extends FormHelper {
	public $helpers = array('Html');
    public function create($model = null, $options = array()) {
		
        $defaultOptions = array(
            'inputDefaults' => array(
                'div' => array(
                	'class' => 'form-group'
                ),
                'label' => array(
                	'class' => 'col-lg-2 control-label'
                ),
                'between' => '<div class="col-lg-10">',
                'seperator' => '</div>',
                'after' => '</div>',
                'class' => 'form-control',
            ),
            'class' => 'form-horizontal',
            'role' => 'form',
			'novalidate' => false
        );
		
		if(isset($options['error']) && $options['error'] == false){
			$defaultOptions['inputDefaults']['error'] = false;
			unset($options['error']);
		}
		if (isset($options['bootstrap']) && $options['bootstrap'] == false) {
			$defaultOptions = array(
				'class' => 'form-horizontal',
				'role' => 'form',
				'novalidate' => false
			);
			unset($options['bootstrap']);
		}
        if(!empty($options['inputDefaults'])) {
            $options = array_merge($defaultOptions['inputDefaults'], $options['inputDefaults']);
        } else {
            $options = array_merge($defaultOptions, $options);
        } 
        return parent::create($model, $options);
    }
    
    // Remove this function to show the fieldset & language again
    public function inputs($fields = null, $blacklist = null, $options = array()) {
		if (isset($options['bootstrap']) && $options['bootstrap'] == false) {
			unset($options['bootstrap']);
			return parent::inputs($fields, $blacklist, $options);
		}
		
    	$options = array_merge(array('fieldset' => false), $options);
    	return parent::inputs($fields, $blacklist, $options);
    }
    
    public function submit($caption = null, $options = array()) {
	
	    $defaultOptions = array(
	    	'class' => 'btn btn-primary',
	    	'div' =>  'form-group',
			'data-style' => 'expand-right',
	    	'before' => '<div class="col-lg-offset-2 col-lg-10">',
	    	'after' => '</div>',
	    );
		if (isset($options['bootstrap']) && $options['bootstrap'] == false) {
			$defaultOptions = array(
				'class' => 'btn btn-primary',
			);
			
			unset($options['bootstrap']);
			
		}
		
		if(!isset($options['bootstrap'])){
			if(isset($options['confirm'])){
				$options['onclick'] = 'confirmSubmitForm($(this), \''. $options['confirm'] .'\'); return false;';
			}
			unset($options['confirm']);
		}
	
        $options = array_merge($defaultOptions, $options); 
		$options['class'] .= ' ladda-button';
	    return parent::submit($caption, $options);
    }
	public function input($fieldName, $options = array()) {
		if (isset($options['bootstrap']) && $options['bootstrap'] == false) {
			unset($options['bootstrap']);
			return parent::input($fieldName, $options);
		}
		$model = $this->_getModel($this->model());
		 
		if($model && is_array($fieldOptions = $model->getFieldProperties($fieldName)))
		{
			if(!empty($options))
				$options = am($fieldOptions, $options);
			else
				$options = $fieldOptions;
		}
		
		return parent::input($fieldName, $options);
	}
	
	public function inputDatepicker($fieldName, $options = array()) {
		$ran = substr(str_shuffle(md5(time())),0, 5);
		$defaultOptions = array(
			'format' => phpToJsDateFormat(Configure::read('Settings.Formats.date_format')),
			'startWeek' => (int)Configure::read('Settings.Formats.start_week'),
			'pickTime' => 0
		);
		
		if(!isset($options['id'])){
			$options['id'] = 'datetimepicker';
		}
		else{
			$options['id'] = 'datetimepicker_'. $ran;
		}
		if(!empty($options['datepicker_setup'])){
			$datepicker_setup = array_merge($defaultOptions, $options['datepicker_setup']);
		}else{
			$datepicker_setup = $defaultOptions;
		}
		
		$options['type'] = 'text';
		$options['autocomplete'] = 'off';
		
		$script = '$(function () {
			$(\'#'.$options['id'].'\').datetimepicker('. json_encode($datepicker_setup) .');
		});';
		
		unset($options['datepicker_setup']);
		return $this->input($fieldName, $options).$this->Html->scriptBlock($script);
	}
	
	public function inputFilter($tableName, $fieldName, $type, $options = array()) {
		
		$value = isset($this->request->query['Search']) ? $this->request->query['Search'] : array();
		$options['bootstrap'] = false;
		if (isset($this->request->query['Search']) && isset($this->request->query['Search'][$tableName]) && isset($this->request->query['Search'][$tableName][$fieldName])) {
			$value = $this->request->query['Search'][$tableName][$fieldName];
		} else {
			$value = false;
		}

		switch ($type){
			case "range_date":
				if(!isset($options['class'])){
					$options['class'] = 'form-control input-xsmall';
				}
				else{
					$options['class'] .= ' form-control input-xsmall';
				}
				
				if(!isset($options['placeholder'])){
					$placeholder = __('From').' '.ucfirst($fieldName);
					$placeholder_to = __('To').' '.ucfirst($fieldName);
				}
				else{
					$placeholder = __('From').' '.$options['placeholder'];
					$placeholder_to = __('To').' '.$options['placeholder'];
				}
				if(!isset($options['type'])){
					$options['type'] = 'text';
				}
				
				$options['label'] = array('text' => '');
				
				$options1 = $options;
				
				$options['placeholder'] = $placeholder;
				$options['id'] = 'datepicker_from_search';
				$options1['id'] = 'datepicker_to_search';
				$options1['placeholder'] = $placeholder_to;
				if($value){
					if (is_array($value)) {
						if(isset($value['from_date']) && $value['from_date'] != ''){
							$options['value'] =  $value['from_date'];
						}
						if(isset($value['to_date']) && $value['to_date'] != ''){
							$options1['value'] =  $value['to_date'];
						}					
					}				
				}

				return $this->inputDatepicker("Search[$tableName][$fieldName][from_date]", $options).$this->inputDatepicker("Search[$tableName][$fieldName][to_date]", $options1);
			case "select":
				if(!isset($options['class'])){
					$options['class'] = 'form-control';
				}
				else{
					$options['class'] .= ' form-control';
				}
				
				$options['type'] = 'select';
				$options['label'] = array('text' => '');	 
				$options['empty'] = __('Select %s', $options['placeholder']);	 
				if($value){
					$options['value'] = $value;
				}

				return $this->input("Search[$tableName][$fieldName]", $options);
			case "range":
				if(!isset($options['class'])){
					$options['class'] = 'form-control input-xsmall';
				}
				else{
					$options['class'] .= ' form-control input-xsmall';
				}
				
				if(!isset($options['placeholder'])){
					$placeholder = __('From').' '.ucfirst($fieldName);
					$placeholder_to = __('To').' '.ucfirst($fieldName);
				}
				else{
					$placeholder = __('From').' '.$options['placeholder'];
					$placeholder_to = __('To').' '.$options['placeholder'];
				}
				if(!isset($options['type'])){
					$options['type'] = 'text';
				}
				
				$options['label'] = array('text' => '');
				
				$options1 = $options;
				
				$options['placeholder'] = $placeholder;
				$options1['placeholder'] = $placeholder_to;

				if($value){
					if (is_array($value)) {
						if(isset($value['from']) && $value['from'] != ''){
							$options['value'] =  $value['from'];
						}
						if(isset($value['to']) && $value['to'] != ''){
							$options1['value'] =  $value['to'];
						}					
					}				
				}
				
				return $this->input("Search[$tableName][$fieldName][from]", $options).$this->input("Search[$tableName][$fieldName][to]", $options1);
			default:
				if(!isset($options['class'])){
					$options['class'] = 'form-control';
				}
				else{
					$options['class'] .= ' form-control';
				}
				
				if(!isset($options['placeholder'])){
					$options['placeholder'] = ucfirst($fieldName);
				}
				if(!isset($options['type'])){
					$options['type'] = 'text';
				}
				$options['label'] = array('text' => '');	
				
				if($value){
					$options['value'] = $value;
				
				}
				
				return $this->input("Search[$tableName][$fieldName]", $options);
		}
	}
	public function inputUpload($options = array()){
		$id_container = 'ajax_upload-' . substr(str_shuffle(md5(time())),0, 5);
		$defaultOptions = array(
			'maxFiles' => 1,
			'fieldName' => 'upload[]',
			'maxFileSize' => '2M',
			'url' => Router::url('/upload.php', true),
			'flash' => Router::url('/uploader.swf', true),
			'errorBootstrapDialog' => true,
			'label' => 'Upload file',
			'verticalFrom' => false
		);
		$options = array_merge($defaultOptions, $options);
		$options['elementId'] = $id_container;
		
		$html = '';
		if($options['verticalFrom']){
			$html .= '<div class="form-group"><label class="col-lg-2 control-label">'. $options['label'] .'</label><div class="col-lg-10">';
		}
		$html .= '<div id="'. $id_container .'"></div>';
		
		$html .= "<script type=\"text/javascript\"> \n";
		$html .= '$("#'. $id_container .'").ajaxupload(';
		
		//option of ajax upload
		$html .= json_encode($options);
		
		$html .= "); \n </script>";
		if($options['verticalFrom']){
			$html .= '</div></div>';
		}
		return $html;
	}
	public function checkExport($value = '', $header = ''){
		$html = '<label id="headercheck_3" class="checkbox checked">' .
					'<span class="icons">' .
						'<span class="first-icon fui-checkbox-unchecked"></span>' .
						'<span class="second-icon fui-checkbox-checked"></span>' .
					'</span>' .
					'<input class="fieldname" type="checkbox" data-toggle="checkbox" value="'. $value .'" checked="checked">'. 
					'<input class="fieldheader" type="hidden" value="'. $header .'">';
		return $html;
	}
	public function gridPostAction($icon, $url = null, $item = false, $confirmMessage = false, $option = array())
	{
		$title = '';
		$options = $option;
		$options['escape'] = false;
		/*if($confirmMessage){
			$options['onclick'] = 'confirmDialog(this.href, \''. str_replace("'","\'", $confirmMessage). '\'); return false;';
			$confirmMessage = false;
		}*/
		
		if($icon){
			$title = '<i class="glyphicon glyphicon-'. $icon .'"></i>';
		}
		
		if(is_array($url)){
			if(!isset($url['action'])) {
				$url['action'] = 'index';
			}		
			//if(isset($url['action'])){
				$module = isset($url['plugin']) ? $url['plugin'] : $this->plugin;
				$controller	 = isset($url['controller']) ? $url['controller'] : $this->params['controller'];
				$user_module = Access::__getPermissionCurrentModule($module);
				$action = $url['action'];
				
				switch ($action){
					case "add":
						if(!Access::checkPermissionCreateModule($module)){
							return '';
						}
						return parent::postLink($title, $url, $options, $confirmMessage);
					default:
						if(!Access::checkRow($module, $controller, $action, $item)){
							return '';
						}
						return parent::postLink($title, $url, $options, $confirmMessage);
				}
			//}
		}
		else{
			die('not support');
		}
		return parent::postLink($title, $url, $options, $confirmMessage);
    }
}