<!-- app/View/Ratecard/form.ctp -->
<?php 
	$act = $this->action;
?>
<div class="company form">
<?php echo $this->Form->create('Ratecard'); ?>

<?php
    echo $this->Form->input('name', array('readonly' => true));
    echo $this->Form->input('description', array(
		'type' => 'textarea',
		'readonly' => true
	));
?>

<?php 
		echo $this->Form->input('type', array(
		'label' => false,
		'class' => '',
		'before' => '<label class="col-lg-2 control-label">'. __('Retainer type') . '</label><div class="col-lg-10"><label class="radio Ratecard-retainer_type">',
		'separator' => '</label><label class="radio Ratecard-retainer_type">',
		'after' => '</label></div>',
		'type' => 'radio',
		'legend' => false,
		'options' => $RetainerType,
		'data-toggle' => 'radio',
		'default' => 1,
		'disabled' => true
	));
	
	?>
<?php 
	echo $this->Form->input('discount', array(
		'label' => array(
			'text' => 'Flat Retainer Discount Rate',
			'class' => 'col-lg-2 control-label'
		),
		'readonly' => true	
	));
	echo $this->Form->input('clients', array(
		'id' => 'multiple-suggest-select',
		'type' => 'text',
		'disabled' => true
	));
	?>

	<?php echo $this->Form->end(); ?>
</div>
<script type="text/javascript">
	function setVisible(check){
		if(check == 2){
			$('#RatecardDiscount').val('');
			$('#RatecardDiscount').attr('disabled','disabled');
		}else{
			$('#RatecardDiscount').removeAttr('disabled');
		}
	}
	var onloadVal = $('.Ratecard-retainer_type input:checked').val();
	setVisible(onloadVal);
	$('label.Ratecard-retainer_type').click(function(){
		var check = $(this).children('input').val();
		setVisible(check);
	});
	$('#notify_message').parent().parent().addClass('displaynone');
	// $('#send_notify_check').click(function(){
		// var check = $(this).children('input').prop('checked');
		// if(check == false){
			// $(this).parent().parent().next().removeClass('displaynone');
		// }else{
			// $(this).parent().parent().next().addClass('displaynone');
			// $(this).parent().parent().next().find('textarea').val('');
		// }
	// });
	<?php if($act != 'delete'){ ?>
	$('#multiple-suggest-select').tokenInput(
		<?php echo json_encode($all_clients); ?> 
			,{
				preventDuplicates: true,
				onAdd: function (item) {
                    if(!item.default){
						$('#multiple-suggest-select').tokenInput("remove", {id: item.id});
						BootstrapDialog.alert('Ratecard already exists for client, please remove before applying this ratecard');
					}
                },
				<?php if(!empty($current_clients)){ ?>
				prePopulate: <?php echo json_encode($current_clients); ?>
				<?php } ?>
			}
	);
	<?php } ?>
</script>