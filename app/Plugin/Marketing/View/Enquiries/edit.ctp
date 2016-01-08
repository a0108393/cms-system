<!-- app/View/Company/form.ctp -->
<?php 
echo $this->Session->flash();
?>

<div class="marketing form">
	<?php echo $this->Form->create('Enquiry', array('id' => 'edit_data_form')); ?>

	<?php					
    echo $this->Form->inputDatepicker('enq_date',array( 
												'label' => array('class' => 'col-lg-2 control-label', 'text' => __('Enquiry Date')), 
												'class' => 'form-control',
												'id'	=>'datetimepicker',
												'required'=>'required'));
    echo $this->Form->input('name');
    echo $this->Form->input('email');
    echo $this->Form->input('contact',array('label'=>array(
													'class'	=>'col-lg-2 control-label','text'=>__('Contact No')),
													'class'	=>'form-control'));
    echo $this->Form->input('company');
    echo $this->Form->input('designation');
    echo $this->Form->input('marketing_channels_id', array(
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
																		'class' => 'form-group advertising_links'
																   ),
																   'empty'=>'Select one',
																   'options'=> ($advertisingLinks)));

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
	$thumb_status = false;
				if( !empty($files) ){ 
					echo '<div id="wrap-image-upload"><ul>';
					$thumbnail = array('jpg', 'png', 'gif', 'bmp');
					foreach( $files as $key => $value ){
						$thumb = '';
						$info = new SplFileInfo($value);
						if(in_array($info->getExtension(), $thumbnail)) {
							$thumb_status = true;
							$thumb = '<img width="50" src="'. $this->Html->url('/uploads/enq/'.$value) .'" alt="" />'; 
							echo '<li>'. $thumb .'<a class="group1" target="_blank" href="'.$this->Html->url('/uploads/enq/'.$value).'">'.$key.'</a> 
									  <a onClick="delete_file(this)" href="javascript:void(0)" class="delete-file">x</a>
									  <input type="hidden" value="'.$value.'" name="data[Enquiry][files]['.$key.']">
							  </li>';
						}
						else {
							echo '<li><a target="_blank" href="'.$this->Html->url('/uploads/enq/'.$value).'">'.$key.'</a> 
									  <a onClick="delete_file(this)" href="javascript:void(0)" class="delete-file">x</a>
									  <input type="hidden" value="'.$value.'" name="data[Enquiry][files]['.$key.']">
							  </li>';
						}
						
					}
					echo ' </ul></div>';
					
				}
				
				
		echo '</div>
		 </div>';
	if($thumb_status) {
		echo '<script type="text/javascript">
			$(document).ready(function() {
				$(".group1").colorbox({rel:\'group1\'});
			})
		</script>';
	}
	echo $this->Form->input('agent_id',array('type'=>'select',
											 'div' => array(
												'class' => 'form-group'
											 ),
											 'empty'=>'Select one',
											 'options'=>$userAgents,
											 'default'=> CakeSession::read("Auth.User.id"),
											 'id'=>'agent',
											 'label'=>array('text'=>'Sales Agent',
														   'class'=>'col-lg-2 control-label') 
											));
	
	echo $this->Form->input('affiliate_id',array('type'=>'select',
												 'div' => array(
													'class' => 'form-group affiliate',
												 ),
												 'empty'=>'',
												 'options'=>$affiliates,
												 'id'=>'affiliate',
												 'after'=>'<div id="search_suggest"></div></div>',
												 'label'=>array('text'=>'Affiliate Agent',
																'class'=>'col-lg-2 control-label')
												 ));
	
	echo $this->Form->submit('Submit', array(
		'class' => 'btn btn-primary',
		'div' =>  'form-group',
		'before' => '<div class="col-lg-offset-2 col-lg-10">',
		'after' => $this->Html->link('Cancel', array('action' => 'index'), array('class' => 'btn btn-default')) . '</div>',
	));
	echo $this->Form->end(); ?>
</div>

<?php
$this->Js->get('#marketing_channels')->event('change', 
	$this->Js->request(array(
		'controller'=>'enquiries',
		'action'=>'bindAjax'
		), array(
		'update'=>'#marketing_advertising_links',
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

?>
<script type="text/javascript" charset="utf-8">
	$('#affiliate').selectToAutocomplete();
	function push_adv_link(value){
		/* if( value == 14 ){
			$('.advertising_links').hide();
			$('.affiliate').show();
		}else{
			$('.advertising_links').show();
			$('.affiliate').hide();
		} */
	}
	var mar_channels = $('#marketing_channels').val();
	push_adv_link(mar_channels);
	
	function delete_file(el){
		$(el).parent().remove();
	}
</script>

