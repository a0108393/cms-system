<!-- app/View/Company/form.ctp -->
<?php 
echo $this->Session->flash();
?>	
	<?php echo $this->Form->create('Enquiry', array('id' => 'edit_data_form')); ?>
<div class="box">
	<div class="enquiry login-form">
	<?php					
   /*  echo $this->Form->inputDatepicker('enq_date',array( 
												'label' => array('class' => 'col-lg-2 control-label', 'text' => __('Enquiry Date')), 
												'class' => 'form-control',
												'id'	=>'datetimepicker')); */
    echo $this->Form->input('name');
    echo $this->Form->input('email');
    echo $this->Form->input('contact',array('label'=>array(
													'class'	=>'col-lg-2 control-label','text'=>__('Contact No')),
													'class'	=>'form-control'));
    echo $this->Form->input('company');
    echo $this->Form->input('designation');
    echo $this->Form->input('marketing_channels_id', array(
							'label' => array('text' => __('How did you hear about us?'), 'class' => 'col-lg-2 control-label'),
							'options' => $channels,
							'div'=>array('class'=>'form-group channels'),
							'class'=>'select-ui-primary', 
							'default'=> Configure::read('Settings.Company.DefaultChannel'),
							'id'=>'marketing_channels',
							'onChange'=>'push_adv_link(this.value)',
							'after'=>'<img id="loading" src="'.$this->base.'/images/loading.gif'.'" /> </div>'
	));
    echo $this->Form->input('marketing_advertising_links_id',array('id'=>'marketing_advertising_links',
																   'div' => array(
																		'class' => 'form-group advertising_links',
																		'style'=>'display:none;'
																   ),
																   'empty'=>'Select one',
																   'options'=> ($advertisingLinks)));
    echo $this->Form->input('affiliate_id',array('type'=>'select',
												 'div' => array(
													'class' => 'form-group affiliate',
													'style'=>'display:none;'
												 ),
												 'empty'=>'Select affiliate',
												 'options'=>$affiliates,
												 'id'=>'affiliate',
												 'after'=>'<div id="search_suggest"></div></div>',
												 'label'=>array('text'=>'Affiliate Agent',
																'class'=>'col-lg-2 control-label')
												 ));
    echo $this->Form->input('request');
	echo '<div class="form-group">
			<label class="col-lg-2 control-label" ></label>
			<div class="col-lg-10">
	';
	echo $this->Form->inputUpload(array(
		'fieldName' => 'data[Enquiry][files]', 
		'title' => 'Select a file or drop file to here',
		'maxFiles'=>10,
		'thumbPath' => 'uploads/enq/'
	));
	
				if( !empty($files) ){ 
				echo '<div id="wrap-image-upload"><ul>';
					foreach( $files as $key => $value ){
						echo '<li><a href="'.$this->Html->url('/uploads/'.$value).'">'.$key.'</a> 
									  <a onClick="delete_file(this)" href="javascript:void(0)" class="delete-file">x</a>
									  <input type="hidden" value="'.$value.'" name="data[Enquiry][files]['.$key.']">
							  </li>
							 ';
					}
				echo ' </ul></div>';
				}
				
				
		echo '</div>
		 </div>';
	$this->Captcha->render(array('modelName'=>'Enquiry','required'=>true));
	echo $this->Form->submit('Submit', array(
		'class' => 'btn btn-primary',
		'div' =>  'form-group',
		'before' => '<div class="col-lg-offset-2 col-lg-10">',
		'after' => $this->Html->link('Cancel', array('action' => 'index'), array('class' => 'btn btn-default')) . '</div>',
	));
?>	
	</div>
</div>
<?php	
	echo $this->Form->end(); ?>

<?php
$this->Js->get('#marketing_channels')->event('change', 
	$this->Js->request(array(
		'controller'=>'enquiries',
		'action'=>'bindAjax'
		), array(
		// 'update'=>'#marketing_advertising_links',
		'async' => true,
		'method' => 'post',
		'dataExpression'=>true,
		'before'   => '$("img#loading").attr("style", "display:block;")', 
		'complete' => '$("img#loading").attr("style", "display:none")',
		'data'=> $this->Js->serializeForm(array(
			'isForm' => true,
			'inline' => true
			))
	))
);

/* $this->Js->get('#affiliate')->event('keyup', 
	$this->Js->request(array(
		'controller'=>'enquiries',
		'action'=>'bindSuggestAjax'
		), array(
			'update'=>'#search_suggest',
			'async' => true,
			'method' => 'post',
			'dataExpression'=>true,
			'data'=> $this->Js->serializeForm(array('
				isForm' => true,
				'inline' => true 
				))
	))
); */
?>
<script type="text/javascript" charset="utf-8">
	// $('#affiliate').selectToAutocomplete();
	$('#marketing_channels').select2({
		'width': 300
	});
	function push_adv_link(value){
		var _text = $("#marketing_channels option:selected").text();
		console.log(_text.indexOf('Referrals'));
		if( _text.indexOf('Referrals') > 0 ){
			//$('.advertising_links').hide();
			$('.affiliate').show();
			$('.advertising_links').show();
		}else{
			//$('.advertising_links').show();
			$('.affiliate').hide();
			$('.advertising_links').hide();
		}
	}
	var mar_channels = $('#marketing_channels').val();
	push_adv_link(mar_channels);
	
	function delete_file(el){
		$(el).parent().remove();
	}
</script>

